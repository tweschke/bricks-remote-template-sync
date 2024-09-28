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
        if (!current_user_can('manage_options')) {
            return;
        }

        $is_license_valid = is_client_plugin_license_valid();
        $saved_google_sheet_url = get_option('bricks_remote_sync_google_sheet_url', '');
        $feedback_message = '';
        $feedback_type = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $is_license_valid) {
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
                $result = Bricks_Remote_Template_Sync_Reset::reset_remote_templates();
                $feedback_message = $result ? 'All remote templates have been reset.' : 'Failed to reset remote templates.';
                $feedback_type = $result ? 'warning' : 'error';
            }
        }

        include plugin_dir_path(__FILE__) . '../admin/partials/import-export-page.php';
    }
}