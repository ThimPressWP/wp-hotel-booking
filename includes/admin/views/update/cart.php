<?php
/**
 * Template Cart Params
 * @since  1.1
 */
$cart_params = apply_filters( 'hotel_booking_admin_cart_params', $cart_params );

$rooms = array();
$child = array();
foreach ( $cart_params as $key => $cart_item ) {
    if ( $cart_item->product_data->post && $cart_item->product_data->post->post_type === 'hb_room' ) {
        $rooms[ $key ] = $cart_item->product_data;
    }

    if ( isset( $cart_item->parent_id ) ) {
        if ( ! array_key_exists( $cart_item->parent_id, $child ) ) {
            $child[ $cart_item->parent_id ] = array();
        }
        $child[ $cart_item->parent_id ][] = $key;
    }
}

?>

<table class="booking-details hb-booking-table hb-table-width70">
    <thead>
        <th colspan="28">
            <h3><?php _e( 'Booking Details', 'tp-hotel-booking') ?></h3>
        </th>
    </thead>
    <thead>
        <th colspan="4">
            <h3><?php _e( 'Room', 'tp-hotel-booking') ?></h3>
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
        <th colspan="4">
            <h3><?php _e( 'Gross Total', 'tp-hotel-booking') ?></h3>
        </th>
    </thead>
    <tbody>
        <!--Cart item-->
        <?php foreach( $rooms as $cart_id => $room ) : ?>

            <tr>
                <td class="hb_room_type" colspan="4" rowspan="<?php echo array_key_exists( $cart_id, $child ) ? count( $child[ $cart_id ] ) + 2 : 1 ?>">
                    <a href="<?php echo get_edit_post_link( $room->ID ); ?>"><?php echo $room->name; ?><?php printf( '%s', $room->capacity_title ? ' ('.$room->capacity_title.')' : '' ); ?></a>
                </td>
                <td class="hb_capacity" colspan="4"><?php echo sprintf( _n( '%d adult', '%d adults', $room->capacity, 'tp-hotel-booking' ), $room->capacity ); ?> </td>
                <td class="hb_quantity" colspan="4"><?php echo $room->quantity; ?></td>
                <td class="hb_check_in" colspan="4"><?php echo $room->get_data( 'check_in_date' ) ?></td>
                <td class="hb_check_out" colspan="4"><?php echo $room->get_data( 'check_out_date' ) ?></td>
                <td class="hb_night" colspan="4"><?php echo hb_count_nights_two_dates( $room->get_data( 'check_out_date' ), $room->get_data( 'check_in_date' ) ) ?></td>
                <td class="hb_gross_total" colspan="4">
                    <?php echo hb_format_price( $rooms[ $cart_id ]->amount_singular_exclude_tax, hb_get_currency_symbol( $booking->currency ) ); ?>
                </td>
            </tr>

            <?php do_action( 'hotel_booking_admin_cart_after_item', $cart_params, $cart_id, $booking ); ?>
        <?php endforeach; ?>
        <!--Coupon-->
        <?php if ( $booking->coupon ) : ?>

            <tr class="hb_coupon">
                <td class="hb_coupon_remove" colspan="28">
                    <span class="hb-remove-coupon_code"><?php printf( __( 'Coupon applied: %s', 'tp-hotel-booking' ), $booking->coupon['code'] ); ?></span>
                    <span class="hb-align-right">
                        -<?php echo hb_format_price( $booking->coupon['value'], hb_get_currency_symbol( $booking->currency ) ); ?>
                    </span>
                </td>
            </tr>

        <?php endif; ?>
        <!--Subtotal-->
        <tr class="hb_sub_total">
            <td colspan="24">
                <?php _e( 'Sub Total', 'tp-hotel-booking' ); ?>
            </td>
            <td colspan="4">
                <?php echo hb_format_price( $booking->sub_total, hb_get_currency_symbol( $booking->currency ) ); ?>
            </td>
        </tr>
        <!--Tax-->
        <?php if ( $booking->tax ) : ?>
            <tr class="hb_advance_tax">
                <td colspan="24">
                    <?php _e( 'Tax', 'tp-hotel-booking' ); ?>
                    <?php if( $booking->tax < 0 ) { ?>
                        <span><?php printf( __( '(price including tax)', 'tp-hotel-booking' ) ); ?></span>
                    <?php } ?>
                </td>
                <td colspan="4">
                    <?php echo apply_filters( 'hotel_booking_admin_book_details', abs( $booking->tax * 100 ) . '%', $booking ); ?>
                </td>
            </tr>
        <?php endif; ?>
        <!--Total-->
        <tr class="hb_advance_grand_total">
            <td colspan="24">
                <?php _e( 'Grand Total', 'tp-hotel-booking' ); ?>
            </td>
            <td colspan="4">
                <?php echo hb_format_price( $booking->total, hb_get_currency_symbol( $booking->currency ) ) ?>
            </td>
        </tr>
    </tbody>
</table>