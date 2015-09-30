<?php
/**
 * The Template for displaying all single products.
 *
 * Override this template by copying it to yourtheme/tp-hotel-booking/templates/single-product.php
 *
 * @author 		ThimPress
 * @package 	tp-hotel-booking/templates
 * @version     0.9
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$room = HB_Room::instance(get_the_ID());
$galeries = $room->get_gallery(false);

global $hb_settings;
?>

<?php $size = array( $hb_settings->get('room_image_gallery_width'), $hb_settings->get('room_image_gallery_height') ); ?>

<div class="hb_room_gallery camera_wrap camera_emboss" id="camera_wrap_<?php the_ID() ?>">
	<?php foreach ($galeries as $key => $gallery): ?>
		<?php $src = wp_get_attachment_image_src( $gallery['id'], $size ); ?>
	    <div data-thumb="<?php echo esc_attr( $gallery['thumb'] ); ?>" data-src="<?php echo apply_filters( 'hotel_booking_room_gallery_size', $src[0]); ?>"></div>
	<?php endforeach; ?>
</div>

<script type="text/javascript">
	(function($){
		"use strict";
		$(document).ready(function(){
			$('#camera_wrap_<?php the_ID() ?>').camera({
				height: '400px',
				loader: 'bar',
				pagination: false,
				thumbnails: true
			});
		});
	})(jQuery);
</script>