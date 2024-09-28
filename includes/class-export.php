<?php
// Ensure this file is being included by a parent file
if (!defined('ABSPATH')) {
    die('Direct access to this file is not allowed.');
}

class Bricks_Remote_Template_Sync_Export {
    public static function export_to_csv() {
        $remote_templates = get_option('bricks_remote_templates', array());
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="bricks_remote_templates.csv"');
        $output = fopen('php://output', 'w');
        fputcsv($output, array('Template ID', 'Name', 'URL', 'Password'));
        foreach ($remote_templates as $id => $template) {
            fputcsv($output, array($id, $template['name'], $template['url'], $template['password']));
        }
        fclose($output);
        exit;
    }

    public static function export_to_json() {
        $remote_templates = get_option('bricks_remote_templates', array());
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="bricks_remote_templates.json"');
        echo json_encode($remote_templates, JSON_PRETTY_PRINT);
        exit;
    }
}