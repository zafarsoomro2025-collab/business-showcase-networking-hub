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
