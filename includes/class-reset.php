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
            
            if ($result) {
                // If deletion was successful, add an empty array as the new value
                add_option('bricks_remote_templates', array());
                error_log("Bricks Remote Template Sync: Empty array added as new value for bricks_remote_templates");
                return true;
            }
        } else {
            // If the option doesn't exist, create it with an empty array
            $result = add_option('bricks_remote_templates', array());
            error_log("Bricks Remote Template Sync: Add empty option result: " . ($result ? 'true' : 'false'));
            return $result;
        }

        // Final verification
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