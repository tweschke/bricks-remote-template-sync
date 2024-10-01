<?php
if (!defined('ABSPATH')) {
    die('Direct access is not allowed.');
}

class Bricks_Remote_Template_Sync_Admin {
    public function init() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
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

    public function enqueue_admin_scripts($hook) {
        if (strpos($hook, 'bb-import-remote-templates') === false) {
            return;
        }
        wp_enqueue_script('bb-admin-script', BRICKS_REMOTE_SYNC_PLUGIN_URL . 'admin/js/admin-script.js', array('jquery'), BRICKS_REMOTE_SYNC_VERSION, true);
        wp_localize_script('bb-admin-script', 'bricksRemoteSync', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'export_nonce' => wp_create_nonce('bb_export_templates'),
            'save_url_nonce' => wp_create_nonce('bb_save_google_sheet_url'),
            'sync_nonce' => wp_create_nonce('bb_run_google_sheet_sync')
        ));
    }

    public function display_import_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        Bricks_Remote_Template_Sync_Import_Export::render_import_export_page();
    }
}