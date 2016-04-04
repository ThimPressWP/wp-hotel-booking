<?php
/**
 * @Author: ducnvtt
 * @Date:   2016-03-25 09:32:53
 * @Last Modified by:   ducnvtt
 * @Last Modified time: 2016-04-04 17:25:25
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
					<div class="customer_details">
						<select name="_hb_user_id" id="_hb_user_id"></select>
					</div>
				</li>
			</ul>
		</div>

		<div class="section">

			<h4>
				<?php _e( 'Customer\'s Details', 'tp-hotel-booking' ); ?>
				<a href="#" class="edit" data-id="30"><i class="fa fa-pencil"></i></a>
			</h4>
			<div class="customer_details">
				<div class="address">
					<strong><?php _e( 'Address', 'tp-hotel-booking' ); ?></strong>
					<br />
					<small><?php printf( '%s', hb_get_customer_fullname( $post->ID, true ) ); ?></small>
					<br />
					<small><?php printf( '%s', $booking->customer_address ) ?></small>
					<br />
					<small><?php printf( '%s', $booking->customer_city ) ?></small>
					<br />
					<small><?php printf( '%s', $booking->customer_state ) ?></small>
					<br />
					<small><?php printf( '%s', $booking->customer_postal_code ) ?></small>
					<br />
					<?php $customer_email = $booking->user_id ? HB_User::instance( $booking->user_id )->user_email : $booking->customer_email; ?>
					<strong><?php _e( 'Email', 'tp-hotel-booking' ) ?></strong>
					<br />
					<a href="mailto:<?php echo esc_attr( $customer_email ) ?>"><?php printf( '%s', $customer_email ) ?></a>
					<br />
					<strong><?php _e( 'Phone', 'tp-hotel-booking' ) ?></strong>
					<br />
					<small><?php printf( '%s', $booking->customer_phone ) ?></small>
				</div>
				<div class="edit_address">

				</div>
			</div>

		</div>

		<div class="section">

			<h4>
				<?php _e( 'Customer\'s Notes', 'tp-hotel-booking' ); ?>
				<a href="#" class="edit" data-id="30"><i class="fa fa-pencil"></i></a>
			</h4>
			<div class="customer_details">
				<p><?php printf( '%s', $post->post_content ) ?></p>
			</div>

		</div>
	</div>

</div>
