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
        
        var form = $('<form>', {
            'method': 'POST',
            'action': ajaxurl
        }).append($('<input>', {
            'type': 'hidden',
            'name': 'action',
            'value': 'bb_export_remote_templates_to_' + exportType
        })).append($('<input>', {
            'type': 'hidden',
            'name': 'nonce',
            'value': bricksRemoteSync.nonce
        }));

        $('body').append(form);
        form.submit();
        form.remove();
    });

    // Handle saving Google Sheet URL
    $('#google-sheet-form').on('submit', function(e) {
        e.preventDefault();
        var googleSheetUrl = $('#google_sheet_url').val();
        
        $.ajax({
            url: ajaxurl,
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