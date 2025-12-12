<?php
/**
 * Plugin Name: Business Showcase & Networking Hub
 * Plugin URI: https://github.com/zafarsoomro2025-collab/business-showcase-networking-hub
 * Description: A comprehensive plugin for showcasing businesses and facilitating networking connections.
 * Version: 1.0.0
 * Author: Zafar Soomro
 * Author URI: https://github.com/zafarsoomro2025-collab
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: business-showcase-networking-hub
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Plugin Version
 */
define( 'BUSINESS_SHOWCASE_VERSION', '1.0.0' );

/**
 * Plugin Directory Path
 */
define( 'BUSINESS_SHOWCASE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Plugin Directory URL
 */
define( 'BUSINESS_SHOWCASE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Activation Hook
 * Runs when the plugin is activated
 */
function business_showcase_activate() {
    // Set default options
    add_option( 'business_showcase_version', BUSINESS_SHOWCASE_VERSION );
    
    // Flush rewrite rules
    flush_rewrite_rules();
    
    // Add activation timestamp
    add_option( 'business_showcase_activated', current_time( 'timestamp' ) );
}
register_activation_hook( __FILE__, 'business_showcase_activate' );

/**
 * Deactivation Hook
 * Runs when the plugin is deactivated
 */
function business_showcase_deactivate() {
    // Flush rewrite rules
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'business_showcase_deactivate' );

/**
 * Load Plugin Text Domain for Translations
 */
function business_showcase_load_textdomain() {
    load_plugin_textdomain(
        'business-showcase-networking-hub',
        false,
        dirname( plugin_basename( __FILE__ ) ) . '/languages'
    );
}
add_action( 'plugins_loaded', 'business_showcase_load_textdomain' );

/**
 * Check if Business Showcase content is present on the page
 */
function business_showcase_has_content() {
    global $post;
    
    // Always load on single business profile pages
    if ( is_singular( 'business_profile' ) ) {
        return true;
    }
    
    // Always load on business category archive pages
    if ( is_tax( 'business_category' ) ) {
        return true;
    }
    
    // Check if post content has shortcodes or blocks
    if ( is_a( $post, 'WP_Post' ) ) {
        // Check for shortcodes
        if ( has_shortcode( $post->post_content, 'business_directory' ) ||
             has_shortcode( $post->post_content, 'featured_businesses' ) ) {
            return true;
        }
        
        // Check for Gutenberg blocks
        if ( function_exists( 'has_block' ) ) {
            if ( has_block( 'business-showcase/directory', $post ) ||
                 has_block( 'business-showcase/success-stories', $post ) ) {
                return true;
            }
        }
    }
    
    return false;
}

/**
 * Enqueue CSS and JS Files
 * Assets are loaded conditionally only on pages with business showcase content
 */
function business_showcase_enqueue_scripts() {
    // Check if we should load assets
    if ( ! business_showcase_has_content() ) {
        return;
    }
    
    // Enqueue main CSS with responsive grid, hover effects, and mobile-friendly layout
    wp_enqueue_style(
        'business-showcase-style',
        BUSINESS_SHOWCASE_PLUGIN_URL . 'assets/css/style.css',
        array(),
        BUSINESS_SHOWCASE_VERSION,
        'all'
    );
    
    // Enqueue single business profile CSS on single pages
    if ( is_singular( 'business_profile' ) ) {
        wp_enqueue_style(
            'business-showcase-single-style',
            BUSINESS_SHOWCASE_PLUGIN_URL . 'assets/css/single-business-profile.css',
            array( 'business-showcase-style' ),
            BUSINESS_SHOWCASE_VERSION,
            'all'
        );
    }
    
    // Enqueue JavaScript with jQuery dependency for AJAX filtering and star rating interactions
    wp_enqueue_script(
        'business-showcase-script',
        BUSINESS_SHOWCASE_PLUGIN_URL . 'assets/js/script.js',
        array( 'jquery' ),
        BUSINESS_SHOWCASE_VERSION,
        true
    );
    
    // Localize script for AJAX functionality
    wp_localize_script(
        'business-showcase-script',
        'businessShowcaseAjax',
        array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'business_showcase_nonce' ),
            'loading_text' => __( 'Loading...', 'business-showcase-networking-hub' ),
            'error_text' => __( 'Error loading businesses. Please try again.', 'business-showcase-networking-hub' ),
        )
    );
}
add_action( 'wp_enqueue_scripts', 'business_showcase_enqueue_scripts' );

/**
 * Add inline styles for dynamic content (ratings, featured badges)
 */
function business_showcase_inline_styles() {
    if ( ! business_showcase_has_content() ) {
        return;
    }
    
    $custom_css = "
        /* Dynamic Star Rating Colors */
        .star-rating-display .star.filled {
            color: #ffc107;
        }
        
        .star-rating-display .star.half {
            background: linear-gradient(90deg, #ffc107 50%, #e0e0e0 50%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Featured Badge Animation */
        @keyframes pulse-featured {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }
        
        .business-card.featured .featured-badge {
            animation: pulse-featured 2s ease-in-out infinite;
        }
        
        /* Mobile Touch Improvements */
        @media (max-width: 768px) {
            .business-card,
            .success-story-item,
            .featured-business-item {
                -webkit-tap-highlight-color: rgba(34, 113, 177, 0.1);
            }
        }
    ";
    
    wp_add_inline_style( 'business-showcase-style', $custom_css );
}
add_action( 'wp_enqueue_scripts', 'business_showcase_inline_styles', 20 );

/**
 * Enqueue Admin CSS and JS Files
 */
function business_showcase_enqueue_admin_scripts( $hook ) {
    // Enqueue admin CSS
    wp_enqueue_style(
        'business-showcase-admin-style',
        BUSINESS_SHOWCASE_PLUGIN_URL . 'assets/css/admin-style.css',
        array(),
        BUSINESS_SHOWCASE_VERSION,
        'all'
    );
    
    // Enqueue admin JavaScript
    wp_enqueue_script(
        'business-showcase-admin-script',
        BUSINESS_SHOWCASE_PLUGIN_URL . 'assets/js/admin-script.js',
        array( 'jquery' ),
        BUSINESS_SHOWCASE_VERSION,
        true
    );
}
add_action( 'admin_enqueue_scripts', 'business_showcase_enqueue_admin_scripts' );

/**
 * Plugin initialization
 */
function business_showcase_init() {
    // Register custom post types and taxonomies
    business_showcase_register_post_type();
    business_showcase_register_taxonomy();
}
add_action( 'init', 'business_showcase_init' );

/**
 * Register Custom Post Type: Business Profile
 */
function business_showcase_register_post_type() {
    $labels = array(
        'name'                  => _x( 'Business Profiles', 'Post Type General Name', 'business-showcase-networking-hub' ),
        'singular_name'         => _x( 'Business Profile', 'Post Type Singular Name', 'business-showcase-networking-hub' ),
        'menu_name'             => __( 'Business Profiles', 'business-showcase-networking-hub' ),
        'name_admin_bar'        => __( 'Business Profile', 'business-showcase-networking-hub' ),
        'archives'              => __( 'Business Profile Archives', 'business-showcase-networking-hub' ),
        'attributes'            => __( 'Business Profile Attributes', 'business-showcase-networking-hub' ),
        'parent_item_colon'     => __( 'Parent Business Profile:', 'business-showcase-networking-hub' ),
        'all_items'             => __( 'All Business Profiles', 'business-showcase-networking-hub' ),
        'add_new_item'          => __( 'Add New Business Profile', 'business-showcase-networking-hub' ),
        'add_new'               => __( 'Add New', 'business-showcase-networking-hub' ),
        'new_item'              => __( 'New Business Profile', 'business-showcase-networking-hub' ),
        'edit_item'             => __( 'Edit Business Profile', 'business-showcase-networking-hub' ),
        'update_item'           => __( 'Update Business Profile', 'business-showcase-networking-hub' ),
        'view_item'             => __( 'View Business Profile', 'business-showcase-networking-hub' ),
        'view_items'            => __( 'View Business Profiles', 'business-showcase-networking-hub' ),
        'search_items'          => __( 'Search Business Profile', 'business-showcase-networking-hub' ),
        'not_found'             => __( 'Not found', 'business-showcase-networking-hub' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 'business-showcase-networking-hub' ),
        'featured_image'        => __( 'Business Logo', 'business-showcase-networking-hub' ),
        'set_featured_image'    => __( 'Set business logo', 'business-showcase-networking-hub' ),
        'remove_featured_image' => __( 'Remove business logo', 'business-showcase-networking-hub' ),
        'use_featured_image'    => __( 'Use as business logo', 'business-showcase-networking-hub' ),
        'insert_into_item'      => __( 'Insert into business profile', 'business-showcase-networking-hub' ),
        'uploaded_to_this_item' => __( 'Uploaded to this business profile', 'business-showcase-networking-hub' ),
        'items_list'            => __( 'Business profiles list', 'business-showcase-networking-hub' ),
        'items_list_navigation' => __( 'Business profiles list navigation', 'business-showcase-networking-hub' ),
        'filter_items_list'     => __( 'Filter business profiles list', 'business-showcase-networking-hub' ),
    );
    
    $args = array(
        'label'                 => __( 'Business Profile', 'business-showcase-networking-hub' ),
        'description'           => __( 'Business profiles for showcase and networking', 'business-showcase-networking-hub' ),
        'labels'                => $labels,
        'supports'              => array( 'title', 'editor', 'thumbnail', 'custom-fields', 'excerpt', 'author', 'revisions' ),
        'taxonomies'            => array( 'business_category' ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-building',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        'show_in_rest'          => true,
        'rewrite'               => array( 'slug' => 'business-profile' ),
    );
    
    register_post_type( 'business_profile', $args );
}

/**
 * Register Custom Taxonomy: Business Category
 */
function business_showcase_register_taxonomy() {
    $labels = array(
        'name'                       => _x( 'Business Categories', 'Taxonomy General Name', 'business-showcase-networking-hub' ),
        'singular_name'              => _x( 'Business Category', 'Taxonomy Singular Name', 'business-showcase-networking-hub' ),
        'menu_name'                  => __( 'Business Categories', 'business-showcase-networking-hub' ),
        'all_items'                  => __( 'All Categories', 'business-showcase-networking-hub' ),
        'parent_item'                => __( 'Parent Category', 'business-showcase-networking-hub' ),
        'parent_item_colon'          => __( 'Parent Category:', 'business-showcase-networking-hub' ),
        'new_item_name'              => __( 'New Category Name', 'business-showcase-networking-hub' ),
        'add_new_item'               => __( 'Add New Category', 'business-showcase-networking-hub' ),
        'edit_item'                  => __( 'Edit Category', 'business-showcase-networking-hub' ),
        'update_item'                => __( 'Update Category', 'business-showcase-networking-hub' ),
        'view_item'                  => __( 'View Category', 'business-showcase-networking-hub' ),
        'separate_items_with_commas' => __( 'Separate categories with commas', 'business-showcase-networking-hub' ),
        'add_or_remove_items'        => __( 'Add or remove categories', 'business-showcase-networking-hub' ),
        'choose_from_most_used'      => __( 'Choose from the most used', 'business-showcase-networking-hub' ),
        'popular_items'              => __( 'Popular Categories', 'business-showcase-networking-hub' ),
        'search_items'               => __( 'Search Categories', 'business-showcase-networking-hub' ),
        'not_found'                  => __( 'Not Found', 'business-showcase-networking-hub' ),
        'no_terms'                   => __( 'No categories', 'business-showcase-networking-hub' ),
        'items_list'                 => __( 'Categories list', 'business-showcase-networking-hub' ),
        'items_list_navigation'      => __( 'Categories list navigation', 'business-showcase-networking-hub' ),
    );
    
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
        'show_in_rest'               => true,
        'rewrite'                    => array( 'slug' => 'business-category' ),
    );
    
    register_taxonomy( 'business_category', array( 'business_profile' ), $args );
}

/**
 * Add Meta Boxes for Business Profile
 */
function business_showcase_add_meta_boxes() {
    add_meta_box(
        'business_profile_details',
        __( 'Business Profile Details', 'business-showcase-networking-hub' ),
        'business_showcase_render_meta_box',
        'business_profile',
        'normal',
        'high'
    );
    
    add_meta_box(
        'business_profile_featured',
        __( 'Featured Business', 'business-showcase-networking-hub' ),
        'business_showcase_render_featured_meta_box',
        'business_profile',
        'side',
        'high'
    );
}
add_action( 'add_meta_boxes', 'business_showcase_add_meta_boxes' );

/**
 * Render Meta Box Content
 */
function business_showcase_render_meta_box( $post ) {
    // Add nonce for security
    wp_nonce_field( 'business_showcase_meta_box', 'business_showcase_meta_box_nonce' );
    
    // Get existing values
    $website_url = get_post_meta( $post->ID, '_business_website_url', true );
    $contact_email = get_post_meta( $post->ID, '_business_contact_email', true );
    $facebook_url = get_post_meta( $post->ID, '_business_facebook_url', true );
    $twitter_url = get_post_meta( $post->ID, '_business_twitter_url', true );
    $linkedin_url = get_post_meta( $post->ID, '_business_linkedin_url', true );
    $services = get_post_meta( $post->ID, '_business_services', true );
    
    // Services options
    $services_options = array(
        'consulting' => __( 'Consulting', 'business-showcase-networking-hub' ),
        'design' => __( 'Design', 'business-showcase-networking-hub' ),
        'development' => __( 'Development', 'business-showcase-networking-hub' ),
        'marketing' => __( 'Marketing', 'business-showcase-networking-hub' ),
        'sales' => __( 'Sales', 'business-showcase-networking-hub' ),
        'support' => __( 'Support', 'business-showcase-networking-hub' ),
        'training' => __( 'Training', 'business-showcase-networking-hub' ),
        'other' => __( 'Other', 'business-showcase-networking-hub' ),
    );
    
    if ( ! is_array( $services ) ) {
        $services = array();
    }
    ?>
    
    <div class="business-showcase-meta-box">
        
        <!-- Services -->
        <div class="business-meta-field">
            <label class="business-meta-label">
                <strong><?php esc_html_e( 'Services Offered', 'business-showcase-networking-hub' ); ?></strong>
            </label>
            <div class="business-meta-checkboxes">
                <?php foreach ( $services_options as $value => $label ) : ?>
                    <label style="display: block; margin: 5px 0;">
                        <input type="checkbox" 
                               name="business_services[]" 
                               value="<?php echo esc_attr( $value ); ?>"
                               <?php checked( in_array( $value, $services ), true ); ?> />
                        <?php echo esc_html( $label ); ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
        
        <hr style="margin: 20px 0;" />
        
        <!-- Website URL -->
        <div class="business-meta-field">
            <label class="business-meta-label" for="business_website_url">
                <strong><?php esc_html_e( 'Website URL', 'business-showcase-networking-hub' ); ?></strong>
            </label>
            <input type="url" 
                   id="business_website_url" 
                   name="business_website_url" 
                   value="<?php echo esc_attr( $website_url ); ?>" 
                   style="width: 100%; max-width: 500px;" 
                   placeholder="https://example.com" />
            <p class="description"><?php esc_html_e( 'Enter the business website URL', 'business-showcase-networking-hub' ); ?></p>
        </div>
        
        <!-- Contact Email -->
        <div class="business-meta-field">
            <label class="business-meta-label" for="business_contact_email">
                <strong><?php esc_html_e( 'Contact Email', 'business-showcase-networking-hub' ); ?></strong>
            </label>
            <input type="email" 
                   id="business_contact_email" 
                   name="business_contact_email" 
                   value="<?php echo esc_attr( $contact_email ); ?>" 
                   style="width: 100%; max-width: 500px;" 
                   placeholder="contact@example.com" />
            <p class="description"><?php esc_html_e( 'Enter the business contact email', 'business-showcase-networking-hub' ); ?></p>
        </div>
        
        <hr style="margin: 20px 0;" />
        
        <h3><?php esc_html_e( 'Social Media Links', 'business-showcase-networking-hub' ); ?></h3>
        
        <!-- Facebook URL -->
        <div class="business-meta-field">
            <label class="business-meta-label" for="business_facebook_url">
                <strong><?php esc_html_e( 'Facebook URL', 'business-showcase-networking-hub' ); ?></strong>
            </label>
            <input type="url" 
                   id="business_facebook_url" 
                   name="business_facebook_url" 
                   value="<?php echo esc_attr( $facebook_url ); ?>" 
                   style="width: 100%; max-width: 500px;" 
                   placeholder="https://facebook.com/yourpage" />
        </div>
        
        <!-- Twitter URL -->
        <div class="business-meta-field">
            <label class="business-meta-label" for="business_twitter_url">
                <strong><?php esc_html_e( 'Twitter URL', 'business-showcase-networking-hub' ); ?></strong>
            </label>
            <input type="url" 
                   id="business_twitter_url" 
                   name="business_twitter_url" 
                   value="<?php echo esc_attr( $twitter_url ); ?>" 
                   style="width: 100%; max-width: 500px;" 
                   placeholder="https://twitter.com/yourhandle" />
        </div>
        
        <!-- LinkedIn URL -->
        <div class="business-meta-field">
            <label class="business-meta-label" for="business_linkedin_url">
                <strong><?php esc_html_e( 'LinkedIn URL', 'business-showcase-networking-hub' ); ?></strong>
            </label>
            <input type="url" 
                   id="business_linkedin_url" 
                   name="business_linkedin_url" 
                   value="<?php echo esc_attr( $linkedin_url ); ?>" 
                   style="width: 100%; max-width: 500px;" 
                   placeholder="https://linkedin.com/in/yourprofile" />
        </div>
        
    </div>
    
    <style>
        .business-showcase-meta-box {
            padding: 10px 0;
        }
        .business-meta-field {
            margin-bottom: 20px;
        }
        .business-meta-label {
            display: block;
            margin-bottom: 8px;
        }
        .business-meta-checkboxes {
            margin-top: 8px;
        }
    </style>
    
    <?php
}

/**
 * Render Featured Business Meta Box
 */
function business_showcase_render_featured_meta_box( $post ) {
    // Add nonce for security
    wp_nonce_field( 'business_showcase_featured_meta_box', 'business_showcase_featured_meta_box_nonce' );
    
    // Get existing value
    $is_featured = get_post_meta( $post->ID, '_business_is_featured', true );
    ?>
    
    <div class="business-featured-meta-box">
        <label style="display: block; margin: 10px 0;">
            <input type="checkbox" 
                   name="business_is_featured" 
                   value="1"
                   <?php checked( $is_featured, '1' ); ?> />
            <?php esc_html_e( 'Mark this business as featured', 'business-showcase-networking-hub' ); ?>
        </label>
        <p class="description">
            <?php esc_html_e( 'Featured businesses will be displayed prominently on the showcase page.', 'business-showcase-networking-hub' ); ?>
        </p>
    </div>
    
    <?php
}

/**
 * Save Meta Box Data
 */
function business_showcase_save_meta_box( $post_id ) {
    // Check if nonce is set
    if ( ! isset( $_POST['business_showcase_meta_box_nonce'] ) ) {
        return;
    }
    
    // Verify nonce
    if ( ! wp_verify_nonce( $_POST['business_showcase_meta_box_nonce'], 'business_showcase_meta_box' ) ) {
        return;
    }
    
    // Check if this is an autosave
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    
    // Check user permissions
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }
    
    // Sanitize and save Services (checkbox group)
    if ( isset( $_POST['business_services'] ) && is_array( $_POST['business_services'] ) ) {
        $services = array_map( 'sanitize_text_field', $_POST['business_services'] );
        update_post_meta( $post_id, '_business_services', $services );
    } else {
        delete_post_meta( $post_id, '_business_services' );
    }
    
    // Sanitize and save Website URL
    if ( isset( $_POST['business_website_url'] ) ) {
    
    // Save Featured Business checkbox
    if ( isset( $_POST['business_showcase_featured_meta_box_nonce'] ) && 
         wp_verify_nonce( $_POST['business_showcase_featured_meta_box_nonce'], 'business_showcase_featured_meta_box' ) ) {
        
        if ( isset( $_POST['business_is_featured'] ) ) {
            update_post_meta( $post_id, '_business_is_featured', '1' );
        } else {
            update_post_meta( $post_id, '_business_is_featured', '0' );
        }
    }
        $website_url = esc_url_raw( $_POST['business_website_url'] );
        update_post_meta( $post_id, '_business_website_url', $website_url );
    }
    
    // Sanitize and save Contact Email
    if ( isset( $_POST['business_contact_email'] ) ) {
        $contact_email = sanitize_email( $_POST['business_contact_email'] );
        update_post_meta( $post_id, '_business_contact_email', $contact_email );
    }
    
    // Sanitize and save Facebook URL
    if ( isset( $_POST['business_facebook_url'] ) ) {
        $facebook_url = esc_url_raw( $_POST['business_facebook_url'] );
        update_post_meta( $post_id, '_business_facebook_url', $facebook_url );
    }
    
    // Sanitize and save Twitter URL
    if ( isset( $_POST['business_twitter_url'] ) ) {
        $twitter_url = esc_url_raw( $_POST['business_twitter_url'] );
        update_post_meta( $post_id, '_business_twitter_url', $twitter_url );
    }
    
    // Sanitize and save LinkedIn URL
    if ( isset( $_POST['business_linkedin_url'] ) ) {
        $linkedin_url = esc_url_raw( $_POST['business_linkedin_url'] );
        update_post_meta( $post_id, '_business_linkedin_url', $linkedin_url );
    }
}
add_action( 'save_post_business_profile', 'business_showcase_save_meta_box' );

/**
 * Register Shortcode: [business_directory]
 */
function business_showcase_directory_shortcode( $atts ) {
    // Shortcode attributes
    $atts = shortcode_atts( array(
        'posts_per_page' => 12,
        'category' => '',
        'featured_only' => false,
    ), $atts );
    
    // Start output buffering
    ob_start();
    
    // Get all business categories
    $categories = get_terms( array(
        'taxonomy' => 'business_category',
        'hide_empty' => true,
    ) );
    
    // Get all unique services
    $all_services = business_showcase_get_all_services();
    
    ?>
    <div class="business-showcase-directory" id="business-directory">
        
        <!-- Filters -->
        <div class="business-directory-filters">
            <div class="filter-group">
                <label for="category-filter">
                    <?php esc_html_e( 'Filter by Category:', 'business-showcase-networking-hub' ); ?>
                </label>
                <select id="category-filter" class="business-filter">
                    <option value=""><?php esc_html_e( 'All Categories', 'business-showcase-networking-hub' ); ?></option>
                    <?php if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) : ?>
                        <?php foreach ( $categories as $category ) : ?>
                            <option value="<?php echo esc_attr( $category->slug ); ?>">
                                <?php echo esc_html( $category->name ); ?> (<?php echo intval( $category->count ); ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="service-filter">
                    <?php esc_html_e( 'Filter by Service:', 'business-showcase-networking-hub' ); ?>
                </label>
                <select id="service-filter" class="business-filter">
                    <option value=""><?php esc_html_e( 'All Services', 'business-showcase-networking-hub' ); ?></option>
                    <?php foreach ( $all_services as $service_key => $service_label ) : ?>
                        <option value="<?php echo esc_attr( $service_key ); ?>">
                            <?php echo esc_html( $service_label ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="filter-group">
                <button type="button" id="reset-filters" class="business-reset-btn">
                    <?php esc_html_e( 'Reset Filters', 'business-showcase-networking-hub' ); ?>
                </button>
            </div>
        </div>
        
        <!-- Loading indicator -->
        <div id="business-loading" class="business-loading" style="display: none;">
            <p><?php esc_html_e( 'Loading...', 'business-showcase-networking-hub' ); ?></p>
        </div>
        
        <!-- Business Grid -->
        <div id="business-grid" class="business-grid">
            <?php echo wp_kses_post( business_showcase_get_business_grid( $atts ) ); ?>
        </div>
        
    </div>
    <?php
    
    return ob_get_clean();
}
add_shortcode( 'business_directory', 'business_showcase_directory_shortcode' );

/**
 * Register Shortcode: [featured_businesses]
 */
function business_showcase_featured_shortcode( $atts ) {
    // Shortcode attributes
    $atts = shortcode_atts( array(
        'posts_per_page' => 6,
        'columns' => 3,
    ), $atts );
    
    // Query arguments for featured businesses
    $query_args = array(
        'post_type' => 'business_profile',
        'posts_per_page' => intval( $atts['posts_per_page'] ),
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC',
        'meta_query' => array(
            array(
                'key' => '_business_is_featured',
                'value' => '1',
            ),
        ),
    );
    
    $query = new WP_Query( $query_args );
    
    ob_start();
    
    if ( $query->have_posts() ) :
        $columns = intval( $atts['columns'] );
        $columns_class = 'columns-' . $columns;
        ?>
        
        <div class="featured-businesses-container">
            <div class="featured-businesses-grid <?php echo esc_attr( $columns_class ); ?>">
                
                <?php while ( $query->have_posts() ) : $query->the_post();
                    $post_id = get_the_ID();
                    $star_rating = get_post_meta( $post_id, '_business_star_rating', true );
                    $categories = get_the_terms( $post_id, 'business_category' );
                ?>
                
                <div class="featured-business-item">
                    
                    <div class="featured-business-inner">
                        
                        <?php if ( has_post_thumbnail() ) : ?>
                            <div class="featured-business-logo">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail( 'medium', array( 'alt' => esc_attr( get_the_title() ) ) ); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <div class="featured-business-content">
                            
                            <h3 class="featured-business-title">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_title(); ?>
                                </a>
                            </h3>
                            
                            <?php if ( $categories && ! is_wp_error( $categories ) ) : ?>
                                <div class="featured-business-category">
                                    <?php 
                                    $category_names = array();
                                    foreach ( $categories as $category ) {
                                        $category_names[] = $category->name;
                                    }
                                    echo esc_html( implode( ', ', $category_names ) );
                                    ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ( ! empty( $star_rating ) && $star_rating > 0 ) : ?>
                                <div class="featured-business-rating">
                                    <?php echo business_showcase_display_star_rating( floatval( $star_rating ) ); ?>
                                    <span class="rating-number"><?php echo esc_html( number_format( floatval( $star_rating ), 1 ) ); ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <a href="<?php the_permalink(); ?>" class="featured-business-link">
                                <?php esc_html_e( 'View Profile', 'business-showcase-networking-hub' ); ?>
                                <span class="arrow">→</span>
                            </a>
                            
                        </div>
                        
                    </div>
                    
                </div>
                
                <?php endwhile; ?>
                
            </div>
        </div>
        
        <?php
        wp_reset_postdata();
    else : ?>
        <p class="no-featured-businesses">
            <?php esc_html_e( 'No featured businesses found.', 'business-showcase-networking-hub' ); ?>
        </p>
    <?php endif;
    
    return ob_get_clean();
}
add_shortcode( 'featured_businesses', 'business_showcase_featured_shortcode' );

/**
 * Get Business Grid HTML
 */
function business_showcase_get_business_grid( $args = array() ) {
    $defaults = array(
        'posts_per_page' => 12,
        'category' => '',
        'service' => '',
        'featured_only' => false,
    );
    
    $args = wp_parse_args( $args, $defaults );
    
    // Query arguments
    $query_args = array(
        'post_type' => 'business_profile',
        'posts_per_page' => intval( $args['posts_per_page'] ),
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC',
    );
    
    // Filter by category
    if ( ! empty( $args['category'] ) ) {
        $query_args['tax_query'] = array(
            array(
                'taxonomy' => 'business_category',
                'field' => 'slug',
                'terms' => sanitize_text_field( $args['category'] ),
            ),
        );
    }
    
    // Filter by featured
    if ( $args['featured_only'] ) {
        $query_args['meta_query'] = array(
            array(
                'key' => '_business_is_featured',
                'value' => '1',
            ),
        );
    }
    
    // Filter by service (meta query)
    if ( ! empty( $args['service'] ) ) {
        if ( ! isset( $query_args['meta_query'] ) ) {
            $query_args['meta_query'] = array();
        }
        $query_args['meta_query'][] = array(
            'key' => '_business_services',
            'value' => serialize( sanitize_text_field( $args['service'] ) ),
            'compare' => 'LIKE',
        );
    }
    
    $query = new WP_Query( $query_args );
    
    ob_start();
    
    if ( $query->have_posts() ) :
        while ( $query->have_posts() ) : $query->the_post();
            $post_id = get_the_ID();
            $website_url = get_post_meta( $post_id, '_business_website_url', true );
            $contact_email = get_post_meta( $post_id, '_business_contact_email', true );
            $services = get_post_meta( $post_id, '_business_services', true );
            $is_featured = get_post_meta( $post_id, '_business_is_featured', true );
            $categories = get_the_terms( $post_id, 'business_category' );
            ?>
            
            <div class="business-card <?php echo ( $is_featured == '1' ) ? 'featured' : ''; ?>">
                
                <?php if ( $is_featured == '1' ) : ?>
                    <span class="featured-badge"><?php esc_html_e( 'Featured', 'business-showcase-networking-hub' ); ?></span>
                <?php endif; ?>
                
                <?php if ( has_post_thumbnail() ) : ?>
                    <div class="business-logo">
                        <a href="<?php the_permalink(); ?>">
                            <?php the_post_thumbnail( 'medium' ); ?>
                        </a>
                    </div>
                <?php endif; ?>
                
                <div class="business-content">
                    <h3 class="business-title">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h3>
                    
                    <?php if ( $categories && ! is_wp_error( $categories ) ) : ?>
                        <div class="business-categories">
                            <?php foreach ( $categories as $category ) : ?>
                                <span class="business-category-tag"><?php echo esc_html( $category->name ); ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="business-excerpt">
                        <?php echo wp_kses_post( wp_trim_words( get_the_excerpt(), 20 ) ); ?>
                    </div>
                    
                    <?php if ( ! empty( $services ) && is_array( $services ) ) : ?>
                        <div class="business-services">
                            <strong><?php esc_html_e( 'Services:', 'business-showcase-networking-hub' ); ?></strong>
                            <?php 
                            $service_labels = business_showcase_get_service_labels();
                            $service_names = array();
                            foreach ( $services as $service ) {
                                if ( isset( $service_labels[ $service ] ) ) {
                                    $service_names[] = $service_labels[ $service ];
                                }
                            }
                            echo esc_html( implode( ', ', $service_names ) );
                            ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="business-actions">
                        <?php if ( $website_url ) : ?>
                            <a href="<?php echo esc_url( $website_url ); ?>" target="_blank" class="business-btn">
                                <?php esc_html_e( 'Visit Website', 'business-showcase-networking-hub' ); ?>
                            </a>
                        <?php endif; ?>
                        <a href="<?php the_permalink(); ?>" class="business-btn business-btn-secondary">
                            <?php esc_html_e( 'View Details', 'business-showcase-networking-hub' ); ?>
                        </a>
                    </div>
                </div>
                
            </div>
            
        <?php endwhile;
        wp_reset_postdata();
    else : ?>
        <p class="no-businesses-found"><?php esc_html_e( 'No businesses found.', 'business-showcase-networking-hub' ); ?></p>
    <?php endif;
    
    return ob_get_clean();
}

/**
 * Get all available services
 */
function business_showcase_get_all_services() {
    return array(
        'consulting' => __( 'Consulting', 'business-showcase-networking-hub' ),
        'design' => __( 'Design', 'business-showcase-networking-hub' ),
        'development' => __( 'Development', 'business-showcase-networking-hub' ),
        'marketing' => __( 'Marketing', 'business-showcase-networking-hub' ),
        'sales' => __( 'Sales', 'business-showcase-networking-hub' ),
        'support' => __( 'Support', 'business-showcase-networking-hub' ),
        'training' => __( 'Training', 'business-showcase-networking-hub' ),
        'other' => __( 'Other', 'business-showcase-networking-hub' ),
    );
}

/**
 * Get service labels (helper function)
 */
function business_showcase_get_service_labels() {
    return business_showcase_get_all_services();
}

/**
 * AJAX Handler for filtering businesses
 */
function business_showcase_filter_businesses() {
    check_ajax_referer( 'business_showcase_nonce', 'nonce' );
    
    $category = isset( $_POST['category'] ) ? sanitize_text_field( $_POST['category'] ) : '';
    $service = isset( $_POST['service'] ) ? sanitize_text_field( $_POST['service'] ) : '';
    
    $args = array(
        'posts_per_page' => 12,
        'category' => $category,
        'service' => $service,
    );
    
    $html = business_showcase_get_business_grid( $args );
    
    // Ensure HTML is properly escaped before sending
    wp_send_json_success( array( 'html' => wp_kses_post( $html ) ) );
}
add_action( 'wp_ajax_business_showcase_filter', 'business_showcase_filter_businesses' );
add_action( 'wp_ajax_nopriv_business_showcase_filter', 'business_showcase_filter_businesses' );

/**
 * Handle Business Contact Form Submission via AJAX
 */
function business_showcase_handle_contact_form() {
    // Verify nonce
    if ( ! isset( $_POST['business_contact_nonce'] ) || 
         ! wp_verify_nonce( $_POST['business_contact_nonce'], 'business_contact_form_nonce' ) ) {
        wp_send_json_error( array(
            'message' => __( 'Security verification failed. Please refresh the page and try again.', 'business-showcase-networking-hub' )
        ) );
    }
    
    // Get and validate post ID
    $post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
    
    if ( ! $post_id || get_post_type( $post_id ) !== 'business_profile' ) {
        wp_send_json_error( array(
            'message' => __( 'Invalid business profile.', 'business-showcase-networking-hub' )
        ) );
    }
    
    // Get business contact email
    $business_email = get_post_meta( $post_id, '_business_contact_email', true );
    
    if ( ! $business_email || ! is_email( $business_email ) ) {
        wp_send_json_error( array(
            'message' => __( 'Business contact email is not available.', 'business-showcase-networking-hub' )
        ) );
    }
    
    // Sanitize and validate form inputs
    $contact_name = isset( $_POST['contact_name'] ) ? sanitize_text_field( $_POST['contact_name'] ) : '';
    $contact_email = isset( $_POST['contact_email'] ) ? sanitize_email( $_POST['contact_email'] ) : '';
    $contact_subject = isset( $_POST['contact_subject'] ) ? sanitize_text_field( $_POST['contact_subject'] ) : '';
    $contact_message = isset( $_POST['contact_message'] ) ? sanitize_textarea_field( $_POST['contact_message'] ) : '';
    
    // Validate required fields
    if ( empty( $contact_name ) ) {
        wp_send_json_error( array(
            'message' => __( 'Please enter your name.', 'business-showcase-networking-hub' )
        ) );
    }
    
    if ( empty( $contact_email ) || ! is_email( $contact_email ) ) {
        wp_send_json_error( array(
            'message' => __( 'Please enter a valid email address.', 'business-showcase-networking-hub' )
        ) );
    }
    
    if ( empty( $contact_subject ) ) {
        wp_send_json_error( array(
            'message' => __( 'Please enter a subject.', 'business-showcase-networking-hub' )
        ) );
    }
    
    if ( empty( $contact_message ) ) {
        wp_send_json_error( array(
            'message' => __( 'Please enter your message.', 'business-showcase-networking-hub' )
        ) );
    }
    
    // Get business name
    $business_name = get_the_title( $post_id );
    $business_url = get_permalink( $post_id );
    
    // Prepare email
    $email_subject = sprintf(
        __( 'New inquiry from %s - %s', 'business-showcase-networking-hub' ),
        $contact_name,
        $contact_subject
    );
    
    // Email headers
    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        sprintf( 'From: %s <%s>', $contact_name, $contact_email ),
        sprintf( 'Reply-To: %s <%s>', $contact_name, $contact_email )
    );
    
    // Email body
    $email_body = sprintf(
        '<html><body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
        <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
            <h2 style="color: #2271b1; border-bottom: 2px solid #2271b1; padding-bottom: 10px;">%s</h2>
            
            <div style="background: #f9f9f9; padding: 20px; border-radius: 6px; margin: 20px 0;">
                <p style="margin: 0 0 10px 0;"><strong>%s</strong> %s</p>
                <p style="margin: 0 0 10px 0;"><strong>%s</strong> <a href="mailto:%s">%s</a></p>
                <p style="margin: 0 0 10px 0;"><strong>%s</strong> %s</p>
                <p style="margin: 0;"><strong>%s</strong> %s</p>
            </div>
            
            <div style="background: #fff; padding: 20px; border-left: 4px solid #2271b1; margin: 20px 0;">
                <h3 style="margin-top: 0; color: #333;">%s</h3>
                <p style="white-space: pre-line;">%s</p>
            </div>
            
            <div style="background: #e3f2fd; padding: 15px; border-radius: 6px; margin-top: 20px;">
                <p style="margin: 0; font-size: 14px; color: #666;">
                    %s <a href="%s" style="color: #2271b1; text-decoration: none;">%s</a>
                </p>
                <p style="margin: 10px 0 0 0; font-size: 12px; color: #999;">
                    %s
                </p>
            </div>
        </div>
        </body></html>',
        esc_html__( 'New Contact Form Submission', 'business-showcase-networking-hub' ),
        esc_html__( 'Name:', 'business-showcase-networking-hub' ),
        esc_html( $contact_name ),
        esc_html__( 'Email:', 'business-showcase-networking-hub' ),
        esc_attr( $contact_email ),
        esc_html( $contact_email ),
        esc_html__( 'Subject:', 'business-showcase-networking-hub' ),
        esc_html( $contact_subject ),
        esc_html__( 'Business Profile:', 'business-showcase-networking-hub' ),
        esc_html( $business_name ),
        esc_html__( 'Message:', 'business-showcase-networking-hub' ),
        esc_html( $contact_message ),
        esc_html__( 'This message was sent from your business profile:', 'business-showcase-networking-hub' ),
        esc_url( $business_url ),
        esc_html( $business_name ),
        esc_html__( 'Sent via Business Showcase & Networking Hub plugin', 'business-showcase-networking-hub' )
    );
    
    // Send email
    $email_sent = wp_mail( $business_email, $email_subject, $email_body, $headers );
    
    // Send copy to site admin (optional)
    $admin_email = get_option( 'admin_email' );
    if ( $admin_email && $admin_email !== $business_email ) {
        $admin_subject = sprintf(
            __( '[Copy] Contact form submission for %s', 'business-showcase-networking-hub' ),
            $business_name
        );
        wp_mail( $admin_email, $admin_subject, $email_body, $headers );
    }
    
    if ( $email_sent ) {
        // Log the contact attempt (optional - can be used for analytics)
        $contact_count = get_post_meta( $post_id, '_business_contact_count', true );
        $contact_count = $contact_count ? intval( $contact_count ) + 1 : 1;
        update_post_meta( $post_id, '_business_contact_count', $contact_count );
        
        wp_send_json_success( array(
            'message' => __( 'Your message has been sent successfully! The business will respond to you shortly.', 'business-showcase-networking-hub' )
        ) );
    } else {
        wp_send_json_error( array(
            'message' => __( 'Failed to send message. Please try again later or contact the business directly.', 'business-showcase-networking-hub' )
        ) );
    }
}
add_action( 'wp_ajax_business_showcase_contact', 'business_showcase_handle_contact_form' );
add_action( 'wp_ajax_nopriv_business_showcase_contact', 'business_showcase_handle_contact_form' );

/**
 * Load Custom Template for Single Business Profile
 */
function business_showcase_load_template( $template ) {
    if ( is_singular( 'business_profile' ) ) {
        $plugin_template = BUSINESS_SHOWCASE_PLUGIN_DIR . 'templates/single-business-profile.php';
        
        if ( file_exists( $plugin_template ) ) {
            return $plugin_template;
        }
    }
    
    return $template;
}
add_filter( 'template_include', 'business_showcase_load_template' );

/**
 * Add Star Rating Meta Box
 */
function business_showcase_add_rating_meta_box() {
    add_meta_box(
        'business_profile_rating',
        __( 'Business Rating', 'business-showcase-networking-hub' ),
        'business_showcase_render_rating_meta_box',
        'business_profile',
        'side',
        'default'
    );
}
add_action( 'add_meta_boxes', 'business_showcase_add_rating_meta_box' );

/**
 * Render Star Rating Meta Box
 */
function business_showcase_render_rating_meta_box( $post ) {
    wp_nonce_field( 'business_showcase_rating_meta_box', 'business_showcase_rating_meta_box_nonce' );
    
    $star_rating = get_post_meta( $post->ID, '_business_star_rating', true );
    ?>
    
    <div class="business-rating-meta-box">
        <label for="business_star_rating">
            <strong><?php esc_html_e( 'Star Rating (0-5)', 'business-showcase-networking-hub' ); ?></strong>
        </label>
        <input type="number" 
               id="business_star_rating" 
               name="business_star_rating" 
               value="<?php echo esc_attr( $star_rating ); ?>" 
               min="0" 
               max="5" 
               step="0.5"
               style="width: 100%;" />
        <p class="description">
            <?php esc_html_e( 'Enter a rating between 0 and 5 (e.g., 4.5)', 'business-showcase-networking-hub' ); ?>
        </p>
    </div>
    
    <?php
}

/**
 * Save Star Rating
 */
function business_showcase_save_rating( $post_id ) {
    if ( ! isset( $_POST['business_showcase_rating_meta_box_nonce'] ) ) {
        return;
    }
    
    if ( ! wp_verify_nonce( $_POST['business_showcase_rating_meta_box_nonce'], 'business_showcase_rating_meta_box' ) ) {
        return;
    }
    
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }
    
    if ( isset( $_POST['business_star_rating'] ) ) {
        $rating = floatval( $_POST['business_star_rating'] );
        
        // Validate rating is between 0 and 5
        if ( $rating < 0 ) {
            $rating = 0;
        } elseif ( $rating > 5 ) {
            $rating = 5;
        }
        
        update_post_meta( $post_id, '_business_star_rating', $rating );
    }
}
add_action( 'save_post_business_profile', 'business_showcase_save_rating' );

/**
 * Display Star Rating HTML
 */
function business_showcase_display_star_rating( $rating ) {
    $output = '<div class="star-rating-display">';
    
    $full_stars = floor( $rating );
    $half_star = ( $rating - $full_stars ) >= 0.5 ? 1 : 0;
    $empty_stars = 5 - $full_stars - $half_star;
    
    // Full stars
    for ( $i = 0; $i < $full_stars; $i++ ) {
        $output .= '<span class="star filled">★</span>';
    }
    
    // Half star
    if ( $half_star ) {
        $output .= '<span class="star half">★</span>';
    }
    
    // Empty stars
    for ( $i = 0; $i < $empty_stars; $i++ ) {
        $output .= '<span class="star">★</span>';
    }
    
    $output .= '</div>';
    
    return $output;
}

/**
 * Add Rating Field to Comment Form for Business Profile
 */
function business_showcase_add_rating_field( $comment_id ) {
    if ( ! is_singular( 'business_profile' ) ) {
        return;
    }
    ?>
    <div class="comment-form-rating">
        <label for="rating">
            <?php esc_html_e( 'Your Rating', 'business-showcase-networking-hub' ); ?> 
            <span class="required">*</span>
        </label>
        <div class="star-rating-input" id="star-rating-input">
            <input type="radio" name="rating" id="rating-5" value="5" required />
            <label for="rating-5" class="star" title="5 stars">★</label>
            
            <input type="radio" name="rating" id="rating-4" value="4" required />
            <label for="rating-4" class="star" title="4 stars">★</label>
            
            <input type="radio" name="rating" id="rating-3" value="3" required />
            <label for="rating-3" class="star" title="3 stars">★</label>
            
            <input type="radio" name="rating" id="rating-2" value="2" required />
            <label for="rating-2" class="star" title="2 stars">★</label>
            
            <input type="radio" name="rating" id="rating-1" value="1" required />
            <label for="rating-1" class="star" title="1 star">★</label>
        </div>
        <p class="description">
            <?php esc_html_e( 'Click on the stars to rate this business', 'business-showcase-networking-hub' ); ?>
        </p>
    </div>
    <?php
}
add_action( 'comment_form_logged_in_after', 'business_showcase_add_rating_field' );
add_action( 'comment_form_after_fields', 'business_showcase_add_rating_field' );

/**
 * Validate Rating Field
 */
function business_showcase_validate_rating( $commentdata ) {
    if ( get_post_type( $commentdata['comment_post_ID'] ) !== 'business_profile' ) {
        return $commentdata;
    }
    
    if ( ! isset( $_POST['rating'] ) || empty( $_POST['rating'] ) ) {
        wp_die( 
            esc_html__( 'Error: You must select a rating to submit a review.', 'business-showcase-networking-hub' ),
            esc_html__( 'Rating Required', 'business-showcase-networking-hub' ),
            array( 'back_link' => true )
        );
    }
    
    $rating = intval( $_POST['rating'] );
    
    if ( $rating < 1 || $rating > 5 ) {
        wp_die( 
            esc_html__( 'Error: Invalid rating value.', 'business-showcase-networking-hub' ),
            esc_html__( 'Invalid Rating', 'business-showcase-networking-hub' ),
            array( 'back_link' => true )
        );
    }
    
    return $commentdata;
}
add_filter( 'preprocess_comment', 'business_showcase_validate_rating' );

/**
 * Save Rating as Comment Meta
 */
function business_showcase_save_rating( $comment_id ) {
    if ( isset( $_POST['rating'] ) && ! empty( $_POST['rating'] ) ) {
        $rating = intval( $_POST['rating'] );
        
        if ( $rating >= 1 && $rating <= 5 ) {
            add_comment_meta( $comment_id, 'rating', $rating );
            
            // Update post average rating
            $comment = get_comment( $comment_id );
            if ( $comment ) {
                business_showcase_update_post_rating( $comment->comment_post_ID );
            }
        }
    }
}
add_action( 'comment_post', 'business_showcase_save_rating' );

/**
 * Update Post Average Rating
 */
function business_showcase_update_post_rating( $post_id ) {
    if ( get_post_type( $post_id ) !== 'business_profile' ) {
        return;
    }
    
    // Get all approved comments for this post
    $comments = get_comments( array(
        'post_id' => $post_id,
        'status' => 'approve',
        'type' => 'comment',
    ) );
    
    $total_rating = 0;
    $rating_count = 0;
    
    foreach ( $comments as $comment ) {
        $rating = get_comment_meta( $comment->comment_ID, 'rating', true );
        if ( $rating ) {
            $total_rating += intval( $rating );
            $rating_count++;
        }
    }
    
    if ( $rating_count > 0 ) {
        $average_rating = $total_rating / $rating_count;
        update_post_meta( $post_id, '_business_star_rating', $average_rating );
        update_post_meta( $post_id, '_business_rating_count', $rating_count );
    } else {
        delete_post_meta( $post_id, '_business_star_rating' );
        delete_post_meta( $post_id, '_business_rating_count' );
    }
}

/**
 * Recalculate rating when comment status changes
 */
function business_showcase_recalculate_rating_on_status_change( $comment_id, $comment_status ) {
    $comment = get_comment( $comment_id );
    if ( $comment && get_post_type( $comment->comment_post_ID ) === 'business_profile' ) {
        business_showcase_update_post_rating( $comment->comment_post_ID );
    }
}
add_action( 'transition_comment_status', 'business_showcase_recalculate_rating_on_status_change', 10, 2 );

/**
 * Display Rating in Comments
 */
function business_showcase_display_comment_rating( $comment_text, $comment ) {
    if ( get_post_type( $comment->comment_post_ID ) !== 'business_profile' ) {
        return $comment_text;
    }
    
    $rating = get_comment_meta( $comment->comment_ID, 'rating', true );
    
    if ( $rating ) {
        $rating_html = '<div class="comment-rating">';
        $rating_html .= business_showcase_display_star_rating( intval( $rating ) );
        $rating_html .= '</div>';
        
        return $rating_html . $comment_text;
    }
    
    return $comment_text;
}
add_filter( 'comment_text', 'business_showcase_display_comment_rating', 10, 2 );

/**
 * Get Business Rating Summary
 */
function business_showcase_get_rating_summary( $post_id ) {
    $average_rating = get_post_meta( $post_id, '_business_star_rating', true );
    $rating_count = get_post_meta( $post_id, '_business_rating_count', true );
    
    if ( ! $average_rating || ! $rating_count ) {
        return array(
            'average' => 0,
            'count' => 0,
            'html' => ''
        );
    }
    
    $average_rating = floatval( $average_rating );
    $rating_count = intval( $rating_count );
    
    ob_start();
    ?>
    <div class="business-rating-summary">
        <div class="rating-stars">
            <?php echo business_showcase_display_star_rating( $average_rating ); ?>
        </div>
        <div class="rating-meta">
            <span class="rating-average"><?php echo esc_html( number_format( $average_rating, 1 ) ); ?></span>
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
    <?php
    
    return array(
        'average' => $average_rating,
        'count' => $rating_count,
        'html' => ob_get_clean()
    );
}

/**
 * Display Rating Summary (Helper Function)
 */
function business_showcase_display_rating_summary( $post_id ) {
    $summary = business_showcase_get_rating_summary( $post_id );
    echo $summary['html'];
}

/**
 * Custom Comment Display Callback
 */
function business_showcase_custom_comment( $comment, $args, $depth ) {
    $GLOBALS['comment'] = $comment;
    $rating = get_comment_meta( $comment->comment_ID, 'rating', true );
    ?>
    <li id="comment-<?php comment_ID(); ?>" <?php comment_class( 'review-item' ); ?>>
        <article class="review-content">
            
            <div class="review-header">
                <div class="reviewer-avatar">
                    <?php echo get_avatar( $comment, 60 ); ?>
                </div>
                
                <div class="reviewer-info">
                    <div class="reviewer-name">
                        <?php comment_author(); ?>
                    </div>
                    
                    <?php if ( $rating ) : ?>
                        <div class="reviewer-rating">
                            <?php echo business_showcase_display_star_rating( intval( $rating ) ); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="review-date">
                        <?php
                        printf(
                            esc_html__( '%s ago', 'business-showcase-networking-hub' ),
                            human_time_diff( get_comment_time( 'U' ), current_time( 'timestamp' ) )
                        );
                        ?>
                    </div>
                </div>
            </div>
            
            <div class="review-text">
                <?php comment_text(); ?>
            </div>
            
            <?php if ( $comment->comment_approved == '0' ) : ?>
                <p class="review-awaiting-moderation">
                    <?php esc_html_e( 'Your review is awaiting moderation.', 'business-showcase-networking-hub' ); ?>
                </p>
            <?php endif; ?>
            
            <div class="review-actions">
                <?php
                comment_reply_link( array_merge( $args, array(
                    'depth'     => $depth,
                    'max_depth' => $args['max_depth'],
                    'reply_text' => esc_html__( 'Reply', 'business-showcase-networking-hub' ),
                ) ) );
                ?>
                
                <?php edit_comment_link( esc_html__( 'Edit', 'business-showcase-networking-hub' ), ' | ' ); ?>
            </div>
            
        </article>
    <?php
}

/**
 * Load Custom Comments Template
 */
function business_showcase_custom_comments_template( $template ) {
    if ( get_post_type() === 'business_profile' ) {
        $custom_template = BUSINESS_SHOWCASE_PLUGIN_DIR . 'templates/comments-business-profile.php';
        
        if ( file_exists( $custom_template ) ) {
            return $custom_template;
        }
    }
    
    return $template;
}
add_filter( 'comments_template', 'business_showcase_custom_comments_template' );

/**
 * Add Custom Columns to Business Profile Admin Listing
 */
function business_showcase_add_custom_columns( $columns ) {
    // Remove default columns we'll reposition
    $date = $columns['date'];
    unset( $columns['date'] );
    
    // Add custom columns
    $columns['logo'] = __( 'Logo', 'business-showcase-networking-hub' );
    $columns['services'] = __( 'Services', 'business-showcase-networking-hub' );
    $columns['website'] = __( 'Website', 'business-showcase-networking-hub' );
    $columns['featured'] = __( 'Featured', 'business-showcase-networking-hub' );
    $columns['rating'] = __( 'Rating', 'business-showcase-networking-hub' );
    
    // Re-add date column at the end
    $columns['date'] = $date;
    
    return $columns;
}
add_filter( 'manage_business_profile_posts_columns', 'business_showcase_add_custom_columns' );

/**
 * Populate Custom Columns Content
 */
function business_showcase_populate_custom_columns( $column, $post_id ) {
    switch ( $column ) {
        
        case 'logo':
            if ( has_post_thumbnail( $post_id ) ) {
                echo '<div class="business-admin-logo">';
                echo get_the_post_thumbnail( $post_id, array( 60, 60 ) );
                echo '</div>';
            } else {
                echo '<span class="dashicons dashicons-format-image" style="color: #ddd; font-size: 40px;"></span>';
            }
            break;
        
        case 'services':
            $services = get_post_meta( $post_id, '_business_services', true );
            if ( ! empty( $services ) && is_array( $services ) ) {
                $service_labels = business_showcase_get_service_labels();
                $service_names = array();
                
                foreach ( $services as $service ) {
                    if ( isset( $service_labels[ $service ] ) ) {
                        $service_names[] = $service_labels[ $service ];
                    }
                }
                
                if ( count( $service_names ) > 0 ) {
                    echo '<div class="business-admin-services">';
                    foreach ( $service_names as $name ) {
                        echo '<span class="service-tag">' . esc_html( $name ) . '</span>';
                    }
                    echo '</div>';
                } else {
                    echo '<span style="color: #999;">—</span>';
                }
            } else {
                echo '<span style="color: #999;">—</span>';
            }
            break;
        
        case 'website':
            $website_url = get_post_meta( $post_id, '_business_website_url', true );
            if ( $website_url ) {
                echo '<a href="' . esc_url( $website_url ) . '" target="_blank" rel="noopener noreferrer" class="business-website-link">';
                echo '<span class="dashicons dashicons-admin-links"></span> ';
                echo esc_html__( 'Visit', 'business-showcase-networking-hub' );
                echo '</a>';
            } else {
                echo '<span style="color: #999;">—</span>';
            }
            break;
        
        case 'featured':
            $is_featured = get_post_meta( $post_id, '_business_is_featured', true );
            if ( $is_featured == '1' ) {
                echo '<span class="featured-status featured-yes">';
                echo '<span class="dashicons dashicons-star-filled"></span> ';
                echo esc_html__( 'Featured', 'business-showcase-networking-hub' );
                echo '</span>';
            } else {
                echo '<span class="featured-status featured-no" style="color: #999;">—</span>';
            }
            break;
        
        case 'rating':
            $average_rating = get_post_meta( $post_id, '_business_star_rating', true );
            $rating_count = get_post_meta( $post_id, '_business_rating_count', true );
            
            if ( $average_rating && $rating_count ) {
                echo '<div class="business-admin-rating">';
                echo '<span class="rating-stars" style="color: #ffc107; font-size: 16px;">';
                
                $rating = floatval( $average_rating );
                $full_stars = floor( $rating );
                $half_star = ( $rating - $full_stars ) >= 0.5 ? 1 : 0;
                
                for ( $i = 0; $i < $full_stars; $i++ ) {
                    echo '★';
                }
                if ( $half_star ) {
                    echo '★';
                }
                
                echo '</span><br>';
                echo '<span class="rating-text">' . esc_html( number_format( $rating, 1 ) ) . ' ';
                echo '<span style="color: #999;">(' . intval( $rating_count ) . ')</span>';
                echo '</span>';
                echo '</div>';
            } else {
                echo '<span style="color: #999;">' . esc_html__( 'No reviews', 'business-showcase-networking-hub' ) . '</span>';
            }
            break;
    }
}
add_action( 'manage_business_profile_posts_custom_column', 'business_showcase_populate_custom_columns', 10, 2 );

/**
 * Make Custom Columns Sortable
 */
function business_showcase_sortable_columns( $columns ) {
    $columns['featured'] = 'featured';
    $columns['rating'] = 'rating';
    $columns['website'] = 'website';
    
    return $columns;
}
add_filter( 'manage_edit-business_profile_sortable_columns', 'business_showcase_sortable_columns' );

/**
 * Handle Custom Column Sorting
 */
function business_showcase_handle_column_sorting( $query ) {
    if ( ! is_admin() || ! $query->is_main_query() ) {
        return;
    }
    
    if ( $query->get( 'post_type' ) !== 'business_profile' ) {
        return;
    }
    
    $orderby = $query->get( 'orderby' );
    
    switch ( $orderby ) {
        case 'featured':
            $query->set( 'meta_key', '_business_is_featured' );
            $query->set( 'orderby', 'meta_value' );
            break;
        
        case 'rating':
            $query->set( 'meta_key', '_business_star_rating' );
            $query->set( 'orderby', 'meta_value_num' );
            break;
        
        case 'website':
            $query->set( 'meta_key', '_business_website_url' );
            $query->set( 'orderby', 'meta_value' );
            break;
    }
}
add_action( 'pre_get_posts', 'business_showcase_handle_column_sorting' );

/**
 * Add Custom CSS for Admin Columns
 */
function business_showcase_admin_column_styles() {
    global $typenow;
    
    if ( $typenow === 'business_profile' ) {
        ?>
        <style>
            /* Logo Column */
            .column-logo {
                width: 80px;
            }
            
            .business-admin-logo img {
                width: 60px;
                height: 60px;
                object-fit: cover;
                border-radius: 4px;
                border: 1px solid #ddd;
            }
            
            /* Services Column */
            .column-services {
                width: 200px;
            }
            
            .business-admin-services {
                display: flex;
                flex-wrap: wrap;
                gap: 4px;
            }
            
            .business-admin-services .service-tag {
                display: inline-block;
                padding: 3px 8px;
                background: #e3f2fd;
                color: #1976d2;
                border-radius: 3px;
                font-size: 11px;
                white-space: nowrap;
            }
            
            /* Website Column */
            .column-website {
                width: 100px;
            }
            
            .business-website-link {
                display: inline-flex;
                align-items: center;
                text-decoration: none;
            }
            
            .business-website-link .dashicons {
                font-size: 16px;
                width: 16px;
                height: 16px;
            }
            
            /* Featured Column */
            .column-featured {
                width: 100px;
            }
            
            .featured-status {
                display: inline-flex;
                align-items: center;
                gap: 4px;
                font-weight: 600;
            }
            
            .featured-yes {
                color: #ffc107;
            }
            
            .featured-yes .dashicons {
                color: #ffc107;
            }
            
            /* Rating Column */
            .column-rating {
                width: 100px;
            }
            
            .business-admin-rating {
                text-align: left;
            }
            
            .business-admin-rating .rating-stars {
                display: block;
                margin-bottom: 2px;
                letter-spacing: 2px;
            }
            
            .business-admin-rating .rating-text {
                font-size: 13px;
                color: #333;
                font-weight: 600;
            }
        </style>
        <?php
    }
}
add_action( 'admin_head', 'business_showcase_admin_column_styles' );

/**
 * Add Export Admin Menu Page
 */
function business_showcase_add_export_menu() {
    add_submenu_page(
        'edit.php?post_type=business_profile',
        __( 'Export Business Profiles', 'business-showcase-networking-hub' ),
        __( 'Export to CSV', 'business-showcase-networking-hub' ),
        'manage_options',
        'business-profile-export',
        'business_showcase_render_export_page'
    );
}
add_action( 'admin_menu', 'business_showcase_add_export_menu' );

/**
 * Render Export Admin Page
 */
function business_showcase_render_export_page() {
    // Check user capabilities
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'business-showcase-networking-hub' ) );
    }
    
    // Get business profile statistics
    $total_profiles = wp_count_posts( 'business_profile' );
    $published_count = isset( $total_profiles->publish ) ? $total_profiles->publish : 0;
    
    $featured_query = new WP_Query( array(
        'post_type' => 'business_profile',
        'post_status' => 'publish',
        'meta_key' => '_business_is_featured',
        'meta_value' => '1',
        'fields' => 'ids',
        'posts_per_page' => -1,
    ) );
    $featured_count = $featured_query->found_posts;
    
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Export Business Profiles', 'business-showcase-networking-hub' ); ?></h1>
        
        <div class="export-stats-container">
            <div class="export-stat-card">
                <div class="stat-icon">
                    <span class="dashicons dashicons-building"></span>
                </div>
                <div class="stat-content">
                    <div class="stat-number"><?php echo esc_html( number_format_i18n( $published_count ) ); ?></div>
                    <div class="stat-label"><?php esc_html_e( 'Total Profiles', 'business-showcase-networking-hub' ); ?></div>
                </div>
            </div>
            
            <div class="export-stat-card">
                <div class="stat-icon featured">
                    <span class="dashicons dashicons-star-filled"></span>
                </div>
                <div class="stat-content">
                    <div class="stat-number"><?php echo esc_html( number_format_i18n( $featured_count ) ); ?></div>
                    <div class="stat-label"><?php esc_html_e( 'Featured Profiles', 'business-showcase-networking-hub' ); ?></div>
                </div>
            </div>
        </div>
        
        <div class="export-form-container">
            <h2><?php esc_html_e( 'Export Options', 'business-showcase-networking-hub' ); ?></h2>
            
            <form method="post" action="">
                <?php wp_nonce_field( 'business_showcase_export_csv', 'business_showcase_export_nonce' ); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="export_type">
                                <?php esc_html_e( 'Export Type', 'business-showcase-networking-hub' ); ?>
                            </label>
                        </th>
                        <td>
                            <select name="export_type" id="export_type" class="regular-text">
                                <option value="all"><?php esc_html_e( 'All Business Profiles', 'business-showcase-networking-hub' ); ?></option>
                                <option value="featured"><?php esc_html_e( 'Featured Only', 'business-showcase-networking-hub' ); ?></option>
                            </select>
                            <p class="description">
                                <?php esc_html_e( 'Choose which profiles to export', 'business-showcase-networking-hub' ); ?>
                            </p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <?php esc_html_e( 'Fields to Export', 'business-showcase-networking-hub' ); ?>
                        </th>
                        <td>
                            <p class="description">
                                <?php esc_html_e( 'All available fields will be exported:', 'business-showcase-networking-hub' ); ?>
                            </p>
                            <ul style="list-style: disc; margin-left: 20px; margin-top: 10px;">
                                <li><?php esc_html_e( 'Business Name', 'business-showcase-networking-hub' ); ?></li>
                                <li><?php esc_html_e( 'Description', 'business-showcase-networking-hub' ); ?></li>
                                <li><?php esc_html_e( 'Logo URL', 'business-showcase-networking-hub' ); ?></li>
                                <li><?php esc_html_e( 'Categories', 'business-showcase-networking-hub' ); ?></li>
                                <li><?php esc_html_e( 'Services', 'business-showcase-networking-hub' ); ?></li>
                                <li><?php esc_html_e( 'Website URL', 'business-showcase-networking-hub' ); ?></li>
                                <li><?php esc_html_e( 'Contact Email', 'business-showcase-networking-hub' ); ?></li>
                                <li><?php esc_html_e( 'Facebook URL', 'business-showcase-networking-hub' ); ?></li>
                                <li><?php esc_html_e( 'Twitter URL', 'business-showcase-networking-hub' ); ?></li>
                                <li><?php esc_html_e( 'LinkedIn URL', 'business-showcase-networking-hub' ); ?></li>
                                <li><?php esc_html_e( 'Featured Status', 'business-showcase-networking-hub' ); ?></li>
                                <li><?php esc_html_e( 'Average Rating', 'business-showcase-networking-hub' ); ?></li>
                                <li><?php esc_html_e( 'Review Count', 'business-showcase-networking-hub' ); ?></li>
                                <li><?php esc_html_e( 'Publish Date', 'business-showcase-networking-hub' ); ?></li>
                            </ul>
                        </td>
                    </tr>
                </table>
                
                <p class="submit">
                    <button type="submit" name="export_csv" class="button button-primary button-hero">
                        <span class="dashicons dashicons-download" style="margin-top: 6px;"></span>
                        <?php esc_html_e( 'Export to CSV', 'business-showcase-networking-hub' ); ?>
                    </button>
                </p>
            </form>
        </div>
        
        <style>
            .export-stats-container {
                display: flex;
                gap: 20px;
                margin: 30px 0;
            }
            
            .export-stat-card {
                background: #fff;
                border: 1px solid #ddd;
                border-radius: 8px;
                padding: 20px;
                display: flex;
                gap: 20px;
                align-items: center;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
                flex: 1;
            }
            
            .stat-icon {
                width: 60px;
                height: 60px;
                background: #2271b1;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }
            
            .stat-icon.featured {
                background: #ffc107;
            }
            
            .stat-icon .dashicons {
                font-size: 32px;
                width: 32px;
                height: 32px;
                color: #fff;
            }
            
            .stat-content {
                flex: 1;
            }
            
            .stat-number {
                font-size: 32px;
                font-weight: 700;
                color: #333;
                line-height: 1;
                margin-bottom: 5px;
            }
            
            .stat-label {
                font-size: 14px;
                color: #666;
            }
            
            .export-form-container {
                background: #fff;
                border: 1px solid #ddd;
                border-radius: 8px;
                padding: 30px;
                margin-top: 30px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            }
            
            .export-form-container h2 {
                margin-top: 0;
                padding-bottom: 15px;
                border-bottom: 2px solid #2271b1;
            }
            
            .button-hero .dashicons {
                display: inline-block;
            }
        </style>
    </div>
    <?php
}

/**
 * Handle CSV Export
 */
function business_showcase_handle_csv_export() {
    // Check if export form is submitted
    if ( ! isset( $_POST['export_csv'] ) ) {
        return;
    }
    
    // Verify nonce
    if ( ! isset( $_POST['business_showcase_export_nonce'] ) || 
         ! wp_verify_nonce( $_POST['business_showcase_export_nonce'], 'business_showcase_export_csv' ) ) {
        wp_die( esc_html__( 'Security check failed.', 'business-showcase-networking-hub' ) );
    }
    
    // Check user capabilities
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'You do not have sufficient permissions.', 'business-showcase-networking-hub' ) );
    }
    
    // Get export type
    $export_type = isset( $_POST['export_type'] ) ? sanitize_text_field( $_POST['export_type'] ) : 'all';
    
    // Query arguments
    $args = array(
        'post_type' => 'business_profile',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'date',
        'order' => 'DESC',
    );
    
    // Add featured filter if needed
    if ( $export_type === 'featured' ) {
        $args['meta_key'] = '_business_is_featured';
        $args['meta_value'] = '1';
    }
    
    $query = new WP_Query( $args );
    
    if ( ! $query->have_posts() ) {
        wp_die( esc_html__( 'No business profiles found to export.', 'business-showcase-networking-hub' ) );
    }
    
    // Set headers for CSV download
    header( 'Content-Type: text/csv; charset=utf-8' );
    header( 'Content-Disposition: attachment; filename=business-profiles-' . date( 'Y-m-d-H-i-s' ) . '.csv' );
    header( 'Pragma: no-cache' );
    header( 'Expires: 0' );
    
    // Create output stream
    $output = fopen( 'php://output', 'w' );
    
    // Add BOM for UTF-8
    fprintf( $output, chr(0xEF).chr(0xBB).chr(0xBF) );
    
    // CSV Headers
    $headers = array(
        'ID',
        'Business Name',
        'Description',
        'Logo URL',
        'Categories',
        'Services',
        'Website URL',
        'Contact Email',
        'Facebook URL',
        'Twitter URL',
        'LinkedIn URL',
        'Featured',
        'Average Rating',
        'Review Count',
        'Publish Date',
        'Profile URL',
    );
    
    fputcsv( $output, $headers );
    
    // Export data
    while ( $query->have_posts() ) {
        $query->the_post();
        $post_id = get_the_ID();
        
        // Get all meta data
        $services = get_post_meta( $post_id, '_business_services', true );
        $website_url = get_post_meta( $post_id, '_business_website_url', true );
        $contact_email = get_post_meta( $post_id, '_business_contact_email', true );
        $facebook_url = get_post_meta( $post_id, '_business_facebook_url', true );
        $twitter_url = get_post_meta( $post_id, '_business_twitter_url', true );
        $linkedin_url = get_post_meta( $post_id, '_business_linkedin_url', true );
        $is_featured = get_post_meta( $post_id, '_business_is_featured', true );
        $average_rating = get_post_meta( $post_id, '_business_star_rating', true );
        $rating_count = get_post_meta( $post_id, '_business_rating_count', true );
        
        // Get logo URL
        $logo_url = get_the_post_thumbnail_url( $post_id, 'full' );
        
        // Get categories
        $categories = get_the_terms( $post_id, 'business_category' );
        $category_names = array();
        if ( $categories && ! is_wp_error( $categories ) ) {
            foreach ( $categories as $category ) {
                $category_names[] = $category->name;
            }
        }
        
        // Format services
        $service_names = array();
        if ( ! empty( $services ) && is_array( $services ) ) {
            $service_labels = business_showcase_get_service_labels();
            foreach ( $services as $service ) {
                if ( isset( $service_labels[ $service ] ) ) {
                    $service_names[] = $service_labels[ $service ];
                }
            }
        }
        
        // Get description (strip HTML tags)
        $description = wp_strip_all_tags( get_the_content() );
        $description = str_replace( array( "\r", "\n" ), ' ', $description );
        
        // Prepare row data
        $row = array(
            $post_id,
            get_the_title(),
            $description,
            $logo_url ? $logo_url : '',
            implode( ', ', $category_names ),
            implode( ', ', $service_names ),
            $website_url ? $website_url : '',
            $contact_email ? $contact_email : '',
            $facebook_url ? $facebook_url : '',
            $twitter_url ? $twitter_url : '',
            $linkedin_url ? $linkedin_url : '',
            $is_featured == '1' ? 'Yes' : 'No',
            $average_rating ? number_format( floatval( $average_rating ), 2 ) : '',
            $rating_count ? $rating_count : '0',
            get_the_date( 'Y-m-d H:i:s' ),
            get_permalink(),
        );
        
        fputcsv( $output, $row );
    }
    
    fclose( $output );
    wp_reset_postdata();
    
    exit;
}
add_action( 'admin_init', 'business_showcase_handle_csv_export' );

/**
 * Add Custom Bulk Actions
 */
function business_showcase_add_bulk_actions( $bulk_actions ) {
    $bulk_actions['mark_as_featured'] = __( 'Mark as Featured', 'business-showcase-networking-hub' );
    $bulk_actions['remove_featured'] = __( 'Remove Featured', 'business-showcase-networking-hub' );
    return $bulk_actions;
}
add_filter( 'bulk_actions-edit-business_profile', 'business_showcase_add_bulk_actions' );

/**
 * Handle Custom Bulk Actions
 */
function business_showcase_handle_bulk_actions( $redirect_to, $action, $post_ids ) {
    
    if ( $action === 'mark_as_featured' ) {
        $updated_count = 0;
        
        foreach ( $post_ids as $post_id ) {
            // Verify it's a business profile
            if ( get_post_type( $post_id ) === 'business_profile' ) {
                update_post_meta( $post_id, '_business_is_featured', '1' );
                $updated_count++;
            }
        }
        
        $redirect_to = add_query_arg( 'bulk_featured_marked', $updated_count, $redirect_to );
        return $redirect_to;
    }
    
    if ( $action === 'remove_featured' ) {
        $updated_count = 0;
        
        foreach ( $post_ids as $post_id ) {
            // Verify it's a business profile
            if ( get_post_type( $post_id ) === 'business_profile' ) {
                update_post_meta( $post_id, '_business_is_featured', '0' );
                $updated_count++;
            }
        }
        
        $redirect_to = add_query_arg( 'bulk_featured_removed', $updated_count, $redirect_to );
        return $redirect_to;
    }
    
    return $redirect_to;
}
add_filter( 'handle_bulk_actions-edit-business_profile', 'business_showcase_handle_bulk_actions', 10, 3 );

/**
 * Display Admin Notices for Bulk Actions
 */
function business_showcase_bulk_action_admin_notices() {
    global $pagenow, $typenow;
    
    if ( $pagenow === 'edit.php' && $typenow === 'business_profile' ) {
        
        // Mark as Featured notice
        if ( ! empty( $_REQUEST['bulk_featured_marked'] ) ) {
            $count = intval( $_REQUEST['bulk_featured_marked'] );
            
            printf(
                '<div class="notice notice-success is-dismissible"><p>' .
                esc_html(
                    _n(
                        '%d business profile marked as featured.',
                        '%d business profiles marked as featured.',
                        $count,
                        'business-showcase-networking-hub'
                    )
                ) .
                '</p></div>',
                $count
            );
        }
        
        // Remove Featured notice
        if ( ! empty( $_REQUEST['bulk_featured_removed'] ) ) {
            $count = intval( $_REQUEST['bulk_featured_removed'] );
            
            printf(
                '<div class="notice notice-success is-dismissible"><p>' .
                esc_html(
                    _n(
                        '%d business profile removed from featured.',
                        '%d business profiles removed from featured.',
                        $count,
                        'business-showcase-networking-hub'
                    )
                ) .
                '</p></div>',
                $count
            );
        }
    }
}
add_action( 'admin_notices', 'business_showcase_bulk_action_admin_notices' );

/**
 * Register Gutenberg Block
 */
function business_showcase_register_block() {
    // Check if Gutenberg is available
    if ( ! function_exists( 'register_block_type' ) ) {
        return;
    }
    
    // Register Business Directory block script
    wp_register_script(
        'business-showcase-block',
        BUSINESS_SHOWCASE_PLUGIN_URL . 'assets/js/block-business-directory.js',
        array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n', 'wp-server-side-render' ),
        BUSINESS_SHOWCASE_VERSION,
        true
    );
    
    // Register Success Stories block script
    wp_register_script(
        'business-showcase-success-stories-block',
        BUSINESS_SHOWCASE_PLUGIN_URL . 'assets/js/block-success-stories.js',
        array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n', 'wp-server-side-render' ),
        BUSINESS_SHOWCASE_VERSION,
        true
    );
    
    // Get categories for block
    $categories = get_terms( array(
        'taxonomy' => 'business_category',
        'hide_empty' => false,
    ) );
    
    $category_list = array();
    if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
        foreach ( $categories as $category ) {
            $category_list[] = array(
                'name' => $category->name,
                'slug' => $category->slug,
            );
        }
    }
    
    // Localize script with data
    wp_localize_script(
        'business-showcase-block',
        'businessShowcaseBlock',
        array(
            'categories' => $category_list,
            'services' => business_showcase_get_all_services(),
        )
    );
    
    // Register Business Directory block
    register_block_type( 'business-showcase/directory', array(
        'editor_script' => 'business-showcase-block',
        'attributes' => array(
            'postsPerPage' => array(
                'type' => 'number',
                'default' => 12,
            ),
            'category' => array(
                'type' => 'string',
                'default' => '',
            ),
            'service' => array(
                'type' => 'string',
                'default' => '',
            ),
            'featuredOnly' => array(
                'type' => 'boolean',
                'default' => false,
            ),
            'columns' => array(
                'type' => 'number',
                'default' => 3,
            ),
        ),
        'render_callback' => 'business_showcase_render_block',
    ) );
    
    // Register Success Stories block
    register_block_type( 'business-showcase/success-stories', array(
        'editor_script' => 'business-showcase-success-stories-block',
        'attributes' => array(
            'numberOfItems' => array(
                'type' => 'number',
                'default' => 6,
            ),
            'orderBy' => array(
                'type' => 'string',
                'default' => 'rating',
            ),
            'layoutStyle' => array(
                'type' => 'string',
                'default' => 'grid',
            ),
            'columns' => array(
                'type' => 'number',
                'default' => 3,
            ),
            'showRating' => array(
                'type' => 'boolean',
                'default' => true,
            ),
            'showReviewCount' => array(
                'type' => 'boolean',
                'default' => true,
            ),
        ),
        'render_callback' => 'business_showcase_render_success_stories_block',
    ) );
}
add_action( 'init', 'business_showcase_register_block' );

/**
 * Render Block Callback
 */
function business_showcase_render_block( $attributes ) {
    $atts = shortcode_atts( array(
        'postsPerPage' => 12,
        'category' => '',
        'service' => '',
        'featuredOnly' => false,
        'columns' => 3,
    ), $attributes );
    
    // Convert camelCase to snake_case for consistency with shortcode
    $args = array(
        'posts_per_page' => intval( $atts['postsPerPage'] ),
        'category' => sanitize_text_field( $atts['category'] ),
        'service' => sanitize_text_field( $atts['service'] ),
        'featured_only' => (bool) $atts['featuredOnly'],
    );
    
    // Get business grid HTML
    $grid_html = business_showcase_get_business_grid( $args );
    
    $columns = intval( $atts['columns'] );
    $columns_class = 'columns-' . $columns;
    
    ob_start();
    ?>
    <div class="business-showcase-block">
        <div class="business-grid business-block-grid <?php echo esc_attr( $columns_class ); ?>">
            <?php echo $grid_html; ?>
        </div>
    </div>
    <?php
    
    return ob_get_clean();
}

/**
 * Render Success Stories Block Callback
 */
function business_showcase_render_success_stories_block( $attributes ) {
    $atts = shortcode_atts( array(
        'numberOfItems' => 6,
        'orderBy' => 'rating',
        'layoutStyle' => 'grid',
        'columns' => 3,
        'showRating' => true,
        'showReviewCount' => true,
    ), $attributes );
    
    // Build query arguments
    $query_args = array(
        'post_type' => 'business_profile',
        'posts_per_page' => intval( $atts['numberOfItems'] ),
        'post_status' => 'publish',
    );
    
    // Set ordering
    $order_by = sanitize_text_field( $atts['orderBy'] );
    
    if ( $order_by === 'rating' ) {
        $query_args['meta_key'] = '_business_star_rating';
        $query_args['orderby'] = 'meta_value_num';
        $query_args['order'] = 'DESC';
        // Only show businesses with ratings
        $query_args['meta_query'] = array(
            array(
                'key' => '_business_star_rating',
                'compare' => 'EXISTS',
            ),
            array(
                'key' => '_business_star_rating',
                'value' => 0,
                'compare' => '>',
                'type' => 'DECIMAL',
            ),
        );
    } elseif ( $order_by === 'review_count' ) {
        $query_args['meta_key'] = '_business_rating_count';
        $query_args['orderby'] = 'meta_value_num';
        $query_args['order'] = 'DESC';
        $query_args['meta_query'] = array(
            array(
                'key' => '_business_rating_count',
                'compare' => 'EXISTS',
            ),
        );
    } else {
        $query_args['orderby'] = 'date';
        $query_args['order'] = 'DESC';
    }
    
    $query = new WP_Query( $query_args );
    
    if ( ! $query->have_posts() ) {
        return '<p class="no-success-stories">' . esc_html__( 'No success stories found.', 'business-showcase-networking-hub' ) . '</p>';
    }
    
    $layout_style = sanitize_text_field( $atts['layoutStyle'] );
    $columns = intval( $atts['columns'] );
    $show_rating = (bool) $atts['showRating'];
    $show_review_count = (bool) $atts['showReviewCount'];
    
    ob_start();
    ?>
    
    <div class="success-stories-block layout-<?php echo esc_attr( $layout_style ); ?>">
        
        <div class="success-stories-container <?php echo ( $layout_style !== 'list' ) ? 'columns-' . esc_attr( $columns ) : ''; ?>">
            
            <?php 
            $story_count = 1;
            while ( $query->have_posts() ) : $query->the_post();
                $post_id = get_the_ID();
                $average_rating = get_post_meta( $post_id, '_business_star_rating', true );
                $rating_count = get_post_meta( $post_id, '_business_rating_count', true );
                $website_url = get_post_meta( $post_id, '_business_website_url', true );
                $categories = get_the_terms( $post_id, 'business_category' );
            ?>
            
            <article class="success-story-item rank-<?php echo esc_attr( $story_count ); ?>">
                
                <?php if ( $story_count <= 3 ) : ?>
                    <span class="story-rank-badge">#<?php echo esc_html( $story_count ); ?></span>
                <?php endif; ?>
                
                <?php if ( has_post_thumbnail() ) : ?>
                    <div class="story-logo">
                        <a href="<?php the_permalink(); ?>">
                            <?php the_post_thumbnail( 'medium' ); ?>
                        </a>
                    </div>
                <?php endif; ?>
                
                <div class="story-content">
                    
                    <h3 class="story-title">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h3>
                    
                    <?php if ( $categories && ! is_wp_error( $categories ) ) : ?>
                        <div class="story-category">
                            <?php echo esc_html( $categories[0]->name ); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ( $show_rating && $average_rating ) : ?>
                        <div class="story-rating">
                            <?php echo business_showcase_display_star_rating( floatval( $average_rating ) ); ?>
                            <span class="rating-value">
                                <?php echo esc_html( number_format( floatval( $average_rating ), 1 ) ); ?>
                                <?php if ( $show_review_count && $rating_count ) : ?>
                                    <span class="review-count">
                                        (<?php 
                                        printf(
                                            esc_html( _n( '%d review', '%d reviews', intval( $rating_count ), 'business-showcase-networking-hub' ) ),
                                            intval( $rating_count )
                                        );
                                        ?>)
                                    </span>
                                <?php endif; ?>
                            </span>
                        </div>
                    <?php endif; ?>
                    
                    <div class="story-excerpt">
                        <?php echo wp_trim_words( get_the_excerpt(), 20 ); ?>
                    </div>
                    
                    <div class="story-actions">
                        <a href="<?php the_permalink(); ?>" class="story-btn">
                            <?php esc_html_e( 'View Success Story', 'business-showcase-networking-hub' ); ?>
                        </a>
                        <?php if ( $website_url ) : ?>
                            <a href="<?php echo esc_url( $website_url ); ?>" target="_blank" class="story-btn-secondary">
                                <?php esc_html_e( 'Visit Website', 'business-showcase-networking-hub' ); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                    
                </div>
                
            </article>
            
            <?php 
            $story_count++;
            endwhile; 
            wp_reset_postdata();
            ?>
            
        </div>
        
    </div>
    
    <?php
    return ob_get_clean();
}

/**
 * Add Block Category
 */
function business_showcase_block_category( $categories ) {
    return array_merge(
        $categories,
        array(
            array(
                'slug' => 'business-showcase',
                'title' => __( 'Business Showcase', 'business-showcase-networking-hub' ),
                'icon' => 'building',
            ),
        )
    );
}
add_filter( 'block_categories_all', 'business_showcase_block_category', 10, 1 );
