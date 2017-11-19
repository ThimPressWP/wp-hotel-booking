<?php

/**
 * The template for displaying booking thank you page.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/checkout/thank-you.php.
 *
 * @version     2.0
 * @package     WP_Hotel_Booking/Templates
 * @category    Templates
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;
?>

<?php get_header(); ?>

<?php
$booking_id = isset( $_GET['booking'] ) ? $_GET['booking'] : '';
$key        = isset( $_GET['key'] ) ? $_GET['key'] : '';
?>

<?php if ( $booking_id && get_post_type( $booking_id ) == 'hb_booking' ) {
	$booking = WPHB_Booking::instance( $booking_id );

	if ( $booking->booking_key === $key ) {

		$rooms = hb_get_order_items( $booking_id );
		?>
        <div class="hb-message message">
            <div class="hb-message-content">
				<?php echo __( 'Thank you! Your booking has been placed. We will contact you to confirm about the booking soon.', 'wp-hotel-booking' ); ?>
            </div>
        </div>

        <div id="booking-details">
            <div class="booking-data">
                <h3 class="booking-data-number"><?php echo sprintf( esc_attr__( 'Booking %s', 'wp-hotel-booking' ), hb_format_order_number( $booking_id ) ); ?></h3>
                <div class="booking-date">
					<?php echo sprintf( __( 'Date %s', 'wp-hotel-booking' ), get_the_date( '', $booking_id ) ); ?>
                </div>
            </div>
        </div>

        <div id="booking-items">

            <h3><?php echo __( 'Booking Items', 'wp-hotel-booking' ); ?></h3>

            <table cellpadding="0" cellspacing="0" class="booking_item_table">
                <thead>
                <tr>
                    <th><?php _e( 'Item', 'wp-hotel-booking' ); ?></th>
                    <th><?php _e( 'Check in - Checkout', 'wp-hotel-booking' ) ?></th>
                    <th><?php _e( 'Night', 'wp-hotel-booking' ); ?></th>
                    <th><?php _e( 'Qty', 'wp-hotel-booking' ); ?></th>
                    <th><?php _e( 'Total', 'wp-hotel-booking' ); ?></th>
                </tr>
                </thead>
                <tbody>

				<?php foreach ( $rooms as $k => $room ) { ?>

                    <tr>
                        <td>
							<?php printf( '<a href="%s">%s</a>', get_permalink( hb_get_order_item_meta( $room->order_item_id, 'product_id', true ) ), $room->order_item_name ) ?>
                        </td>
                        <td>
							<?php printf( '%s - %s', date_i18n( hb_get_date_format(), hb_get_order_item_meta( $room->order_item_id, 'check_in_date', true ) ), date_i18n( hb_get_date_format(), hb_get_order_item_meta( $room->order_item_id, 'check_out_date', true ) ) ) ?>
                        </td>
                        <td>
							<?php printf( '%d', hb_count_nights_two_dates( hb_get_order_item_meta( $room->order_item_id, 'check_out_date', true ), hb_get_order_item_meta( $room->order_item_id, 'check_in_date', true ) ) ) ?>
                        </td>
                        <td>
							<?php printf( '%s', hb_get_order_item_meta( $room->order_item_id, 'qty', true ) ) ?>
                        </td>
                        <td>
							<?php printf( '%s', hb_format_price( hb_get_order_item_meta( $room->order_item_id, 'subtotal', true ), hb_get_currency_symbol( $booking->currency ) ) ); ?>
                        </td>
                    </tr>

					<?php $packages = hb_get_order_items( $booking->id, 'sub_item', $room->order_item_id ); ?>
					<?php if ( $packages ) { ?>
						<?php foreach ( $packages as $package ) { ?>
							<?php $extra = hotel_booking_get_product_class( hb_get_order_item_meta( $package->order_item_id, 'product_id', true ) ); ?>
                            <tr data-order-parent="<?php echo esc_attr( $room->order_item_id ); ?>">
                                <td colspan="3">
									<?php echo esc_html( $package->order_item_name ); ?>
                                </td>
                                <td>
									<?php echo esc_html( hb_get_order_item_meta( $package->order_item_id, 'qty', true ) ); ?>
                                </td>
                                <td>
									<?php echo esc_html( hb_format_price( hb_get_order_item_meta( $package->order_item_id, 'subtotal', true ), hb_get_currency_symbol( $booking->currency ) ) ); ?>
                                </td>
                            </tr>
						<?php } ?>
					<?php } ?>
				<?php } ?>

                <tr>
                    <td colspan="4"><?php _e( 'Sub Total', 'wp-hotel-booking' ) ?></td>
                    <td>
						<?php printf( '%s', hb_format_price( hb_booking_subtotal( $booking->id ), hb_get_currency_symbol( $booking->currency ) ) ); ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="4"><?php _e( 'Tax', 'wp-hotel-booking' ) ?></td>
                    <td>
						<?php printf( '%s', apply_filters( 'hotel_booking_admin_booking_details', hb_format_price( hb_booking_tax_total( $booking->id ), hb_get_currency_symbol( $booking->currency ) ), $booking ) ); ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="4"><?php _e( 'Grand Total', 'wp-hotel-booking' ) ?></td>
                    <td>
						<?php printf( '%s', hb_format_price( hb_booking_total( $booking->id ), hb_get_currency_symbol( $booking->currency ) ) ) ?>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <div id="booking-customer">

            <div class="customer-details">
                <ul class="hb-form-table">

                    <li>
                        <label for="_hb_customer_title"><?php echo __( 'Title:', 'wp-hotel-booking' ); ?></label>
		                <?php echo esc_html( $booking->customer_title ); ?>
                    </li>

                    <li>
                        <label for="_hb_customer_first_name"><?php echo __( 'First Name:', 'wp-hotel-booking' ); ?></label>
		                <?php echo esc_html( $booking->customer_first_name ); ?>
                    </li>

                    <li>
                        <label for="_hb_customer_last_name"><?php echo __( 'Last Name:', 'wp-hotel-booking' ); ?></label>
		                <?php echo esc_html( $booking->customer_last_name ); ?>
                    </li>

                    <li>
                        <label for="_hb_customer_address"><?php echo __( 'Address:', 'wp-hotel-booking' ); ?></label>
		                <?php echo esc_html( $booking->customer_address ); ?>
                    </li>

                    <li>
                        <label for="_hb_customer_city"><?php echo __( 'City:', 'wp-hotel-booking' ); ?></label>
		                <?php echo esc_html( $booking->customer_city ); ?>
                    </li>

                    <li>
                        <label for="_hb_customer_state"><?php echo __( 'State:', 'wp-hotel-booking' ); ?></label>
		                <?php echo esc_html( $booking->customer_state ); ?>
                    </li>

                    <li>
                        <label for="_hb_customer_postal_code"><?php echo __( 'Postal Code:', 'wp-hotel-booking' ); ?></label>
		                <?php echo esc_html( $booking->customer_postal_code ); ?>
                    </li>

                    <li>
                        <label for="_hb_customer_country"><?php echo __( 'Country:', 'wp-hotel-booking' ); ?></label>
		                <?php echo esc_html( $booking->customer_country ); ?>
                    </li>

                    <li>
                        <label for="_hb_customer_phone"><?php echo __( 'Phone:', 'wp-hotel-booking' ); ?></label>
		                <?php echo esc_html( $booking->customer_phone ); ?>
                    </li>

                    <li>
                        <label for="_hb_customer_email"><?php echo __( 'Email:', 'wp-hotel-booking' ); ?></label>
		                <?php echo esc_html( $booking->customer_email ); ?>
                    </li>

                    <li>
                        <label for="_hb_customer_fax"><?php echo __( 'Fax:', 'wp-hotel-booking' ); ?></label>
		                <?php echo esc_html( $booking->customer_tax ); ?>
                    </li>

                </ul>
            </div>

            <div class="booking-notes">
                <label for="_hb_customer_notes"><?php echo __( 'Booking Notes:', 'wp-hotel-booking' ); ?></label>
				<?php echo esc_html( $booking->post->post_content ); ?>
            </div>

        </div>
	<?php } else { ?>
        <p><?php echo esc_html__( 'Booking invalid', 'wp-hotel-booking' ) ?></p>
	<?php }
} else { ?>
    <p><?php echo esc_html__( 'Booking invalid', 'wp-hotel-booking' ) ?></p>
<?php } ?>

<?php get_footer(); ?>