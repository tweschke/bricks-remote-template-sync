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
            if (!check_ajax_referer('bb_export_templates', 'nonce', false)) {
                throw new Exception('Nonce verification failed');
            }

            $templates = self::get_remote_templates();
            $filename = 'bricks_remote_templates_' . date('Y-m-d') . '.csv';

            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Pragma: no-cache');
            header('Expires: 0');

            $output = fopen('php://output', 'w');
            if ($output === false) {
                throw new Exception('Failed to open output stream');
            }

            fputcsv($output, array('ID', 'Name', 'URL', 'Password'));

            foreach ($templates as $id => $template) {
                fputcsv($output, array($id, $template['name'], $template['url'], $template['password']));
            }

            fclose($output);
            exit;
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
            if (!check_ajax_referer('bb_export_templates', 'nonce', false)) {
                throw new Exception('Nonce verification failed');
            }

            $templates = self::get_remote_templates();
            $filename = 'bricks_remote_templates_' . date('Y-m-d') . '.json';

            header('Content-Type: application/json');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Pragma: no-cache');
            header('Expires: 0');

            echo json_encode($templates, JSON_PRETTY_PRINT);
            exit;
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