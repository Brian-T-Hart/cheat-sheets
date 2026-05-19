<?php

/**
 * Gets a list of plugins that have available updates
 * Accessible via AJAX at /wp-admin/admin-ajax.php?action=get_plugin_update_list
 */
function get_plugin_update_list()
{

  if (!current_user_can('manage_options')) {
    wp_send_json_error("You do not have permission to access this page");
  }

  if (!function_exists('get_plugin_updates')) {
    include_once(ABSPATH . 'wp-admin/includes/update.php');
  }

  $plugin_updates = get_plugin_updates();

  if (empty($plugin_updates)) {
    echo "All plugins are up to date";
    wp_die();
  }

  $plugin_info = "";

  foreach ($plugin_updates as $plugin) {
    $plugin_info .= $plugin->Name . " " . $plugin->Version . " => " . $plugin->update->new_version . "<br>";
  }

  echo $plugin_info;
  wp_die();
}
add_action("wp_ajax_get_plugin_update_list", "get_plugin_update_list");
