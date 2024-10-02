<?php
/**
 * Admin page template for Bricks Remote Template Options
 *
 * @package Bricks_Remote_Template_Sync
 */

if (!defined('ABSPATH')) {
    die('Direct access is not allowed.');
}
?>
<div class="wrap bricks-remote-template-options">
    <h1><?php echo esc_html__('Bricks Remote Template Options', 'bricks-remote-template-sync'); ?></h1>

    <div id="main-ui" class="bricks-ui-container">
        <div class="bricks-feature-card">
            <h2>Import</h2>
            <p>Import your Bricks Remote Template links via your .csv or .json file.</p>
            <button class="button show-sub-ui" data-target="import-ui">Run Import</button>
        </div>

        <div class="bricks-feature-card">
            <h2>Export</h2>
            <p>Export your Bricks Remote Template links to a .csv or .json file.</p>
            <button class="button show-sub-ui" data-target="export-ui">Run Export</button>
        </div>

        <div class="bricks-feature-card">
            <h2>Import via Google Sheet</h2>
            <p>Import your Bricks Remote Template links via Google Sheet.</p>
            <button class="button show-sub-ui" data-target="google-sheet-ui">Run Import Google Sheet</button>
        </div>

        <div class="bricks-feature-card">
            <h2>Delete All</h2>
            <p>Delete all your Bricks Remote Template links.</p>
            <button class="button delete-button show-sub-ui" data-target="delete-ui">Delete</button>
        </div>
    </div>

    <div id="import-ui" class="bricks-ui-container bricks-sub-ui hidden">
        <div class="bricks-feature-card">
            <h2>Import</h2>
            <p>Import your Bricks Remote Template links via your .csv or .json file.</p>
            <form method="POST" enctype="multipart/form-data" class="ajax-form">
                <?php wp_nonce_field('bb_import_templates', 'bb_import_nonce'); ?>
                <input type="file" name="import_file" accept=".csv,.json" required>
                <button type="submit" name="import_templates" class="button">Import Templates</button>
            </form>
            <button class="button return-to-main">Back to Main Menu</button>
        </div>
    </div>

    <div id="export-ui" class="bricks-ui-container bricks-sub-ui hidden">
        <div class="bricks-feature-card">
            <h2>Export</h2>
            <p>Export your Bricks Remote Template links to a .csv or .json file.</p>
            <form method="POST" class="ajax-form">
                <?php wp_nonce_field('bb_export_templates', 'bb_export_nonce'); ?>
                <button type="submit" name="export_csv" class="button">Export to CSV</button>
                <button type="submit" name="export_json" class="button">Export to JSON</button>
            </form>
            <button class="button return-to-main">Back to Main Menu</button>
        </div>
    </div>

    <div id="google-sheet-ui" class="bricks-ui-container bricks-sub-ui hidden">
        <div class="bricks-feature-card">
            <h2>Import via Google Sheet</h2>
            <p>Import your Bricks Remote Template links via Google Sheet.</p>
            <form method="POST" class="ajax-form">
                <?php wp_nonce_field('bb_import_templates', 'bb_import_nonce'); ?>
                <input type="url" name="google_sheet_url" placeholder="Enter Google Sheet URL" required>
                <button type="submit" name="import_google_sheet" class="button">Import from Google Sheet</button>
            </form>
            <button class="button return-to-main">Back to Main Menu</button>
        </div>
    </div>

    <div id="delete-ui" class="bricks-ui-container bricks-sub-ui hidden">
        <div class="bricks-feature-card">
            <h2>Delete All Templates</h2>
            <p>Are you sure you want to delete all your Bricks Remote Template links? This action cannot be undone.</p>
            <form method="POST" class="ajax-form">
                <?php wp_nonce_field('bb_delete_templates', 'bb_delete_nonce'); ?>
                <button type="submit" name="delete_all_templates" class="button delete-button" id="delete-all-button">Confirm Delete All</button>
            </form>
            <button class="button return-to-main">Back to Main Menu</button>
        </div>
    </div>
</div>