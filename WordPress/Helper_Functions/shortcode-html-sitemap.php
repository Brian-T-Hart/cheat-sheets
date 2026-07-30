<?php

/**
 * HTML Sitemap
 *
 * Examples:
 *
 * [html_sitemap]
 * [html_sitemap post_types="page,post,resource"]
 * [html_sitemap show_categories="false"]
 * [html_sitemap exclude="12,34"]
 */

add_shortcode( 'html_sitemap', function( $atts ) {

    $atts = shortcode_atts( [
        'post_types'      => 'page, post',
        'show_categories' => 'true',
        'exclude'         => '',
    ], $atts );

    $post_types = array_map( 'trim', explode( ',', $atts['post_types'] ) );

    $exclude = array_filter(
        array_map( 'intval', explode( ',', $atts['exclude'] ) )
    );

    ob_start();

    echo '<div class="html-sitemap">';

    foreach ( $post_types as $post_type ) {

        /*
         * ----------------------------
         * Pages
         * ----------------------------
         */

        if ( $post_type === 'page' ) {

            $pages = get_pages( [
                'sort_column' => 'menu_order,post_title',
                'post_status' => 'publish',
                'exclude'     => $exclude,
            ] );

            $filtered = [];

            foreach ( $pages as $page ) {

                if ( post_password_required( $page ) ) {
                    continue;
                }

                if ( html_sitemap_is_noindex( $page->ID ) ) {
                    continue;
                }

                $filtered[] = $page;
            }

            if ( $filtered ) {

                echo '<section class="sitemap-section sitemap-pages">';
                echo '<h2>Pages</h2>';
                echo '<ul>';

                echo wp_list_pages( [
                    'title_li' => '',
                    'echo'     => false,
                    'include'  => wp_list_pluck( $filtered, 'ID' ),
                ] );

                echo '</ul>';
                echo '</section>';
            }

            continue;
        }

        /*
         * ----------------------------
         * Posts / CPTs
         * ----------------------------
         */

        $object = get_post_type_object( $post_type );

        if ( ! $object ) {
            continue;
        }

        $posts = get_posts( [
            'post_type'      => $post_type,
            'posts_per_page' => -1,
            'orderby'        => 'title',
            'order'          => 'ASC',
            'post_status'    => 'publish',
            'post__not_in'   => $exclude,
        ] );

        $posts = array_filter( $posts, function( $post ) {

            if ( post_password_required( $post ) ) {
                return false;
            }

            if ( html_sitemap_is_noindex( $post->ID ) ) {
                return false;
            }

            return true;

        } );

        if ( empty( $posts ) ) {
            continue;
        }

        echo '<section class="sitemap-section sitemap-' . esc_attr( $post_type ) . '">';

        echo '<h2>' . esc_html( $object->labels->name ) . '</h2>';

        echo '<ul>';

        foreach ( $posts as $post ) {

            echo '<li>';

            echo '<a href="' . esc_url( get_permalink( $post ) ) . '">';

            echo esc_html( get_the_title( $post ) );

            echo '</a>';

            echo '</li>';

        }

        echo '</ul>';

        echo '</section>';

    }

    /*
     * ----------------------------
     * Categories
     * ----------------------------
     */

    if ( filter_var( $atts['show_categories'], FILTER_VALIDATE_BOOLEAN ) ) {

        $categories = get_categories( [
            'hide_empty' => true,
            'orderby'    => 'name',
        ] );

        if ( $categories ) {

            echo '<section class="sitemap-section sitemap-categories">';

            echo '<h2>Categories</h2>';

            echo '<ul>';

            foreach ( $categories as $category ) {

                echo '<li>';

                echo '<a href="' . esc_url( get_category_link( $category ) ) . '">';

                echo esc_html( $category->name );

                echo '</a>';

                echo ' (' . intval( $category->count ) . ')';

                echo '</li>';

            }

            echo '</ul>';

            echo '</section>';

        }

    }

    echo '</div>';

    return ob_get_clean();

} );

/**
 * Detect if a post/page is set to noindex in Yoast SEO.
 */
function html_sitemap_is_noindex( $post_id ) {

    // Yoast stores this as "1" when noindex is enabled.
    $robots = get_post_meta( $post_id, '_yoast_wpseo_meta-robots-noindex', true );

    return (string) $robots === '1';

}