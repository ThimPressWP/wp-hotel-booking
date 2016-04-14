<?php
/**
 * @Author: ducnvtt
 * @Date:   2016-04-14 10:38:17
 * @Last Modified by:   ducnvtt
 * @Last Modified time: 2016-04-14 14:02:42
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

?>

<table class="callout">
	<tr>
		<td class="callout-inner secondary">
			<table class="row">
				<tbody>
					<tr>
						<th class="small-12 large-6 columns first">
							<table>
								<tr>
									<th>
										<p> <strong><?php _e( 'Payment Method', 'tp-hotel-booking' ) ?></strong><br> <?php echo esc_html( $booking->payment_title ) ?> </p>
										<p> <strong><?php _e( 'Email Address', 'tp-hotel-booking' ) ?></strong><br> <?php echo esc_html( $booking->customer_email ) ?> </p>
										<p> <strong><?php _e( 'Booking ID', 'tp-hotel-booking' ) ?></strong><br> <?php echo esc_html( $booking->get_booking_number() ) ?> </p>
									</th>
								</tr>
							</table>
						</th>
						<th class="small-12 large-6 columns last">
							<table>
								<tr>
									<th>
										<p>
											<strong><?php _e( 'Customer Name', 'tp-hotel-booking' ); ?></strong><br>
											<?php printf( '%s', hb_get_customer_fullname( $booking->id ) ) ?><br>
											<strong><?php _e( 'Address', 'tp-hotel-booking' ) ?></strong><br>
											<?php printf( '%s', $booking->customer_country ) ?><br>
											<?php printf( '%s', $booking->customer_address ) ?><br>
											<?php printf( '%s', $booking->customer_city ) ?><br><br>
											<?php printf( '%s', $booking->customer_state ) ?>
										</p>
									</th>
								</tr>
							</table>
						</th>
					</tr>
				</tbody>
			</table>
		</td>
		<td class="expander"></td>
	</tr>
</table>