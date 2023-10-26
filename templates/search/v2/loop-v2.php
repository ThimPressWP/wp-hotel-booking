<?php
/**
 * The template for displaying search room item loop v2.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/search/loop.php.
 *
 * @author  ThimPress, leehld
 * @package WP-Hotel-Booking/Templates
 * @version 1.6
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

if ( ! isset( $room ) && ! isset( $atts ) ) {
	return;
}

global $hb_settings;
/**
 * @var $hb_settings WPHB_Settings
 */


$custom_process = get_option( 'tp_hotel_booking_custom_process' );
?>

<li class="hb-room clearfix">
	<form name="hb-page-search-results"
			class="hb-page-search-room-results <?php echo ! empty( $custom_process ) ? 'custom-process' : 'extra-option-loop'; ?>">
		<?php do_action( 'hotel_booking_loop_before_item', $room->post->ID ); ?>
		<div class="hb-room-content">
			<?php do_action( 'wphb/loop-v2/room-content', $room ); ?>
		</div>

		<?php wp_nonce_field( 'hb_booking_nonce_action', 'nonce' ); ?>
		<input type="hidden" name="check_in_date"
				value="<?php echo hb_get_request( 'check_in_date' ); ?>"/>
		<input type="hidden" name="check_out_date"
				value="<?php echo hb_get_request( 'check_out_date' ); ?>">
		<input type="hidden" name="room-id" value="<?php echo esc_attr( $room->post->ID ); ?>">
		<input type="hidden" name="hotel-booking" value="cart">
		<input type="hidden" name="action" value="hotel_booking_ajax_add_to_cart"/>

		<?php do_action( 'hotel_booking_loop_after_item', $room->post->ID ); ?>
	</form>

	<?php if ( ( isset( $atts['gallery'] ) && $atts['gallery'] === 'true' ) || $hb_settings->get( 'enable_gallery_lightbox' ) ) { ?>
		<?php hb_get_template( 'loop/gallery-lightbox.php', array( 'room' => $room ) ); ?>
	<?php } ?>
</li>
