<?php

if ( !defined( 'ABSPATH' ) ) {
	exit();
}

?>
<?php if ( !is_user_logged_in() ) : ?>

    <div class="hb-order-existing-customer" data-label="<?php esc_attr_e( '-Or-', 'wp-hotel-booking' ); ?>">
        <div class="hb-col-padding hb-col-border">
            <h4><?php _e( 'Existing customer?', 'wp-hotel-booking' ); ?></h4>
            <ul class="hb-form-table">
                <li class="hb-form-field">
                    <label class="hb-form-field-label"><?php _e( 'Email', 'wp-hotel-booking' ); ?></label>
                    <div class="hb-form-field-input">
                        <input type="email" name="existing-customer-email" value="<?php echo esc_attr( WP_Hotel_Booking::instance()->cart->customer_email ); ?>" placeholder="<?php _e( 'Your email here', 'wp-hotel-booking' ); ?>" />
                    </div>
                </li>
                <li>
                    <button type="button" id="fetch-customer-info"><?php _e( 'Apply', 'wp-hotel-booking' ); ?></button>
                </li>
            </ul>
        </div>
    </div>

<?php endif; ?>