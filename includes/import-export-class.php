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
                self::log_message("Initiating CSV import");
                $message = Bricks_Remote_Template_Sync_Import::import_from_csv($_FILES['csv_file']['tmp_name']);
                self::log_message("CSV import result: $message");
            } elseif (isset($_POST['import_remote_templates_json']) && !empty($_FILES['json_file']['tmp_name'])) {
                self::log_message("Initiating JSON import");
                $message = Bricks_Remote_Template_Sync_Import::import_from_json($_FILES['json_file']['tmp_name']);
                self::log_message("JSON import result: $message");
            } elseif (isset($_POST['import_from_google_sheet']) && !empty($_POST['google_sheet_url'])) {
                self::log_message("Initiating Google Sheet import");
                $result = Bricks_Remote_Template_Sync_Sync::import_from_google_sheet($_POST['google_sheet_url']);
                $message = $result['message'];
                $message_type = $result['success'] ? 'updated' : 'error';
                self::log_message("Google Sheet import result: " . ($result['success'] ? 'Success' : 'Failure') . " - $message");
            } elseif (isset($_POST['reset_remote_templates'])) {
                self::log_message("Initiating template reset");
                $message = Bricks_Remote_Template_Sync_Reset::reset_remote_templates();
                self::log_message("Template reset result: $message");
            } elseif (isset($_POST['export_to_csv'])) {
                self::log_message("Initiating CSV export");
                Bricks_Remote_Template_Sync_Export::export_to_csv();
            } elseif (isset($_POST['export_to_json'])) {
                self::log_message("Initiating JSON export");
                Bricks_Remote_Template_Sync_Export::export_to_json();
            }

            if (strpos($message, 'Error') !== false || strpos($message, 'Failed') !== false) {
                $message_type = 'error';
            }
        }

        include BRICKS_REMOTE_SYNC_PLUGIN_DIR . 'admin/partials/import-export-page.php';
    }

    /**
     * Save Google Sheet URL via AJAX and perform immediate import
     */
    public static function save_google_sheet_url() {
        self::log_message("save_google_sheet_url method called");
        
        check_ajax_referer('bb_save_google_sheet_url', 'nonce');
        
        if (!current_user_can('manage_options')) {
            self::log_message("User does not have sufficient permissions");
            wp_send_json_error('You do not have sufficient permissions to perform this action.');
            return;
        }

        if (!isset($_POST['google_sheet_url'])) {
            self::log_message("Google Sheet URL not provided");
            wp_send_json_error('Google Sheet URL not provided.');
            return;
        }

        $url = $_POST['google_sheet_url'];
        $save_result = Bricks_Remote_Template_Sync_Sync::save_google_sheet_url($url);
        
        if ($save_result['success']) {
            self::log_message("Google Sheet URL saved successfully. Initiating import.");
            $import_result = Bricks_Remote_Template_Sync_Sync::import_from_google_sheet($url);
            
            if ($import_result['success']) {
                self::log_message("Import successful: " . $import_result['message']);
                wp_send_json_success("URL saved and import successful: " . $import_result['message']);
            } else {
                self::log_message("Import failed: " . $import_result['message']);
                wp_send_json_error("URL saved but import failed: " . $import_result['message']);
            }
        } else {
            self::log_message("Failed to save Google Sheet URL: " . $save_result['message']);
            wp_send_json_error($save_result['message']);
        }
    }

    /**
     * Log messages for debugging
     * 
     * @param string $message Message to log
     */
    private static function log_message($message) {
        error_log("Bricks Remote Template Sync (Import/Export): $message");
    }
}