<?php
// Ensure this file is being included by a parent file
if (!defined('ABSPATH')) {
    die('Direct access to this file is not allowed.');
}

require_once plugin_dir_path(__FILE__) . 'class-import.php';
require_once plugin_dir_path(__FILE__) . 'class-export.php';
require_once plugin_dir_path(__FILE__) . 'class-sync.php';
require_once plugin_dir_path(__FILE__) . 'class-reset.php';

class Bricks_Remote_Template_Sync_Import_Export {
    public static function render_import_export_page() {
        error_log("Rendering import/export page");
        
        if (!current_user_can('manage_options')) {
            error_log("User does not have manage_options capability");
            return;
        }

        $is_license_valid = is_client_plugin_license_valid();
        error_log("License valid: " . ($is_license_valid ? 'true' : 'false'));

        $saved_google_sheet_url = get_option('bricks_remote_sync_google_sheet_url', '');
        $feedback_message = '';
        $feedback_type = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $is_license_valid) {
            error_log("POST request received");
            // ... (rest of the POST handling code remains the same)
        }

        $template_file = plugin_dir_path(__FILE__) . '../admin/partials/import-export-page.php';
        error_log("Template file path: " . $template_file);

        if (file_exists($template_file)) {
            error_log("Template file exists, including it now");
            include $template_file;
        } else {
            error_log("Template file does not exist");
            echo "Error: Template file not found.";
        }
    }
}