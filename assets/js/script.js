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
        initStarRating();
        initContactForm();
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
     * Initialize Star Rating Input
     */
    function initStarRating() {
        if ($('.star-rating-input').length === 0) {
            return;
        }
        
        // Highlight stars on hover
        $('.star-rating-input label.star').on('mouseenter', function() {
            var $this = $(this);
            $this.addClass('hover');
            $this.nextAll('label.star').addClass('hover');
        });
        
        $('.star-rating-input').on('mouseleave', function() {
            $('.star-rating-input label.star').removeClass('hover');
        });
        
        // Handle star click
        $('.star-rating-input input[type="radio"]').on('change', function() {
            var rating = $(this).val();
            console.log('Rating selected:', rating);
        });
        
        // Validate rating on form submit
        $('#commentform').on('submit', function(e) {
            if ($('.star-rating-input').length > 0) {
                var ratingSelected = $('.star-rating-input input[type="radio"]:checked').length > 0;
                
                if (!ratingSelected) {
                    e.preventDefault();
                    alert('Please select a rating before submitting your review.');
                    $('.star-rating-input').addClass('error');
                    return false;
                }
            }
        });
    }

    /**
     * Initialize Business Contact Form
     */
    function initContactForm() {
        if ($('#business-contact-form').length === 0) {
            return;
        }
        
        var $form = $('#business-contact-form');
        var $submitBtn = $('#contact-submit-btn');
        var $messages = $('#contact-form-messages');
        
        $form.on('submit', function(e) {
            e.preventDefault();
            
            // Clear previous messages
            $messages.html('').removeClass('success error');
            
            // Disable submit button
            $submitBtn.prop('disabled', true);
            $submitBtn.find('.btn-text').hide();
            $submitBtn.find('.btn-loading').show();
            
            // Get form data
            var formData = {
                action: 'business_showcase_contact',
                business_contact_nonce: $form.find('input[name="business_contact_nonce"]').val(),
                post_id: $form.data('post-id'),
                contact_name: $('#contact_name').val().trim(),
                contact_email: $('#contact_email').val().trim(),
                contact_subject: $('#contact_subject').val().trim(),
                contact_message: $('#contact_message').val().trim()
            };
            
            // Validate fields
            if (!formData.contact_name) {
                showMessage('error', businessShowcaseAjax.error_text || 'Please enter your name.');
                resetSubmitButton();
                return;
            }
            
            if (!formData.contact_email || !isValidEmail(formData.contact_email)) {
                showMessage('error', 'Please enter a valid email address.');
                resetSubmitButton();
                return;
            }
            
            if (!formData.contact_subject) {
                showMessage('error', 'Please enter a subject.');
                resetSubmitButton();
                return;
            }
            
            if (!formData.contact_message) {
                showMessage('error', 'Please enter your message.');
                resetSubmitButton();
                return;
            }
            
            // Send AJAX request
            $.ajax({
                url: businessShowcaseAjax.ajaxurl,
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        showMessage('success', response.data.message);
                        $form[0].reset();
                        
                        // Scroll to success message
                        $('html, body').animate({
                            scrollTop: $messages.offset().top - 100
                        }, 500);
                    } else {
                        showMessage('error', response.data.message || 'Failed to send message. Please try again.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Contact form error:', error);
                    showMessage('error', 'An error occurred. Please try again later.');
                },
                complete: function() {
                    resetSubmitButton();
                }
            });
        });
        
        /**
         * Show message
         */
        function showMessage(type, message) {
            $messages.removeClass('success error').addClass(type);
            
            var icon = type === 'success' ? '✓' : '✕';
            $messages.html('<div class="message-content"><span class="message-icon">' + icon + '</span>' + message + '</div>');
            $messages.slideDown(300);
        }
        
        /**
         * Reset submit button
         */
        function resetSubmitButton() {
            $submitBtn.prop('disabled', false);
            $submitBtn.find('.btn-text').show();
            $submitBtn.find('.btn-loading').hide();
        }
        
        /**
         * Validate email format
         */
        function isValidEmail(email) {
            var regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return regex.test(email);
        }
        
        // Real-time email validation
        $('#contact_email').on('blur', function() {
            var $input = $(this);
            var email = $input.val().trim();
            
            if (email && !isValidEmail(email)) {
                $input.addClass('error');
                $input.next('.field-error').remove();
                $input.after('<span class="field-error">Please enter a valid email address.</span>');
            } else {
                $input.removeClass('error');
                $input.next('.field-error').remove();
            }
        });
        
        // Clear error on input
        $form.find('input, textarea').on('input', function() {
            $(this).removeClass('error');
            $(this).next('.field-error').remove();
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
