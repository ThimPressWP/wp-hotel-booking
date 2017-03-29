<?php

if ( !defined( 'ABSPATH' ) ) {
	exit();
}

$sliderId = 'hotel_booking_slider_' . uniqid();
$items    = isset( $atts['number'] ) ? (int) $atts['number'] : 4;
?>
<div id="<?php echo esc_attr( $sliderId ); ?>" class="hb_room_carousel_container tp-hotel-booking">
	<?php if ( isset( $atts['title'] ) && $atts['title'] ): ?>
        <h3><?php echo esc_html( $atts['title'] ); ?></h3>
	<?php endif; ?>
    <!--navigation-->
	<?php if ( ( !isset( $atts['navigation'] ) || $atts['navigation'] ) && count( $query->posts ) > $items ): ?>
        <div class="navigation owl-buttons">
            <div class="prev"><span class="pe-7s-angle-left"></span></div>
            <div class="next"><span class="pe-7s-angle-right"></span></div>
        </div>
	<?php endif; ?>
    <!--pagination-->
	<?php if ( !isset( $atts['pagination'] ) || $atts['pagination'] ): ?>
        <div class="pagination"></div>
	<?php endif; ?>
    <!--text_link-->
	<?php if ( isset( $atts['text_link'] ) && $atts['text_link'] !== '' ): ?>
        <div class="text_link">
            <a href="<?php echo get_post_type_archive_link( 'hb_room' ); ?>"><?php echo esc_html( $atts['text_link'] ); ?></a>
        </div>
	<?php endif; ?>
    <div class="hb_room_carousel">
		<?php hotel_booking_room_loop_start(); ?>

		<?php while ( $query->have_posts() ) : $query->the_post(); ?>

			<?php hb_get_template_part( 'content', 'room' ); ?>

		<?php endwhile; // end of the loop. ?>

		<?php hotel_booking_room_loop_end(); ?>
		<?php wp_reset_postdata(); ?>
    </div>
</div>
<script type="text/javascript">
	(function ($) {
		"use strict";
		$(document).ready(function () {
			var thimpress_hotel_booking_carousel = $('#<?php echo esc_js( $sliderId ) ?> .hb_room_carousel .rooms');
			thimpress_hotel_booking_carousel.owlCarousel({
				navigation     : <?php echo esc_js( ( !isset( $atts['navigation'] ) || $atts['navigation'] ) ? 'true' : 'false' )  ?>,
				pagination     : <?php echo esc_js( ( !isset( $atts['pagination'] ) || $atts['pagination'] ) ? 'true' : 'false' )  ?>,
				items          : <?php echo esc_js( $items ); ?>,
				paginationSpeed: 600,
				slideSpeed     : 600,
				autoPlay       : true,
				stopOnHover    : true
			});
			// next
			$('#<?php echo esc_js( $sliderId ); ?> .navigation .next').click(function () {
				thimpress_hotel_booking_carousel.trigger('owl.next');
			});
			// prev
			$('#<?php echo esc_js( $sliderId ); ?> .navigation .prev').click(function () {
				thimpress_hotel_booking_carousel.trigger('owl.prev');
			});
		});
	})(jQuery);
</script>