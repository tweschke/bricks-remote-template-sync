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
            <?php echo esc_html(get_admin_page_title()); ?>
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
                <path d="M4 16V17C4 18.6569 5.34315 20 7 20H17C18.6569 20 20 18.6569 20 17V16M4 16V8C4 6.34315 5.34315 5 7 5H17C18.6569 5 20 6.34315 20 8V16M4 16H20M12 12V8M12 12L9 9M12 12L15 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <h2><?php _e('Import Templates', 'bricks-remote-template-sync'); ?></h2>
            <button class="button button-primary section-toggle" data-section="import-section">
                <?php _e('Import Templates', 'bricks-remote-template-sync'); ?>
            </button>
        </div>

        <!-- Export Templates Card -->
        <div class="bricks-card">
            <svg class="bricks-icon" width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4 16V17C4 18.6569 5.34315 20 7 20H17C18.6569 20 20 18.6569 20 17V16M4 16V8C4 6.34315 5.34315 5 7 5H17C18.6569 5 20 6.34315 20 8V16M4 16H20M12 12V16M12 12L9 15M12 12L15 15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <h2><?php _e('Export Templates', 'bricks-remote-template-sync'); ?></h2>
            <button class="button button-primary section-toggle" data-section="export-section">
                <?php _e('Export Templates', 'bricks-remote-template-sync'); ?>
            </button>
        </div>

        <!-- Sync Templates Card -->
        <div class="bricks-card">
            <svg class="bricks-icon" width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4 4V5H20V4M4 4V9M4 4H9M20 4V9M20 4H15M4 20V19H20V20M4 20V15M4 20H9M20 20V15M20 20H15M9 9H15M9 9V15M9 9H4M15 9H20M15 9V15M9 15H15M9 15H4M15 15H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <h2><?php _e('Sync Templates', 'bricks-remote-template-sync'); ?></h2>
            <button class="button button-primary section-toggle" data-section="sync-section">
                <?php _e('Sync Templates', 'bricks-remote-template-sync'); ?>
            </button>
        </div>

        <!-- Reset Templates Card -->
        <div class="bricks-card">
            <svg class="bricks-icon" width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3M21 12C21 7.02944 16.9706 3 12 3M21 12H12M12 3V12M12 12L17 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
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