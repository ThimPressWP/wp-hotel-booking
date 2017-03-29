<?php

if ( !defined( 'ABSPATH' ) ) {
	exit();
}

?>
<div id="hotel_booking_lastest_reviews-<?php echo uniqid(); ?>" class="hotel_booking_lastest_reviews tp-hotel-booking">
	<?php if ( isset( $atts['title'] ) && $atts['title'] ): ?>
        <h3><?php echo esc_html( $atts['title'] ); ?></h3>
	<?php endif; ?>
	<?php hotel_booking_room_loop_start(); ?>

	<?php while ( $query->have_posts() ) : $query->the_post(); ?>

		<?php hb_get_template_part( 'content', 'room' ); ?>

	<?php endwhile; // end of the loop. ?>

	<?php hotel_booking_room_loop_end(); ?>
</div>