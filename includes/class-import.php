<?php
if (!defined('ABSPATH')) {
    die('Direct access is not allowed.');
}

class Bricks_Remote_Template_Sync_Import {
    /**
     * Import templates from a CSV file
     *
     * @param string $file_path Path to the CSV file
     * @return string Result message
     */
    public static function import_from_csv($file_path) {
        if (($handle = fopen($file_path, "r")) !== FALSE) {
            $new_templates = array();
            $row = 0;
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if ($row > 0 && count($data) >= 3) { // Skip header row and ensure we have all fields
                    $new_templates[] = array(
                        'name' => sanitize_text_field($data[1]),
                        'url' => esc_url_raw($data[2]),
                        'password' => isset($data[3]) ? sanitize_text_field($data[3]) : ''
                    );
                }
                $row++;
            }
            fclose($handle);
            
            self::update_remote_templates($new_templates);
            return "Templates imported successfully from CSV.";
        }
        return "Error reading CSV file.";
    }

    /**
     * Import templates from a JSON file
     *
     * @param string $file_path Path to the JSON file
     * @return string Result message
     */
    public static function import_from_json($file_path) {
        $json_data = file_get_contents($file_path);
        $templates = json_decode($json_data, true);
        
        if (json_last_error() === JSON_ERROR_NONE) {
            $new_templates = array();
            foreach ($templates as $template) {
                if (isset($template['name'], $template['url'])) {
                    $new_templates[] = array(
                        'name' => sanitize_text_field($template['name']),
                        'url' => esc_url_raw($template['url']),
                        'password' => isset($template['password']) ? sanitize_text_field($template['password']) : ''
                    );
                }
            }
            self::update_remote_templates($new_templates);
            return "Templates imported successfully from JSON.";
        }
        return "Error reading JSON file.";
    }

    /**
     * Update remote templates in the database
     *
     * @param array $new_templates Array of new templates
     */
    private static function update_remote_templates($new_templates) {
        $global_settings = get_option('Bricks_Global_Settings', array());
        $global_settings['remoteTemplates'] = $new_templates;
        update_option('Bricks_Global_Settings', $global_settings);
    }
}