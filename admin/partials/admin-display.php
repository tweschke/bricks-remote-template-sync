<?php
// Prevent direct access to this file
if (!defined('ABSPATH')) {
    exit;
}

// Display success message if templates were reset
if (isset($_GET['reset']) && $_GET['reset'] === 'success') {
    echo '<div class="notice notice-success is-dismissible"><p>Remote templates have been successfully reset.</p></div>';
}
?>
<div class="wrap bricks-importer">
    <h1 class="wp-heading-inline"><?php echo esc_html(get_admin_page_title()); ?></h1>
    <hr class="wp-header-end">

    <!-- CSV Import Section -->
    <div class="bricks-section">
        <h2>Import from CSV</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="csv_file" accept=".csv" required>
            <button type="submit" name="import_remote_templates" class="button button-primary">Import Templates</button>
        </form>
    </div>

    <!-- JSON Import Section -->
    <div class="bricks-section">
        <h2>Import from JSON</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="json_file" accept=".json" required>
            <button type="submit" name="import_remote_templates_json" class="button button-primary">Import Templates</button>
        </form>
    </div>

    <!-- Google Sheets Sync Section -->
    <div class="bricks-section">
        <h2>Sync with Google Sheets</h2>
        <form method="POST">
            <input type="url" name="google_sheet_url" placeholder="https://docs.google.com/spreadsheets/d/..." required>
            <button type="submit" name="import_from_google_sheet" class="button button-primary">Sync Templates</button>
        </form>
    </div>

    <!-- Export Section -->
    <div class="bricks-section">
        <h2>Export Templates</h2>
        <button id="export-csv" class="button button-secondary">Export to CSV</button>
        <button id="export-json" class="button button-secondary">Export to JSON</button>
    </div>

    <!-- Reset Section -->
    <div class="bricks-section">
        <h2>Reset Templates</h2>
        <form method="POST" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" onsubmit="return confirm('Are you sure you want to reset all remote templates? This action cannot be undone.');">
            <input type="hidden" name="action" value="reset_remote_templates">
            <?php wp_nonce_field('reset_remote_templates'); ?>
            <button type="submit" class="button button-secondary">Reset Remote Templates</button>
        </form>
    </div>
</div>