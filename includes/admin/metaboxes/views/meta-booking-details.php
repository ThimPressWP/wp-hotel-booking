<?php
/**
 * @Author: ducnvtt
 * @Date:   2016-03-25 09:32:53
 * @Last Modified by:   ducnvtt
 * @Last Modified time: 2016-04-01 17:18:54
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

global $post;
$booking = HB_Booking::instance( $post->ID );
$customers = hb_get_customers();
?>

<style type="text/css">
	#normal-sortables,
	#hb-booking-details .ui-sortable-handle{
		display: none;
	}
</style>
<div id="booking_details">
	<h2 class="hb_meta_title">
		<?php printf( __( 'Book ID %s', 'tp-hotel-booking' ), hb_format_order_number( $post->ID ) ) ?>
	</h2>
	<p class="description"><?php printf( __( 'Booked on %s', 'tp-hotel-booking' ), $post->post_date ) ?></p>
	<div id="booking_details_section">

		<div class="section">
			<h4><?php _e( 'General', 'tp-hotel-booking' ); ?></h4>
			<ul>
				<li>
					<label><?php _e( 'Payment Method:', 'tp-hotel-booking' ); ?></label>
					<select name="_hb_method">
						<?php $methods = hb_get_payment_gateways( array( 'enable' => true ) ); ?>
						<?php foreach ( $methods as $id => $method ) : ?>

							<option value="<?php echo esc_attr( $id ) ?>" <?php selected( $post->method, $id ); ?>><?php printf( '%s(%s)', $method->title, $method->description ) ?></option>

						<?php endforeach; ?>
					</select>
				</li>
				<li>
					<label><?php _e( 'Booking Status:', 'tp-hotel-booking' ); ?></label>
					<select name="_hb_booking_status">
						<?php $status = hb_get_booking_statuses(); ?>
						<?php foreach ( $status as $st => $status ) : ?>

							<option value="<?php echo esc_attr( $st ) ?>" <?php selected( $post->post_status, $st ); ?>><?php printf( '%s', $status ) ?></option>

						<?php endforeach; ?>
					</select>
				</li>
				<li>
					<label><?php _e( 'Customer:', 'tp-hotel-booking' ); ?></label>
					<select name="_hb_customer_id">
						<option value=""><?php _e( '---None---', 'tp-hotel-booking' ); ?></option>
						<?php foreach ( $customers as $cus ) : ?>
							<option value="<?php echo esc_attr( $cus->ID ) ?>" <?php selected( $booking->customer_id, $cus->ID ); ?>><?php printf( '%s', hb_get_customer_fullname( $cus->ID ) ) ?></option>
						<?php endforeach; ?>
					</select>
				</li>
			</ul>
		</div>

		<div class="section">

			<h4><?php _e( 'Customer\'s Details', 'tp-hotel-booking' ); ?></h4>
			<div class="customer_details">
				<strong></strong>
			</div>

		</div>

	</div>

</div>
