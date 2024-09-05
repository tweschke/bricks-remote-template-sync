<?php
namespace Bricks_Remote_Template_Sync;

class Admin {
    public function enqueue_scripts($hook) {
        if (strpos($hook, 'bb-import-remote-templates') === false) {
            return;
        }
        wp_enqueue_style('bb-admin-styles', BRICKS_REMOTE_SYNC_PLUGIN_URL . 'admin/css/admin-style.css', array(), BRICKS_REMOTE_SYNC_VERSION);
        wp_enqueue_script('bb-admin-script', BRICKS_REMOTE_SYNC_PLUGIN_URL . 'admin/js/admin-script.js', array('jquery'), BRICKS_REMOTE_SYNC_VERSION, true);
        wp_localize_script('bb-admin-script', 'bbAjax', array('ajaxurl' => admin_url('admin-ajax.php')));
    }

    public function add_plugin_admin_menu() {
        add_menu_page(
            'Bricks Templates',
            'Bricks Templates',
            'manage_options',
            'bb-import-remote-templates',
            array($this, 'display_plugin_admin_page'),
            'dashicons-database-import',
            60
        );
    }

    public function display_plugin_admin_page() {
        $view = isset($_GET['view']) ? sanitize_key($_GET['view']) : '';

        switch ($view) {
            case 'readme':
                $this->display_readme();
                break;
            case 'changelog':
                $this->display_changelog();
                break;
            default:
                require_once BRICKS_REMOTE_SYNC_PLUGIN_DIR . 'admin/partials/admin-display.php';
                break;
        }
    }

    private function display_readme() {
        echo '<div class="wrap bricks-docs">';
        include BRICKS_REMOTE_SYNC_PLUGIN_DIR . 'admin/partials/read-me.php';
        echo '</div>';
    }

    private function display_changelog() {
        echo '<div class="wrap bricks-docs">';
        include BRICKS_REMOTE_SYNC_PLUGIN_DIR . 'admin/partials/changelog.php';
        echo '</div>';
    }
}