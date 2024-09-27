<?php
class Bricks_Remote_Template_Sync_Admin {
    public function init() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles'));
        add_action('wp_ajax_bb_export_remote_templates_to_csv', array($this, 'export_remote_templates_to_csv'));
        add_action('wp_ajax_bb_export_remote_templates_to_json', array($this, 'export_remote_templates_to_json'));
        add_action('wp_ajax_bb_save_google_sheet_url', array($this, 'save_google_sheet_url'));
    }

    public function add_admin_menu() {
        add_menu_page(
            'Bricks Templates',
            'Bricks Templates',
            'manage_options',
            'bb-import-remote-templates',
            array($this, 'import_remote_templates_page'),
            'dashicons-database-import',
            60
        );

        add_submenu_page(
            'bb-import-remote-templates',
            'Import/Export',
            'Import/Export',
            'manage_options',
            'bb-import-remote-templates',
            array($this, 'import_remote_templates_page')
        );

        add_submenu_page(
            'bb-import-remote-templates',
            'Read Me',
            'Read Me',
            'manage_options',
            'bb-read-me',
            array($this, 'read_me_page')
        );

        add_submenu_page(
            'bb-import-remote-templates',
            'Changelog',
            'Changelog',
            'manage_options',
            'bb-changelog',
            array($this, 'changelog_page')
        );

        add_submenu_page(
            'bb-import-remote-templates',
            'License',
            'License',
            'manage_options',
            'bb-license',
            'client_plugin_license_page'
        );
    }

    public function enqueue_admin_styles($hook) {
        if (strpos($hook, 'bb-import-remote-templates') === false &&
            strpos($hook, 'bb-read-me') === false &&
            strpos($hook, 'bb-changelog') === false &&
            strpos($hook, 'bb-license') === false) {
            return;
        }
        wp_enqueue_style('bb-admin-styles', plugin_dir_url(__FILE__) . 'css/admin-style.css');
    }

    public function import_remote_templates_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        Bricks_Remote_Template_Sync_Import_Export::render_import_export_page();
    }

    public function read_me_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        $is_license_valid = is_client_plugin_license_valid();
        
        if (!$is_license_valid) {
            echo '<div class="wrap"><h1>Bricks Remote Template Sync - Read Me</h1>';
            echo '<p>Please <a href="' . admin_url('admin.php?page=bb-license') . '">activate your license</a> to access this page.</p></div>';
            return;
        }

        include plugin_dir_path(__FILE__) . 'partials/read-me.php';
    }

    public function changelog_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        $is_license_valid = is_client_plugin_license_valid();
        
        if (!$is_license_valid) {
            echo '<div class="wrap"><h1>Bricks Remote Template Sync - Changelog</h1>';
            echo '<p>Please <a href="' . admin_url('admin.php?page=bb-license') . '">activate your license</a> to access this page.</p></div>';
            return;
        }

        include plugin_dir_path(__FILE__) . 'partials/changelog.php';
    }

    public function export_remote_templates_to_csv() {
        if (!current_user_can('manage_options') || !is_client_plugin_license_valid()) {
            wp_die('You do not have sufficient permissions to access this page.');
        }

        // Implement CSV export logic here
        $templates = $this->get_remote_templates();
        $csv = $this->generate_csv($templates);

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="bricks_remote_templates.csv"');
        echo $csv;
        exit;
    }

    public function export_remote_templates_to_json() {
        if (!current_user_can('manage_options') || !is_client_plugin_license_valid()) {
            wp_die('You do not have sufficient permissions to access this page.');
        }

        // Implement JSON export logic here
        $templates = $this->get_remote_templates();
        wp_send_json($templates);
    }

    public function save_google_sheet_url() {
        if (!current_user_can('manage_options') || !is_client_plugin_license_valid()) {
            wp_send_json_error('You do not have sufficient permissions to perform this action.');
        }

        check_ajax_referer('bb_save_google_sheet_url');

        if (isset($_POST['google_sheet_url'])) {
            $google_sheet_url = esc_url_raw($_POST['google_sheet_url']);
            update_option('bricks_remote_sync_google_sheet_url', $google_sheet_url);
            wp_send_json_success(['message' => 'Google Sheet URL saved successfully.']);
        } else {
            wp_send_json_error(['message' => 'Google Sheet URL missing.']);
        }
    }

    private function get_remote_templates() {
        // Implement logic to retrieve remote templates
        // This is a placeholder. Replace with your actual implementation.
        return [
            ['id' => 1, 'name' => 'Template 1', 'url' => 'https://example.com/template1'],
            ['id' => 2, 'name' => 'Template 2', 'url' => 'https://example.com/template2'],
        ];
    }

    private function generate_csv($data) {
        $output = fopen('php://temp', 'w');
        fputcsv($output, array('ID', 'Name', 'URL'));

        foreach ($data as $row) {
            fputcsv($output, $row);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }
}