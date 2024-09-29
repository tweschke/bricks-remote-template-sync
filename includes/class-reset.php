<?php
// Ensure this file is being included by a parent file
if (!defined('ABSPATH')) {
    die('Direct access to this file is not allowed.');
}

class Bricks_Remote_Template_Sync_Reset {
    public static function reset_remote_templates() {
        $global_settings = get_option('Bricks_Global_Settings');
        
        if (!is_array($global_settings)) {
            return "Error: Unable to retrieve Bricks global settings.";
        }

        if (isset($global_settings['remoteTemplates'])) {
            $global_settings['remoteTemplates'] = array();
            $update_result = update_option('Bricks_Global_Settings', $global_settings);
            
            if ($update_result) {
                return "All remote templates have been successfully reset.";
            } else {
                return "Error: Failed to update Bricks global settings. No changes were made.";
            }
        } else {
            return "No remote templates found in Bricks global settings. Nothing to reset.";
        }
    }

    public static function update_remote_templates($new_templates) {
        $global_settings = get_option('Bricks_Global_Settings', array());
        $global_settings['remoteTemplates'] = $new_templates;
        return update_option('Bricks_Global_Settings', $global_settings);
    }

    public static function get_remote_templates() {
        $global_settings = get_option('Bricks_Global_Settings', array());
        return isset($global_settings['remoteTemplates']) ? $global_settings['remoteTemplates'] : array();
    }
}