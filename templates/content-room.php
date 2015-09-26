<?php
/**
 * The template for displaying room content in the single-room.php template
 *
 * Override this template by copying it to yourtheme/tp-hotel-booking/content-single-room.php
 *
 * @author 		ThimPress
 * @package 	tp-hotel-booking/templates
 * @version     0.9
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<?php
	/**
	 * hotel_booking_before_single_room hook
	 *
	 */
	 do_action( 'hotel_booking_before_single_room' );

	 if ( post_password_required() ) {
	 	echo get_the_password_form();
	 	return;
	 }
?>

<div id="room-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php
		/**
		 * hotel_booking_before_single_room_summary hook
		 *
		 * @hooked hotel_booking_show_room_sale_flash - 10
		 * @hooked hotel_booking_show_room_images - 20
		 */
		do_action( 'hotel_booking_before_single_room_summary' );
	?>

	<div class="summary entry-summary">

		<?php
			/**
			 * hotel_booking_single_room_summary hook
			 *
			 * @hooked hotel_booking_template_single_title - 5
			 * @hooked hotel_booking_template_single_rating - 10
			 * @hooked hotel_booking_template_single_price - 10
			 * @hooked hotel_booking_template_single_excerpt - 20
			 * @hooked hotel_booking_template_single_add_to_cart - 30
			 * @hooked hotel_booking_template_single_meta - 40
			 * @hooked hotel_booking_template_single_sharing - 50
			 */
			do_action( 'hotel_booking_single_room_summary' );
		?>

	</div><!-- .summary -->

	<?php
		/**
		 * hotel_booking_after_single_room_summary hook
		 *
		 * @hooked hotel_booking_output_room_data_tabs - 10
		 * @hooked hotel_booking_upsell_display - 15
		 * @hooked hotel_booking_output_related_rooms - 20
		 */
		do_action( 'hotel_booking_after_single_room_summary' );
	?>

	<meta itemprop="url" content="<?php the_permalink(); ?>" />

</div><!-- #room-<?php the_ID(); ?> -->

<?php do_action( 'hotel_booking_after_single_room' ); ?>
