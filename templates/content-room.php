<?php
/**
 * The template for displaying room content in the archive-room.php template
 *
 * Override this template by copying it to yourtheme/tp-hotel-booking/content-room.php
 *
 * @author        ThimPress
 * @package       wp-hotel-booking/templates
 * @version       1.6
 */

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<li id="room-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php
	/**
	 * hotel_booking_before_loop_room_summary hook
	 *
	 * @hooked hotel_booking_show_room_sale_flash - 10
	 * @hooked hotel_booking_show_room_images - 20
	 */
	do_action( 'hotel_booking_before_loop_room_item' );
	?>

    <div class="summary entry-summary">

		<?php
		/**
		 * hotel_booking_loop_room_thumbnail hook
		 */
		do_action( 'hotel_booking_loop_room_thumbnail' );

		/**
		 * hotel_booking_loop_room_title hook
		 */
		do_action( 'hotel_booking_loop_room_title' );

		/**
		 * hotel_booking_loop_room_price hook
		 */
		do_action( 'hotel_booking_loop_room_price' );

		/**
		 * hotel_booking_loop_room_price hook
		 */
		do_action( 'hotel_booking_loop_room_rating' );
		?>

    </div><!-- .summary -->

	<?php
	/**
	 * hotel_booking_after_loop_room_item hook
	 *
	 * @hooked hotel_booking_show_room_sale_flash - 10
	 * @hooked hotel_booking_show_room_images - 20
	 */
	do_action( 'hotel_booking_after_loop_room_item' );
	?>

</li>

<?php do_action( 'hotel_booking_after_loop_room' ); ?>
