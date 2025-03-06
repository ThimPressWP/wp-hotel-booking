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
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M464 256A208 208 0 1 0 48 256a208 208 0 1 0 416 0zM0 256a256 256 0 1 1 512 0A256 256 0 1 1 0 256zM188.3 147.1c7.6-4.2 16.8-4.1 24.3 .5l144 88c7.1 4.4 11.5 12.1 11.5 20.5s-4.4 16.1-11.5 20.5l-144 88c-7.4 4.5-16.7 4.7-24.3 .5s-12.3-12.2-12.3-20.9l0-176c0-8.7 4.7-16.7 12.3-20.9z"/></svg>
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
