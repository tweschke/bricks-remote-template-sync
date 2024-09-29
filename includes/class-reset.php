<?php
// Ensure this file is being included by a parent file
if (!defined('ABSPATH')) {
    die('Direct access to this file is not allowed.');
}

class Bricks_Remote_Template_Sync_Reset {
    public static function reset_remote_templates() {
        global $wpdb;
        error_log("Bricks Remote Template Sync: Attempting to reset remote templates...");

        // Check if the option exists before trying to delete it
        $option_exists = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $wpdb->options WHERE option_name = %s",
            'bricks_remote_templates'
        ));

        error_log("Bricks Remote Template Sync: Option exists check: " . ($option_exists ? 'true' : 'false'));

        if ($option_exists) {
            $result = delete_option('bricks_remote_templates');
            error_log("Bricks Remote Template Sync: Delete option result: " . ($result ? 'true' : 'false'));
        } else {
            error_log("Bricks Remote Template Sync: Option 'bricks_remote_templates' does not exist. Creating empty option.");
            $result = add_option('bricks_remote_templates', array());
            error_log("Bricks Remote Template Sync: Add empty option result: " . ($result ? 'true' : 'false'));
        }

        // Verify that the option has been reset
        $check = get_option('bricks_remote_templates', 'option_not_found');
        if ($check === 'option_not_found') {
            error_log("Bricks Remote Template Sync: Option 'bricks_remote_templates' not found after reset attempt.");
            return false;
        } elseif (empty($check)) {
            error_log("Bricks Remote Template Sync: Option 'bricks_remote_templates' successfully reset to empty array.");
            return true;
        } else {
            error_log("Bricks Remote Template Sync: Option 'bricks_remote_templates' still contains data after reset attempt.");
            return false;
        }
    }
}