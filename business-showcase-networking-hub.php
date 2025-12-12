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
    // Register custom post types, taxonomies, etc.
    // Add your initialization code here
}
add_action( 'init', 'business_showcase_init' );
