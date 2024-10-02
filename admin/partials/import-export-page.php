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
                <path d="M3 6.2C3 5.07989 3 4.51984 3.21799 4.09202C3.40973 3.71569 3.71569 3.40973 4.09202 3.21799C4.51984 3 5.07989 3 6.2 3H17.8C18.9201 3 19.4802 3 19.908 3.21799C20.2843 3.40973 20.5903 3.71569 20.782 4.09202C21 4.51984 21 5.07989 21 6.2V17.8C21 18.9201 21 19.4802 20.782 19.908C20.5903 20.2843 20.2843 20.5903 19.908 20.782C19.4802 21 18.9201 21 17.8 21H6.2C5.07989 21 4.51984 21 4.09202 20.782C3.71569 20.5903 3.40973 20.2843 3.21799 19.908C3 19.4802 3 18.9201 3 17.8V6.2Z" stroke="currentColor" stroke-width="2"/>
                <path d="M3 9H21" stroke="currentColor" stroke-width="2"/>
                <path d="M9 21V9" stroke="currentColor" stroke-width="2"/>
            </svg>
            <?php echo esc_html__('Bricks Remote Template Options', 'bricks-remote-template-sync'); ?>
        </h1>
        <nav class="bricks-nav">
            <a href="#">Settings</a>
        </nav>
    </header>

    <?php if (!empty($message)): ?>
        <div id="feedback-message" class="notice notice-<?php echo $message_type; ?> is-dismissible">
            <p><?php echo esc_html($message); ?></p>
        </div>
    <?php endif; ?>

    <div class="bricks-grid">
    <!-- Import Templates Card -->
    <div class="bricks-card">
        <svg class="bricks-icon" width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 3v12m0 0l-4-4m4 4l4-4m-9 5H6a2 2 0 01-2-2V7a2 2 0 012-2h12a2 2 0 012 2v7a2 2 0 01-2 2h-1m-6 0H7a2 2 0 002 2h6a2 2 0 002-2h-3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <h2><?php _e('Import Templates', 'bricks-remote-template-sync'); ?></h2>
        <button class="button button-primary section-toggle" data-section="import-section">
            <?php _e('Import Templates', 'bricks-remote-template-sync'); ?>
        </button>
    </div>

    <!-- Export Templates Card -->
    <div class="bricks-card">
        <svg class="bricks-icon" width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 21v-12m0 0l-4 4m4-4l4 4m-9-5H6a2 2 0 00-2 2v7a2 2 0 002 2h1m6 0h3a2 2 0 002-2v-7a2 2 0 00-2-2h-1m-6 0H7a2 2 0 00-2 2v7a2 2 0 002 2h3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <h2><?php _e('Export Templates', 'bricks-remote-template-sync'); ?></h2>
        <button class="button button-primary section-toggle" data-section="export-section">
            <?php _e('Export Templates', 'bricks-remote-template-sync'); ?>
        </button>
    </div>

    <!-- Sync Templates Card -->
    <div class="bricks-card">
        <svg class="bricks-icon" width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M4 4v5h5m11-5v5h-5m-6 11v-5H4m16 0v5h-5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M19.657 11.828a8 8 0 10-15.314 0" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <h2><?php _e('Sync Templates', 'bricks-remote-template-sync'); ?></h2>
        <button class="button button-primary section-toggle" data-section="sync-section">
            <?php _e('Sync Templates', 'bricks-remote-template-sync'); ?>
        </button>
    </div>

    <!-- Reset Templates Card -->
    <div class="bricks-card">
        <svg class="bricks-icon" width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M4 4v5h5M4 4l7 7m9-7v5h-5m5-5l-7 7m-9 9v-5h5m-5 5l7-7m9 7v-5h-5m5 5l-7-7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <h2><?php _e('Reset Templates', 'bricks-remote-template-sync'); ?></h2>
        <button class="button button-primary section-toggle" data-section="reset-section">
            <?php _e('Reset Templates', 'bricks-remote-template-sync'); ?>
        </button>
    </div>
</div>

    <!-- Import Section -->
    <div id="import-section" class="bricks-section hidden">
        <button class="close-button">×</button>
        <h2><?php _e('Import Templates', 'bricks-remote-template-sync'); ?></h2>
        <form method="POST" enctype="multipart/form-data">
            <?php wp_nonce_field('bb_import_templates', 'bb_import_nonce'); ?>
            <input type="file" name="import_file" accept=".csv,.json" required>
            <button type="submit" name="import_remote_templates" class="button button-primary"><?php _e('Import Templates', 'bricks-remote-template-sync'); ?></button>
        </form>
    </div>

    <!-- Export Section -->
    <div id="export-section" class="bricks-section hidden">
        <button class="close-button">×</button>
        <h2><?php _e('Export Templates', 'bricks-remote-template-sync'); ?></h2>
        <div class="button-group">
            <button id="export-csv" class="button button-secondary"><?php _e('Export to CSV', 'bricks-remote-template-sync'); ?></button>
            <button id="export-json" class="button button-secondary"><?php _e('Export to JSON', 'bricks-remote-template-sync'); ?></button>
        </div>
    </div>

    <!-- Sync Section -->
    <div id="sync-section" class="bricks-section hidden">
        <button class="close-button">×</button>
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

    <!-- Reset Section -->
    <div id="reset-section" class="bricks-section hidden">
        <button class="close-button">×</button>
        <h2><?php _e('Reset Templates', 'bricks-remote-template-sync'); ?></h2>
        <form method="POST" onsubmit="return confirm('<?php _e('Are you sure you want to reset all remote templates? This action cannot be undone.', 'bricks-remote-template-sync'); ?>');">
            <?php wp_nonce_field('bb_import_templates', 'bb_import_nonce'); ?>
            <button type="submit" name="reset_remote_templates" class="button button-secondary bricks-reset-button"><?php _e('Reset Remote Templates', 'bricks-remote-template-sync'); ?></button>
        </form>
    </div>
</div>