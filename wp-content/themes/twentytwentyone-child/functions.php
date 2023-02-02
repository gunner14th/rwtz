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
    wp_enqueue_script('common-js', _THEME_URI . '/dist/admin-js/admin-scripts.min.js', [ 'jquery' ], time());
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
 * Custom fields for posts
 */
add_action( 'add_meta_boxes', 'rw_olx_image_add_metabox' );
function rw_olx_image_add_metabox () {
    add_meta_box( 'rw_olx_image', __( 'Publication Image', 'twentytwentyone-child' ), 'rw_olx_image_metabox', 'rw_olx', 'side', 'low');
}
function rw_olx_image_metabox ( $post ) {
    global $content_width, $_wp_additional_image_sizes;

    $image_id = get_post_meta( $post->ID, '_rw_olx_image_id', true );

    $old_content_width = $content_width;
    $content_width = 254;

    if ( $image_id && get_post( $image_id ) ) {

        if ( ! isset( $_wp_additional_image_sizes['post-thumbnail'] ) ) {
            $thumbnail_html = wp_get_attachment_image( $image_id, array( $content_width, $content_width ) );
        } else {
            $thumbnail_html = wp_get_attachment_image( $image_id, 'post-thumbnail' );
        }

        if ( ! empty( $thumbnail_html ) ) {
            $content = $thumbnail_html;
            $content .= '<p class="hide-if-no-js"><a href="javascript:;" id="remove_rw_olx_image_button" >' . esc_html__( 'Remove image', 'twentytwentyone-child' ) . '</a></p>';
            $content .= '<input type="hidden" id="upload_rw_olx_image" name="_rw_olx_cover_image" value="' . esc_attr( $image_id ) . '" />';
        }

        $content_width = $old_content_width;
    }
    else {

        $content = '<img src="" style="width:' . esc_attr( $content_width ) . 'px;height:auto;border:0;display:none;" />';
        $content .= '<p class="hide-if-no-js"><a title="' . esc_attr__( 'Set image', 'text-domain' ) . '" href="javascript:;" id="upload_rw_olx_image_button" id="set-rw_olx-image" data-uploader_title="' . esc_attr__( 'Choose an image', 'twentytwentyone-child' ) . '" data-uploader_button_text="' . esc_attr__( 'Set listing image', 'twentytwentyone-child' ) . '">' . esc_html__( 'Set image', 'twentytwentyone-child' ) . '</a></p>';
        $content .= '<input type="hidden" id="upload_rw_olx_image" name="_rw_olx_cover_image" value="" />';

    }

    echo $content;
}
add_action( 'save_post', 'rw_olx_image_save', 10, 1 );
function rw_olx_image_save ( $post_id ) {
    if( isset( $_POST['_rw_olx_cover_image'] ) ) {
        $image_id = (int) $_POST['_rw_olx_cover_image'];
        update_post_meta( $post_id, '_rw_olx_image_id', $image_id );
    }
}

/**
 * Debug helper
 */
function dumper ( $param ) {
    echo "<pre>";
    var_dump( $param );
    echo "</pre>";
}