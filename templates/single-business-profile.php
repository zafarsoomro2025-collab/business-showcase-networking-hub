<?php
/**
 * Template for Single Business Profile
 * 
 * @package Business Showcase & Networking Hub
 */

get_header();

while ( have_posts() ) : the_post();
    
    $post_id = get_the_ID();
    
    // Get meta data
    $website_url = get_post_meta( $post_id, '_business_website_url', true );
    $contact_email = get_post_meta( $post_id, '_business_contact_email', true );
    $facebook_url = get_post_meta( $post_id, '_business_facebook_url', true );
    $twitter_url = get_post_meta( $post_id, '_business_twitter_url', true );
    $linkedin_url = get_post_meta( $post_id, '_business_linkedin_url', true );
    $services = get_post_meta( $post_id, '_business_services', true );
    $is_featured = get_post_meta( $post_id, '_business_is_featured', true );
    
    // Get rating stats
    $rating_data = business_showcase_get_rating_stats( $post_id );
    $average_rating = $rating_data['average'];
    $rating_count = $rating_data['count'];
    
    // Get categories
    $categories = get_the_terms( $post_id, 'business_category' );
    
    ?>
    
    <div class="business-profile-wrapper">
        <article id="post-<?php the_ID(); ?>" <?php post_class( 'business-profile-single' ); ?>>
            
            <div class="business-profile-container">
                
                <!-- Hero Header Section -->
                <div class="business-profile-hero">
                    
                    <div class="hero-content">
                        
                        <div class="hero-left">
                            <?php if ( has_post_thumbnail() ) : ?>
                                <div class="business-profile-logo">
                                    <?php the_post_thumbnail( 'large', array( 'alt' => esc_attr( get_the_title() ) ) ); ?>
                                </div>
                            <?php else : ?>
                                <div class="business-profile-logo placeholder">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="120" height="120" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                        <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                        <polyline points="21 15 16 10 5 21"></polyline>
                                    </svg>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="hero-right">
                            
                            <div class="hero-title-row">
                                <h1 class="business-profile-title"><?php the_title(); ?></h1>
                                <?php if ( $is_featured == '1' ) : ?>
                                    <span class="featured-badge-hero">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                        </svg>
                                        <?php esc_html_e( 'Featured', 'business-showcase-networking-hub' ); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <?php if ( $categories && ! is_wp_error( $categories ) ) : ?>
                                <div class="business-profile-categories">
                                    <?php foreach ( $categories as $category ) : ?>
                                        <a href="<?php echo esc_url( get_term_link( $category ) ); ?>" class="business-category-badge">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M20 7h-9"></path>
                                                <path d="M14 17H5"></path>
                                                <circle cx="17" cy="17" r="3"></circle>
                                                <circle cx="7" cy="7" r="3"></circle>
                                            </svg>
                                            <?php echo esc_html( $category->name ); ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ( $average_rating > 0 ) : ?>
                                <div class="business-star-rating-hero">
                                    <div class="rating-stars-large">
                                        <?php echo business_showcase_get_star_rating_html( $average_rating ); ?>
                                    </div>
                                    <div class="rating-meta-large">
                                        <span class="rating-average"><?php echo number_format( $average_rating, 1 ); ?></span>
                                        <span class="rating-separator">/</span>
                                        <span class="rating-max">5</span>
                                        <span class="rating-count">
                                            <?php 
                                            printf(
                                                esc_html( _n( '(%d review)', '(%d reviews)', $rating_count, 'business-showcase-networking-hub' ) ),
                                                $rating_count
                                            );
                                            ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Quick Action Buttons -->
                            <div class="hero-actions">
                                <?php if ( $website_url ) : ?>
                                    <a href="<?php echo esc_url( $website_url ); ?>" class="btn-primary" target="_blank" rel="nofollow noopener noreferrer">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <line x1="2" y1="12" x2="22" y2="12"></line>
                                            <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                                        </svg>
                                        <?php esc_html_e( 'Visit Website', 'business-showcase-networking-hub' ); ?>
                                    </a>
                                <?php endif; ?>
                                <?php if ( $contact_email ) : ?>
                                    <a href="#contact-form" class="btn-secondary smooth-scroll">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                            <polyline points="22,6 12,13 2,6"></polyline>
                                        </svg>
                                        <?php esc_html_e( 'Contact Business', 'business-showcase-networking-hub' ); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                            
                        </div>
                        
                    </div>
                    
                </div>
                
                <!-- Two Column Layout -->
                <div class="business-profile-layout">
                    
                    <!-- Main Content Column -->
                    <div class="business-profile-main">
                        
                        <!-- About Section -->
                        <div class="profile-section about-section">
                            <h2 class="section-heading">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                </svg>
                                <?php esc_html_e( 'About This Business', 'business-showcase-networking-hub' ); ?>
                            </h2>
                            <div class="section-content">
                                <?php the_content(); ?>
                            </div>
                        </div>
                        
                        <!-- Services Section -->
                        <?php if ( ! empty( $services ) ) : ?>
                            <div class="profile-section services-section">
                                <h2 class="section-heading">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path>
                                    </svg>
                                    <?php esc_html_e( 'Services Offered', 'business-showcase-networking-hub' ); ?>
                                </h2>
                                <div class="section-content">
                                    <div class="services-grid">
                                        <?php
                                        // Handle both string and array formats
                                        if ( is_array( $services ) ) {
                                            $services_array = $services;
                                        } else {
                                            $services_array = array_map( 'trim', explode( ',', $services ) );
                                        }
                                        
                                        foreach ( $services_array as $service ) :
                                            if ( ! empty( $service ) ) :
                                        ?>
                                            <div class="service-item-card">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                </svg>
                                                <span><?php echo esc_html( $service ); ?></span>
                                            </div>
                                        <?php
                                            endif;
                                        endforeach;
                                        ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Contact Form Section -->
                        <?php if ( $contact_email ) : ?>
                            <div id="contact-form" class="profile-section contact-form-section">
                                <h2 class="section-heading">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                        <polyline points="22,6 12,13 2,6"></polyline>
                                    </svg>
                                    <?php esc_html_e( 'Send a Message', 'business-showcase-networking-hub' ); ?>
                                </h2>
                                <div class="section-content">
                                    <p class="contact-intro"><?php esc_html_e( 'Have a question or inquiry? Send a message directly to this business.', 'business-showcase-networking-hub' ); ?></p>
                                    
                                    <form id="business-contact-form" class="business-contact-form-modern" method="post" data-post-id="<?php echo esc_attr( $post_id ); ?>">
                                        
                                        <?php wp_nonce_field( 'business_contact_form_nonce', 'business_contact_nonce' ); ?>
                                        
                                        <div class="form-messages" id="contact-form-messages"></div>
                                        
                                        <div class="form-row">
                                            <div class="form-group">
                                                <label for="contact_name">
                                                    <?php esc_html_e( 'Your Name', 'business-showcase-networking-hub' ); ?>
                                                    <span class="required">*</span>
                                                </label>
                                                <input 
                                                    type="text" 
                                                    id="contact_name" 
                                                    name="contact_name" 
                                                    class="form-control" 
                                                    required 
                                                    placeholder="<?php esc_attr_e( 'John Doe', 'business-showcase-networking-hub' ); ?>" />
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="contact_email">
                                                    <?php esc_html_e( 'Your Email', 'business-showcase-networking-hub' ); ?>
                                                    <span class="required">*</span>
                                                </label>
                                                <input 
                                                    type="email" 
                                                    id="contact_email" 
                                                    name="contact_email" 
                                                    class="form-control" 
                                                    required 
                                                    placeholder="<?php esc_attr_e( 'john@example.com', 'business-showcase-networking-hub' ); ?>" />
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="contact_subject">
                                                <?php esc_html_e( 'Subject', 'business-showcase-networking-hub' ); ?>
                                                <span class="required">*</span>
                                            </label>
                                            <input 
                                                type="text" 
                                                id="contact_subject" 
                                                name="contact_subject" 
                                                class="form-control" 
                                                required 
                                                placeholder="<?php esc_attr_e( 'I\'m interested in your services', 'business-showcase-networking-hub' ); ?>" />
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="contact_message">
                                                <?php esc_html_e( 'Message', 'business-showcase-networking-hub' ); ?>
                                                <span class="required">*</span>
                                            </label>
                                            <textarea 
                                                id="contact_message" 
                                                name="contact_message" 
                                                class="form-control" 
                                                rows="6" 
                                                required 
                                                placeholder="<?php esc_attr_e( 'Tell us more about your inquiry...', 'business-showcase-networking-hub' ); ?>"></textarea>
                                        </div>
                                        
                                        <div class="form-group">
                                            <button type="submit" class="submit-btn-modern" id="contact-submit-btn">
                                                <span class="btn-text">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <line x1="22" y1="2" x2="11" y2="13"></line>
                                                        <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                                                    </svg>
                                                    <?php esc_html_e( 'Send Message', 'business-showcase-networking-hub' ); ?>
                                                </span>
                                                <span class="btn-loading" style="display: none;">
                                                    <span class="spinner"></span>
                                                    <?php esc_html_e( 'Sending...', 'business-showcase-networking-hub' ); ?>
                                                </span>
                                            </button>
                                        </div>
                                        
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                    </div>
                    
                    <!-- Sidebar Column -->
                    <aside class="business-profile-sidebar">
                        
                        <!-- Contact Card -->
                        <div class="sidebar-card contact-card">
                            <h3 class="sidebar-card-title">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                </svg>
                                <?php esc_html_e( 'Contact Info', 'business-showcase-networking-hub' ); ?>
                            </h3>
                            
                            <div class="contact-items">
                                <?php if ( $website_url ) : ?>
                                    <a href="<?php echo esc_url( $website_url ); ?>" class="contact-item-link" target="_blank" rel="nofollow noopener noreferrer">
                                        <div class="contact-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <line x1="2" y1="12" x2="22" y2="12"></line>
                                                <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                                            </svg>
                                        </div>
                                        <div class="contact-details">
                                            <span class="contact-label"><?php esc_html_e( 'Website', 'business-showcase-networking-hub' ); ?></span>
                                            <span class="contact-value"><?php echo esc_html( parse_url( $website_url, PHP_URL_HOST ) ); ?></span>
                                        </div>
                                    </a>
                                <?php endif; ?>
                                
                                <?php if ( $contact_email ) : ?>
                                    <a href="mailto:<?php echo esc_attr( $contact_email ); ?>" class="contact-item-link">
                                        <div class="contact-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                                <polyline points="22,6 12,13 2,6"></polyline>
                                            </svg>
                                        </div>
                                        <div class="contact-details">
                                            <span class="contact-label"><?php esc_html_e( 'Email', 'business-showcase-networking-hub' ); ?></span>
                                            <span class="contact-value"><?php echo esc_html( $contact_email ); ?></span>
                                        </div>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Social Links Card -->
                        <?php if ( $facebook_url || $twitter_url || $linkedin_url ) : ?>
                            <div class="sidebar-card social-card">
                                <h3 class="sidebar-card-title">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
                                    </svg>
                                    <?php esc_html_e( 'Social Media', 'business-showcase-networking-hub' ); ?>
                                </h3>
                                
                                <div class="social-links-modern">
                                    <?php if ( $facebook_url ) : ?>
                                        <a href="<?php echo esc_url( $facebook_url ); ?>" class="social-link-modern facebook" target="_blank" rel="nofollow noopener noreferrer">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                            </svg>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php if ( $twitter_url ) : ?>
                                        <a href="<?php echo esc_url( $twitter_url ); ?>" class="social-link-modern twitter" target="_blank" rel="nofollow noopener noreferrer">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                            </svg>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php if ( $linkedin_url ) : ?>
                                        <a href="<?php echo esc_url( $linkedin_url ); ?>" class="social-link-modern linkedin" target="_blank" rel="nofollow noopener noreferrer">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                            </svg>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Share Card -->
                        <!-- Share Card -->
                        <div class="sidebar-card share-card">
                            <h3 class="sidebar-card-title">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="18" cy="5" r="3"></circle>
                                    <circle cx="6" cy="12" r="3"></circle>
                                    <circle cx="18" cy="19" r="3"></circle>
                                    <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line>
                                    <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line>
                                </svg>
                                <?php esc_html_e( 'Share', 'business-showcase-networking-hub' ); ?>
                            </h3>
                            
                            <div class="share-buttons-modern">
                                <button type="button" class="share-button share-facebook" data-share-type="facebook" data-url="<?php echo esc_url( get_permalink() ); ?>" data-title="<?php echo esc_attr( get_the_title() ); ?>">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                    </svg>
                                    Facebook
                                </button>
                                
                                <button type="button" class="share-button share-twitter" data-share-type="twitter" data-url="<?php echo esc_url( get_permalink() ); ?>" data-title="<?php echo esc_attr( get_the_title() ); ?>">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                    </svg>
                                    Twitter
                                </button>
                                
                                <button type="button" class="share-button share-linkedin" data-share-type="linkedin" data-url="<?php echo esc_url( get_permalink() ); ?>" data-title="<?php echo esc_attr( get_the_title() ); ?>">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                    </svg>
                                    LinkedIn
                                </button>
                                
                                <button type="button" class="share-button share-copy" data-share-type="copy" data-url="<?php echo esc_url( get_permalink() ); ?>">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path>
                                        <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path>
                                    </svg>
                                    <span class="copy-text">Copy Link</span>
                                    <span class="copied-text" style="display: none;">Copied!</span>
                                </button>
                            </div>
                        </div>
                        
                    </aside>
                    
                </div>
                
                <!-- Reviews Section - Full Width at Bottom -->
                <div class="business-reviews-wrapper">
                    <div class="profile-section reviews-section">
                        <h2 class="section-heading">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                            </svg>
                            <?php esc_html_e( 'Customer Reviews', 'business-showcase-networking-hub' ); ?>
                        </h2>
                        <div class="section-content">
                            <?php comments_template(); ?>
                        </div>
                    </div>
                </div>
                
            </div>
            
        </article>
    </div>
    
<?php
endwhile;

get_footer();
