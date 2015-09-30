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
$size = array(
	'width' 	=> $hb_settings->get('room_image_gallery_width'),
	'height'	=> $hb_settings->get('room_image_gallery_height')
);
// resizer class
$resizer = HB_Reizer::getInstance( $size );
?>

<?php if( $galeries ): ?>
	<div class="hb_room_gallery camera_wrap camera_emboss" id="camera_wrap_<?php the_ID() ?>">
		<?php foreach ($galeries as $key => $gallery): ?>
			<?php $src = $resizer->process( $gallery['id'], $size ); ?>
		    <div data-thumb="<?php echo esc_attr( $gallery['thumb'] ); ?>" data-src="<?php echo apply_filters( 'hotel_booking_room_gallery_size', $src); ?>"></div>
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
<?php endif; ?>