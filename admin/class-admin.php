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
    }

    public function enqueue_admin_styles($hook) {
        if (strpos($hook, 'bb-import-remote-templates') === false &&
            strpos($hook, 'bb-read-me') === false &&
            strpos($hook, 'bb-changelog') === false) {
            return;
        }
        wp_enqueue_style('bb-admin-styles', plugin_dir_url(__FILE__) . 'css/admin-style.css');
    }

    public function import_remote_templates_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        if (!is_client_plugin_license_valid()) {
            echo '<div class="wrap"><h1>Bricks Remote Template Sync</h1>';
            echo '<p>Please activate your license to use this plugin. <a href="' . admin_url('options-general.php?page=client-plugin-license') . '">Activate License</a></p></div>';
            return;
        }

        // Rest of the import_remote_templates_page function remains the same
        // ...
    }

    public function read_me_page() {
        if (!is_client_plugin_license_valid()) {
            echo '<div class="wrap"><h1>Bricks Remote Template Sync - Read Me</h1>';
            echo '<p>Please activate your license to access this page. <a href="' . admin_url('options-general.php?page=client-plugin-license') . '">Activate License</a></p></div>';
            return;
        }

        include plugin_dir_path(__FILE__) . 'partials/read-me.php';
    }

    public function changelog_page() {
        if (!is_client_plugin_license_valid()) {
            echo '<div class="wrap"><h1>Bricks Remote Template Sync - Changelog</h1>';
            echo '<p>Please activate your license to access this page. <a href="' . admin_url('options-general.php?page=client-plugin-license') . '">Activate License</a></p></div>';
            return;
        }

        include plugin_dir_path(__FILE__) . 'partials/changelog.php';
    }

    // Rest of the class methods remain the same
    // ...
}