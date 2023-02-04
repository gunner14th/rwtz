<?php

if ( ! defined('ABSPATH') ) { die(); }

/**
 * Custom fields for posts
 */
function rw_olx_image_add_metabox () {
    add_meta_box( 'rw_olx_image', __( 'Publication Image', 'twentytwentyone-child' ), 'rw_olx_image_metabox', 'rw_olx', 'side', 'low');
}
add_action( 'add_meta_boxes', 'rw_olx_image_add_metabox' );

function rw_olx_image_metabox ( $post ) {
    global $content_width, $_wp_additional_image_sizes;

    $image_id = get_post_meta( $post->ID, '_rw_olx_image_id', true );

    $content = '';
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

/**
 * @param $post_id
 * @return void
 * Update rw_olx custom image
 */
function rw_olx_image_save ( $post_id ) {
    if( isset( $_POST['_rw_olx_cover_image'] ) ) {
        $image_id = (int) $_POST['_rw_olx_cover_image'];
        update_post_meta( $post_id, '_rw_olx_image_id', $image_id );
    }
}
add_action( 'save_post_rw_olx', 'rw_olx_image_save', 10, 1 );

/**
 * Sending an email to admin when creating a rw_olx
 */
add_action('transition_post_status', 'send_new_post', 10, 3);
// Listen for publishing of a new post
function send_new_post($new_status, $old_status, $post) {
    if('publish' === $new_status && 'publish' !== $old_status && $post->post_type === 'rw_olx') {
        $post_title = $post->post_title;
        $post_url = get_permalink( $post->post_id );
        $post_author_email = get_userdata(intval($post->post_author))->user_email;
        $subject = 'Публікацію додали';

        $message = "На вашому сайті була створена наступна публікація:\n\n";
        $message .= $post_title . ": " . $post_url . "\n";

        // Send email to admin
        wp_mail( get_option('admin_email'), $subject, $message );

        // Setup cron task for publication author
        wp_schedule_single_event( time() + 120, 'send_new_post_to_author', [ $post_author_email, $post_title, $post_url ] );
    }
}

/**
 * Action for cron task for publication author
 */
add_action( 'send_new_post_to_author', 'send_new_post_to_author_func', 10, 3 );
function send_new_post_to_author_func( $post_author_email, $post_title, $post_url ){
    $subject = 'Публікація додана';
    $message = "Ваша публікація успішно додана:\n\n";
    $message .= $post_title . ": " . $post_url . "\n";
    wp_mail( $post_author_email, $subject, $message );
}

/**
 * PHPMailer config
 */
function rwtz_phpmailer_smtp( $phpmailer ) {
    $phpmailer->isSMTP();
    $phpmailer->Host = SMTP_server;
    $phpmailer->SMTPAuth = SMTP_AUTH;
    $phpmailer->Port = SMTP_PORT;
    $phpmailer->Username = SMTP_username;
    $phpmailer->Password = SMTP_password;
    $phpmailer->SMTPSecure = SMTP_SECURE;
    $phpmailer->From = SMTP_FROM;
    $phpmailer->FromName = SMTP_NAME;
}
add_action( 'phpmailer_init', 'rwtz_phpmailer_smtp' );