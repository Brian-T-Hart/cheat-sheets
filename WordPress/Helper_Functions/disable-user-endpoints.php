<?php

/**
 * Disable the WordPress REST API users endpoint for non-logged-in users.
 * This is a security measure to prevent unauthorized access to user data.
 */
add_filter('rest_authentication_errors', function ($result) {
  if (! is_user_logged_in()) {
    if (strpos($_SERVER['REQUEST_URI'], '/wp/v2/users') !== false) {
      return new WP_Error('rest_cannot_access', 'User data is protected.', array('status' => 403));
    }
  }
  return $result;
});
