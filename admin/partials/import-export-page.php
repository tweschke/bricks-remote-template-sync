<?php
/**
 * Admin page template for import and export functionality
 *
 * @package Bricks_Remote_Template_Sync
 */

if (!defined('ABSPATH')) {
    die('Direct access is not allowed.');
}
?>
<div class="wrap bricks-importer">
    <header class="bricks-header">
        <h1 class="wp-heading-inline">
            <svg class="bricks-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- SVG path data -->
            </svg>
            <?php echo esc_html(get_admin_page_title()); ?>
        </h1>
        <nav class="bricks-nav">
            <a href="#" class="active">Dashboard</a>
            <a href="#">Bricks Manager</a>
            <a href="#">Settings</a>
        </nav>
    </header>

    <?php if (!empty($message)): ?>
        <div id="feedback-message" class="notice notice-<?php echo $message_type; ?> is-dismissible">
            <p><?php echo esc_html($message); ?></p>
        </div>
    <?php endif; ?>

    <div class="bricks-grid">
        <div class="bricks-card">
            <svg class="bricks-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- SVG path data for import icon -->
            </svg>
            <h2><?php _e('Import Templates', 'bricks-remote-template-sync'); ?></h2>
            <button class="button button-primary section-toggle" data-section="import-section">
                <?php _e('Import Templates', 'bricks-remote-template-sync'); ?>
            </button>
        </div>

        <div class="bricks-card">
            <svg class="bricks-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- SVG path data for export icon -->
            </svg>
            <h2><?php _e('Export Templates', 'bricks-remote-template-sync'); ?></h2>
            <button class="button button-primary section-toggle" data-section="export-section">
                <?php _e('Export Templates', 'bricks-remote-template-sync'); ?>
            </button>
        </div>

        <div class="bricks-card">
            <svg class="bricks-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- SVG path data for sync icon -->
            </svg>
            <h2><?php _e('Sync Templates', 'bricks-remote-template-sync'); ?></h2>
            <button class="button button-primary section-toggle" data-section="sync-section">
                <?php _e('Sync Templates', 'bricks-remote-template-sync'); ?>
            </button>
        </div>

        <div class="bricks-card">
            <svg class="bricks-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- SVG path data for reset icon -->
            </svg>
            <h2><?php _e('Reset Templates', 'bricks-remote-template-sync'); ?></h2>
            <button class="button button-primary section-toggle" data-section="reset-section">
                <?php _e('Reset Templates', 'bricks-remote-template-sync'); ?>
            </button>
        </div>
    </div>

    <div id="import-section" class="bricks-section hidden">
        <h2><?php _e('Import Templates', 'bricks-remote-template-sync'); ?></h2>
        <form method="POST" enctype="multipart/form-data">
            <?php wp_nonce_field('bb_import_templates', 'bb_import_nonce'); ?>
            <input type="file" name="import_file" accept=".csv,.json" required>
            <button type="submit" name="import_remote_templates" class="button button-primary"><?php _e('Import Templates', 'bricks-remote-template-sync'); ?></button>
        </form>
    </div>

    <div id="export-section" class="bricks-section hidden">
        <h2><?php _e('Export Templates', 'bricks-remote-template-sync'); ?></h2>
        <div class="button-group">
            <button id="export-csv" class="button button-secondary"><?php _e('Export to CSV', 'bricks-remote-template-sync'); ?></button>
            <button id="export-json" class="button button-secondary"><?php _e('Export to JSON', 'bricks-remote-template-sync'); ?></button>
        </div>
    </div>

    <div id="sync-section" class="bricks-section hidden">
        <h2><?php _e('Sync with Google Sheets', 'bricks-remote-template-sync'); ?></h2>
        <form method="POST" id="google-sheet-form">
            <?php wp_nonce_field('bb_import_templates', 'bb_import_nonce'); ?>
            <input type="url" name="google_sheet_url" id="google_sheet_url" value="<?php echo esc_attr($saved_google_sheet_url); ?>" placeholder="https://docs.google.com/spreadsheets/d/..." required>
            <button type="submit" name="save_google_sheet_url" class="button button-primary"><?php _e('Save Sync URL', 'bricks-remote-template-sync'); ?></button>
        </form>
        <form method="POST" id="google-sheet-sync-form">
            <?php wp_nonce_field('bb_import_templates', 'bb_import_nonce'); ?>
            <button type="submit" name="run_google_sheet_sync" class="button button-primary" <?php echo empty($saved_google_sheet_url) ? 'disabled' : ''; ?>><?php _e('Run Sync', 'bricks-remote-template-sync'); ?></button>
        </form>
    </div>

    <div id="reset-section" class="bricks-section hidden">
        <h2><?php _e('Reset Templates', 'bricks-remote-template-sync'); ?></h2>
        <form method="POST" onsubmit="return confirm('<?php _e('Are you sure you want to reset all remote templates? This action cannot be undone.', 'bricks-remote-template-sync'); ?>');">
            <?php wp_nonce_field('bb_import_templates', 'bb_import_nonce'); ?>
            <button type="submit" name="reset_remote_templates" class="button button-secondary bricks-reset-button"><?php _e('Reset Remote Templates', 'bricks-remote-template-sync'); ?></button>
        </form>
    </div>
</div>