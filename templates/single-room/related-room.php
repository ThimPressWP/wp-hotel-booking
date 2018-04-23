<?php

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$room    = WPHB_Room::instance( get_the_ID() );
$related = $room->get_related_rooms();
?>
<?php if ( $related->posts ): ?>
    <div class="hb_related_other_room has_slider">
        <h3 class="title"><?php _e( 'Other Rooms', 'wp-hotel-booking' ); ?></h3>
		<?php if ( count( $related->posts ) > 3 ) : ?>
            <div class="navigation">
                <div class="prev"><span class="pe-7s-angle-left"></span></div>
                <div class="next"><span class="pe-7s-angle-right"></span></div>
            </div>
		<?php endif; ?>
		<?php hotel_booking_room_loop_start(); ?>

		<?php while ( $related->have_posts() ) : $related->the_post(); ?>

			<?php hb_get_template_part( 'content', 'room' ); ?>

		<?php endwhile; // end of the loop. ?>

		<?php hotel_booking_room_loop_end(); ?>
    </div>

    <script type="text/javascript">
		(function ($) {
			"use strict";
			$(document).ready(function () {
				var thimpress_hotel_booking_carousel_related = $('.hb_related_other_room .rooms');
				thimpress_hotel_booking_carousel_related.owlCarousel({
					navigation     : false,
					pagination     : false,
					items          : 3,
					paginationSpeed: 600,
					slideSpeed     : 600,
					autoPlay       : true,
					stopOnHover    : true
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
<?php endif; ?>
