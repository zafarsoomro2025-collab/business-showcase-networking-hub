<?php
/**
 * Template for Business Profile Archive
 * 
 * @package Business Showcase & Networking Hub
 */

get_header();

// Get current filters from URL
$current_category = isset( $_GET['business_category'] ) ? sanitize_text_field( $_GET['business_category'] ) : '';
$current_service = isset( $_GET['business_service'] ) ? sanitize_text_field( $_GET['business_service'] ) : '';
$current_search = isset( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '';

// Get categories for filter
$categories = get_terms( array(
    'taxonomy' => 'business_category',
    'hide_empty' => true,
) );

// Get all unique services
$all_services = business_showcase_get_all_services();

?>

<div class="business-archive-wrapper">
    
    <div class="business-archive-container">
        
        <!-- Archive Header -->
        <div class="business-archive-header">
            <h1 class="archive-title">
                <?php 
                if ( is_post_type_archive( 'business_profile' ) ) {
                    esc_html_e( 'Business Directory', 'business-showcase-networking-hub' );
                } else {
                    the_archive_title();
                }
                ?>
            </h1>
            
            <?php if ( is_post_type_archive( 'business_profile' ) ) : ?>
                <p class="archive-description">
                    <?php esc_html_e( 'Explore our comprehensive directory of businesses. Use the search and filters below to find exactly what you\'re looking for.', 'business-showcase-networking-hub' ); ?>
                </p>
            <?php else : ?>
                <div class="archive-description">
                    <?php the_archive_description(); ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Search and Filter Section -->
        <div class="business-archive-filters">
            
            <div class="filters-container">
                
                <!-- Search Bar -->
                <div class="business-search-wrapper">
                    <div class="business-search-container">
                        <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.35-4.35"></path>
                        </svg>
                        <input 
                            type="text" 
                            id="business-search" 
                            class="business-search-input" 
                            placeholder="<?php esc_attr_e( 'Search businesses...', 'business-showcase-networking-hub' ); ?>"
                            value="<?php echo esc_attr( $current_search ); ?>"
                            autocomplete="off"
                        >
                        <button type="button" id="clear-search" class="clear-search-btn" style="<?php echo empty( $current_search ) ? 'display: none;' : ''; ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                        <div id="search-results" class="search-results-dropdown"></div>
                    </div>
                </div>
                
                <!-- Filter Dropdowns -->
                <div class="filter-controls">
                    
                    <!-- Category Filter -->
                    <div class="filter-item">
                        <label for="category-filter" class="filter-label">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="7" height="7"></rect>
                                <rect x="14" y="3" width="7" height="7"></rect>
                                <rect x="14" y="14" width="7" height="7"></rect>
                                <rect x="3" y="14" width="7" height="7"></rect>
                            </svg>
                            <?php esc_html_e( 'Category', 'business-showcase-networking-hub' ); ?>
                        </label>
                        <select id="category-filter" class="filter-select">
                            <option value=""><?php esc_html_e( 'All Categories', 'business-showcase-networking-hub' ); ?></option>
                            <?php if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) : ?>
                                <?php foreach ( $categories as $category ) : ?>
                                    <option value="<?php echo esc_attr( $category->slug ); ?>" <?php selected( $current_category, $category->slug ); ?>>
                                        <?php echo esc_html( $category->name ); ?> (<?php echo esc_html( $category->count ); ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <!-- Service Filter -->
                    <div class="filter-item">
                        <label for="service-filter" class="filter-label">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path>
                            </svg>
                            <?php esc_html_e( 'Service', 'business-showcase-networking-hub' ); ?>
                        </label>
                        <select id="service-filter" class="filter-select">
                            <option value=""><?php esc_html_e( 'All Services', 'business-showcase-networking-hub' ); ?></option>
                            <?php if ( ! empty( $all_services ) ) : ?>
                                <?php foreach ( $all_services as $service ) : ?>
                                    <option value="<?php echo esc_attr( $service ); ?>" <?php selected( $current_service, $service ); ?>>
                                        <?php echo esc_html( $service ); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <!-- Reset Button -->
                    <button type="button" id="reset-filters" class="reset-filters-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"></path>
                            <path d="M21 3v5h-5"></path>
                            <path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"></path>
                            <path d="M3 21v-5h5"></path>
                        </svg>
                        <?php esc_html_e( 'Reset', 'business-showcase-networking-hub' ); ?>
                    </button>
                    
                </div>
                
            </div>
            
            <!-- Active Search Info -->
            <?php if ( ! empty( $current_search ) ) : ?>
                <div id="search-info" class="search-info-banner">
                    <div class="search-info-content">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.35-4.35"></path>
                        </svg>
                        <span>
                            <?php esc_html_e( 'Showing results for:', 'business-showcase-networking-hub' ); ?>
                            <strong class="search-query">"<?php echo esc_html( $current_search ); ?>"</strong>
                        </span>
                        <button type="button" id="clear-search-filter" class="clear-search-filter-btn">
                            <?php esc_html_e( 'Clear search', 'business-showcase-networking-hub' ); ?>
                        </button>
                    </div>
                </div>
            <?php endif; ?>
            
        </div>
        
        <!-- Loading Indicator -->
        <div id="business-loading" class="business-loading" style="display: none;">
            <div class="loading-spinner"></div>
            <p><?php esc_html_e( 'Loading businesses...', 'business-showcase-networking-hub' ); ?></p>
        </div>
        
        <!-- Business Grid -->
        <div id="business-directory" class="business-archive-grid-wrapper">
            
            <?php if ( have_posts() ) : ?>
                
                <div id="business-grid" class="business-grid">
                    
                    <?php while ( have_posts() ) : the_post(); ?>
                        
                        <?php
                        $post_id = get_the_ID();
                        $is_featured = get_post_meta( $post_id, '_business_is_featured', true );
                        $star_rating = get_post_meta( $post_id, '_business_star_rating', true );
                        $website_url = get_post_meta( $post_id, '_business_website_url', true );
                        $services = get_post_meta( $post_id, '_business_services', true );
                        $categories = get_the_terms( $post_id, 'business_category' );
                        
                        // Get rating stats
                        $rating_data = business_showcase_get_rating_stats( $post_id );
                        $avg_rating = $rating_data['average'];
                        $rating_count = $rating_data['count'];
                        ?>
                        
                        <div class="business-card <?php echo $is_featured == '1' ? 'featured' : ''; ?>">
                            
                            <?php if ( $is_featured == '1' ) : ?>
                                <div class="featured-ribbon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                    </svg>
                                    <?php esc_html_e( 'Featured', 'business-showcase-networking-hub' ); ?>
                                </div>
                            <?php endif; ?>
                            
                            <a href="<?php the_permalink(); ?>" class="business-card-link">
                                
                                <!-- Business Logo/Image -->
                                <div class="business-card-image">
                                    <?php if ( has_post_thumbnail() ) : ?>
                                        <?php the_post_thumbnail( 'medium', array( 'alt' => esc_attr( get_the_title() ) ) ); ?>
                                    <?php else : ?>
                                        <div class="business-placeholder-image">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                                <polyline points="21 15 16 10 5 21"></polyline>
                                            </svg>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Business Info -->
                                <div class="business-card-content">
                                    
                                    <h3 class="business-card-title"><?php the_title(); ?></h3>
                                    
                                    <!-- Categories -->
                                    <?php if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) : ?>
                                        <div class="business-card-categories">
                                            <?php 
                                            $category_names = array();
                                            foreach ( $categories as $category ) {
                                                $category_names[] = esc_html( $category->name );
                                            }
                                            echo implode( ' • ', array_slice( $category_names, 0, 2 ) );
                                            ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Excerpt -->
                                    <div class="business-card-excerpt">
                                        <?php echo wp_trim_words( get_the_excerpt(), 15, '...' ); ?>
                                    </div>
                                    
                                    <!-- Rating -->
                                    <?php if ( $avg_rating > 0 ) : ?>
                                        <div class="business-card-rating">
                                            <div class="rating-stars" data-rating="<?php echo esc_attr( $avg_rating ); ?>">
                                                <?php echo business_showcase_get_star_rating_html( $avg_rating ); ?>
                                            </div>
                                            <span class="rating-text">
                                                <strong><?php echo number_format( $avg_rating, 1 ); ?></strong>
                                                <span class="rating-count">(<?php echo esc_html( $rating_count ); ?>)</span>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Services -->
                                    <?php if ( ! empty( $services ) ) : ?>
                                        <div class="business-card-services">
                                            <?php
                                            // Handle both string and array formats
                                            if ( is_array( $services ) ) {
                                                $services_array = array_slice( $services, 0, 3 );
                                            } else {
                                                $services_array = array_slice( array_map( 'trim', explode( ',', $services ) ), 0, 3 );
                                            }
                                            
                                            if ( ! empty( $services_array ) ) {
                                                foreach ( $services_array as $service ) {
                                                    if ( ! empty( $service ) ) {
                                                        echo '<span class="service-tag">' . esc_html( $service ) . '</span>';
                                                    }
                                                }
                                            }
                                            ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                </div>
                                
                                <!-- View Profile Button -->
                                <div class="business-card-footer">
                                    <span class="view-profile-btn">
                                        <?php esc_html_e( 'View Profile', 'business-showcase-networking-hub' ); ?>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <line x1="5" y1="12" x2="19" y2="12"></line>
                                            <polyline points="12 5 19 12 12 19"></polyline>
                                        </svg>
                                    </span>
                                </div>
                                
                            </a>
                            
                        </div>
                        
                    <?php endwhile; ?>
                    
                </div>
                
                <!-- Pagination -->
                <div class="business-archive-pagination">
                    <?php
                    the_posts_pagination( array(
                        'mid_size' => 2,
                        'prev_text' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg> ' . __( 'Previous', 'business-showcase-networking-hub' ),
                        'next_text' => __( 'Next', 'business-showcase-networking-hub' ) . ' <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>',
                    ) );
                    ?>
                </div>
                
            <?php else : ?>
                
                <!-- No Results Found -->
                <div class="no-results-found">
                    <div class="no-results-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </div>
                    <h2><?php esc_html_e( 'No Businesses Found', 'business-showcase-networking-hub' ); ?></h2>
                    <p><?php esc_html_e( 'We couldn\'t find any businesses matching your criteria. Try adjusting your search or filters.', 'business-showcase-networking-hub' ); ?></p>
                    <button type="button" id="reset-all-filters" class="btn-primary">
                        <?php esc_html_e( 'Clear All Filters', 'business-showcase-networking-hub' ); ?>
                    </button>
                </div>
                
            <?php endif; ?>
            
        </div>
        
    </div>
    
</div>

<?php get_footer(); ?>
