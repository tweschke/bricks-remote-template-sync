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

    <!-- Sub UIs will be similar to the main UI, but with specific content for each feature -->
    <!-- They will be hidden by default and shown when the corresponding button is clicked -->
</div>