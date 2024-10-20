<?php

/**
 * Set page redirects based on specific conditions
 */
function custom_template_redirect() {
    if ( is_page( 'goodies' ) && ! is_user_logged_in() ) {
        wp_redirect( home_url( '/signup/' ) );
        exit();
    }
}
add_action( 'template_redirect', 'custom_template_redirect' );