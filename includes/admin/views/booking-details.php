<?php
/**
 * Template Booking Details
 * @since  1.0.3
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

?>
<?php
$booking_id = hb_get_request( 'id' );
?>
<div class="wrap">
    <h2><?php _e( 'Booking Details: ','tp-hotel-booking' ); echo sprintf( '%s', hb_format_order_number( $booking_id ) );  ?></h2>
<?php
    $booking = HB_Booking::instance( $booking_id );
    if( ! $booking->post->ID ){
        _e( 'Invalid booking', 'tp-hotel-booking' );
        return;
    }
    $customer_id = get_post_meta( $booking_id, '_hb_customer_id', true );
    $default_currency = get_post_meta( $booking_id, '_hb_currency', true );
    $default_currency_symbol = hb_get_currency_symbol( $default_currency );

    $payment_currency = get_post_meta( $booking_id, '_hb_payment_currency', true );
    if( ! $payment_currency  )
        $payment_currency = $default_currency;

    $currency_symbol = hb_get_currency_symbol( $payment_currency );
    $rate = get_post_meta( $booking_id, '_hb_payment_currency_rate', true );
    if( ! $rate )
        $rate = 1;
?>
    <table  class="hb-booking-table customer-information">
        <thead>
            <th colspan="2">
                <h3><?php _e( 'Customer information', 'tp-hotel-booking') ?></h3>
            </th>
        </thead>
        <tbody>
            <tr>
                <th><?php _e( 'Name', 'tp-hotel-booking' ) ?> </th>
                <td><?php
                    $title = hb_get_title_by_slug( get_post_meta( $customer_id, '_hb_title', true ) );
                    $first_name = get_post_meta( $customer_id, '_hb_first_name', true );
                    $last_name = get_post_meta( $customer_id, '_hb_last_name', true );
                    printf( '%s %s %s', $title ? $title : 'Cus.', $first_name, $last_name );
                    ?></td>
            </tr>
            <tr>
                <th> <?php _e( 'Address ', 'tp-hotel-booking'); ?> </th>
                <td><?php echo esc_html( get_post_meta( $customer_id, '_hb_address', true ) ); ?></td>
            </tr>
            <tr>
                <th> <?php _e( 'City ', 'tp-hotel-booking' ); ?> </th>
                <td><?php echo esc_html( get_post_meta( $customer_id, '_hb_city', true ) ) ?></td>
            </tr>
            <tr>
                <th><?php _e( 'State ', 'tp-hotel-booking' ); ?></th>
                <td><?php echo esc_html( get_post_meta( $customer_id, '_hb_state', true ) ) ?></td>
            </tr>
            <tr>
                <th><?php _e( 'Country ', 'tp-hotel-booking' ); ?></th>
                <td><?php echo esc_html( get_post_meta( $customer_id, '_hb_country', true ) ) ?></td>
            </tr>
            <tr>
                <th><?php _e( 'Zip/ Post Code ','tp-hotel-booking' ); ?></th>
                <td><?php echo esc_html( get_post_meta( $customer_id, '_hb_postal_code', true ) ) ?></td>
            </tr>
            <tr>
                <th><?php _e( 'Phone ', 'tp-hotel-booking' ); ?></th>
                <td><?php echo esc_html( get_post_meta( $customer_id, '_hb_phone', true ) ) ?></td>
            </tr>
            <tr>
                <th><?php _e( 'Fax ', 'tp-hotel-booking' ); ?></th>
                <td><?php echo esc_html( get_post_meta( $customer_id, '_hb_fax', true) ) ?></td>
            </tr>
            <tr>
                <th><?php _e( 'Email ', 'tp-hotel-booking' ); ?></th>
                <td><?php echo esc_html( get_post_meta( $customer_id, '_hb_email', true ) ); ?></td>
            </tr>
        </tbody>
    </table>
    <table class="booking-details hb-booking-table">
        <thead>
            <th colspan="24">
                <h3><?php _e( 'Booking Details', 'tp-hotel-booking') ?></h3>
            </th>
        </thead>
        <thead>
            <th colspan="4">
                <h3><?php _e( 'Room Name', 'tp-hotel-booking') ?></h3>
            </th>
            <th colspan="4">
                <h3><?php _e( 'Capacity', 'tp-hotel-booking') ?></h3>
            </th>
            <th colspan="4">
                <h3><?php _e( 'Quantity', 'tp-hotel-booking') ?></h3>
            </th>
            <th colspan="4">
                <h3><?php _e( 'Check - in', 'tp-hotel-booking') ?></h3>
            </th>
            <th colspan="4">
                <h3><?php _e( 'Check - out', 'tp-hotel-booking') ?></h3>
            </th>
            <th colspan="4">
                <h3><?php _e( 'Night', 'tp-hotel-booking') ?></h3>
            </th>
        </thead>
        <tbody>
            <?php $booking_rooms_params = get_post_meta( $booking_id, '_hb_booking_params', true ); ?>
            <?php //$currency = hb_get_currency_symbol( get_post_meta( $booking_id, '_hb_currency', true ) ); ?>
                <?php if( $booking_rooms_params ): ?>
                    <?php foreach ($booking_rooms_params as $search_key => $rooms): ?>

                                <?php foreach ($rooms as $id => $room_param) : ?>
                                    <tr style="background-color: #FFFFFF;">
                                        <td colspan="4">
                                            <?php
                                                $room = HB_Room::instance( $id, $room_param );
                                                echo esc_html( get_the_title( $id ) );
                                                $terms = wp_get_post_terms( $id, 'hb_room_type' );
                                                $room_types = array();
                                                foreach ($terms as $key => $term) {
                                                    $room_types[] = $term->name;
                                                }
                                                if( ! is_wp_error( $terms ) && ! empty( $room_types ) ) echo " (", implode(', ', $room_types), ")";
                                            ?>
                                        </td>
                                        <td style="text-align: right;" colspan="4">
                                            <?php
                                                $cap_id = get_post_meta( $id, '_hb_room_capacity', true );
                                                $term = get_term( $cap_id, 'hb_room_capacity' );
                                                if( $term )
                                                {
                                                    if( ! is_wp_error( $term  ) ){
                                                        $qty = get_term_meta( $cap_id, 'hb_max_number_of_adults', true );
                                                        printf( '%s (%d)', $term->name, $qty ? $qty : (int)get_option( 'hb_taxonomy_capacity_' . $cap_id ) );
                                                    }
                                                    else
                                                    {
                                                        printf( '%s', $term->name );
                                                    }
                                                }
                                            ?>
                                        </td>
                                        <td style="text-align: right;" colspan="4"><?php echo esc_html( $room->quantity ); ?></td>
                                        <td style="text-align: right;" colspan="4"><?php echo date_i18n( hb_get_date_format(), strtotime( $room->get_data( 'check_in_date' ) ) ); ?></td>
                                        <td style="text-align: right;" colspan="4"><?php echo date_i18n( hb_get_date_format(), strtotime( $room->get_data( 'check_out_date' ) ) ); ?></td>
                                        <td style="text-align: right;" colspan="4">
                                            <?php
                                                echo sprintf( '%s', hb_count_nights_two_dates( $room->get_data( 'check_out_date' ), $room->get_data( 'check_in_date' ) ) );
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
        </tbody>
    </table>
    <table class="hb-booking-rooms">
        <thead>
            <tr>
                <th colspan="5">
                    <h3><?php _e( 'Room Details', 'tp-hotel-booking') ?></h3>
                </th>
            </tr>
            <tr>
                <th align="left"><?php _e( 'Name', 'tp-hotel-booking' ); ?></th>
                <th align="left"><?php _e( 'Type', 'tp-hotel-booking' ); ?></th>
                <th align="left"><?php _e( 'Extra Package', 'tp-hotel-booking' ); ?></th>
                <th align="right"><?php _e( 'Quantity', 'tp-hotel-booking' ); ?></th>
                <th align="right"><?php _e( 'Sub total', 'tp-hotel-booking' ); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php $booking_rooms_params = get_post_meta( $booking_id, '_hb_booking_params', true ); ?>
            <?php //$currency = hb_get_currency_symbol( get_post_meta( $booking_id, '_hb_currency', true ) ); ?>
                <?php if( $booking_rooms_params ): ?>
                    <?php foreach ($booking_rooms_params as $search_key => $rooms): ?>

                            <?php foreach ($rooms as $id => $room_param) : ?>
                                <tr style="background-color: #FFFFFF;">
                                    <td>
                                        <?php
                                            $room = HB_Room::instance( $id, $room_param );
                                            echo get_the_title( $id );
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                            $terms = wp_get_post_terms( $id, 'hb_room_type' );
                                            $room_types = array();
                                            foreach ($terms as $key => $term) {
                                                $room_types[] = $term->name;
                                            }
                                            if( ! empty( $room_types ) ) echo implode(', ', $room_types);
                                        ?>
                                    </td>
                                    <td align="left">
                                        <?php do_action( 'hotel_booking_room_details_quantity', $booking_rooms_params, $search_key, $id ); ?>
                                    </td>
                                    <td align="right">
                                        <?php echo esc_html( $room->quantity ); ?>
                                    </td>
                                    <td align="right"><?php echo hb_format_price( $room->total, $currency_symbol ); ?></td>
                                </tr>
                            <?php endforeach; ?>

                    <?php endforeach; ?>
                <?php endif; ?>
        <?php if( $coupon = get_post_meta( $booking_id, '_hb_coupon', true ) ){ ?>
            <tr>
                <th colspan="4" align="left"><?php printf( __( 'Coupon Applied (%s)', 'tp-hotel-booking' ), $coupon['code'] ); ?></th>
                <td align="right" class="negative-price">-<?php echo hb_format_price( $coupon['value'], $currency_symbol ); ?></td>
            </tr>
        <?php } ?>
            <tr>
                <th colspan="4" align="left"><?php _e( 'Sub Total', 'tp-hotel-booking' ); ?></th>
                <td align="right"><?php echo hb_format_price( get_post_meta( $booking_id, '_hb_sub_total', true ), $currency_symbol ); ?></td>
            </tr>
            <tr>
                <th colspan="4" align="left"><?php _e( 'Price including tax', 'tp-hotel-booking' ); ?></th>
                <td align="right"><?php echo get_post_meta( $booking_id, '_hb_price_including_tax', true ) == 'yes' ? __( 'Yes', 'tp-hotel-booking') : __( 'No', 'tp-hotel-booking'); ?></td>
            </tr>
            <tr>
                <th colspan="4" align="left"><?php _e( 'Tax', 'tp-hotel-booking' ); ?></th>
                <td align="right">
                    <?php
                        $html = '';
                        if( $tax = get_post_meta( $booking_id, '_hb_tax', true ) )
                        {
                            if( is_string($tax) )
                                $html = get_post_meta( $booking_id, '_hb_tax', true ) * 100 .'%' ;
                        }
                        echo sprintf( '%s', apply_filters( 'hotel_booking_admin_book_details', $html, $booking_id ) );
                    ?>
                </td>
            </tr>
            <tr>
                <th colspan="4" align="left"><?php _e( 'Default Currency', 'tp-hotel-booking' ); ?></th>
                <td align="right"><?php echo sprintf( '%s', $default_currency ); ?></td>
            </tr>
            <tr>
                <th colspan="4" align="left"><?php _e( 'Payment Currency', 'tp-hotel-booking' ); ?></th>
                <td align="right"><?php echo sprintf( '%s', $payment_currency ); ?></td>
            </tr>
            <tr>
                <th colspan="4" align="left">
                    <?php _e( 'Currency Rate', 'tp-hotel-booking' ); ?>
                    <?php printf( '%s / %s', $default_currency, $payment_currency ) ?>
                </th>
                <td align="right"><?php printf( '%s', $rate ); ?></td>
            </tr>
            <tr>
                <th colspan="4" align="left"><?php _e( 'Total', 'tp-hotel-booking' ); ?></th>
                <td align="right">
                    <?php echo hb_format_price( get_post_meta( $booking_id, '_hb_total', true ), $currency_symbol ); ?>
                    <?php if( $rate && $rate != 1 ): ?>
                        (<?php printf( '%s', hb_format_price( (float)get_post_meta( $booking_id, '_hb_total', true ) / (float)$rate , $default_currency_symbol ) ) ?>)
                    <?php endif; ?>
                </td>
            </tr>
        </tbody>
    </table>
    <table class="hb-booking-table">
        <thead>
            <tr>
                <td colspan="2">
                    <h3><?php _e( 'Payment Details', 'tp-hotel-booking') ?></h3>
                </td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th>
                    <?php _e( 'Payment Gateway', 'tp-hotel-booking' ); ?>
                </th>
                <td>
                    <?php echo sprintf( '%s', get_post_meta( $booking_id, '_hb_method_title', true ) ); ?>
                </td>
            </tr>
            <tr>
                <th>
                    <?php _e( 'Booking status', 'tp-hotel-booking' ); ?>
                </th>
                <td>
                    <span class="hb-booking-status <?php echo esc_attr( get_post_status( $booking_id ) ); ?>">
                        <a href="javascript:void(0)">
                            <?php echo sprintf( '%s', hb_get_booking_status_label( $booking_id ) ); ?>
                        </a>
                    </span>
                </td>
            </tr>
        </tbody>
    </table>
    <?php if( $addition_information = get_post_field( 'post_content', $booking_id ) ) { ?>
    <table class="hb-booking-table">
        <thead>
        <tr>
            <td colspan="2">
                <h3><?php _e( 'Addition Information', 'tp-hotel-booking') ?></h3>
            </td>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td colspan="2">
                <?php echo sprintf( '%s', esc_html( $addition_information ) ); ?>
            </td>
        </tr>
        </tbody>
    </table>
    <?php } ?>
</div>