<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

$cart = TP_Hotel_Booking::instance()->cart;
global $hb_settings;

?>
<?php if ( TP_Hotel_Booking::instance()->cart->cart_items_count != 0  ) : ?>
    <div id="hotel-booking-cart">

        <form id="hb-cart-form" method="post">
            <h3><?php _e( 'Cart', 'tp-hotel-booking' ); ?></h3>
            <table class="hb_table">
                <thead>
                    <th>&nbsp;</th>
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
                            ?>
                            <tr class="hb_checkout_item" data-cart-id="<?php echo esc_attr( $cart_id ); ?>">
                                <td<?php echo defined( 'TP_HB_EXTRA' ) && $cart_extra ? ' rowspan="'. ( count( $cart_extra ) + 2 ) .'"' : ''  ?>>
                                    <a href="javascript:void(0)" class="hb_remove_cart_item" data-cart-id="<?php echo esc_attr( $cart_id ); ?>">
                                        <i class="fa fa-times"></i>
                                    </a>
                                </td>
                                <td class="hb_room_type"><a href="<?php echo get_permalink( $room->ID ); ?>"><?php echo esc_html( $room->name ); ?><?php printf( '%s', $room->capacity_title ? ' ('.$room->capacity_title.')' : '' ); ?></a></td>
                                <td class="hb_capacity"><?php echo sprintf( _n( '%d adult', '%d adults', $room->capacity, 'tp-hotel-booking' ), $room->capacity ); ?> </td>
                                <td class="hb_quantity"><input type="number" min="0" class="hb_room_number_edit" name="hotel_booking_cart[<?php echo esc_attr( $cart_id ) ?>]" value="<?php echo esc_attr( $num_of_rooms ); ?>" /></td>
                                <td class="hb_check_in"><?php echo date_i18n( hb_get_date_format(), strtotime( $room->get_data( 'check_in_date' ) ) ) ?></td>
                                <td class="hb_check_out"><?php echo date_i18n( hb_get_date_format(), strtotime( $room->get_data( 'check_out_date' ) ) ) ?></td>
                                <td class="hb_night"><?php echo hb_count_nights_two_dates( $room->get_data( 'check_out_date' ), $room->get_data( 'check_in_date' ) ) ?></td>
                                <td class="hb_gross_total">
                                    <?php echo hb_format_price( $room->total ); ?>
                                </td>
                            </tr>
                            <?php do_action( 'hotel_booking_cart_after_item', $room, $cart_id ); ?>
                    <?php endforeach; ?>

                <?php endif; ?>

                    <?php do_action( 'hotel_booking_before_cart_total' ); ?>

                    <tr class="hb_sub_total">
                        <td colspan="8"><?php _e( 'Sub Total', 'tp-hotel-booking' ); ?>
                            <span class="hb-align-right hb_sub_total_value">
                                <?php echo hb_format_price( $cart->sub_total ); ?>
                            </span>
                        </td>
                    </tr>
                    <?php if( $tax = hb_get_tax_settings() ) : ?>
                        <tr class="hb_advance_tax">
                            <td colspan="8">
                                <?php _e( 'Tax', 'tp-hotel-booking' ); ?>
                                <?php if( $tax < 0 ) { ?>
                                    <span><?php printf( __( '(price including tax)', 'tp-hotel-booking' ) ); ?></span>
                                <?php } ?>
                                <span class="hb-align-right"><?php echo apply_filters( 'hotel_booking_cart_tax_display', abs( $tax * 100 ) . '%' ); ?></span>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <tr class="hb_advance_grand_total">
                        <td colspan="8">
                            <?php _e( 'Grand Total', 'tp-hotel-booking' ); ?>
                            <span class="hb-align-right hb_grand_total_value"><?php echo hb_format_price( $cart->total ) ?></span>
                        </td>
                    </tr>
                    <?php if( $advance_payment = $cart->advance_payment ) : ?>
                    <tr class="hb_advance_payment">
                        <td colspan="8">
                            <?php printf( __( 'Advance Payment (%s%% of Grand Total)', 'tp-hotel-booking' ), hb_get_advance_payment() ); ?>
                            <span class="hb-align-right hb_advance_payment_value"><?php echo hb_format_price( $advance_payment ); ?></span>
                        </td>
                    </tr>
                    <?php endif; ?>

                    <tr>
                        <?php wp_nonce_field( 'hb_cart_field', 'hb_cart_field' ); ?>
                    </tr>
            </table>
            <p>
                <a href="<?php echo hb_get_checkout_url() ?>" class="hb_button hb_checkout"><?php _e( 'Check Out', 'tp-hotel-booking' ); ?></a>
                <button type="submit" class="hb_button update"><?php _e( 'Update', 'tp-hotel-booking' ); ?></button>
            </p>
        </form>
    </div>

<?php else: ?>
    <!--Empty cart-->
    <div class="hb-message message">
        <div class="hb-message-content">
            <?php _e( 'Your cart is empty!', 'tp-hotel-booking' ) ?>
        </div>
    </div>
<?php endif; ?>
