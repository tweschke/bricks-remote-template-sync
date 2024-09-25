1. Introduction
The Bricks Builder Remote Template Importer plugin allows you to easily import, export, and manage remote templates for Bricks Builder. This guide will walk you through each feature of the plugin.

3. Importing Templates from CSV
Navigate to the "Import from CSV" section.
Click "Choose File" and select your CSV file.
Click "Import Templates" to upload and import the templates.
The CSV file should have the following structure:
template_id,name,url,password
Note: The first row (header) will be skipped during import.

4. Importing Templates from JSON
Go to the "Import from JSON" section.
Click "Choose File" and select your JSON file.
Click "Import Templates" to upload and import the templates.
The JSON file should contain an array of template objects with 'name', 'url', and 'password' properties.
5. Syncing Templates from Google Sheets
Ensure your Google Sheet is published to the web as a CSV.
Copy the published CSV URL.
Paste the URL into the input field in the "Sync with Google Sheets" section.
Click "Sync Templates from Google Sheets" to import the templates.
The Google Sheet should have the same structure as the CSV file mentioned earlier.
6. Exporting Templates to CSV
Navigate to the "Export to CSV" section.
Click the "Export Templates to CSV" button.
Your browser will prompt you to download the CSV file containing all your templates.
7. Exporting Templates to JSON
Go to the "Export to JSON" section.
Click the "Export Templates to JSON" button.
Your browser will prompt you to download the JSON file containing all your templates.
8. Resetting Templates
Navigate to the "Reset Templates" section.
Click the "Reset Remote Templates" button.
Confirm the action when prompted.
Warning: This action will remove all existing remote templates and cannot be undone.
Additional Notes
Always backup your templates before performing import or reset operations.
Ensure your CSV and JSON files are properly formatted to avoid import errors.
For Google Sheets sync, make sure the sheet is publicly accessible and published as CSV.
