<?php
if ( ! isset( $room ) ) {
	return;
}

$gallery  = $room->gallery;
$featured = $gallery ? array_shift( $gallery ) : false;

$w = hb_settings()->get( 'catalog_image_width', 270 );
$h = hb_settings()->get( 'catalog_image_height', 270 );
?>
<div class="<?php echo esc_attr( apply_filters( 'wphb/filter/loop-v2/thumbnail/class', 'hb-room-thumbnail' ) ); ?>">
	<?php if ( $featured ) : ?>
        <a class="hb-room-gallery"
           data-lightbox="hb-room-gallery[<?php echo esc_attr( $room->post->ID ); ?>]"
           data-title="<?php echo esc_attr( $featured['alt'] ); ?>"
           href="<?php echo esc_attr( $featured['src'] ); ?>">
			<?php $room->getImage( 'catalog' ); ?>
        </a>
	<?php else : ?>
        <a class="hb-room-gallery"
           data-lightbox="hb-room-gallery[<?php echo esc_attr( $room->post->ID ); ?>]"
           data-title="<?php echo esc_attr( $room->post->post_name ); ?>"
           href="<?php echo get_the_post_thumbnail_url( $room->post->ID, 'full' ); ?>">
			<?php echo get_the_post_thumbnail( $room->post->ID, array( $w, $h ) ); ?>
        </a>
	<?php endif; ?>
</div>
