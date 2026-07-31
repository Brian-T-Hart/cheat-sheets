<?php

/**
 * Shortcode to display the post type of a post/page/etc
 * @return string HTML output displaying the post type.
 */
function shortcode_display_post_type()
{
    global $post;
    $type = get_post_type($post);
    $object = get_post_type_object($type);
    $output = sprintf('<p>Found in %s</p>', ucwords($object->label));
    return $output;
}
add_shortcode('display_post_type', 'shortcode_display_post_type');
