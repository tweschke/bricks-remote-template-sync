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
        error_log("Bricks Remote Template Sync: Rendering import/export page");
        
        if (!current_user_can('manage_options')) {
            error_log("Bricks Remote Template Sync: User does not have manage_options capability");
            return;
        }

        $is_license_valid = is_client_plugin_license_valid();
        error_log("Bricks Remote Template Sync: License valid: " . ($is_license_valid ? 'true' : 'false'));

        $saved_google_sheet_url = get_option('bricks_remote_sync_google_sheet_url', '');
        $feedback_message = '';
        $feedback_type = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $is_license_valid) {
            error_log("Bricks Remote Template Sync: POST request received");
            
            if (isset($_POST['import_remote_templates']) && !empty($_FILES['csv_file']['tmp_name'])) {
                check_admin_referer('bb_import_templates');
                $result = Bricks_Remote_Template_Sync_Import::import_from_csv($_FILES['csv_file']['tmp_name']);
                $feedback_message = $result ? 'Templates imported successfully from CSV.' : 'Failed to import templates from CSV.';
                $feedback_type = $result ? 'success' : 'error';
            }

            if (isset($_POST['import_from_google_sheet']) && !empty($_POST['google_sheet_url'])) {
                check_admin_referer('bb_import_templates');
                $result = Bricks_Remote_Template_Sync_Sync::import_from_google_sheet($_POST['google_sheet_url']);
                $feedback_message = $result ? 'Templates synced successfully from Google Sheets.' : 'Failed to sync templates from Google Sheets.';
                $feedback_type = $result ? 'success' : 'error';
            }

            if (isset($_POST['import_remote_templates_json']) && !empty($_FILES['json_file']['tmp_name'])) {
                check_admin_referer('bb_import_templates');
                $result = Bricks_Remote_Template_Sync_Import::import_from_json($_FILES['json_file']['tmp_name']);
                $feedback_message = $result ? 'Templates imported successfully from JSON.' : 'Failed to import templates from JSON.';
                $feedback_type = $result ? 'success' : 'error';
            }

            if (isset($_POST['reset_remote_templates'])) {
                check_admin_referer('bb_import_templates');
                error_log("Bricks Remote Template Sync: Reset templates request received");
                $result = Bricks_Remote_Template_Sync_Reset::reset_remote_templates();
                if ($result) {
                    $feedback_message = 'All remote templates have been reset. The bricks_remote_templates option is now an empty array.';
                    $feedback_type = 'warning';
                    error_log("Bricks Remote Template Sync: Templates reset successfully");
                } else {
                    $feedback_message = 'Failed to reset remote templates. Please check the error log for details.';
                    $feedback_type = 'error';
                    error_log("Bricks Remote Template Sync: Failed to reset templates");
                }
            }
        }

        $template_file = plugin_dir_path(__FILE__) . '../admin/partials/import-export-page.php';
        error_log("Bricks Remote Template Sync: Template file path: " . $template_file);

        if (file_exists($template_file)) {
            error_log("Bricks Remote Template Sync: Template file exists, including it now");
            include $template_file;
        } else {
            error_log("Bricks Remote Template Sync: Template file does not exist");
            echo "Error: Template file not found.";
        }
    }

    public static function export_to_csv() {
        if (!current_user_can('manage_options') || !is_client_plugin_license_valid()) {
            wp_die('You do not have sufficient permissions to access this page.');
        }
        Bricks_Remote_Template_Sync_Export::export_to_csv();
    }

    public static function export_to_json() {
        if (!current_user_can('manage_options') || !is_client_plugin_license_valid()) {
            wp_die('You do not have sufficient permissions to access this page.');
        }
        Bricks_Remote_Template_Sync_Export::export_to_json();
    }

    public static function save_google_sheet_url() {
        check_ajax_referer('bb_save_google_sheet_url', 'nonce');
        if (!current_user_can('manage_options') || !is_client_plugin_license_valid()) {
            wp_send_json_error('You do not have sufficient permissions to perform this action.');
        }
        Bricks_Remote_Template_Sync_Sync::save_google_sheet_url();
    }
}