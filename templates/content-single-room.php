<?php
/**
 * The template for displaying content single room.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/content-single-room.php.
 *
 * @author  ThimPress, leehld
 * @package WP-Hotel-Booking/Templates
 * @version 1.6
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit(); ?>

<?php
/**
 * hotel_booking_before_single_product hook
 */
do_action( 'hotel_booking_before_single_product' );

if ( post_password_required() ) {
	echo get_the_password_form();

	return;
} ?>

<div id="room-<?php the_ID(); ?>" <?php post_class( 'hb_single_room' ); ?>>

	<?php
	/**
	 * hotel_booking_before_loop_room_summary hook
	 */
	do_action( 'hotel_booking_before_single_room' );
	?>

    <div class="summary entry-summary">

		<?php
		/**
		 * hotel_booking_single_room_title hook
		 */
		do_action( 'hotel_booking_single_room_title' );

		/**
		 * hotel_booking_loop_room_price hook
		 */
		do_action( 'hotel_booking_loop_room_price' );

		/**
		 * hotel_booking_single_room_gallery hook
		 */
		do_action( 'hotel_booking_single_room_gallery' );

		/**
		 * hotel_booking_single_room_infomation hook
		 */
		do_action( 'hotel_booking_single_room_infomation' );
		?>

    </div>

	<?php
	/**
	 * hotel_booking_after_single_room hook
	 */
	do_action( 'hotel_booking_after_single_room' );
	?>

</div>

<?php
/**
 * hotel_booking_after_single_product hook
 */
do_action( 'hotel_booking_after_single_product' ); ?>
