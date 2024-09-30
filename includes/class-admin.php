<?php
if (!defined('ABSPATH')) {
    die('Direct access is not allowed.');
}

class Bricks_Remote_Template_Sync_Admin {
    public function init() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles'));
    }

    public function add_admin_menu() {
        add_menu_page(
            'Bricks Templates',
            'Bricks Templates',
            'manage_options',
            'bb-import-remote-templates',
            array($this, 'display_import_page'),
            'dashicons-database-import',
            60
        );
    }

    public function enqueue_admin_styles($hook) {
        if (strpos($hook, 'bb-import-remote-templates') === false) {
            return;
        }
        wp_enqueue_style('bb-admin-styles', BRICKS_REMOTE_SYNC_PLUGIN_URL . 'admin/css/admin-style.css', array(), BRICKS_REMOTE_SYNC_VERSION);
    }

    public function display_import_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        Bricks_Remote_Template_Sync_Import_Export::render_import_export_page();
    }
}