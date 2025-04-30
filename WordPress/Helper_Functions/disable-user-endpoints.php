<?php

/* Block WP REST API access to users */
add_filter('rest_authentication_errors', function ($result) {
  if (strpos($_SERVER['REQUEST_URI'], '/wp/v2/users') !== false) {
    if (! is_user_logged_in() || ! current_user_can('list_users')) {
      return new WP_Error('rest_cannot_access', 'User data is protected.', array('status' => 403));
    }
  }
  return $result;
});
