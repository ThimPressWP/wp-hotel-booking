<?php
/**
 * gallery lightbox
 *
 * @author 		ThimPress
 * @package 	Tp-hotel-booking/Templates
 * @version     0.9
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

$gallery = $room->gallery;
?>
<div class="hb-room-type-gallery">
    <?php if( $gallery ): foreach( $gallery as $image ){?>
        <a  class="hb-room-gallery" data-fancybox-group="hb-room-gallery-<?php echo esc_attr( $room->post->ID ); ?>" data-lightbox="hb-room-gallery[<?php echo esc_attr( $room->post->ID ); ?>]" data-title="<?php echo esc_attr( $image['alt'] ); ?>" href="<?php echo esc_attr( $image['src'] ); ?>">
            <img src="<?php echo esc_attr( $image['thumb'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" data-id="<?php echo esc_attr( $image['id'] ); ?>" />
        </a>
    <?php } endif; ?>
</div>