/**
 * Bricks Remote Template Sync Admin JavaScript
 * 
 * This script handles the client-side functionality for the Bricks Remote Template Sync plugin,
 * including CSV and JSON exports, and saving the Google Sheet URL.
 */

jQuery(document).ready(function($) {
    console.log('Bricks Remote Sync script loaded');

    // Handle click events for CSV and JSON export buttons
    $('#export-csv, #export-json').on('click', function(e) {
        e.preventDefault();
        var exportType = $(this).attr('id').split('-')[1];
        console.log('Export ' + exportType + ' button clicked');
        
        $.ajax({
            url: bricksRemoteSync.ajaxurl,
            type: 'POST',
            data: {
                action: 'bb_export_remote_templates_to_' + exportType,
                nonce: bricksRemoteSync.nonce
            },
            success: function(response) {
                if (response.success) {
                    // Create a download link and trigger the download
                    var blob = new Blob([response.data], {type: exportType === 'csv' ? 'text/csv' : 'application/json'});
                    var downloadUrl = window.URL.createObjectURL(blob);
                    var a = document.createElement('a');
                    a.style.display = 'none';
                    a.href = downloadUrl;
                    a.download = 'bricks_remote_templates.' + exportType;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(downloadUrl);
                    a.remove();
                } else {
                    console.error('Export failed:', response.data);
                    alert('Export failed. Please check the console for more information.');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('AJAX request failed:', textStatus, errorThrown);
                alert('Export failed. Please check the console for more information.');
            }
        });
    });

    // Handle saving Google Sheet URL
    $('#google-sheet-form').on('submit', function(e) {
        e.preventDefault();
        var googleSheetUrl = $('#google_sheet_url').val();
        
        $.ajax({
            url: bricksRemoteSync.ajaxurl,
            type: 'POST',
            data: {
                action: 'bb_save_google_sheet_url',
                nonce: bricksRemoteSync.nonce,
                google_sheet_url: googleSheetUrl
            },
            success: function(response) {
                if (response.success) {
                    alert('Google Sheet URL saved successfully.');
                } else {
                    console.error('Failed to save Google Sheet URL:', response.data);
                    alert('Failed to save Google Sheet URL. Please check the console for more information.');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('AJAX request failed:', textStatus, errorThrown);
                alert('Failed to save Google Sheet URL. Please check the console for more information.');
            }
        });
    });

    // You can add more JavaScript functionality here as needed
});