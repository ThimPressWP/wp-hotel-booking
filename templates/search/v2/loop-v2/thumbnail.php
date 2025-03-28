<?php
if ( ! isset( $room ) ) {
	return;
}

$gallery  = $room->gallery;
$featured = $gallery ? array_shift( $gallery ) : false;

$w = hb_settings()->get( 'catalog_image_width', 270 );
$h = hb_settings()->get( 'catalog_image_height', 270 );

$room_id = $room->post->ID;

?>

<?php if ( !empty( get_post_thumbnail_id( $room_id ) ) ) : ?>
	<div class="<?php echo esc_attr( apply_filters( 'wphb/filter/loop-v2/thumbnail/class', 'hb-room-thumbnail' ) ); ?>">
		<?php if ( $featured ) : ?>
			<a class="hb-room-gallery"
				data-lightbox="hb-room-gallery[<?php echo esc_attr( $room_id ); ?>]"
				data-title="<?php echo esc_attr( $featured['alt'] ); ?>"
				href="<?php echo esc_attr( $featured['src'] ); ?>">
				<?php $room->getImage( 'catalog' ); ?>
			</a>
		<?php else : ?>
			<a class="hb-room-gallery"
				data-lightbox="hb-room-gallery[<?php echo esc_attr( $room_id ); ?>]"
				data-title="<?php echo esc_attr( $room->post->post_name ); ?>"
				href="<?php echo get_the_post_thumbnail_url( $room_id, 'full' ); ?>">
				<?php echo get_the_post_thumbnail( $room_id, array( $w, $h ) ); ?>
			</a>
		<?php endif; ?>
	</div>
<?php endif; ?>