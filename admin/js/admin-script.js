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
                nonce: bricksRemoteSync.export_nonce
            },
            success: function(response) {
                if (response.success) {
                    var blob = new Blob([response.data], {type: exportType === 'csv' ? 'text/csv' : 'application/json'});
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = 'bricks_remote_templates.' + exportType;
                    link.click();
                } else {
                    console.error('Export failed:', response.data);
                    alert('Export failed: ' + response.data);
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
                nonce: bricksRemoteSync.save_url_nonce,
                google_sheet_url: googleSheetUrl
            },
            success: function(response) {
                if (response.success) {
                    alert('Success: ' + response.data);
                } else {
                    console.error('Failed to save Google Sheet URL:', response.data);
                    alert('Error: ' + response.data);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('AJAX request failed:', textStatus, errorThrown);
                alert('Failed to save Google Sheet URL. Error: ' + textStatus);
            }
        });
    });
});