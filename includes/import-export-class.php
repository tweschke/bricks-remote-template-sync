<?php
/**
 * Import and Export functionality for Bricks Remote Template Sync
 *
 * This file contains the class that handles import and export operations
 * for the Bricks Remote Template Sync plugin.
 *
 * @package Bricks_Remote_Template_Sync
 */

if (!defined('ABSPATH')) {
    die('Direct access is not allowed.');
}

require_once BRICKS_REMOTE_SYNC_PLUGIN_DIR . 'includes/class-import.php';
require_once BRICKS_REMOTE_SYNC_PLUGIN_DIR . 'includes/class-export.php';
require_once BRICKS_REMOTE_SYNC_PLUGIN_DIR . 'includes/class-reset.php';
require_once BRICKS_REMOTE_SYNC_PLUGIN_DIR . 'includes/class-sync.php';
// Removed: require_once BRICKS_REMOTE_SYNC_PLUGIN_DIR . 'includes/license-check.php';

class Bricks_Remote_Template_Sync_Import_Export {
    /**
     * Render the import/export page
     */
    public static function render_import_export_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        $saved_google_sheet_url = get_option('bricks_remote_sync_google_sheet_url', '');
        $message = '';
        $message_type = 'updated';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['import_remote_templates']) && !empty($_FILES['csv_file']['tmp_name'])) {
                self::log_message("Initiating CSV import");
                $message = Bricks_Remote_Template_Sync_Import::import_from_csv($_FILES['csv_file']['tmp_name']);
                self::log_message("CSV import result: $message");
            } elseif (isset($_POST['import_remote_templates_json']) && !empty($_FILES['json_file']['tmp_name'])) {
                self::log_message("Initiating JSON import");
                $message = Bricks_Remote_Template_Sync_Import::import_from_json($_FILES['json_file']['tmp_name']);
                self::log_message("JSON import result: $message");
            } elseif (isset($_POST['save_google_sheet_url']) && !empty($_POST['google_sheet_url'])) {
                self::log_message("Saving Google Sheet URL");
                $save_result = Bricks_Remote_Template_Sync_Sync::save_google_sheet_url($_POST['google_sheet_url']);
                $message = $save_result['message'];
                $message_type = $save_result['success'] ? 'updated' : 'error';
                self::log_message("Save Google Sheet URL result: " . ($save_result['success'] ? 'Success' : 'Failure') . " - $message");
                $saved_google_sheet_url = get_option('bricks_remote_sync_google_sheet_url', '');
            } elseif (isset($_POST['run_google_sheet_sync'])) {
                self::log_message("Initiating Google Sheet sync");
                $sync_url = get_option('bricks_remote_sync_google_sheet_url', '');
                if (empty($sync_url)) {
                    $message = "No Google Sheet URL saved. Please save a URL first.";
                    $message_type = 'error';
                } else {
                    $result = Bricks_Remote_Template_Sync_Sync::import_from_google_sheet($sync_url);
                    $message = $result['message'];
                    $message_type = $result['success'] ? 'updated' : 'error';
                    self::log_message("Google Sheet sync result: " . ($result['success'] ? 'Success' : 'Failure') . " - $message");
                }
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
     * Save Google Sheet URL via AJAX
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

        $result = Bricks_Remote_Template_Sync_Sync::save_google_sheet_url($_POST['google_sheet_url']);
        if ($result['success']) {
            self::log_message("Google Sheet URL saved successfully");
            wp_send_json_success($result['message']);
        } else {
            self::log_message("Failed to save Google Sheet URL: " . $result['message']);
            wp_send_json_error($result['message']);
        }
    }

    /**
     * Run Google Sheet sync via AJAX
     */
    public static function run_google_sheet_sync() {
        self::log_message("run_google_sheet_sync method called");
        
        check_ajax_referer('bb_run_google_sheet_sync', 'nonce');
        
        if (!current_user_can('manage_options')) {
            self::log_message("User does not have sufficient permissions");
            wp_send_json_error('You do not have sufficient permissions to perform this action.');
            return;
        }

        $sync_url = get_option('bricks_remote_sync_google_sheet_url', '');
        if (empty($sync_url)) {
            self::log_message("No Google Sheet URL saved");
            wp_send_json_error('No Google Sheet URL saved. Please save a URL first.');
            return;
        }

        $result = Bricks_Remote_Template_Sync_Sync::import_from_google_sheet($sync_url);
        if ($result['success']) {
            self::log_message("Google Sheet sync completed successfully");
            wp_send_json_success($result['message']);
        } else {
            self::log_message("Google Sheet sync failed: " . $result['message']);
            wp_send_json_error($result['message']);
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