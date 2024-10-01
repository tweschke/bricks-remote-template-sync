<?php
if (!defined('ABSPATH')) {
    die('Direct access is not allowed.');
}

class Bricks_Remote_Template_Sync_Admin {
    /**
     * Initialize the class and set its properties.
     */
    public function init() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }

    /**
     * Register the admin menu.
     */
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

    /**
     * Enqueue admin-specific styles.
     */
    public function enqueue_admin_styles($hook) {
        if (strpos($hook, 'bb-import-remote-templates') === false) {
            return;
        }
        wp_enqueue_style('bb-admin-styles', BRICKS_REMOTE_SYNC_PLUGIN_URL . 'admin/css/admin-style.css', array(), BRICKS_REMOTE_SYNC_VERSION);
    }

    /**
     * Enqueue admin-specific scripts.
     */
    public function enqueue_admin_scripts($hook) {
        if (strpos($hook, 'bb-import-remote-templates') === false) {
            return;
        }
        wp_enqueue_script('bb-admin-script', BRICKS_REMOTE_SYNC_PLUGIN_URL . 'admin/js/admin-script.js', array('jquery'), BRICKS_REMOTE_SYNC_VERSION, true);
        wp_localize_script('bb-admin-script', 'bricksRemoteSync', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('bb_export_templates')
        ));
    }

    /**
     * Display the import page.
     */
    public function display_import_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        Bricks_Remote_Template_Sync_Import_Export::render_import_export_page();
    }
}