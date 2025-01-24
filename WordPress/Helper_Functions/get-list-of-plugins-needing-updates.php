<?php

// /wp-admin/admin-ajax.php?action=get_list_of_plugins_needing_updates
add_action("wp_ajax_get_list_of_plugins_needing_updates", "get_list_of_plugins_needing_updates");

/**
 * Get list of plugins needing updates
 */
function get_list_of_plugins_needing_updates()
{

  if (!function_exists('get_plugin_updates')) {
    include_once(ABSPATH . 'wp-admin/includes/update.php');
  }

  $plugin_updates = get_plugin_updates();

  if (empty($plugin_updates)) {
    return "All plugins are up to date";
  }

  $plugin_info = "";

  foreach ($plugin_updates as $plugin) {
    $plugin_info .= $plugin->Name . " " . $plugin->Version . " => " . $plugin->update->new_version . "<br>";
  }

  echo $plugin_info;
  wp_die();
}