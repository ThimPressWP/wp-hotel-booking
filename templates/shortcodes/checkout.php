<?php
$cart = TP_Hotel_Booking::instance()->cart;
global $hb_settings;
?>
<div id="hotel-booking-payment">

    <form name="hb-payment-form" id="hb-payment-form" method="post" action="<?php echo isset( $search_page ) ? $search_page : ''; ?>">
        <h3><?php _e( 'Booking Rooms', 'tp-hotel-booking' ); ?></h3>
        <table class="hb_table">
            <thead>
                <th class="hb_room_type"><?php _e( 'Room type', 'tp-hotel-booking' ); ?></th>
                <th class="hb_capacity"><?php _e( 'Capacity', 'tp-hotel-booking' ); ?></th>
                <th class="hb_quantity"><?php _e( 'Quantity', 'tp-hotel-booking' ); ?></th>
                <th class="hb_check_in"><?php _e( 'Check - in', 'tp-hotel-booking' ); ?></th>
                <th class="hb_check_out"><?php _e( 'Check - out', 'tp-hotel-booking' ); ?></th>
                <th class="hb_night"><?php _e( 'Night', 'tp-hotel-booking' ); ?></th>
                <th class="hb_gross_total"><?php _e( 'Gross Total', 'tp-hotel-booking' ); ?></th>
            </thead>
            <?php if( $rooms = $cart->get_rooms() ): ?>
                <?php foreach( $rooms as $cart_id => $room ): ?>
                        <?php
                            if( ( $num_of_rooms = (int)$room->get_data('quantity') ) == 0 ) continue;
                            $cart_extra = TP_Hotel_Booking::instance()->cart->get_extra_packages( $cart_id );
                            $sub_total = $room->get_total( $room->check_in_date, $room->check_out_date, $num_of_rooms, false );
                        ?>
                        <tr class="hb_checkout_item" data-cart-id="<?php echo esc_attr( $cart_id ); ?>">
                            <td class="hb_room_type"<?php echo defined( 'TP_HB_EXTRA' ) && $cart_extra ? ' rowspan="'. ( count( $cart_extra ) + 2 ) .'"' : ''  ?>><a href="<?php echo get_permalink( $room->ID ); ?>"><?php echo $room->name; ?><?php printf( '%s', $room->capacity_title ? ' ('.$room->capacity_title.')' : '' ); ?></a></td>
                            <td class="hb_capacity"><?php echo sprintf( _n( '%d adult', '%d adults', $room->capacity, 'tp-hotel-booking' ), $room->capacity ); ?> </td>
                            <td class="hb_quantity"><?php echo $num_of_rooms; ?></td>
                            <td class="hb_check_in"><?php echo $room->get_data( 'check_in_date' ) ?></td>
                            <td class="hb_check_out"><?php echo $room->get_data( 'check_out_date' ) ?></td>
                            <td class="hb_night"><?php echo hb_count_nights_two_dates( $room->get_data( 'check_out_date' ), $room->get_data( 'check_in_date' ) ) ?></td>
                            <td class="hb_gross_total">
                                <?php echo hb_format_price( $room->total ); ?>
                            </td>
                        </tr>
                        <?php do_action( 'hotel_booking_cart_after_item', $room, $cart_id ); ?>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if( $hb_settings->get( 'enable_coupon' ) ) { ?>
                <?php
                // if( $coupon = get_transient( 'hb_user_coupon_' . session_id() ) ) {
                if( $coupon = TP_Hotel_Booking::instance()->cart->coupon ) {
                    $coupon = HB_Coupon::instance( $coupon );
                    ?>
                    <tr class="hb_coupon">
                        <td class="hb_coupon_remove" colspan="8">
                            <p class="hb-remove-coupon" align="right">
                                <a href="" id="hb-remove-coupon"><i class="fa fa-times"></i></a>
                            </p>
                            <span class="hb-remove-coupon_code"><?php printf( __( 'Coupon applied: %s', 'tp-hotel-booking' ), $coupon->coupon_code ); ?></span>
                            <span class="hb-align-right">
                                -<?php echo hb_format_price( $coupon->discount_value ); ?>
                            </span>
                        </td>
                    </tr>
                <?php } else { ?>
                    <tr class="hb_coupon">
                        <td colspan="8" class="hb-align-center" >
                            <input type="text" name="hb-coupon-code" value="" placeholder="<?php _e( 'Coupon', 'tp-hotel-booking' ); ?>" style="width: 150px; vertical-align: top;" />
                            <button type="button" id="hb-apply-coupon"><?php _e( 'Apply Coupon', 'tp-hotel-booking' ); ?></button>
                        </td>
                    </tr>
                <?php } ?>
            <?php } ?>

            <tr class="hb_sub_total">
                <td colspan="8"><?php _e( 'Sub Total', 'tp-hotel-booking' ); ?>
                    <span class="hb-align-right hb_sub_total_value">
                        <?php echo hb_format_price( $cart->sub_total ); ?>
                    </span>
                </td>
            </tr>
            <?php if( $tax = hb_get_tax_settings() ) { ?>
            <tr class="hb_advance_tax">
                <td colspan="8">
                    <?php _e( 'Tax', 'tp-hotel-booking' ); ?>
                    <?php if( $tax < 0 ){?>
                        <span><?php printf( __( '(price including tax)', 'tp-hotel-booking' ) ); ?></span>
                    <?php } ?>
                    <span class="hb-align-right"><?php echo apply_filters( 'hotel_booking_cart_tax_display', abs( $tax * 100 ) . '%' ); ?></span>
                </td>
            </tr>
            <?php } ?>

            <tr class="hb_advance_grand_total">
                <td colspan="8">
                    <?php _e( 'Grand Total', 'tp-hotel-booking' ); ?>
                    <span class="hb-align-right hb_grand_total_value"><?php echo hb_format_price( $cart->cart_total_include_tax ); ?></span>
                </td>
            </tr>
            <?php if( $advance_payment = $cart->advance_payment ) { ?>
                <tr class="hb_advance_payment">
                    <td colspan="8">
                        <?php printf( __( 'Advance Payment (%s%% of Grand Total)', 'tp-hotel-booking' ), hb_get_advance_payment() ); ?>
                        <span class="hb-align-right hb_advance_payment_value"><?php echo hb_format_price( $advance_payment ); ?></span>
                    </td>
                </tr>
                <?php if( hb_get_advance_payment() < 100 ) { ?>
                    <tr class="hb_payment_all">
                        <td colspan="8" class="hb-align-right">
                            <label class="hb-align-right">
                                <input type="checkbox" name="pay_all" />
                                <?php _e( 'I want to pay all', 'tp-hotel-booking' ); ?>
                            </label>
                        </td>
                    </tr>
                <?php } ?>
            <?php } ?>

        </table>
        <?php hb_get_template( 'shortcodes/customer.php', array( 'customer' => $customer ) ); ?>
        <?php hb_get_template( 'shortcodes/payment-method.php', array( 'customer' => $customer ) ); ?>
        <?php hb_get_template( 'shortcodes/addition-information.php' ); ?>
        <?php wp_nonce_field( 'hb_customer_place_order', 'hb_customer_place_order_field' ); ?>
        <input type="hidden" name="hotel-booking" value="place_order" />
        <input type="hidden" name="action" value="hotel_booking_place_order" />
        <input type="hidden" name="total_advance" value="<?php echo $cart->advance_payment ? $cart->advance_payment : $cart->total ?>" />
        <input type="hidden" name="total_price" value="<?php echo $cart->total ?>" />
        <input type="hidden" name="currency" value="<?php echo esc_attr( hb_get_currency() ) ?>">
        <?php if( $tos_page_id = hb_get_page_id( 'terms' ) ) { ?>
        <p>
            <label>
                <input type="checkbox" name="tos" value="1" />
                <?php printf( __( 'I agree with <a href="%s" target="_blank">%s</a>' ), get_permalink( $tos_page_id ), get_the_title( $tos_page_id ) ); ?>
            </label>
        </p>
        <?php } ?>
        <p><button type="submit" class="hb_button"><?php _e( 'Check out', 'tp-hotel-booking' ); ?></button></p>
    </form>
</div>