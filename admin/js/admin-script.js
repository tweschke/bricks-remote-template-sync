/**
 * Admin JavaScript for Bricks Remote Template Options
 *
 * This script handles all the UI interactions for the Bricks Remote Template Options page.
 */

(function($) {
    $(document).ready(function() {
        // Show sub UI when a main feature button is clicked
        $('.show-sub-ui').on('click', function() {
            var targetUI = $(this).data('target');
            $('#' + targetUI).toggleClass('hidden');
        });

        $('#confirm-delete').on('click', function() {
            if (confirm('Are you sure you want to delete all templates? This action cannot be undone.')) {
                // Perform delete action here
                alert('All templates have been deleted.');
            }
        });

        $('#export-csv, #export-json').on('click', function() {
            var format = $(this).attr('id').split('-')[1];
            // Perform export action here
            alert('Exporting templates to ' + format.toUpperCase() + ' format.');
        });

        // Return to main UI when "Back to Main Menu" is clicked
        $('.return-to-main').on('click', function() {
            $('.bricks-sub-ui').addClass('hidden');
            $('#main-ui').removeClass('hidden');
        });

        // Handle file input change to show selected filename
        $('input[type="file"]').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            if (fileName) {
                $(this).next('.file-name').text(fileName);
            } else {
                $(this).next('.file-name').text('No file chosen');
            }
        });

        // Confirm delete action
        $('#delete-all-button').on('click', function(e) {
            if (!confirm('Are you sure you want to delete all templates? This action cannot be undone.')) {
                e.preventDefault();
            }
        });

        // Handle AJAX form submissions
        $('.ajax-form').on('submit', function(e) {
            e.preventDefault();
            var $form = $(this);
            var formData = new FormData(this);

            $.ajax({
                url: ajaxurl, // WordPress AJAX URL
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        alert(response.data.message);
                    } else {
                        alert('Error: ' + response.data.message);
                    }
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                }
            });
        });

        // Initialize tooltips if tooltip plugin is available
        if ($.fn.tooltip) {
            $('.tooltip').tooltip();
        }

        // Handle tab navigation in sub UIs if tabs are implemented
        $('.tab-nav').on('click', 'a', function(e) {
            e.preventDefault();
            var $this = $(this);
            var target = $this.attr('href');

            $this.parent().addClass('active').siblings().removeClass('active');
            $(target).removeClass('hidden').siblings('.tab-content').addClass('hidden');
        });
    });
})(jQuery);