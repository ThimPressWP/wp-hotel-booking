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

<?php if ( $galleries ) { ?>
    <div class="hb_room_gallery camera_wrap camera_emboss" id="camera_wrap_<?php the_ID() ?>">
		<?php foreach ( $galleries as $key => $gallery ) { ?>
            <div data-thumb="<?php echo esc_url( $gallery['thumb'] ); ?>"
                 data-src="<?php echo esc_url( $gallery['src'] ); ?>"></div>
		<?php } ?>
    </div>

    <script type="text/javascript">
        (function ($) {
            "use strict";
            $(document).ready(function () {
                $('#camera_wrap_<?php the_ID() ?>').camera({
                    height: '470px',
                    loader: 'none',
                    pagination: false,
                    thumbnails: true
                });
            });
        })(jQuery);
    </script>
<?php } else {
	echo get_the_post_thumbnail( get_the_ID() );
} ?>