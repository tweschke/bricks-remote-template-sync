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

        <div id="import-ui" class="bricks-sub-ui hidden">
            <h3>Import .csv</h3>
            <form method="POST" enctype="multipart/form-data">
                <?php wp_nonce_field('bb_import_templates', 'bb_import_nonce'); ?>
                <input type="file" name="csv_file" accept=".csv" required>
                <button type="submit" name="import_csv" class="button">Import .csv</button>
            </form>
            <h3>Import .json</h3>
            <form method="POST" enctype="multipart/form-data">
                <?php wp_nonce_field('bb_import_templates', 'bb_import_nonce'); ?>
                <input type="file" name="json_file" accept=".json" required>
                <button type="submit" name="import_json" class="button">Import .json</button>
            </form>
        </div>

        <div class="bricks-feature-card">
            <h2>Export</h2>
            <p>Export your Bricks Remote Template links to a .csv or .json file.</p>
            <button class="button show-sub-ui" data-target="export-ui">Run Export</button>
        </div>

        <div id="export-ui" class="bricks-sub-ui hidden">
            <button class="button" id="export-csv">Export to CSV</button>
            <button class="button" id="export-json">Export to JSON</button>
        </div>

        <div class="bricks-feature-card">
            <h2>Import via Google Sheet</h2>
            <p>Import your Bricks Remote Template links via Google Sheet.</p>
            <button class="button show-sub-ui" data-target="google-sheet-ui">Run Import Google Sheet</button>
        </div>

        <div id="google-sheet-ui" class="bricks-sub-ui hidden">
            <form method="POST">
                <?php wp_nonce_field('bb_import_google_sheet', 'bb_google_sheet_nonce'); ?>
                <input type="url" name="google_sheet_url" placeholder="Enter Google Sheet URL" required>
                <button type="submit" name="import_google_sheet" class="button">Import from Google Sheet</button>
            </form>
        </div>

        <div class="bricks-feature-card">
            <h2>Delete All</h2>
            <p>Delete all your Bricks Remote Template links.</p>
            <button class="button delete-button show-sub-ui" data-target="delete-ui">Delete</button>
        </div>

        <div id="delete-ui" class="bricks-sub-ui hidden">
            <p>Are you sure you want to delete all templates? This action cannot be undone.</p>
            <button id="confirm-delete" class="button delete-button">Confirm Delete All</button>
        </div>
    </div>
</div>