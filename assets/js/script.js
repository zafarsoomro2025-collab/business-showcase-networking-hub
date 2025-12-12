/**
 * Business Showcase & Networking Hub
 * Frontend JavaScript
 * Version: 1.0.0
 */

(function($) {
    'use strict';

    /**
     * Initialize plugin
     */
    $(document).ready(function() {
        businessShowcaseInit();
        initBusinessDirectory();
    });

    /**
     * Main initialization function
     */
    function businessShowcaseInit() {
        console.log('Business Showcase plugin loaded');
        
        // Add your frontend JavaScript code here
        
        // Example: Handle business showcase interactions
        $('.business-showcase-item').on('click', function() {
            // Handle item click
        });
    }

    /**
     * Initialize Business Directory Filtering
     */
    function initBusinessDirectory() {
        if ($('#business-directory').length === 0) {
            return;
        }
        
        // Handle filter changes
        $('#category-filter, #service-filter').on('change', function() {
            filterBusinesses();
        });
        
        // Handle reset button
        $('#reset-filters').on('click', function() {
            $('#category-filter').val('');
            $('#service-filter').val('');
            filterBusinesses();
        });
    }

    /**
     * Filter Businesses via AJAX
     */
    function filterBusinesses() {
        var $grid = $('#business-grid');
        var $loading = $('#business-loading');
        var category = $('#category-filter').val();
        var service = $('#service-filter').val();
        
        // Show loading
        $loading.show();
        $grid.css('opacity', '0.5');
        
        $.ajax({
            url: businessShowcaseAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'business_showcase_filter',
                nonce: businessShowcaseAjax.nonce,
                category: category,
                service: service
            },
            success: function(response) {
                if (response.success) {
                    $grid.html(response.data.html);
                    
                    // Smooth scroll to grid
                    $('html, body').animate({
                        scrollTop: $grid.offset().top - 100
                    }, 500);
                }
            },
            error: function(xhr, status, error) {
                console.error('Filter error:', error);
                alert('Error filtering businesses. Please try again.');
            },
            complete: function() {
                $loading.hide();
                $grid.css('opacity', '1');
            }
        });
    }

    /**
     * AJAX Example Function
     */
    function businessShowcaseAjax(action, data) {
        $.ajax({
            url: businessShowcaseAjax.ajaxurl,
            type: 'POST',
            data: {
                action: action,
                nonce: businessShowcaseAjax.nonce,
                data: data
            },
            success: function(response) {
                if (response.success) {
                    console.log('AJAX success:', response.data);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', error);
            }
        });
    }

})(jQuery);
