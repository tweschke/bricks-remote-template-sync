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
            self::log_error('Starting CSV export');
            
            if (!check_ajax_referer('bb_export_templates', 'nonce', false)) {
                self::log_error('Nonce verification failed for CSV export');
                throw new Exception('Nonce verification failed');
            }

            $templates = self::get_remote_templates();
            $filename = 'bricks_remote_templates_' . date('Y-m-d') . '.csv';

            $output = fopen('php://temp', 'w');
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

            self::log_error('CSV export completed successfully');
            wp_send_json_success(array(
                'filename' => $filename,
                'data' => base64_encode($csv_data),
                'type' => 'text/csv'
            ));
        } catch (Exception $e) {
            self::log_error('CSV Export Error: ' . $e->getMessage());
            wp_send_json_error('Export failed: ' . $e->getMessage());
        }
    }

    /**
     * Export templates to JSON
     */
    public static function export_to_json() {
        try {
            self::log_error('Starting JSON export');
            
            if (!check_ajax_referer('bb_export_templates', 'nonce', false)) {
                self::log_error('Nonce verification failed for JSON export');
                throw new Exception('Nonce verification failed');
            }

            $templates = self::get_remote_templates();
            $filename = 'bricks_remote_templates_' . date('Y-m-d') . '.json';

            $json_data = json_encode($templates, JSON_PRETTY_PRINT);

            self::log_error('JSON export completed successfully');
            wp_send_json_success(array(
                'filename' => $filename,
                'data' => base64_encode($json_data),
                'type' => 'application/json'
            ));
        } catch (Exception $e) {
            self::log_error('JSON Export Error: ' . $e->getMessage());
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
        return isset($global_settings['remoteTemplates']) ? $global_settings['remoteTemplates'] : array();
    }

    /**
     * Log error messages
     * 
     * @param string $message Error message to log
     */
    private static function log_error($message) {
        error_log('Bricks Remote Template Sync: ' . $message);
    }
}