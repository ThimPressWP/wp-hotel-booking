<?php
$title                = '';
$first_name           = '';
$last_name            = '';
$address              = '';
$city                 = '';
$state                = '';
$postal_code          = '';
$country              = '';
$phone                = '';
$fax                  = '';
$email                = '';
$addition_information = '';

if ( $email = get_transient( 'hotel_booking_customer_email_' . HB_BLOG_ID, $email ) ) {
	$query_args = array(
		'post_type'  => 'hb_customer',
		'meta_query' => array(
			array(
				'key'     => '_hb_email',
				'value'   => $email,
				'compare' => 'EQUALS'
			),
		)
	);
	set_transient( 'hotel_booking_customer_email_' . HB_BLOG_ID, $email, DAY_IN_SECONDS );
	if ( $posts = get_posts( $query_args ) ) {
		$customer       = $posts[0];
		$customer->data = array();
		$data           = get_post_meta( $customer->ID );
		foreach ( $data as $k => $v ) {
			$k = preg_replace( '!^_hb_!', '', $k );
			$customer->data[$k] = $v[0];
		}
		extract( $customer->data );
	} else {
		$customer = null;
	}
}
//extract( $customer->data );
?>
<div class="hb-order-new-customer" id="hb-order-new-customer">
	<div class="hb-col-padding hb-col-border">
		<h4><?php _e( 'New Customer', 'tp-hotel-booking' ); ?></h4>
		<ul class="hb-form-table col-2">
			<li class="hb-form-field">
				<label class="hb-form-field-label"><?php _e( 'Title', 'tp-hotel-booking' ); ?>
					<span class="hb-required">*</span> </label>

				<div class="hb-form-field-input">
					<?php hb_dropdown_titles( array( 'selected' => $title ) ); ?>
				</div>
			</li>
			<li class="hb-form-field">
				<label class="hb-form-field-label"><?php _e( 'Name', 'tp-hotel-booking' ); ?>
					<span class="hb-required">*</span></label>

				<div class="hb-form-field-input">
					<input type="text" name="first_name" value="<?php echo $first_name; ?>" placeholder="<?php _e( 'First name', 'tp-hotel-booking' ); ?>" />
					<input type="text" name="last_name" value="<?php echo $last_name; ?>" placeholder="<?php _e( 'Last name', 'tp-hotel-booking' ); ?>" />
				</div>
			</li>
			<li class="hb-form-field">
				<label class="hb-form-field-label"><?php _e( 'Address', 'tp-hotel-booking' ); ?>
					<span class="hb-required">*</span></label>

				<div class="hb-form-field-input">
					<input type="text" name="address" value="<?php echo $address; ?>" placeholder="<?php _e( 'Address', 'tp-hotel-booking' ); ?>" />
				</div>
			</li>
			<li class="hb-form-field">
				<label class="hb-form-field-label"><?php _e( 'City', 'tp-hotel-booking' ); ?>
					<span class="hb-required">*</span></label>

				<div class="hb-form-field-input">
					<input type="text" name="city" value="<?php echo $city; ?>" placeholder="<?php _e( 'City', 'tp-hotel-booking' ); ?>" />
				</div>
			</li>
			<li class="hb-form-field">
				<label class="hb-form-field-label"><?php _e( 'State', 'tp-hotel-booking' ); ?>
					<span class="hb-required">*</span></label>

				<div class="hb-form-field-input">
					<input type="text" name="state" value="<?php echo $state; ?>" placeholder="<?php _e( 'State', 'tp-hotel-booking' ); ?>" />
				</div>
			</li>
		</ul>
		<ul class="hb-form-table col-2">
			<li class="hb-form-field">
				<label class="hb-form-field-label"><?php _e( 'Postal Code', 'tp-hotel-booking' ); ?>
					<span class="hb-required">*</span></label>

				<div class="hb-form-field-input">
					<input type="text" name="postal_code" value="<?php echo $postal_code; ?>" placeholder="<?php _e( 'Postal code', 'tp-hotel-booking' ); ?>" />
				</div>
			</li>
			<li class="hb-form-field">
				<label class="hb-form-field-label"><?php _e( 'Country', 'tp-hotel-booking' ); ?>
					<span class="hb-required">*</span></label>

				<div class="hb-form-field-input">
					<?php hb_dropdown_countries( array( 'name' => 'country', 'show_option_none' => __( 'Country', 'tp-hotel-booking' ), 'selected' => $country ) ); ?>
					<!-- <input type="text" name="country" value="<?php echo $country; ?>" placeholder="<?php _e( 'Country', 'tp-hotel-booking' ); ?>" />-->
				</div>
			</li>
			<li class="hb-form-field">
				<label class="hb-form-field-label"><?php _e( 'Phone', 'tp-hotel-booking' ); ?>
					<span class="hb-required">*</span></label>

				<div class="hb-form-field-input">
					<input type="text" name="phone" value="<?php echo $phone; ?>" placeholder="<?php _e( 'Phone Number', 'tp-hotel-booking' ); ?>" />
				</div>
			</li>
			<li class="hb-form-field">
				<label class="hb-form-field-label"><?php _e( 'Email', 'tp-hotel-booking' ); ?>
					<span class="hb-required">*</span></label>

				<div class="hb-form-field-input">
					<input type="email" name="email" value="<?php echo $email; ?>" placeholder="<?php _e( 'Email address', 'tp-hotel-booking' ); ?>" />
				</div>
			</li>
			<li class="hb-form-field">
				<label class="hb-form-field-label"><?php _e( 'Fax', 'tp-hotel-booking' ); ?></label>

				<div class="hb-form-field-input">
					<input type="text" name="fax" value="<?php echo $fax; ?>" placeholder="<?php _e( 'Fax', 'tp-hotel-booking' ); ?>" />
				</div>
			</li>
		</ul>
		<input type="hidden" name="existing-customer-id" value="" />
	</div>
</div>