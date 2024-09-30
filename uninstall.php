<?php
// If uninstall not called from WordPress, then exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Remove the remoteTemplates from the Bricks global settings
$global_settings = get_option('Bricks_Global_Settings');
if (isset($global_settings['remoteTemplates'])) {
    unset($global_settings['remoteTemplates']);
    update_option('Bricks_Global_Settings', $global_settings);
}

// Remove plugin-specific options
delete_option('bricks_remote_sync_google_sheet_url');
delete_option('client_plugin_license_key');
delete_option('client_plugin_license_email');
delete_option('client_plugin_license_status');

// Clear any scheduled hooks
wp_clear_scheduled_hook('check_license_status');

// If you've added any custom database tables, you might want to remove them here
// global $wpdb;
// $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}your_custom_table");

// If you've added any custom user meta, you might want to remove it here
// delete_metadata('user', 0, 'your_user_meta_key', '', true);

// If you've added any custom post meta, you might want to remove it here
// delete_metadata('post', 0, 'your_post_meta_key', '', true);

// If you've registered any custom post types or taxonomies, you might want to 
// unregister them here and then flush rewrite rules
// unregister_post_type('your_custom_post_type');
// unregister_taxonomy('your_custom_taxonomy');
// flush_rewrite_rules();