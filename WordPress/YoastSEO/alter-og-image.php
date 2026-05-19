<?php

/**
 * Alter the OpenGraph image if not in exclusion list
 * Requires Yoast SEO plugin
 */
function alter_existing_opengraph_image($image) {
    $image_id = '';
    $excluded_post_types = array('post');

    if (!is_singular($excluded_post_types) && !empty($image_id)) {
        $image_data = wp_get_attachment_image_src($image_id, 'full');

        if ($image_data) {
            $image = $image_data[0];
        }
    }

    return $image;
}
add_filter('wpseo_opengraph_image', 'alter_existing_opengraph_image');