<?php
/**
 * gallery lightbox
 *
 * @author        ThimPress
 * @package       wp-hotel-booking/templates
 * @version       1.1.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

$gallery = $room->gallery; ?>
<?php if ( $gallery ): ?>
    <div class="hb-room-type-gallery">
		<?php foreach ( $gallery as $image ) {
			if ( $image != $gallery[0] ) {
				?>
                <a class="hb-room-gallery"
                   data-fancybox-group="hb-room-gallery-<?php echo esc_attr( $room->post->ID ); ?>"
                   data-lightbox="hb-room-gallery[<?php echo esc_attr( $room->post->ID ); ?>]"
                   data-title="<?php echo esc_attr( $image['alt'] ); ?>" href="<?php echo esc_url( $image['src'] ); ?>">
                    <img src="<?php echo esc_url( $image['thumb'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>"
                         data-id="<?php echo esc_attr( $image['id'] ); ?>"/>
                </a>
			<?php }
		} ?>
    </div>
<?php endif; ?>