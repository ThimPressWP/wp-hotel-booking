<?php
/**
 * Product loop thumbnail
 *
 * @author  ThimPress
 * @package Tp-hotel-booking/Templates
 * @version 2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $hb_settings;
?>
<div class="media">
	<a href="<?php the_permalink(); ?>">
		<?php $size = array( 'width' => $hb_settings->get('catalog_image_width'), 'height' => $hb_settings->get('catalog_image_height') ); ?>
		<?php
			$resizer = HB_Reizer::getInstance( $size );
            $resizer->process( get_post_thumbnail_id( get_the_ID() ), $size );
		?>
		<?php echo apply_filters( 'hotel_booking_loop_room_thumbnail_size', get_the_post_thumbnail( get_the_ID(), array( $size['width'], $size['height'] )) ); ?>
	</a>
</div>