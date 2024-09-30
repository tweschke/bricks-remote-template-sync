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
     * @return bool True if import was successful, false otherwise
     */
    public static function import_from_google_sheet($google_sheet_url) {
        $response = wp_remote_get($google_sheet_url);
        if (is_wp_error($response)) {
            return false;
        }

        $csv_data = wp_remote_retrieve_body($response);
        $lines = explode("\n", $csv_data);
        array_shift($lines); // Skip header row
        
        $new_templates = array();
        foreach ($lines as $line) {
            $data = str_getcsv($line);
            if (count($data) >= 3) {
                $new_templates[] = array(
                    'name' => sanitize_text_field($data[1]),
                    'url' => esc_url_raw($data[2]),
                    'password' => isset($data[3]) ? sanitize_text_field($data[3]) : ''
                );
            }
        }

        if (!empty($new_templates)) {
            $global_settings = get_option('Bricks_Global_Settings', array());
            $global_settings['remoteTemplates'] = $new_templates;
            update_option('Bricks_Global_Settings', $global_settings);
            return true;
        }

        return false;
    }

    /**
     * Save Google Sheet URL
     * 
     * @param string $url Google Sheet URL
     * @return bool True if URL was saved successfully, false otherwise
     */
    public static function save_google_sheet_url($url) {
        $sanitized_url = esc_url_raw($url);
        return update_option('bricks_remote_sync_google_sheet_url', $sanitized_url);
    }
}