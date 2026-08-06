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
 * [html_sitemap include="12,34"]
 * [html_sitemap post_types="post" group_posts_by_category="true"]
 */

add_shortcode('html_sitemap', function ($atts) {

    // Enqueue the stylesheet for the HTML sitemap.
    $style_path = get_stylesheet_directory() . '/assets/css/html-sitemap.css';
    $style_ver  = file_exists($style_path) ? filemtime($style_path) : mt_rand();

    wp_enqueue_style(
        'haulers-html-sitemap',
        get_stylesheet_directory_uri() . '/assets/css/html-sitemap.css',
        [],
        $style_ver
    );

    // Default shortcode attributes.
    $atts = shortcode_atts([
        'heading'                 => 'Pages',
        'post_types'              => 'page',
        'show_categories'         => 'false',
        'exclude'                 => '',
        'include'                 => '',
        'group_posts_by_category' => 'false',
    ], $atts);

    // Parse attributes.
    $post_types = html_sitemap_parse_csv_list($atts['post_types']);
    $posts_page_id = (int) get_option('page_for_posts');
    $posts_page_url = $posts_page_id > 0 ? get_permalink($posts_page_id) : '';

    // Only nest posts under the posts page when pages are also being rendered.
    $nest_posts_under_posts_page = in_array('post', $post_types, true) && in_array('page', $post_types, true) && $posts_page_id > 0;

    // Parse exclude/include attributes into arrays of IDs.
    $exclude = html_sitemap_parse_csv_ids($atts['exclude']);
    $include = html_sitemap_parse_csv_ids($atts['include']);

    $group_posts_by_category = html_sitemap_parse_bool_attr($atts['group_posts_by_category']);
    $show_categories         = html_sitemap_parse_bool_attr($atts['show_categories']);

    ob_start();

    echo '<div class="html-sitemap">';

    foreach ($post_types as $post_type) {

        /*
         * ----------------------------
         * Pages
         * ----------------------------
         */

        if ($post_type === 'page') {

            $pages = get_pages([
                'sort_column' => 'menu_order,post_title',
                'post_status' => 'publish',
                'exclude'     => $exclude,
            ]);

            $filtered = [];

            foreach ($pages as $page) {

                if (post_password_required($page)) {
                    continue;
                }

                $filtered[] = $page;
            }

            if (! empty($include)) {
                $filtered = html_sitemap_filter_pages_by_include($filtered, $include);
            }

            if ($filtered) {

                echo '<section class="sitemap-section sitemap-pages">';
                echo '<h2>' . esc_html($atts['heading']) . '</h2>';

                $nested_posts = [];

                if ($nest_posts_under_posts_page) {
                    $nested_posts = html_sitemap_get_visible_posts('post', $exclude, $include);
                }

                echo html_sitemap_render_page_tree($filtered, $posts_page_id, $nested_posts, $group_posts_by_category);

                echo '</section>';
            }

            continue;
        }

        if ($post_type === 'post' && $nest_posts_under_posts_page) {
            continue;
        }

        /*
         * ----------------------------
         * Posts / CPTs
         * ----------------------------
         */

        $object = get_post_type_object($post_type);

        if (! $object) {
            continue;
        }

        $posts = html_sitemap_get_visible_posts($post_type, $exclude, $include);

        if (empty($posts)) {
            continue;
        }

        echo '<section class="sitemap-section sitemap-' . esc_attr($post_type) . '">';

        if ($post_type === 'post') {
            $post_heading = trim($atts['heading']) !== '' ? $atts['heading'] : $object->labels->name;

            if (! empty($posts_page_url)) {
                echo '<h2><a href="' . esc_url($posts_page_url) . '">' . esc_html($post_heading) . '</a></h2>';
            } else {
                echo '<h2>' . esc_html($post_heading) . '</h2>';
            }
        } else {
            echo '<h2>' . esc_html($object->labels->name) . '</h2>';
        }

        if ($post_type === 'post' && $group_posts_by_category) {
            echo html_sitemap_render_posts_grouped_by_category($posts, 'sitemap-post-categories');
        } else {
            echo '<ul>';

            foreach ($posts as $post) {

                echo '<li>';

                echo '<a href="' . esc_url(get_permalink($post)) . '">';

                echo esc_html(get_the_title($post));

                echo '</a>';

                echo '</li>';
            }

            echo '</ul>';
        }

        echo '</section>';
    }

    /*
     * ----------------------------
     * Categories
     * ----------------------------
     */

    if ($show_categories && empty($include)) {

        $categories = get_categories([
            'hide_empty' => true,
            'orderby'    => 'name',
        ]);

        if ($categories) {

            echo '<section class="sitemap-section sitemap-categories">';

            echo '<h2>Categories</h2>';

            echo '<ul>';

            foreach ($categories as $category) {

                echo '<li>';

                echo '<a href="' . esc_url(get_category_link($category)) . '">';

                echo esc_html($category->name);

                echo '</a>';

                echo ' (' . intval($category->count) . ')';

                echo '</li>';
            }

            echo '</ul>';

            echo '</section>';
        }
    }

    echo '</div>';

    return ob_get_clean();
});

/**
 * Parse a comma-separated list into trimmed string values.
 */
function html_sitemap_parse_csv_list($value)
{

    $items = array_map('trim', explode(',', (string) $value));

    return array_values(array_filter($items, static function ($item) {
        return $item !== '';
    }));
}

/**
 * Parse a comma-separated list of IDs into unique positive integers.
 */
function html_sitemap_parse_csv_ids($value)
{

    $ids = array_map('intval', explode(',', (string) $value));
    $ids = array_filter($ids, static function ($id) {
        return $id > 0;
    });

    return array_values(array_unique($ids));
}

/**
 * Parse a shortcode boolean-style attribute.
 */
function html_sitemap_parse_bool_attr($value)
{

    return filter_var($value, FILTER_VALIDATE_BOOLEAN);
}

/**
 * Detect if a post/page is set to noindex in Yoast SEO.
 */
function html_sitemap_is_noindex($post_id)
{

    // Yoast stores this as "1" when noindex is enabled.
    $robots = get_post_meta($post_id, '_yoast_wpseo_meta-robots-noindex', true);

    return (string) $robots === '1';
}

/**
 * Get published posts for a post type, excluding password-protected/noindex posts.
 */
function html_sitemap_get_visible_posts($post_type, $exclude = [], $include = [])
{

    $query_args = [
        'post_type'      => $post_type,
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
        'post_status'    => 'publish',
        'post__not_in'   => $exclude,
    ];

    if (! empty($include)) {
        $query_args['post__in'] = $include;
        $query_args['orderby']  = 'post__in';
        unset($query_args['order']);
    }

    $posts = get_posts($query_args);

    return array_filter($posts, function ($post) {

        if (post_password_required($post)) {
            return false;
        }

        if (html_sitemap_is_noindex($post->ID)) {
            return false;
        }

        return true;
    });
}

/**
 * Render hierarchical pages and optionally inject posts under the posts page.
 */
function html_sitemap_render_page_tree($pages, $posts_page_id = 0, $nested_posts = [], $group_posts_by_category = false)
{

    $pages_by_parent = [];
    $pages_by_id     = [];
    $noindex_lookup  = [];

    foreach ($pages as $page) {
        $page_id                    = (int) $page->ID;
        $pages_by_id[$page_id]    = $page;
        $noindex_lookup[$page_id] = html_sitemap_is_noindex($page_id);
    }

    foreach ($pages as $page) {
        $page_id = (int) $page->ID;

        // Remove noindex pages from output but keep their children in the tree.
        if (! empty($noindex_lookup[$page_id])) {
            continue;
        }

        $parent_id = html_sitemap_get_visible_parent_id($page, $pages_by_id, $noindex_lookup);

        if (! isset($pages_by_parent[$parent_id])) {
            $pages_by_parent[$parent_id] = [];
        }

        $pages_by_parent[$parent_id][] = $page;
    }

    return html_sitemap_render_page_branch(0, $pages_by_parent, (int) $posts_page_id, $nested_posts, (bool) $group_posts_by_category);
}

/**
 * Resolve the closest non-noindex parent ID that exists in the current page set.
 */
function html_sitemap_get_visible_parent_id($page, $pages_by_id, $noindex_lookup)
{

    $parent_id = (int) $page->post_parent;

    while ($parent_id > 0) {

        if (! isset($pages_by_id[$parent_id])) {
            return 0;
        }

        if (empty($noindex_lookup[$parent_id])) {
            return $parent_id;
        }

        $parent_id = (int) $pages_by_id[$parent_id]->post_parent;
    }

    return 0;
}

/**
 * Recursively render a page branch.
 */
function html_sitemap_render_page_branch($parent_id, $pages_by_parent, $posts_page_id, $nested_posts, $group_posts_by_category = false)
{

    if (empty($pages_by_parent[$parent_id])) {
        return '';
    }

    $output = '<ul>';

    foreach ($pages_by_parent[$parent_id] as $page) {
        $output .= '<li>';
        $output .= '<a href="' . esc_url(get_permalink($page)) . '">';
        $output .= esc_html(get_the_title($page));
        $output .= '</a>';

        $output .= html_sitemap_render_page_branch((int) $page->ID, $pages_by_parent, $posts_page_id, $nested_posts, $group_posts_by_category);

        if ((int) $page->ID === $posts_page_id && ! empty($nested_posts)) {
            if ($group_posts_by_category) {
                $output .= html_sitemap_render_posts_grouped_by_category($nested_posts, 'sitemap-nested-posts');
            } else {
                $output .= '<ul class="sitemap-nested-posts">';

                foreach ($nested_posts as $post) {
                    $output .= '<li>';
                    $output .= '<a href="' . esc_url(get_permalink($post)) . '">';
                    $output .= esc_html(get_the_title($post));
                    $output .= '</a>';
                    $output .= '</li>';
                }

                $output .= '</ul>';
            }
        }

        $output .= '</li>';
    }

    $output .= '</ul>';

    return $output;
}

/**
 * Keep pages explicitly included and any published descendants of those pages.
 */
function html_sitemap_filter_pages_by_include($pages, $include_ids)
{

    if (empty($include_ids)) {
        return $pages;
    }

    $include_lookup = array_fill_keys(array_map('intval', $include_ids), true);
    $pages_by_id    = [];

    foreach ($pages as $page) {
        $pages_by_id[(int) $page->ID] = $page;
    }

    return array_values(array_filter($pages, function ($page) use ($include_lookup, $pages_by_id) {

        $page_id = (int) $page->ID;

        if (isset($include_lookup[$page_id])) {
            return true;
        }

        $parent_id = (int) $page->post_parent;

        while ($parent_id > 0) {
            if (isset($include_lookup[$parent_id])) {
                return true;
            }

            if (! isset($pages_by_id[$parent_id])) {
                return false;
            }

            $parent_id = (int) $pages_by_id[$parent_id]->post_parent;
        }

        return false;
    }));
}

/**
 * Render posts grouped by category, with each category linking to its archive page.
 */
function html_sitemap_render_posts_grouped_by_category($posts, $wrapper_class = 'sitemap-post-categories')
{

    if (empty($posts)) {
        return '';
    }

    $groups = [];
    $uncategorized_posts = [];

    foreach ($posts as $post) {
        $categories = get_the_category($post->ID);

        if (empty($categories)) {
            $uncategorized_posts[] = $post;
            continue;
        }

        foreach ($categories as $category) {
            $term_id = (int) $category->term_id;

            if (! isset($groups[$term_id])) {
                $groups[$term_id] = [
                    'term'  => $category,
                    'posts' => [],
                ];
            }

            $groups[$term_id]['posts'][] = $post;
        }
    }

    if (! empty($groups)) {
        uasort($groups, function ($a, $b) {
            return strcasecmp($a['term']->name, $b['term']->name);
        });
    }

    $output = '<ul class="' . esc_attr($wrapper_class) . '">';

    foreach ($groups as $group) {
        $category = $group['term'];
        $cat_link = get_category_link($category);

        $output .= '<li>';

        if (is_wp_error($cat_link)) {
            $output .= '<span class="sitemap-category-title">' . esc_html($category->name) . '</span>';
        } else {
            $output .= '<a class="sitemap-category-link" href="' . esc_url($cat_link) . '">';
            $output .= esc_html($category->name);
            $output .= '</a>';
        }

        $output .= '<ul class="sitemap-category-posts">';

        foreach ($group['posts'] as $post) {
            $output .= '<li>';
            $output .= '<a href="' . esc_url(get_permalink($post)) . '">';
            $output .= esc_html(get_the_title($post));
            $output .= '</a>';
            $output .= '</li>';
        }

        $output .= '</ul>';
        $output .= '</li>';
    }

    if (! empty($uncategorized_posts)) {
        $output .= '<li>';
        $output .= '<span class="sitemap-category-title">Uncategorized</span>';
        $output .= '<ul class="sitemap-category-posts">';

        foreach ($uncategorized_posts as $post) {
            $output .= '<li>';
            $output .= '<a href="' . esc_url(get_permalink($post)) . '">';
            $output .= esc_html(get_the_title($post));
            $output .= '</a>';
            $output .= '</li>';
        }

        $output .= '</ul>';
        $output .= '</li>';
    }

    $output .= '</ul>';

    return $output;
}
