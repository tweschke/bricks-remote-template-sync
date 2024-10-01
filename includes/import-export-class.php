<?php
if (!defined('ABSPATH')) {
    die('Direct access is not allowed.');
}

require_once BRICKS_REMOTE_SYNC_PLUGIN_DIR . 'includes/class-import.php';
require_once BRICKS_REMOTE_SYNC_PLUGIN_DIR . 'includes/class-export.php';
require_once BRICKS_REMOTE_SYNC_PLUGIN_DIR . 'includes/class-reset.php';
require_once BRICKS_REMOTE_SYNC_PLUGIN_DIR . 'includes/class-sync.php';

class Bricks_Remote_Template_Sync_Import_Export {
    /**
     * Render the import/export page
     */
    public static function render_import_export_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        $is_license_valid = true; // Replace with actual license check if implemented
        $saved_google_sheet_url = get_option('bricks_remote_sync_google_sheet_url', '');
        $message = '';
        $message_type = 'updated';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $is_license_valid) {
            if (isset($_POST['import_remote_templates']) && !empty($_FILES['csv_file']['tmp_name'])) {
                $message = Bricks_Remote_Template_Sync_Import::import_from_csv($_FILES['csv_file']['tmp_name']);
            } elseif (isset($_POST['import_remote_templates_json']) && !empty($_FILES['json_file']['tmp_name'])) {
                $message = Bricks_Remote_Template_Sync_Import::import_from_json($_FILES['json_file']['tmp_name']);
            } elseif (isset($_POST['import_from_google_sheet']) && !empty($_POST['google_sheet_url'])) {
                $result = Bricks_Remote_Template_Sync_Sync::import_from_google_sheet($_POST['google_sheet_url']);
                $message = $result['message'];
                $message_type = $result['success'] ? 'updated' : 'error';
            } elseif (isset($_POST['reset_remote_templates'])) {
                $message = Bricks_Remote_Template_Sync_Reset::reset_remote_templates();
            } elseif (isset($_POST['export_to_csv'])) {
                Bricks_Remote_Template_Sync_Export::export_to_csv();
            } elseif (isset($_POST['export_to_json'])) {
                Bricks_Remote_Template_Sync_Export::export_to_json();
            }

            if (strpos($message, 'Error') !== false || strpos($message, 'Failed') !== false) {
                $message_type = 'error';
            }
        }

        include BRICKS_REMOTE_SYNC_PLUGIN_DIR . 'admin/partials/import-export-page.php';
    }

    /**
     * Save Google Sheet URL via AJAX
     */
    public static function save_google_sheet_url() {
        check_ajax_referer('bb_save_google_sheet_url', 'nonce');
        if (!current_user_can('manage_options') || !is_client_plugin_license_valid()) {
            wp_send_json_error('You do not have sufficient permissions to perform this action.');
        }
        $result = Bricks_Remote_Template_Sync_Sync::save_google_sheet_url($_POST['google_sheet_url']);
        if ($result['success']) {
            wp_send_json_success($result['message']);
        } else {
            wp_send_json_error($result['message']);
        }
    }
}