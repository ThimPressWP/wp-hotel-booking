<?php
/**
 * The template for displaying single room gallery.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/single-room/gallery.php.
 *
 * @author  ThimPress, leehld
 * @package WP-Hotel-Booking/Templates
 * @version 1.6
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

global $hb_room;
/**
 * @var $hb_room WPHB_Room
 */
$galleries = $hb_room->get_galleries( false );

?>
<div id="hb_room_images"> 
	<?php if ( $galleries ) { ?>
		<?php
		if ( $hb_room->is_preview ) :
			$preview = get_post_meta( $hb_room->ID, '_hb_room_preview_url', true );
			?>
			<span class="room-preview" data-preview="<?php echo ! empty( $preview ) ? esc_attr( $preview ) : ''; ?>">
				<i class="fa fa-play-circle-o" aria-hidden="true"></i>
			</span>
		<?php endif; ?>
		<div class="hb_room_gallery camera_wrap camera_emboss" id="camera_wrap_<?php the_ID(); ?>">
			<?php foreach ( $galleries as $key => $gallery ) { ?>
				<div data-thumb="<?php echo esc_url( $gallery['thumb'] ); ?>"
					data-src="<?php echo esc_url( $gallery['src'] ); ?>"></div>
			<?php } ?>
		</div>

		<script type="text/javascript">
			(function ($) {
				"use strict";
				$(document).ready(function () {
					$('#camera_wrap_<?php the_ID(); ?>').camera({
						height: '470px',
						loader: 'none',
						pagination: false,
						thumbnails: true
					});
				});
			})(jQuery);
		</script>
		<?php
	} else {
		echo get_the_post_thumbnail( get_the_ID() );
	}
	?>
</div>
