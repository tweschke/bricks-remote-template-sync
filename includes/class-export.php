<?php
if (!defined('ABSPATH')) {
    die('Direct access is not allowed.');
}

class Bricks_Remote_Template_Sync_Export {
    /**
     * Export templates to CSV
     */
    public static function export_to_csv() {
        check_ajax_referer('bb_export_templates', 'nonce');

        $templates = self::get_remote_templates();
        $filename = 'bricks_remote_templates_' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        fputcsv($output, array('ID', 'Name', 'URL', 'Password'));

        foreach ($templates as $id => $template) {
            fputcsv($output, array($id, $template['name'], $template['url'], $template['password']));
        }

        fclose($output);
        wp_die();
    }

    /**
     * Export templates to JSON
     */
    public static function export_to_json() {
        check_ajax_referer('bb_export_templates', 'nonce');

        $templates = self::get_remote_templates();
        $filename = 'bricks_remote_templates_' . date('Y-m-d') . '.json';

        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        echo json_encode($templates, JSON_PRETTY_PRINT);
        wp_die();
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
}