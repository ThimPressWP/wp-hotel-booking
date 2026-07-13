<?php
/**
 * The template for displaying order pay.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/checkout/order-pay.php.
 *
 * @author  ThimPress, leehld
 * @package WP-Hotel-Booking/Templates
 * @version 1.6
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit; ?>

<?php $booking = WPHB_Booking::instance( $booking_id ); ?>

<?php $currency_symbol = hb_get_currency_symbol( $booking->currency ); ?>

<?php if ( $booking->get_status() !== 'completed' ) { ?>

	<?php do_action( 'hotel_booking_order_pay_before' ); ?>

	<h3><?php printf( __( 'Booking ID: %s', 'wp-hotel-booking' ), esc_html( $booking->get_booking_number() ) ); ?></h3>
	<p>
		<strong><?php esc_html_e( 'Payment status: ', 'wp-hotel-booking' ); ?></strong>
		<?php printf( '%s', ucfirst( $booking->get_status() ) ); ?>
	</p>
	<p>
		<strong><?php esc_html_e( 'Booking Date: ', 'wp-hotel-booking' ); ?></strong>
		<?php printf( '%s', get_the_date( '', $booking->id ) ); ?>
	</p>
	<p>
		<strong><?php esc_html_e( 'Payment Method: ', 'wp-hotel-booking' ); ?></strong>
		<?php printf( '%s', esc_html( $booking->method_title ) ); ?>
	</p>
	<p>
		<strong><?php esc_html_e( 'Total: ', 'wp-hotel-booking' ); ?></strong>
		<?php printf( '%s', wp_kses_post( hb_format_price( hb_booking_total( $booking_id ), $currency_symbol ) ) ); ?>
	</p>
	<p>
		<strong><?php esc_html_e( 'Advance Payment: ', 'wp-hotel-booking' ); ?></strong>
		<?php printf( '%s', wp_kses_post( hb_format_price( $booking->advance_payment, $currency_symbol ) ) ); ?>
	</p>

	<?php do_action( 'hotel_booking_order_pay_after' ); ?>

<?php } else { ?>

	<h3><?php printf( __( '%s was pay completed', 'wp-hotel-booking' ), esc_html( $booking->get_booking_number() ) ); ?></h3>

<?php } ?>
