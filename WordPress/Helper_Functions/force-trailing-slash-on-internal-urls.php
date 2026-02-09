<?php

/**
 * Force trailing slash on links
 * Ensures that all internal links are output with a trailing slash
 */
function aopp_force_trailing_slash_on_internal_urls( $url, $type ) {
    if ( is_admin() ) {
        return $url;
    }

    // Skip external URLs
    if ( strpos( $url, home_url() ) !== 0 ) {
        return $url;
    }

    // Skip file URLs
    if ( preg_match( '/\.[a-z0-9]{2,5}$/i', $url ) ) {
        return $url;
    }

    return trailingslashit( $url );
}

add_filter('user_trailingslashit', 'aopp_force_trailing_slash_on_internal_urls', 10, 2);
