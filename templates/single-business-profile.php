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
    $star_rating = get_post_meta( $post_id, '_business_star_rating', true );
    
    // Get categories
    $categories = get_the_terms( $post_id, 'business_category' );
    
    ?>
    
    <article id="post-<?php the_ID(); ?>" <?php post_class( 'business-profile-single' ); ?>>
        
        <div class="business-profile-container">
            
            <!-- Header Section -->
            <div class="business-profile-header">
                
                <?php if ( has_post_thumbnail() ) : ?>
                    <div class="business-profile-logo">
                        <?php the_post_thumbnail( 'large', array( 'alt' => esc_attr( get_the_title() ) ) ); ?>
                    </div>
                <?php endif; ?>
                
                <div class="business-profile-header-content">
                    
                    <h1 class="business-profile-title">
                        <?php the_title(); ?>
                        <?php if ( $is_featured == '1' ) : ?>
                            <span class="featured-badge"><?php esc_html_e( 'Featured', 'business-showcase-networking-hub' ); ?></span>
                        <?php endif; ?>
                    </h1>
                    
                    <?php if ( ! empty( $star_rating ) && $star_rating > 0 ) : ?>
                        <div class="business-star-rating">
                            <?php echo business_showcase_display_star_rating( floatval( $star_rating ) ); ?>
                            <span class="rating-value"><?php echo esc_html( number_format( floatval( $star_rating ), 1 ) ); ?>/5</span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ( $categories && ! is_wp_error( $categories ) ) : ?>
                        <div class="business-profile-categories">
                            <?php foreach ( $categories as $category ) : ?>
                                <a href="<?php echo esc_url( get_term_link( $category ) ); ?>" class="business-category-tag">
                                    <?php echo esc_html( $category->name ); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
                </div>
                
            </div>
            
            <!-- Main Content -->
            <div class="business-profile-content">
                
                <!-- Description -->
                <div class="business-profile-description">
                    <h2><?php esc_html_e( 'About This Business', 'business-showcase-networking-hub' ); ?></h2>
                    <div class="business-description-content">
                        <?php the_content(); ?>
                    </div>
                </div>
                
                <!-- Services -->
                <?php if ( ! empty( $services ) && is_array( $services ) ) : ?>
                    <div class="business-profile-services">
                        <h2><?php esc_html_e( 'Services Offered', 'business-showcase-networking-hub' ); ?></h2>
                        <ul class="services-list">
                            <?php 
                            $service_labels = business_showcase_get_service_labels();
                            foreach ( $services as $service ) : 
                                if ( isset( $service_labels[ $service ] ) ) :
                            ?>
                                <li class="service-item">
                                    <span class="service-icon">✓</span>
                                    <?php echo esc_html( $service_labels[ $service ] ); ?>
                                </li>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
            </div>
            
            <!-- Sidebar -->
            <aside class="business-profile-sidebar">
                
                <!-- Contact Information -->
                <div class="sidebar-widget contact-widget">
                    <h3><?php esc_html_e( 'Contact Information', 'business-showcase-networking-hub' ); ?></h3>
                    
                    <?php if ( $website_url ) : ?>
                        <div class="contact-item">
                            <span class="contact-icon">🌐</span>
                            <a href="<?php echo esc_url( $website_url ); ?>" target="_blank" rel="nofollow noopener noreferrer">
                                <?php esc_html_e( 'Visit Website', 'business-showcase-networking-hub' ); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ( $contact_email ) : ?>
                        <div class="contact-item">
                            <span class="contact-icon">✉️</span>
                            <a href="mailto:<?php echo esc_attr( $contact_email ); ?>">
                                <?php echo esc_html( $contact_email ); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                    
                </div>
                
                <!-- Social Links -->
                <?php if ( $facebook_url || $twitter_url || $linkedin_url ) : ?>
                    <div class="sidebar-widget social-widget">
                        <h3><?php esc_html_e( 'Follow Us', 'business-showcase-networking-hub' ); ?></h3>
                        <div class="social-links">
                            
                            <?php if ( $facebook_url ) : ?>
                                <a href="<?php echo esc_url( $facebook_url ); ?>" 
                                   class="social-link facebook" 
                                   target="_blank" 
                                   rel="nofollow noopener noreferrer"
                                   aria-label="<?php esc_attr_e( 'Facebook', 'business-showcase-networking-hub' ); ?>">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                    </svg>
                                    <span>Facebook</span>
                                </a>
                            <?php endif; ?>
                            
                            <?php if ( $twitter_url ) : ?>
                                <a href="<?php echo esc_url( $twitter_url ); ?>" 
                                   class="social-link twitter" 
                                   target="_blank" 
                                   rel="nofollow noopener noreferrer"
                                   aria-label="<?php esc_attr_e( 'Twitter', 'business-showcase-networking-hub' ); ?>">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                    </svg>
                                    <span>Twitter</span>
                                </a>
                            <?php endif; ?>
                            
                            <?php if ( $linkedin_url ) : ?>
                                <a href="<?php echo esc_url( $linkedin_url ); ?>" 
                                   class="social-link linkedin" 
                                   target="_blank" 
                                   rel="nofollow noopener noreferrer"
                                   aria-label="<?php esc_attr_e( 'LinkedIn', 'business-showcase-networking-hub' ); ?>">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                    </svg>
                                    <span>LinkedIn</span>
                                </a>
                            <?php endif; ?>
                            
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Call to Action -->
                <?php if ( $website_url ) : ?>
                    <div class="sidebar-widget cta-widget">
                        <a href="<?php echo esc_url( $website_url ); ?>" 
                           class="cta-button" 
                           target="_blank" 
                           rel="nofollow noopener noreferrer">
                            <?php esc_html_e( 'Visit Our Website', 'business-showcase-networking-hub' ); ?>
                        </a>
                    </div>
                <?php endif; ?>
                
            </aside>
            
        </div>
        
    </article>
    
<?php
endwhile;

get_footer();
