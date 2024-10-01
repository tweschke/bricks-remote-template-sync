<?php
// Ensure this file is being included by a parent file
if (!defined('ABSPATH')) {
    die('Direct access to this file is not allowed.');
}

class Bricks_Remote_Template_Sync_Sync {
    /**
     * Import templates from Google Sheet
     * 
     * @param string $google_sheet_url URL of the Google Sheet
     * @return array Result of the import operation
     */
    public static function import_from_google_sheet($google_sheet_url) {
        self::log_message("Starting import from Google Sheet: $google_sheet_url");

        if (!self::is_valid_google_sheet_url($google_sheet_url)) {
            self::log_message("Invalid Google Sheet URL");
            return array('success' => false, 'message' => 'Invalid Google Sheet URL. Please ensure it ends with /pub?output=csv');
        }

        $response = wp_remote_get($google_sheet_url);
        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            self::log_message("Failed to fetch Google Sheet: $error_message");
            return array('success' => false, 'message' => 'Failed to fetch Google Sheet: ' . $error_message);
        }

        $csv_data = wp_remote_retrieve_body($response);
        if (empty($csv_data)) {
            self::log_message("Retrieved empty data from Google Sheet");
            return array('success' => false, 'message' => 'Retrieved empty data from Google Sheet');
        }

        self::log_message("Successfully retrieved data from Google Sheet");

        $lines = explode("\n", $csv_data);
        array_shift($lines); // Skip header row
        
        $new_templates = array();
        foreach ($lines as $line) {
            $data = str_getcsv($line);
            if (count($data) >= 4) { // Ensure we have at least 4 columns
                $new_templates[] = array(
                    'name' => sanitize_text_field($data[1]), // Use second column for name
                    'url' => esc_url_raw($data[2]), // Use third column for URL
                    'password' => isset($data[3]) ? sanitize_text_field($data[3]) : '' // Use fourth column for password
                );
            }
        }

        if (empty($new_templates)) {
            self::log_message("No valid template data found in the Google Sheet");
            return array('success' => false, 'message' => 'No valid template data found in the Google Sheet');
        }

        self::log_message("Found " . count($new_templates) . " templates in the Google Sheet");

        $global_settings = get_option('Bricks_Global_Settings', array());
        $global_settings['remoteTemplates'] = $new_templates;
        $update_result = update_option('Bricks_Global_Settings', $global_settings);

        if ($update_result) {
            self::log_message("Successfully updated templates in the database");
            return array('success' => true, 'message' => 'Successfully imported ' . count($new_templates) . ' templates');
        } else {
            self::log_message("Failed to update templates in the database");
            return array('success' => false, 'message' => 'Failed to update templates in the database');
        }
    }

    /**
     * Save Google Sheet URL
     * 
     * @param string $url Google Sheet URL
     * @return array Result of the save operation
     */
    public static function save_google_sheet_url($url) {
        self::log_message("Attempting to save Google Sheet URL: $url");

        if (!self::is_valid_google_sheet_url($url)) {
            self::log_message("Invalid Google Sheet URL");
            return array('success' => false, 'message' => 'Invalid Google Sheet URL. Please ensure it ends with /pub?output=csv');
        }

        $sanitized_url = esc_url_raw($url);
        $update_result = update_option('bricks_remote_sync_google_sheet_url', $sanitized_url);

        if ($update_result) {
            self::log_message("Google Sheet URL saved successfully");
            return array('success' => true, 'message' => 'Google Sheet URL saved successfully');
        } else {
            self::log_message("Failed to save Google Sheet URL");
            return array('success' => false, 'message' => 'Failed to save Google Sheet URL');
        }
    }

    /**
     * Validate Google Sheet URL
     * 
     * @param string $url Google Sheet URL to validate
     * @return bool True if valid, false otherwise
     */
    private static function is_valid_google_sheet_url($url) {
        return (strpos($url, 'docs.google.com') !== false && strpos($url, 'output=csv') !== false);
    }

    /**
     * Log messages for debugging
     * 
     * @param string $message Message to log
     */
    private static function log_message($message) {
        error_log("Bricks Remote Template Sync: $message");
    }
}