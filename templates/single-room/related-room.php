<?php
/**
 * The template for displaying related room in single room page.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/single-room/related-room.php.
 *
 * @author  ThimPress, leehld
 * @package WP-Hotel-Booking/Templates
 * @version 1.6
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

$room    = WPHB_Room::instance( get_the_ID() );
$related = $room->get_related_rooms();

/**
 * @var $related WP_Query
 */
?>

<?php if ( $related->posts ) { ?>
    <div class="hb_related_other_room has_slider">
        <h3 class="title"><?php _e( 'Other Rooms', 'wp-hotel-booking' ); ?></h3>

		<?php if ( count( $related->posts ) > 3 ) { ?>
            <div class="navigation">
                <div class="prev"><span class="pe-7s-angle-left"></span></div>
                <div class="next"><span class="pe-7s-angle-right"></span></div>
            </div>
		<?php } ?>

		<?php hotel_booking_room_loop_start(); ?>

		<?php while ( $related->have_posts() ) : $related->the_post(); ?>
			<?php hb_get_template_part( 'content', 'room' ); ?>
		<?php endwhile; ?>

		<?php hotel_booking_room_loop_end(); ?>
    </div>

    <script type="text/javascript">
        (function ($) {
            "use strict";
            $(document).ready(function () {
                var thimpress_hotel_booking_carousel_related = $('.hb_related_other_room .rooms');
                thimpress_hotel_booking_carousel_related.owlCarousel({
                    navigation: false,
                    pagination: false,
                    items: 3,
                    paginationSpeed: 600,
                    slideSpeed: 600,
                    autoPlay: true,
                    stopOnHover: true
                });
                // next
                $('.hb_related_other_room .navigation .next').click(function () {
                    thimpress_hotel_booking_carousel_related.trigger('owl.next');
                });
                // prev
                $('.hb_related_other_room .navigation .prev').click(function () {
                    thimpress_hotel_booking_carousel_related.trigger('owl.prev');
                });
            });
        })(jQuery);
    </script>
<?php } ?>
