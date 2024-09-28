<?php
// Ensure this file is being included by a parent file
if (!defined('ABSPATH')) {
    die('Direct access to this file is not allowed.');
}

class Bricks_Remote_Template_Sync_Reset {
    public static function reset_remote_templates() {
        error_log("Attempting to reset remote templates...");
        $result = delete_option('bricks_remote_templates');
        error_log("Delete option result: " . ($result ? 'true' : 'false'));
        
        // Verify that the option has been deleted
        $check = get_option('bricks_remote_templates', 'option_not_found');
        error_log("Check after delete: " . ($check === 'option_not_found' ? 'Option successfully deleted' : 'Option still exists'));
        
        return $result;
    }
}