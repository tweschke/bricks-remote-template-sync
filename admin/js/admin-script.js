/**
 * Bricks Remote Template Sync Admin JavaScript
 * 
 * This script handles the client-side functionality for the Bricks Remote Template Sync plugin,
 * including CSV and JSON exports, and saving the Google Sheet URL.
 */

jQuery(document).ready(function($) {
    // Handle click events for CSV and JSON export buttons
    $('#export-csv, #export-json').on('click', function(e) {
        e.preventDefault();
        var exportType = $(this).attr('id').split('-')[1];
        
        $.ajax({
            url: bricksRemoteSync.ajaxurl,
            type: 'POST',
            data: {
                action: 'bb_export_remote_templates_to_' + exportType,
                nonce: bricksRemoteSync.nonce
            },
            success: function(response) {
                if (response.success && response.data) {
                    var blob = new Blob([atob(response.data.data)], {type: response.data.type});
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = response.data.filename;
                    link.click();
                } else {
                    console.error('Export failed:', response.data);
                    alert('Export failed: ' + (response.data || 'Unknown error'));
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
                    alert('Failed to save Google Sheet URL: ' + response.data);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('AJAX request failed:', textStatus, errorThrown);
                alert('Failed to save Google Sheet URL. Please check the console for more information.');
            }
        });
    });
});