<?php
/**
 * @Author: ducnvtt
 * @Date  :   2016-04-14 10:38:17
 * @Last  Modified by:   ducnvtt
 * @Last  Modified time: 2016-04-15 16:56:13
 */
if ( !defined( 'ABSPATH' ) ) {
	exit();
}
?>
<h2><?php _e( 'Customer Details', 'tp-hotel-booking' ) ?></h2>
<table class="width-100 customer_details" cellspacing="0" cellpadding="0">
    <tr>
        <td class="callout-inner secondary">
            <table class="width-100">
                <tbody>
                <tr>
                    <th class="width-50 columns first">
                        <table class="width-100">
                            <tr>
                                <th>
                                    <p>
                                        <strong><?php _e( 'Payment Method', 'tp-hotel-booking' ) ?></strong><br> <?php echo esc_html( $booking->method_title ) ?>
                                    </p>
                                    <p>
                                        <strong><?php _e( 'Email Address', 'tp-hotel-booking' ) ?></strong><br> <?php echo esc_html( $booking->customer_email ) ?>
                                    </p>
                                    <p>
                                        <strong><?php _e( 'Booking ID', 'tp-hotel-booking' ) ?></strong><br> <?php echo esc_html( $booking->get_booking_number() ) ?>
                                    </p>
									<?php if ( $booking->post->post_content ) : ?>
                                        <p>
                                            <strong><?php _e( 'Addtion Information', 'tp-hotel-booking' ) ?></strong><br> <?php echo esc_html( $booking->post->post_content ) ?>
                                        </p>
									<?php endif; ?>
                                </th>
                            </tr>
                        </table>
                    </th>
                    <th class="width-50 columns last">
                        <table class="width-100">
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
