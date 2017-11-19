<?php
/**
 * @Author: ducnvtt
 * @Date:   2016-03-25 09:32:53
 * @Last Modified by:   ducnvtt
 * @Last Modified time: 2016-04-13 13:47:55
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

global $post;
$booking = WPHB_Booking::instance( $post->ID );
?>

<style type="text/css">
    #normal-sortables,
    #hb-booking-details .ui-sortable-handle {
        display: none;
    }
</style>
<div id="booking_details">
	<?php wp_nonce_field( 'hotel-booking-metabox-booking-details', 'hotel_booking_metabox_booking_details_nonce' ); ?>
    <h2 class="hb_meta_title">
		<?php printf( __( 'Book ID %s', 'wp-hotel-booking' ), hb_format_order_number( $post->ID ) ) ?>
    </h2>
    <p class="description"><?php printf( __( 'Booked on %s', 'wp-hotel-booking' ), $post->post_date ) ?></p>
    <div id="booking_details_section">

        <div class="section">
            <h4><?php _e( 'General', 'wp-hotel-booking' ); ?></h4>
            <ul>
                <li>
                    <label><?php _e( 'Payment Method:', 'wp-hotel-booking' ); ?></label>
					<?php $methods = hb_get_payment_gateways(); ?>
                    <select name="_hb_method">
						<?php if ( $booking->method && ! array_key_exists( $booking->method, $methods ) ) : ?>
                            <option value="<?php echo esc_attr( $booking->method ) ?>"
                                    selected><?php printf( __( '%s is not available', 'wp-hotel-booking' ), $booking->method_title ) ?></option>
						<?php endif; ?>
						<?php foreach ( $methods as $id => $method ) : ?>
                            <option value="<?php echo esc_attr( $id ) ?>" <?php selected( $booking->method, $id ); ?>><?php printf( '%s(%s)', $method->title, $method->description ) ?></option>
						<?php endforeach; ?>
                    </select>
                </li>
                <li>
                    <label><?php _e( 'Booking Status:', 'wp-hotel-booking' ); ?></label>
                    <select name="_hb_booking_status">
						<?php $status = hb_get_booking_statuses(); ?>
						<?php foreach ( $status as $st => $status ) : ?>

                            <option value="<?php echo esc_attr( $st ) ?>" <?php selected( $post->post_status, $st ); ?>><?php printf( '%s', $status ) ?></option>

						<?php endforeach; ?>
                    </select>
                </li>
            </ul>
        </div>

        <div class="section">

            <h4>
				<?php _e( 'Customer\'s Details', 'wp-hotel-booking' ); ?>
                <a href="#" class="edit" data-id="30"><i class="fa fa-pencil"></i></a>
            </h4>
            <div class="customer_details">
                <div class="address details">
                    <strong><?php _e( 'Name', 'wp-hotel-booking' ); ?></strong>
                    <br/>
                    <small><?php printf( '%s', hb_get_customer_fullname( $post->ID, true ) ); ?></small>
                    <br/>
                    <strong><?php _e( 'Address', 'wp-hotel-booking' ); ?></strong>
                    <br/>
                    <small><?php printf( '%s', $booking->customer_address ) ?></small>
                    <br/>
                    <small><?php printf( '%s', $booking->customer_city ) ?></small>
                    <br/>
                    <small><?php printf( '%s', $booking->customer_state ) ?></small>
                    <br/>
                    <small><?php printf( '%s', $booking->customer_postal_code ) ?></small>
                    <br/>
                    <small><?php printf( '%s', $booking->customer_country ) ?></small>
                    <br/>
					<?php $customer_email = $booking->customer_email; ?>
                    <strong><?php _e( 'Email', 'wp-hotel-booking' ) ?></strong>
                    <br/>
                    <a href="mailto:<?php echo esc_attr( $customer_email ) ?>"><?php printf( '%s', $customer_email ) ?></a>
                    <br/>
                    <strong><?php _e( 'Phone', 'wp-hotel-booking' ) ?></strong>
                    <br/>
                    <small><?php printf( '%s', $booking->customer_phone ) ?></small>
                </div>
                <div class="edit_details">
                    <div class="edit_col">
						<?php hb_dropdown_titles( array(
							'name'     => '_hb_customer_title',
							'class'    => 'normal',
							'selected' => $booking->customer_title
						) ); ?>
                        <input type="text" name="_hb_customer_first_name" id="_hb_customer_first_name"
                               value="<?php echo esc_attr( $booking->customer_first_name ) ?>"
                               placeholder="<?php esc_attr_e( 'First name', 'wp-hotel-booking' ); ?>"/>
                        <input type="text" name="_hb_customer_last_name" id="_hb_customer_last_name"
                               value="<?php echo esc_attr( $booking->customer_last_name ) ?>"
                               placeholder="<?php esc_attr_e( 'Last name', 'wp-hotel-booking' ); ?>"/>
                        <input type="text" name="_hb_customer_address" id="_hb_customer_address"
                               value="<?php echo esc_attr( $booking->customer_address ) ?>"
                               placeholder="<?php esc_attr_e( 'Address', 'wp-hotel-booking' ); ?>"/>
                        <input type="text" name="_hb_customer_city" id="_hb_customer_city"
                               value="<?php echo esc_attr( $booking->customer_city ) ?>"
                               placeholder="<?php esc_attr_e( 'City', 'wp-hotel-booking' ); ?>"/>
                    </div>
                    <div class="edit_col">
                        <input type="text" name="_hb_customer_state" id="_hb_customer_state"
                               value="<?php echo esc_attr( $booking->customer_state ) ?>"
                               placeholder="<?php esc_attr_e( 'State', 'wp-hotel-booking' ); ?>"/>
                        <input type="text" name="_hb_customer_postal_code" id="_hb_customer_postal_code"
                               value="<?php echo esc_attr( $booking->customer_postal_code ) ?>"
                               placeholder="<?php esc_attr_e( 'Postl code', 'wp-hotel-booking' ); ?>"/>
                        <input type="email" placeholder="<?php esc_attr_e( 'Email address', 'wp-hotel-booking' ); ?>"
                               name="_hb_customer_email" value="<?php echo esc_attr( $booking->customer_email ) ?>"/>
                        <input type="text" name="_hb_customer_fax"
                               placeholder="<?php esc_attr_e( 'Fax', 'wp-hotel-booking' ); ?>"
                               value="<?php echo esc_attr( $booking->customer_tax ) ?>"/>
                        <input type="text" name="_hb_customer_phone"
                               placeholder="<?php esc_attr_e( 'Phone', 'wp-hotel-booking' ); ?>"
                               value="<?php echo esc_attr( $booking->customer_phone ) ?>"/>
						<?php hb_dropdown_countries( array(
							'name'             => '_hb_customer_country',
							'class'            => 'normal',
							'show_option_none' => __( 'Country', 'wp-hotel-booking' ),
							'selected'         => $booking->customer_country
						) ); ?>
                    </div>
                </div>
            </div>

        </div>

        <div class="section">

            <h4>
				<?php _e( 'Customer\'s Notes', 'wp-hotel-booking' ); ?>
                <a href="#" class="edit" data-id="30"><i class="fa fa-pencil"></i></a>
            </h4>
            <div class="customer_details">
                <div class="notes details">
                    <p><?php printf( '%s', $post->post_content ) ?></p>
                </div>
                <div class="edit_details">
                    <textarea name="content"
                              placeholder="<?php esc_attr_e( 'Empty Booking Notes', 'wp-hotel-booking' ); ?>" rows="5"
                              cols="10"><?php echo esc_html( $booking->post->post_content ) ?></textarea>
                </div>
            </div>

        </div>
    </div>

</div>
