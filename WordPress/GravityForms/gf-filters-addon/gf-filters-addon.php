<?php

if (class_exists('GFForms')) {
    define('GF_FILTERS_ADDON_VERSION', '1.0');
    require_once(__DIR__ . '/class-gffiltersaddon.php');
    GFFiltersAddOn::get_instance();
}
