<?php

/* Template Name: Publications */

get_header();

$publications = get_posts([
    'post_type' => 'rw_olx',
    'numberposts' => -1,
    'post_status' => 'publish'
]);

?>

    <div class="container">
        <div class="row">
            <div class="col-12 page-title">
                <?php the_title('<h1>', '</h1>'); ?>
            </div>
        </div>
        <div class="row publications-content">
            <?php if ( ! empty($publications) ) : ?>
                <?php foreach ( $publications as $publication ) : ?>
                    <?php $publicationImgID = get_post_meta( $publication->ID, '_rw_olx_image_id', true ); ?>
                    <?php $publicationImgURL = $publicationImgID ? wp_get_attachment_url( intval($publicationImgID) ) : _THEME_URI . '/assets/img/noimage.jpg'; ?>
                    <div class="col-12 col-md-3 publication">
                        <a href="<?php echo get_permalink($publication->ID); ?>" class="pub-content-box">
                            <div class="img-wrap">
                                <img src="<?php echo $publicationImgURL; ?>" alt="pub-img">
                            </div>
                            <h2 class="pub-title">
                                <?php echo $publication->post_title; ?>
                            </h2>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <h2>
                    <?php esc_html_e( 'Немає публікацій', 'twentytwentyone-child' ); ?>
                </h2>
            <?php endif; ?>
        </div>
    </div>

<?php

get_footer();