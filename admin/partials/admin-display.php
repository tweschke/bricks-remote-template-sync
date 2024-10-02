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
    <h1 class="wp-heading-inline">Bricks Template Manager</h1>

    <div class="bricks-grid">
        <div class="bricks-card">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
            </svg>
            <h3>Import Templates</h3>
            <button type="button" class="button" onclick="document.getElementById('import-section').scrollIntoView({behavior: 'smooth'})">Import Templates</button>
        </div>

        <div class="bricks-card">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            <h3>Export Templates</h3>
            <button type="button" class="button" onclick="document.getElementById('export-section').scrollIntoView({behavior: 'smooth'})">Export Templates</button>
        </div>

        <div class="bricks-card">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            <h3>Sync Templates</h3>
            <button type="button" class="button" onclick="document.getElementById('sync-section').scrollIntoView({behavior: 'smooth'})">Sync Templates</button>
        </div>

        <div class="bricks-card">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
            <h3>Reset Templates</h3>
            <button type="button" class="button" id="reset-templates">Reset Templates</button>
        </div>
    </div>

    <div id="import-section" class="bricks-section">
        <h2>Import Templates</h2>
        <form method="POST" enctype="multipart/form-data">
            <?php wp_nonce_field('bb_import_templates'); ?>
            <input type="file" name="import_file" accept=".csv,.json" required>
            <button type="submit" name="import_remote_templates" class="button">Import Templates</button>
        </form>
    </div>

    <div id="export-section" class="bricks-section">
        <h2>Export Templates</h2>
        <form method="POST">
            <?php wp_nonce_field('bb_export_templates'); ?>
            <button type="submit" name="export_to_csv" class="button">Export to CSV</button>
            <button type="submit" name="export_to_json" class="button">Export to JSON</button>
        </form>
    </div>

    <div id="sync-section" class="bricks-section">
        <h2>Sync with Google Sheets</h2>
        <form method="POST">
            <?php wp_nonce_field('bb_import_templates'); ?>
            <input type="url" name="google_sheet_url" placeholder="https://docs.google.com/spreadsheets/d/..." required>
            <button type="submit" name="import_from_google_sheet" class="button">Sync Templates</button>
        </form>
    </div>
</div>

<script>
document.getElementById('reset-templates').addEventListener('click', function() {
    if (confirm('Are you sure you want to reset all remote templates? This action cannot be undone.')) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', ajaxurl, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                alert(response.data);
                if (response.success) {
                    location.reload();
                }
            } else {
                alert('An error occurred while resetting templates.');
            }
        };
        xhr.send('action=bb_reset_remote_templates&nonce=' + '<?php echo wp_create_nonce('bb_reset_remote_templates'); ?>');
    }
});
</script>