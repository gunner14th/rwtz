<?php

if ( ! defined('ABSPATH') ) { die(); }

if (!defined('_S_VERSION')) {
    // Replace the version number of the theme on each release.
    define('_S_VERSION', wp_get_theme()->get( 'Version' ));
}

function rwtz_setup() {
    /**
     * Set up Child Theme's textdomain.
     *
     * Translations can be added to the /languages/ directory.
     */
    load_child_theme_textdomain( 'twentytwentyone-child', get_stylesheet_directory() . '/languages' );
}
add_action('after_setup_theme', 'rwtz_setup');

/**
 * Enqueue scripts and styles.
 */
function rwtz_scripts_and_styles() {
    $uri = get_stylesheet_directory_uri();

    wp_enqueue_style('rwtz-style', get_stylesheet_uri(), [ 'twenty-twenty-one-style' ], _S_VERSION);
    wp_enqueue_style('rwtz-style-main', $uri . '/dist/css/main.min.css', "", time());
    wp_enqueue_script('common-js', $uri . '/dist/js/scripts.min.js', [ 'jquery' ], time());
    wp_localize_script( 'common-js', 'front_data', [
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'nonce' => wp_create_nonce( 'flink_global_nonce' )
    ] );
}
add_action('wp_enqueue_scripts', 'rwtz_scripts_and_styles');

/**
 * Debug helper
 */
function dumper ( $param ) {
    echo "<pre>";
    var_dump( $param );
    echo "</pre>";
}