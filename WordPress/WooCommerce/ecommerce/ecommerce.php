<?php

// Define a constant variable for ecommerce directory
define('YPM_ECOMMERCE_DIR', get_stylesheet_directory() . '/functions/ecommerce/');

// Define a constant variable for ecommerce JS file
define('YPM_ECOMMERCE_JS_DIR', YPM_ECOMMERCE_DIR . 'assets/js/ecommerce-datalayer.js');

require_once YPM_ECOMMERCE_DIR . 'includes/push-view-product-to-datalayer.php';
require_once YPM_ECOMMERCE_DIR . 'includes/push-view-cart-to-datalayer.php';
require_once YPM_ECOMMERCE_DIR . 'includes/push-purchase-to-datalayer.php';