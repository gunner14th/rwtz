<?php

if ( ! defined('ABSPATH') ) { die(); }

if (!defined('_S_VERSION')) {
    // Replace the version number of the theme on each release.
    define('_S_VERSION', wp_get_theme()->get( 'Version' ));
}

/**
 * Defines
 */
define( '_THEME_URI', get_stylesheet_directory_uri() );

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
 * Enqueue scripts and styles Front.
 */
function rwtz_scripts_and_styles() {
    wp_enqueue_style('rwtz-style', get_stylesheet_uri(), [ 'twenty-twenty-one-style' ], _S_VERSION);
    wp_enqueue_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css');
    wp_enqueue_style('rwtz-style-main', _THEME_URI . '/dist/css/main.min.css', "", time());
    wp_enqueue_script('common-js', _THEME_URI . '/dist/js/scripts.min.js', [ 'jquery' ], time());
    wp_localize_script( 'common-js', 'front_data', [
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'nonce' => wp_create_nonce( 'flink_global_nonce' )
    ] );
}
add_action('wp_enqueue_scripts', 'rwtz_scripts_and_styles');

/**
 * Enqueue scripts and styles Front.
 */
function rwtz_scripts_and_styles_admin() {
    wp_enqueue_script('admin-js', _THEME_URI . '/dist/admin-js/admin-scripts.min.js', [ 'jquery' ], time());
}
add_action('admin_enqueue_scripts', 'rwtz_scripts_and_styles_admin');

/**
 * Custom Post types
 */
function register_post_type_rw_olx() {
    register_post_type( 'rw_olx', [
        'labels' => [
            'name'                     => 'Публікації',
            'singular_name'            => 'Публікація',
            'add_new'                  => 'Додати публікацію',
            'add_new_item'             => 'Додати нову публікацію',
            'edit_item'                => 'Змінити публікацію',
            'new_item'                 => 'Нова публікація',
            'view_item'                => 'Перегляд публікації',
            'search_items'             => 'Знайти публікацію',
            'not_found'                => 'Публікацій не знайдено',
            'not_found_in_trash'       => 'В корзине нет авиабилетов',
            'parent_item_colon'        => 'Родительский авиабилет',
            'all_items'                => 'Усі публікації',
            'archives'                 => 'Архивы авиабилетов',
            'menu_name'                => 'Публікації',
            'name_admin_bar'           => 'Публікація',
            'view_items'               => 'Перегляд публікацій',
            'attributes'               => 'Властивості публікації',
        ],
        'public'            => true,
        'menu_position'     => 7,
        'menu_icon'         => 'dashicons-pressthis',
        'supports'          => [ 'title', 'editor', 'page-attributes', 'comments' ]
    ] );
}
add_action( 'init', 'register_post_type_rw_olx' );

/**
 * Including files
 */
require_once get_stylesheet_directory() . '/includes/hooks.php';

/**
 * Debug helper
 */
function dumper ( $param ) {
    echo "<pre>";
    var_dump( $param );
    echo "</pre>";
}