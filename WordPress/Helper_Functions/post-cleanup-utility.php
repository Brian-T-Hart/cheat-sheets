<?php
/**
 * Plugin Name: Post Cleanup Utility
 * Description: Cleans mis-encoded characters and removes old DOCTYPE/html/body tags from posts.
 * Version: 1.0
 * Author: Brian Hart
 */

if (!defined('ABSPATH')) {
    exit; // Prevent direct access
}

// call the function using https://yoursite.com/wp-admin/?cleanup_post_id=95

/**
 * Clean a single post by ID
 */
function pcu_cleanup_single_post($post_id) {
    $post = get_post($post_id);

    if (!$post || $post->post_type !== 'post') {
        return false;
    }

    $replacements = array(
        // 'â€™' => "'",
        // 'â€œ' => '"',
        // 'â€˜' => "'",
        // 'â€“' => '–',
        // 'â€”' => '—',
        // 'â€¦' => '…',
        // 'â€¢' => '•',
        // 'â€' => '"',
        // 'Â' => '',
        // '¦' => '…',
        // 'Ã' => 'A',
        // 'Ã©' => 'é',
        // 'Ã¨' => 'è',
        // 'Ã¢' => 'â',
        // 'Ãª' => 'ê',
        // 'Ã®' => 'î',
        // 'Ã´' => 'ô',
        // 'Ã»' => 'û',
        // 'ÃŸ' => 'ß',
        // 'Ã¡' => 'á',
        // 'Ã±' => 'ñ',
        '&acirc;&#8364;&#8220;' => ',',
        '&acirc;&#8364;&#8482;' => "'",
        '&acirc;&#8364;&#732;' => "'",
        '&acirc;&#8364;&#381;' => '—',
        '&acirc;&#8364;&#352;' => '…',
        '&acirc;&#8364;&brvbar;' => '…',
        '&acirc;&#8364;&#339;' => '"',
        '&Acirc;&nbsp;' => ' ',
        '&acirc;&#8364;' => '"',
    );

    $updated = false;

    $clean_text = function($text) use ($replacements, &$updated) {
        $original = $text;

        // Remove old DOCTYPE, html, body tags
        $text = preg_replace('#<!DOCTYPE.*?>#i', '', $text);
        $text = preg_replace('#<html.*?>#i', '', $text);
        $text = preg_replace('#</html>#i', '', $text);
        $text = preg_replace('#<body.*?>#i', '', $text);
        $text = preg_replace('#</body>#i', '', $text);

        // Replace mis-encoded characters
        $text = str_replace(array_keys($replacements), array_values($replacements), $text);

        if ($text !== $original) {
            $updated = true;
        }

        return $text;
    };

    $post_content = $clean_text($post->post_content);
    $post_title   = $clean_text($post->post_title);
    $post_excerpt = $clean_text($post->post_excerpt);

    if ($updated) {
        wp_update_post(array(
            'ID' => $post->ID,
            'post_content' => $post_content,
            'post_title'   => $post_title,
            'post_excerpt' => $post_excerpt
        ));
    }

    return $updated;
}// pcu_cleanup_single_post

/**
 * Clean all posts
 */
function pcu_cleanup_all_posts() {
    $posts = get_posts(array(
        'numberposts' => -1,
        'post_type'   => 'post',
        'post_status' => 'any'
    ));

    foreach ($posts as $post) {
        pcu_cleanup_single_post($post->ID);
    }
}// pcu_cleanup_all_posts

add_action('admin_init', function() {
    if (isset($_GET['cleanup_post_id'])) {
        $post_id = intval($_GET['cleanup_post_id']);
        pcu_cleanup_single_post($post_id);
        exit('Post cleaned.');
    }
});