/**
 * Bricks Remote Template Sync Admin JavaScript
 * 
 * This script handles the client-side functionality for the Bricks Remote Template Sync plugin,
 * including CSV and JSON exports.
 */

jQuery(document).ready(function($) {
    console.log('Bricks Remote Sync script loaded');

    // Handle click events for CSV and JSON export buttons
    $('#export-csv, #export-json').on('click', function(e) {
        e.preventDefault();
        var type = $(this).attr('id').split('-')[1];
        console.log('Export ' + type + ' button clicked');
        
        // Prepare the data for the AJAX request
        var url = bricksRemoteSync.ajaxurl;
        var data = {
            action: 'bb_export_to_' + type,
            nonce: bricksRemoteSync.nonce
        };

        // Perform the AJAX request
        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            success: function(response) {
                if (response.success) {
                    var blob, filename;
                    if (type === 'csv') {
                        // Handle CSV export
                        blob = new Blob([response.data], {type: 'text/csv'});
                        filename = 'bricks_remote_templates.csv';
                    } else {
                        // Handle JSON export
                        blob = new Blob([JSON.stringify(response.data, null, 2)], {type: 'application/json'});
                        filename = 'bricks_remote_templates.json';
                    }

                    // Create a download link and trigger the download
                    var downloadUrl = window.URL.createObjectURL(blob);
                    var a = document.createElement('a');
                    a.style.display = 'none';
                    a.href = downloadUrl;
                    a.download = filename;
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

    // You can add more JavaScript functionality here as needed
});