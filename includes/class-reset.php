<?php
// Ensure this file is being included by a parent file
if (!defined('ABSPATH')) {
    die('Direct access to this file is not allowed.');
}

class Bricks_Remote_Template_Sync_Reset {
    public static function reset_remote_templates() {
        error_log("Bricks Remote Template Sync: Attempting to reset remote templates...");

        // Reset WordPress option
        update_option('bricks_remote_templates', array());
        error_log("Bricks Remote Template Sync: WordPress option reset to empty array");

        // Reset Bricks Builder remote templates
        if (class_exists('Bricks\Templates')) {
            $bricks_templates = \Bricks\Templates::get_templates();
            foreach ($bricks_templates as $template_id => $template) {
                if (isset($template['source']) && $template['source'] === 'remote') {
                    \Bricks\Templates::delete_template($template_id);
                    error_log("Bricks Remote Template Sync: Deleted Bricks template with ID: " . $template_id);
                }
            }
            error_log("Bricks Remote Template Sync: Bricks Builder remote templates cleared");
        } else {
            error_log("Bricks Remote Template Sync: Bricks\Templates class not found");
        }

        // Verify reset
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