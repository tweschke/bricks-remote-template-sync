<?php
// Ensure this file is being included by a parent file
if (!defined('ABSPATH')) {
    die('Direct access to this file is not allowed.');
}

class Bricks_Remote_Template_Sync_Sync {
    public static function import_from_google_sheet($google_sheet_url) {
        $response = wp_remote_get($google_sheet_url);
        if (!is_wp_error($response)) {
            $csv_data = wp_remote_retrieve_body($response);
            $lines = explode("\n", $csv_data);
            array_shift($lines); // Skip header row
            $imported = 0;
            $remote_templates = get_option('bricks_remote_templates', array());
            foreach ($lines as $line) {
                $data = str_getcsv($line);
                if (count($data) >= 3) {
                    $template_id = sanitize_text_field($data[0]);
                    $name = sanitize_text_field($data[1]);
                    $url = esc_url_raw($data[2]);
                    $password = isset($data[3]) ? sanitize_text_field($data[3]) : '';
                    $remote_templates[$template_id] = array(
                        'name' => $name,
                        'url' => $url,
                        'password' => $password
                    );
                    $imported++;
                }
            }
            update_option('bricks_remote_templates', $remote_templates);
            return $imported > 0;
        }
        return false;
    }

    public static function save_google_sheet_url($url) {
        $sanitized_url = esc_url_raw($url);
        return update_option('bricks_remote_sync_google_sheet_url', $sanitized_url);
    }
}