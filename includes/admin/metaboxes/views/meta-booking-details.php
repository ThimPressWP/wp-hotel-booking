<?php
/**
 * @Author: ducnvtt
 * @Date:   2016-03-25 09:32:53
 * @Last Modified by:   ducnvtt
 * @Last Modified time: 2016-04-08 08:28:01
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
	<?php wp_nonce_field( 'hotel-booking-metabox-booking-details', 'hotel_booking_metabox_booking_details_nonce' ); ?>
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
					<?php $methods = hb_get_payment_gateways(); ?>
					<select name="_hb_method">
						<?php if ( $booking->method && ! array_key_exists( $booking->method, $methods ) ) : ?>
							<option value="<?php echo esc_attr( $booking->method ) ?>" selected><?php printf( __( '%s is not available', 'tp-hotel-booking' ), $booking->method_title ) ?></option>
						<?php endif; ?>
						<?php foreach ( $methods as $id => $method ) : ?>
							<?php if ( $post->method === $id ) : ?>

								<?php $selected = true; ?>

							<?php endif; ?>
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
						<select name="_hb_user_id" id="_hb_user_id">
							<?php if ( $booking->user_id ) : ?>
								<?php $user = get_userdata( $booking->user_id ); ?>
								<option value="<?php echo esc_attr( $booking->user_id ) ?>" selected><?php printf( '%s(#%s %s)', $user->user_login, $booking->user_id, $user->user_email ) ?></option>
							<?php endif; ?>
						</select>
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
				<div class="address details">
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
					<small><?php printf( '%s', $booking->customer_country ) ?></small>
					<br />
					<?php $customer_email = $booking->user_id ? HB_User::get_user( $booking->user_id )->user_email : $booking->customer_email; ?>
					<strong><?php _e( 'Email', 'tp-hotel-booking' ) ?></strong>
					<br />
					<a href="mailto:<?php echo esc_attr( $customer_email ) ?>"><?php printf( '%s', $customer_email ) ?></a>
					<br />
					<strong><?php _e( 'Phone', 'tp-hotel-booking' ) ?></strong>
					<br />
					<small><?php printf( '%s', $booking->customer_phone ) ?></small>
				</div>
				<div class="edit_details">
					<div class="edit_col">
						<?php hb_dropdown_titles( array( 'name' => '_hb_customer_title', 'class' => 'normal', 'selected' => $booking->customer_title ) ); ?>
						<input type="text" name="_hb_customer_first_name" id="_hb_customer_first_name" value="<?php echo esc_attr( $booking->customer_first_name ) ?>" placehoder="<?php esc_attr_e( 'First name', 'tp-hotel-booking' ); ?>"/>
						<input type="text" name="_hb_customer_last_name" id="_hb_customer_last_name" value="<?php echo esc_attr( $booking->customer_last_name ) ?>" placehoder="<?php esc_attr_e( 'Last name', 'tp-hotel-booking' ); ?>"/>
						<input type="text" name="_hb_customer_address" id="_hb_customer_address" value="<?php echo esc_attr( $booking->customer_address ) ?>" placehoder="<?php esc_attr_e( 'Address', 'tp-hotel-booking' ); ?>"/>
						<input type="text" name="_hb_customer_city" id="_hb_customer_city" value="<?php echo esc_attr( $booking->customer_city ) ?>" placehoder="<?php esc_attr_e( 'City', 'tp-hotel-booking' ); ?>"/>
					</div>
					<div class="edit_col">
						<input type="text" name="_hb_customer_state" id="_hb_customer_state" value="<?php echo esc_attr( $booking->customer_state ) ?>" placehoder="<?php esc_attr_e( 'State', 'tp-hotel-booking' ); ?>"/>
						<input type="text" name="_hb_customer_postal_code" id="_hb_customer_postal_code" value="<?php echo esc_attr( $booking->customer_postal_code ) ?>" placehoder="<?php esc_attr_e( 'Postl code', 'tp-hotel-booking' ); ?>"/>
						<input type="email" placeholder="<?php esc_attr_e( 'Email address', 'tp-hotel-booking' ); ?>" name="_hb_customer_email" value="<?php echo esc_attr( $booking->customer_email ) ?>" />
						<input type="text" name="_hb_customer_fax" placeholder="<?php esc_attr_e( 'Fax', 'tp-hotel-booking' ); ?>" value="<?php echo esc_attr( $booking->customer_tax ) ?>" />
						<input type="number" name="_hb_customer_phone" placeholder="<?php esc_attr_e( 'Phone', 'tp-hotel-booking' ); ?>" value="<?php echo esc_attr( $booking->customer_phone ) ?>" />
						<?php hb_dropdown_countries( array( 'name' => '_hb_customer_country', 'class' => 'normal', 'show_option_none' => __( 'Country', 'tp-hotel-booking' ), 'selected' => $booking->customer_country ) ); ?>
					</div>
				</div>
			</div>

		</div>

		<div class="section">

			<h4>
				<?php _e( 'Customer\'s Notes', 'tp-hotel-booking' ); ?>
				<a href="#" class="edit" data-id="30"><i class="fa fa-pencil"></i></a>
			</h4>
			<div class="customer_details">
				<div class="notes details">
					<p><?php printf( '%s', $post->post_content ) ?></p>
				</div>
				<div class="edit_details">
					<textarea name="content" placeholder="<?php esc_attr_e( 'Empty Booking Notes', 'tp-hotel-booking' ); ?>" rows="5" cols="10"><?php echo esc_html( $post->post_content ) ?></textarea>
				</div>
			</div>

		</div>
	</div>

</div>
