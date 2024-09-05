<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bricks Builder Remote Template Importer - User Guide</title>
    <link rel="stylesheet" href="admin-styles.css">
</head>
<body>
    <div class="container">
        <h1>Bricks Builder Remote Template Importer - User Guide</h1>

        <h2>Table of Contents</h2>
        <ol>
            <li><a href="#introduction">Introduction</a></li>
            <li><a href="#importing-csv">Importing Templates from CSV</a></li>
            <li><a href="#importing-json">Importing Templates from JSON</a></li>
            <li><a href="#syncing-google-sheets">Syncing Templates from Google Sheets</a></li>
            <li><a href="#exporting-csv">Exporting Templates to CSV</a></li>
            <li><a href="#exporting-json">Exporting Templates to JSON</a></li>
            <li><a href="#resetting-templates">Resetting Templates</a></li>
        </ol>

        <h2 id="introduction">1. Introduction</h2>
        <p>The Bricks Builder Remote Template Importer plugin allows you to easily import, export, and manage remote templates for Bricks Builder. This guide will walk you through each feature of the plugin.</p>

        <h2 id="importing-csv">2. Importing Templates from CSV</h2>
        <ol>
            <li>Navigate to the "Import from CSV" section.</li>
            <li>Click "Choose File" and select your CSV file.</li>
            <li>Click "Import Templates" to upload and import the templates.</li>
            <li>The CSV file should have the following structure:
                <pre>template_id,name,url,password</pre>
                <p>Note: The first row (header) will be skipped during import.</p>
            </li>
        </ol>

        <h2 id="importing-json">3. Importing Templates from JSON</h2>
        <ol>
            <li>Go to the "Import from JSON" section.</li>
            <li>Click "Choose File" and select your JSON file.</li>
            <li>Click "Import Templates" to upload and import the templates.</li>
            <li>The JSON file should contain an array of template objects with 'name', 'url', and 'password' properties.</li>
        </ol>

        <h2 id="syncing-google-sheets">4. Syncing Templates from Google Sheets</h2>
        <ol>
            <li>Ensure your Google Sheet is published to the web as a CSV.</li>
            <li>Copy the published CSV URL.</li>
            <li>Paste the URL into the input field in the "Sync with Google Sheets" section.</li>
            <li>Click "Sync Templates from Google Sheets" to import the templates.</li>
            <li>The Google Sheet should have the same structure as the CSV file mentioned earlier.</li>
        </ol>

        <h2 id="exporting-csv">5. Exporting Templates to CSV</h2>
        <ol>
            <li>Navigate to the "Export to CSV" section.</li>
            <li>Click the "Export Templates to CSV" button.</li>
            <li>Your browser will prompt you to download the CSV file containing all your templates.</li>
        </ol>

        <h2 id="exporting-json">6. Exporting Templates to JSON</h2>
        <ol>
            <li>Go to the "Export to JSON" section.</li>
            <li>Click the "Export Templates to JSON" button.</li>
            <li>Your browser will prompt you to download the JSON file containing all your templates.</li>
        </ol>

        <h2 id="resetting-templates">7. Resetting Templates</h2>
        <ol>
            <li>Navigate to the "Reset Templates" section.</li>
            <li>Click the "Reset Remote Templates" button.</li>
            <li>Confirm the action when prompted.</li>
            <li class="warning"><strong>Warning:</strong> This action will remove all existing remote templates and cannot be undone.</li>
        </ol>

        <h2>Additional Notes</h2>
        <ul>
            <li>Always backup your templates before performing import or reset operations.</li>
            <li>Ensure your CSV and JSON files are properly formatted to avoid import errors.</li>
            <li>For Google Sheets sync, make sure the sheet is publicly accessible and published as CSV.</li>
        </ul>

        <p>If you encounter any issues or have questions, please contact the plugin support.</p>
    </div>
</body>
</html>