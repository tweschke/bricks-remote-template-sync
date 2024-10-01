jQuery(document).ready(function($) {
    // Handle click events for CSV and JSON export buttons
    $('#export-csv, #export-json').on('click', function(e) {
        e.preventDefault();
        var exportType = $(this).attr('id').split('-')[1];
        
        // Create a form and submit it to trigger the download
        var form = $('<form>', {
            'method': 'POST',
            'action': bricksRemoteSync.ajaxurl
        });

        form.append($('<input>', {
            'type': 'hidden',
            'name': 'action',
            'value': 'bb_export_remote_templates_to_' + exportType
        }));

        form.append($('<input>', {
            'type': 'hidden',
            'name': 'nonce',
            'value': bricksRemoteSync.export_nonce
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

    // Handle running Google Sheet sync
    $('#google-sheet-sync-form').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: bricksRemoteSync.ajaxurl,
            type: 'POST',
            data: {
                action: 'bb_run_google_sheet_sync',
                nonce: bricksRemoteSync.sync_nonce
            },
            success: function(response) {
                if (response.success) {
                    alert('Sync successful: ' + response.data);
                } else {
                    console.error('Sync failed:', response.data);
                    alert('Sync failed: ' + response.data);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('AJAX request failed:', textStatus, errorThrown);
                alert('Sync failed. Please check the console for more information.');
            }
        });
    });
});