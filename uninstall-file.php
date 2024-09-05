<?php
// If uninstall not called from WordPress, then exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Get the Bricks global settings
$global_settings = get_option('Bricks_Global_Settings');

// Remove the remoteTemplates from the global settings
if (isset($global_settings['remoteTemplates'])) {
    unset($global_settings['remoteTemplates']);
    update_option('Bricks_Global_Settings', $global_settings);
}

// Delete any other options or custom tables if you've added any
// delete_option('your_plugin_option');
// global $wpdb;
// $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}your_custom_table");
