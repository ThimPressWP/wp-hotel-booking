<?php
/**
 * The template for displaying bookings in account page.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/account/bookings.php.
 *
 * @author  ThimPress, leehld
 * @package WP-Hotel-Booking/Templates
 * @version 1.6
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

/**
 * @var $booking WPHB_Booking
 */
$user     = WPHB_User::get_current_user();
$bookings = $user->get_bookings();

if ( ! $bookings ) {
	_e( 'You have no order booking system', 'wp-hotel-booking' );

	return;
} ?>

<div class="hb_booking_wrapper">

	<h2><?php _e( 'Bookings', 'wp-hotel-booking' ); ?></h2>

	<table class="hb_booking_table">
		<thead>
		<tr>
			<th><?php _e( 'ID', 'wp-hotel-booking' ); ?></th>
			<th><?php _e( 'Booking Date', 'wp-hotel-booking' ); ?></th>
			<th><?php _e( 'Total', 'wp-hotel-booking' ); ?></th>
			<th><?php _e( 'Status', 'wp-hotel-booking' ); ?></th>
		</tr>
		</thead>

		<tbody>
		<?php foreach ( $bookings as $k => $booking ) { ?>
			<tr>
				<td><?php printf( '%s', $booking->get_booking_number() ); ?></td>
				<td><?php printf( '%s', date_i18n( hb_get_date_format(), strtotime( $booking->post_date ) ) ); ?></td>
				<td><?php printf( '%s', hb_format_price( $booking->total(), hb_get_currency_symbol( $booking->currency ) ) ); ?></td>
				<td><?php printf( '%s', hb_get_booking_status_label( $booking->id ) ); ?></td>
			</tr>
		<?php } ?>
		</tbody>

	</table>

</div>
