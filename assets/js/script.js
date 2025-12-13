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
        console.log('Business Showcase & Networking Hub JS initialized', businessShowcaseAjax);
        businessShowcaseInit();
        initBusinessDirectory();
        initStarRating();
        initContactForm();
        initSocialSharing();
    });

    /**
     * Main initialization function
     */
    function businessShowcaseInit() {
        console.log('Business Showcase plugin loaded');
        
        // Check if AJAX object is available
        if (typeof businessShowcaseAjax === 'undefined') {
            console.error('businessShowcaseAjax is not defined. AJAX functionality will not work.');
        } else {
            console.log('AJAX URL:', businessShowcaseAjax.ajaxurl);
            console.log('Nonce:', businessShowcaseAjax.nonce);
        }
        
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
        $('#reset-filters, #reset-all-filters').on('click', function() {
            $('#category-filter').val('');
            $('#service-filter').val('');
            $('#business-search').val('');
            $('#clear-search').hide();
            $('#search-info').hide();
            filterBusinesses();
        });
        
        // Initialize live search
        initLiveSearch();
    }

    /**
     * Initialize Live Search
     */
    function initLiveSearch() {
        var searchTimeout;
        var $searchInput = $('#business-search');
        var $searchResults = $('#search-results');
        var $clearBtn = $('#clear-search');
        var $clearFilterBtn = $('#clear-search-filter');
        var $searchInfo = $('#search-info');
        var currentSearch = '';
        
        // Handle search input with debounce
        $searchInput.on('input', function() {
            var query = $(this).val().trim();
            
            // Show/hide clear button
            if (query.length > 0) {
                $clearBtn.show();
            } else {
                $clearBtn.hide();
                $searchResults.hide().empty();
                return;
            }
            
            // Clear previous timeout
            clearTimeout(searchTimeout);
            
            // Don't search for less than 2 characters
            if (query.length < 2) {
                $searchResults.html('<div class=\"search-result-message\">' + 
                    'Please enter at least 2 characters' + 
                    '</div>').show();
                return;
            }
            
            // Show loading state
            $searchResults.html('<div class=\"search-result-loading\">' +
                '<span class=\"spinner\"></span> Searching...' +
                '</div>').show();
            
            // Debounce search
            searchTimeout = setTimeout(function() {
                performLiveSearch(query);
            }, 300);
        });
        
        // Handle clear button
        $clearBtn.on('click', function() {
            $searchInput.val('').trigger('input').focus();
            $searchResults.hide().empty();
            $searchInfo.hide();
            currentSearch = '';
            filterBusinesses();
        });
        
        // Handle clear search filter button
        $clearFilterBtn.on('click', function() {
            $searchInput.val('');
            $clearBtn.hide();
            $searchInfo.hide();
            currentSearch = '';
            filterBusinesses();
        });
        
        // Close dropdown when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.business-search-container').length) {
                $searchResults.hide();
            }
        });
        
        // Handle search input focus
        $searchInput.on('focus', function() {
            if ($(this).val().trim().length >= 2 && $searchResults.children().length > 0) {
                $searchResults.show();
            }
        });
        
        // Store current search for filtering
        window.businessCurrentSearch = '';
    }

    /**
     * Perform Live Search via AJAX
     */
    function performLiveSearch(query) {
        var $searchResults = $('#search-results');
        
        // Check if AJAX object is available
        if (typeof businessShowcaseAjax === 'undefined') {
            console.error('businessShowcaseAjax is not defined');
            $searchResults.html('<div class="search-result-error">Configuration error. Please refresh the page.</div>');
            return;
        }
        
        console.log('Performing search for:', query);
        
        $.ajax({
            url: businessShowcaseAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'business_showcase_live_search',
                nonce: businessShowcaseAjax.nonce,
                search: query
            },
            success: function(response) {
                if (response.success && response.data.results) {
                    displaySearchResults(response.data);
                } else {
                    $searchResults.html('<div class=\"search-result-message\">' +
                        'No results found' +
                        '</div>');
                }
            },
            error: function() {
                $searchResults.html('<div class=\"search-result-error\">' +
                    'Search error. Please try again.' +
                    '</div>');
            }
        });
    }

    /**
     * Display Live Search Results
     */
    function displaySearchResults(data) {
        var $searchResults = $('#search-results');
        var html = '';
        
        if (data.results.length === 0) {
            html = '<div class=\"search-result-message\">' +
                'No businesses found matching \"' + data.query + '\"' +
                '</div>';
        } else {
            html += '<div class=\"search-results-header\">' +
                '<span class=\"results-count\">' + data.count + ' result' + (data.count !== 1 ? 's' : '') + ' found</span>' +
                '<button type=\"button\" class=\"view-all-results\" data-search=\"' + data.query + '\">' +
                'View all results' +
                '</button>' +
                '</div>';
            
            html += '<div class=\"search-results-list\">';
            
            $.each(data.results, function(index, result) {
                var featuredBadge = result.is_featured ? '<span class=\"result-featured-badge\">Featured</span>' : '';
                var thumbnail = result.thumbnail ? 
                    '<img src=\"' + result.thumbnail + '\" alt=\"' + result.title + '\">' : 
                    '<div class=\"result-no-image\"><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\"><rect x=\"3\" y=\"3\" width=\"18\" height=\"18\" rx=\"2\" ry=\"2\"></rect><circle cx=\"8.5\" cy=\"8.5\" r=\"1.5\"></circle><polyline points=\"21 15 16 10 5 21\"></polyline></svg></div>';
                
                var rating = '';
                if (result.rating > 0) {
                    rating = '<div class=\"result-rating\">' +
                        '<span class=\"rating-stars\">' + generateStars(result.rating) + '</span>' +
                        '<span class=\"rating-value\">' + result.rating.toFixed(1) + '</span>' +
                        '<span class=\"rating-count\">(' + result.rating_count + ')</span>' +
                        '</div>';
                }
                
                var categories = result.categories.length > 0 ? 
                    '<span class=\"result-categories\">' + result.categories.join(', ') + '</span>' : '';
                
                html += '<a href=\"' + result.url + '\" class=\"search-result-item\">' +
                    '<div class=\"result-thumbnail\">' + thumbnail + featuredBadge + '</div>' +
                    '<div class=\"result-content\">' +
                        '<h4 class=\"result-title\">' + result.title + '</h4>' +
                        categories +
                        '<p class=\"result-excerpt\">' + result.excerpt + '</p>' +
                        rating +
                    '</div>' +
                    '</a>';
            });
            
            html += '</div>';
        }
        
        $searchResults.html(html).show();
        
        // Handle \"View all results\" button
        $('.view-all-results').on('click', function() {
            var searchQuery = $(this).data('search');
            $('#business-search').val(searchQuery);
            $('#search-results').hide();
            window.businessCurrentSearch = searchQuery;
            
            // Show search info
            $('#search-info').show()
                .find('.search-query').text('\"' + searchQuery + '\"');
            
            // Filter with search
            filterBusinesses();
        });
    }

    /**
     * Generate Star Rating HTML
     */
    function generateStars(rating) {
        var stars = '';
        var fullStars = Math.floor(rating);
        var hasHalfStar = (rating % 1) >= 0.5;
        
        for (var i = 0; i < fullStars; i++) {
            stars += '★';
        }
        
        if (hasHalfStar) {
            stars += '½';
        }
        
        var emptyStars = 5 - Math.ceil(rating);
        for (var j = 0; j < emptyStars; j++) {
            stars += '☆';
        }
        
        return stars;
    }

    /**
     * Filter Businesses via AJAX
     */
    function filterBusinesses() {
        var $grid = $('#business-grid');
        var $loading = $('#business-loading');
        var category = $('#category-filter').val();
        var service = $('#service-filter').val();
        var search = window.businessCurrentSearch || '';
        
        // Check if AJAX object is available
        if (typeof businessShowcaseAjax === 'undefined') {
            console.error('businessShowcaseAjax is not defined');
            alert('Configuration error. Please refresh the page.');
            return;
        }
        
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
                service: service,
                search: search
            },
            success: function(response) {
                if (response.success) {
                    $grid.html(response.data.html);
                    
                    // Update search info count
                    var resultCount = $grid.find('.business-card').length;
                    $('#search-info .search-count').text(resultCount + ' result' + (resultCount !== 1 ? 's' : ''));
                    
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
     * Initialize Social Sharing Buttons
     */
    function initSocialSharing() {
        if ($('.business-social-sharing').length === 0) {
            return;
        }
        
        // Handle share button clicks
        $('.share-button').on('click', function(e) {
            e.preventDefault();
            
            var $btn = $(this);
            var shareType = $btn.data('share-type');
            var url = encodeURIComponent($btn.data('url'));
            var title = encodeURIComponent($btn.data('title'));
            var shareUrl = '';
            
            switch(shareType) {
                case 'facebook':
                    shareUrl = 'https://www.facebook.com/sharer/sharer.php?u=' + url;
                    openShareWindow(shareUrl, 'Facebook', 600, 400);
                    break;
                    
                case 'twitter':
                    var text = encodeURIComponent('Check out ' + $btn.data('title'));
                    shareUrl = 'https://twitter.com/intent/tweet?url=' + url + '&text=' + text;
                    openShareWindow(shareUrl, 'Twitter', 600, 400);
                    break;
                    
                case 'linkedin':
                    shareUrl = 'https://www.linkedin.com/sharing/share-offsite/?url=' + url;
                    openShareWindow(shareUrl, 'LinkedIn', 600, 600);
                    break;
                    
                case 'copy':
                    copyToClipboard($btn.data('url'), $btn);
                    break;
            }
        });
    }

    /**
     * Open Share Window
     */
    function openShareWindow(url, title, width, height) {
        var left = (screen.width - width) / 2;
        var top = (screen.height - height) / 2;
        var params = 'width=' + width + ',height=' + height + ',left=' + left + ',top=' + top;
        params += ',toolbar=0,location=0,menubar=0,resizable=1,scrollbars=1';
        
        window.open(url, 'Share on ' + title, params);
    }

    /**
     * Copy Link to Clipboard
     */
    function copyToClipboard(text, $btn) {
        // Modern clipboard API
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(function() {
                showCopySuccess($btn);
            }).catch(function(err) {
                // Fallback to older method
                fallbackCopyToClipboard(text, $btn);
            });
        } else {
            // Fallback for older browsers
            fallbackCopyToClipboard(text, $btn);
        }
    }

    /**
     * Fallback Copy Method for Older Browsers
     */
    function fallbackCopyToClipboard(text, $btn) {
        var $temp = $('<textarea>');
        $('body').append($temp);
        $temp.val(text).select();
        
        try {
            var successful = document.execCommand('copy');
            if (successful) {
                showCopySuccess($btn);
            } else {
                alert('Failed to copy link. Please copy manually: ' + text);
            }
        } catch (err) {
            alert('Failed to copy link. Please copy manually: ' + text);
        }
        
        $temp.remove();
    }

    /**
     * Show Copy Success Feedback
     */
    function showCopySuccess($btn) {
        var $copyText = $btn.find('.copy-text');
        var $copiedText = $btn.find('.copied-text');
        
        $copyText.hide();
        $copiedText.show();
        $btn.addClass('copied');
        
        setTimeout(function() {
            $copyText.show();
            $copiedText.hide();
            $btn.removeClass('copied');
        }, 2000);
    }

    /**
     * AJAX Helper Function
     */
    function performBusinessAjax(action, data) {
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
