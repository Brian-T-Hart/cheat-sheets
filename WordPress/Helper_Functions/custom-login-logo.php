<?php

/**
 * Replace WordPress logo with custom logo on login page
 */
function custom_login_logo() {
  $logo_url = site_url() . '/relative_url_of_logo';

  echo '<style type="text/css">
      #login h1 a, .login h1 a {
          background-image: url(' .  $logo_url . ');
          height: 100px; /* Change the height as needed */
          width: 100%; /* Use 100% width for responsiveness */
          background-size: contain; /* Adjust this property as needed */
      }
  </style>';
}
add_action('login_enqueue_scripts', 'custom_login_logo');