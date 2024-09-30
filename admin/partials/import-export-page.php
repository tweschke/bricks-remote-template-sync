<?php
// Ensure this file is being included by a parent file
if (!defined('ABSPATH')) {
    die('Direct access to this file is not allowed.');
}

class Bricks_Remote_Template_Sync_Import_Export {
    public static function render_import_export_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        $is_license_valid = is_client_plugin_license_valid();
        $saved_google_sheet_url = get_option('bricks_remote_sync_google_sheet_url', '');
        $message = '';
        $message_type = 'updated';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $is_license_valid) {
            if (isset($_POST['import_remote_templates']) && !empty($_FILES['csv_file']['tmp_name'])) {
                $message = self::import_from_csv($_FILES['csv_file']['tmp_name']);
            } elseif (isset($_POST['import_remote_templates_json']) && !empty($_FILES['json_file']['tmp_name'])) {
                $message = self::import_from_json($_FILES['json_file']['tmp_name']);
            } elseif (isset($_POST['import_from_google_sheet']) && !empty($_POST['google_sheet_url'])) {
                $message = self::import_from_google_sheet($_POST['google_sheet_url']);
            } elseif (isset($_POST['reset_remote_templates'])) {
                $message = self::reset_remote_templates();
                if (strpos($message, 'Error') !== false) {
                    $message_type = 'error';
                }
            }
        }

        include plugin_dir_path(__FILE__) . '../admin/partials/import-export-page.php';
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