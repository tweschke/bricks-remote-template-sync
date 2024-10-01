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
        if (!self::is_valid_google_sheet_url($google_sheet_url)) {
            return array('success' => false, 'message' => 'Invalid Google Sheet URL. Please ensure it ends with /pub?output=csv');
        }

        $response = wp_remote_get($google_sheet_url);
        if (is_wp_error($response)) {
            return array('success' => false, 'message' => 'Failed to fetch Google Sheet: ' . $response->get_error_message());
        }

        $csv_data = wp_remote_retrieve_body($response);
        if (empty($csv_data)) {
            return array('success' => false, 'message' => 'Retrieved empty data from Google Sheet');
        }

        $lines = explode("\n", $csv_data);
        array_shift($lines); // Skip header row
        
        $new_templates = array();
        foreach ($lines as $line) {
            $data = str_getcsv($line);
            if (count($data) >= 3) {
                $new_templates[] = array(
                    'name' => sanitize_text_field($data[0]),
                    'url' => esc_url_raw($data[1]),
                    'password' => isset($data[2]) ? sanitize_text_field($data[2]) : ''
                );
            }
        }

        if (empty($new_templates)) {
            return array('success' => false, 'message' => 'No valid template data found in the Google Sheet');
        }

        $global_settings = get_option('Bricks_Global_Settings', array());
        $global_settings['remoteTemplates'] = $new_templates;
        $update_result = update_option('Bricks_Global_Settings', $global_settings);

        if ($update_result) {
            return array('success' => true, 'message' => 'Successfully imported ' . count($new_templates) . ' templates');
        } else {
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
        if (!self::is_valid_google_sheet_url($url)) {
            return array('success' => false, 'message' => 'Invalid Google Sheet URL. Please ensure it ends with /pub?output=csv');
        }

        $sanitized_url = esc_url_raw($url);
        $update_result = update_option('bricks_remote_sync_google_sheet_url', $sanitized_url);

        if ($update_result) {
            return array('success' => true, 'message' => 'Google Sheet URL saved successfully');
        } else {
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
        return (strpos($url, 'docs.google.com') !== false && strpos($url, '/pub?output=csv') !== false);
    }
}