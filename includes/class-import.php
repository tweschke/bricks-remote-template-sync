<?php
// Ensure this file is being included by a parent file
if (!defined('ABSPATH')) {
    die('Direct access to this file is not allowed.');
}

class Bricks_Remote_Template_Sync_Import {
    public static function import_from_csv($file_path) {
        $file = fopen($file_path, 'r');
        if ($file) {
            fgetcsv($file); // Skip header row
            $imported = 0;
            $remote_templates = get_option('bricks_remote_templates', array());
            while (($line = fgetcsv($file)) !== FALSE) {
                if (count($line) >= 3) {
                    $template_id = sanitize_text_field($line[0]);
                    $name = sanitize_text_field($line[1]);
                    $url = esc_url_raw($line[2]);
                    $password = isset($line[3]) ? sanitize_text_field($line[3]) : '';
                    $remote_templates[$template_id] = array(
                        'name' => $name,
                        'url' => $url,
                        'password' => $password
                    );
                    $imported++;
                }
            }
            fclose($file);
            update_option('bricks_remote_templates', $remote_templates);
            return $imported > 0;
        }
        return false;
    }

    public static function import_from_json($file_path) {
        $json_data = file_get_contents($file_path);
        $templates = json_decode($json_data, true);
        if (is_array($templates)) {
            $imported = 0;
            $remote_templates = get_option('bricks_remote_templates', array());
            foreach ($templates as $template_id => $template) {
                if (isset($template['name'], $template['url'])) {
                    $remote_templates[$template_id] = array(
                        'name' => sanitize_text_field($template['name']),
                        'url' => esc_url_raw($template['url']),
                        'password' => isset($template['password']) ? sanitize_text_field($template['password']) : ''
                    );
                    $imported++;
                }
            }
            update_option('bricks_remote_templates', $remote_templates);
            return $imported > 0;
        }
        return false;
    }
}