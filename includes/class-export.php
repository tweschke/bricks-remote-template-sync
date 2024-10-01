<?php
if (!defined('ABSPATH')) {
    die('Direct access is not allowed.');
}

class Bricks_Remote_Template_Sync_Export {
    /**
     * Export templates to CSV
     */
    public static function export_to_csv() {
        try {
            self::log_message("Starting CSV export");
            
            if (!check_ajax_referer('bb_export_templates', 'nonce', false)) {
                throw new Exception('Nonce verification failed');
            }

            $templates = self::get_remote_templates();
            $filename = 'bricks_remote_templates_' . date('Y-m-d') . '.csv';

            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '"');

            $output = fopen('php://temp', 'w+');
            if ($output === false) {
                throw new Exception('Failed to open output stream');
            }

            fputcsv($output, array('ID', 'Name', 'URL', 'Password'));

            foreach ($templates as $id => $template) {
                fputcsv($output, array($id, $template['name'], $template['url'], $template['password']));
            }

            rewind($output);
            $csv_data = stream_get_contents($output);
            fclose($output);

            self::log_message("CSV export completed successfully");
            wp_send_json_success(base64_encode($csv_data));
        } catch (Exception $e) {
            self::log_message("CSV Export Error: " . $e->getMessage());
            wp_send_json_error('Export failed: ' . $e->getMessage());
        }
    }

    /**
     * Export templates to JSON
     */
    public static function export_to_json() {
        try {
            self::log_message("Starting JSON export");
            
            if (!check_ajax_referer('bb_export_templates', 'nonce', false)) {
                throw new Exception('Nonce verification failed');
            }

            $templates = self::get_remote_templates();
            $filename = 'bricks_remote_templates_' . date('Y-m-d') . '.json';

            header('Content-Type: application/json');
            header('Content-Disposition: attachment; filename="' . $filename . '"');

            $json_data = json_encode($templates, JSON_PRETTY_PRINT);
            if ($json_data === false) {
                throw new Exception('Failed to encode templates to JSON');
            }

            self::log_message("JSON export completed successfully");
            wp_send_json_success(base64_encode($json_data));
        } catch (Exception $e) {
            self::log_message("JSON Export Error: " . $e->getMessage());
            wp_send_json_error('Export failed: ' . $e->getMessage());
        }
    }

    /**
     * Get remote templates from database
     * 
     * @return array Array of remote templates
     */
    private static function get_remote_templates() {
        $global_settings = get_option('Bricks_Global_Settings', array());
        $templates = isset($global_settings['remoteTemplates']) ? $global_settings['remoteTemplates'] : array();
        self::log_message("Retrieved " . count($templates) . " templates from database");
        return $templates;
    }

    /**
     * Log messages for debugging
     * 
     * @param string $message Message to log
     */
    private static function log_message($message) {
        error_log("Bricks Remote Template Sync Export: " . $message);
    }
}