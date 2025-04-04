<?php
/**
 * The template for displaying single room gallery.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/single-room/gallery.php.
 *
 * @author  ThimPress, leehld
 * @package WP-Hotel-Booking/Templates
 * @version 1.6.1
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

global $hb_room;
/**
 * @var $hb_room WPHB_Room
 */
$galleries    = $hb_room->get_galleries( false );
$has_featured = get_the_post_thumbnail( $hb_room->ID ) ? true : false;
$url          = get_post_meta( $hb_room->ID, '_hb_room_preview_url', true );

/** update new gallery replace old camera gallery
	* if customers still want to use camera_gallery: add_filter('wp_hotel_booking_ft_camera_gallery', '__return_true');
	* @see __return_true(), add_filter()
*/
$camera_gallery = apply_filters( 'wp_hotel_booking_ft_camera_gallery', false );

?>

<?php if ( $camera_gallery ) : // camera gallery ?>
	<div class="spacing-35"></div>
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
							thumbnails: true,
							onLoaded: function () {
							setTimeout(function () { // fix thumbnails not show in first slide
								$('.camera_fakehover').css('height', '470px');
								$('.camera_thumbs_cont').fadeIn();
							}, 500);
						}
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
<?php else : // new gallery ?> 
	<div class="spacing-35"></div>
	<?php if ( $hb_room->is_preview ) { ?>
		<?php if ( ( $has_featured || ( count( $galleries ) > 0 ) ) && $url ) : ?>
			<ul class="hb_single_room_tabs images_video_tabs">
				<li>
					<a href="#hb_room_images" class="active"> 
						<?php
						if ( count( $galleries ) > 0 ) {
							echo esc_html__( 'All Images', 'wp-hotel-booking' ) . ' ' . '(' . count( $galleries ) . ')';
						} else {
							echo esc_html__( 'Image', 'wp-hotel-booking' );
						}
						?>
					</a>
				</li>
				<li>
					<a href="#hb_room_video"> 
						<?php esc_html_e( 'Video', 'wp-hotel-booking' ); ?>
					</a>
				</li>
			</ul>
		<?php endif; ?>

		<div id="hb_room_video" class="room_media_content">
			<?php
				$video_html = '';

			if ( strpos( $url, '<iframe' ) !== false ) {
				// If the URL contains an iframe, output the iframe
				$video_html .= $url;
			} elseif ( preg_match( '/\.(mp4|webm|ogg)$/', $url ) ) {
				// If the URL is a direct video link
				$video_html .= '<video width="600" controls>
					<source src="' . htmlspecialchars( $url ) . '" type="video/mp4">
					Your browser does not support the video tag.
					</video>';
			} elseif ( strpos( $url, 'youtube.com' ) !== false || strpos( $url, 'vimeo.com' ) !== false ) {
				// If it's a YouTube or Vimeo link, embed the video
				$embedUrl = '';
				if ( strpos( $url, 'youtube.com' ) !== false ) {
					// Convert YouTube link to embed format
					preg_match( '/v=([^&]+)/', $url, $matches );
					if ( isset( $matches[1] ) ) {
						$embedUrl = 'https://www.youtube.com/embed/' . $matches[1];
					}
				} elseif ( strpos( $url, 'vimeo.com' ) !== false ) {
					// Convert Vimeo link to embed format
					preg_match( '/vimeo\.com\/(\d+)/', $url, $matches );
					if ( isset( $matches[1] ) ) {
						$embedUrl = 'https://player.vimeo.com/video/' . $matches[1];
					}
				}

				if ( $embedUrl ) {
					$video_html .= '<iframe width="100%" height="373" src="' . htmlspecialchars( $embedUrl ) . '" frameborder="0" allowfullscreen></iframe>';
				}
			} else {
				$video_html .= 'Unable to display the video. The URL is not valid.';
			}
				echo $video_html;
			?>
		</div>
	<?php } ?>

	<div id="hb_room_images" class="room_media_content">

		<?php if ( $galleries ) { ?>
			<script type="text/javascript">
				(function ($) {
					"use strict";
					$(document).ready(function () {
						if ($('#carousel').length) {
							$('#carousel').flexslider({
								animation: "slide",
								controlNav: false,
								slideshow: false,
								itemWidth: 210,
								itemMargin: 20,
								asNavFor: '#slider',
							});
						} 
						if ($('#slider').length) {
							$('#slider').flexslider({
								controlNav: false,
								slideshow: false,
								sync: "#carousel",
							});
						}
					});
				})(jQuery);
			</script>

			<div class="hb_room_gallery flexslider" id="slider">
				<ul class="slides">
					<?php foreach ( $galleries as $key => $gallery ) : ?>
						<li><img src="<?php echo esc_url( $gallery['src'] ); ?>"></li>
					<?php endforeach; ?>
				</ul>
			</div>
			<div class="hb_room_gallery flexslider" id="carousel"> 
				<ul class="slides">
					<?php foreach ( $galleries as $key => $gallery ) : ?>
						<li><img src="<?php echo esc_url( $gallery['thumb'] ); ?>"></li>
					<?php endforeach; ?>
				</ul>
			</div>
			<?php
		} else {
			echo get_the_post_thumbnail( get_the_ID() );
		}
		?>
	</div>
<?php endif; ?>