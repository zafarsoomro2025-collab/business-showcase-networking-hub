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
 * Enqueue CSS and JS Files
 */
function business_showcase_enqueue_scripts() {
    // Enqueue CSS
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
            array(),
            BUSINESS_SHOWCASE_VERSION,
            'all'
        );
    }
    
    // Enqueue JavaScript
    wp_enqueue_script(
        'business-showcase-script',
        BUSINESS_SHOWCASE_PLUGIN_URL . 'assets/js/script.js',
        array( 'jquery' ),
        BUSINESS_SHOWCASE_VERSION,
        true
    );
    
    // Localize script for AJAX
    wp_localize_script(
        'business-showcase-script',
        'businessShowcaseAjax',
        array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'business_showcase_nonce' )
        )
    );
}
add_action( 'wp_enqueue_scripts', 'business_showcase_enqueue_scripts' );

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
                                <?php echo esc_html( $category->name ); ?> (<?php echo $category->count; ?>)
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
            <?php echo business_showcase_get_business_grid( $atts ); ?>
        </div>
        
    </div>
    <?php
    
    return ob_get_clean();
}
add_shortcode( 'business_directory', 'business_showcase_directory_shortcode' );

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
                        <?php echo wp_trim_words( get_the_excerpt(), 20 ); ?>
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
    
    wp_send_json_success( array( 'html' => $html ) );
}
add_action( 'wp_ajax_business_showcase_filter', 'business_showcase_filter_businesses' );
add_action( 'wp_ajax_nopriv_business_showcase_filter', 'business_showcase_filter_businesses' );

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
