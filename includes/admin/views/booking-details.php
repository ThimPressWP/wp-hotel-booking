<?php
$booking_id = hb_get_request( 'id' );

?>
<div class="wrap">
    <h2><?php _e( 'Booking Details: ','tp-hotel-booking' ); echo hb_format_order_number( $booking_id );  ?></h2>
    <?php
    $booking = HB_Booking::instance( $booking_id );
    $booking_details = $booking->booking_details_rooms($booking_id);
    if( ! $booking->post->ID ){
        _e( 'Invalid booking', 'tp-hotel-booking' );
        return;
    }
    $customer_id = get_post_meta( $booking_id, '_hb_customer_id', true );
    $currency_symbol = hb_get_currency_symbol( get_post_meta( $booking_id, '_hb_currency', true ) );
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
                <td><?php echo get_post_meta( $customer_id, '_hb_address', true ); ?></td>
            </tr>
            <tr>
                <th> <?php _e( 'City ', 'tp-hotel-booking' ); ?> </th>
                <td><?php echo get_post_meta( $customer_id, '_hb_city', true ) ?></td>
            </tr>
            <tr>
                <th><?php _e( 'State ', 'tp-hotel-booking' ); ?></th>
                <td><?php echo get_post_meta( $customer_id, '_hb_state', true ) ?></td>
            </tr>
            <tr>
                <th><?php _e( 'Country ', 'tp-hotel-booking' ); ?></th>
                <td><?php echo get_post_meta( $customer_id, '_hb_country', true ) ?></td>
            </tr>
            <tr>
                <th><?php _e( 'Zip/ Post Code ','tp-hotel-booking' ); ?></th>
                <td><?php echo get_post_meta( $customer_id, '_hb_postal_code', true ) ?></td>
            </tr>
            <tr>
                <th><?php _e( 'Phone ', 'tp-hotel-booking' ); ?></th>
                <td><?php echo get_post_meta( $customer_id, '_hb_phone', true ) ?></td>
            </tr>
            <tr>
                <th><?php _e( 'Fax ', 'tp-hotel-booking' ); ?></th>
                <td><?php echo get_post_meta( $customer_id, '_hb_fax', true) ?></td>
            </tr>
            <tr>
                <th><?php _e( 'Email ', 'tp-hotel-booking' ); ?></th>
                <td><?php echo get_post_meta( $customer_id, '_hb_email', true ) ?></td>
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
                <h3><?php _e( 'Number of rooms', 'tp-hotel-booking') ?></h3>
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
            <?php foreach ($booking_details as $key => $details): ?>
                <?php
                    $roomID = (int)get_post_meta( $details->ID, '_hb_id', true );
                    $check_out = (int)get_post_meta( $details->ID, '_hb_check_out_date', true );
                    $check_in = (int)get_post_meta( $details->ID, '_hb_check_in_date', true );
                    $room = HB_Room::instance( $roomID, array(
                            'id'            => $roomID,
                            'check_in_date' => $check_in,
                            'check_out_date' => $check_out
                        ) );
                ?>
                <tr>
                    <td colspan="4"><?php echo $room->name;?> (<?php echo $room->capacity_title; ?>)</td>
                    <td colspan="4"><?php echo sprintf( _n( '%d adult', '%d adults', HB_Room::instance($roomID)->capacity , 'tp-hotel-booking' ), HB_Room::instance($roomID)->capacity ); ?></td>
                    <td colspan="4"><?php echo get_post_meta( $details->ID, '_hb_quantity', true ) ?></td>
                    <td colspan="4"><?php echo date( _x( 'F d, Y', 'Check-in date format', 'tp-hotel-booking' ), $room->check_in_date ); ?></td>
                    <td colspan="4"><?php echo date( _x( 'F d, Y', 'Check-in date format', 'tp-hotel-booking' ), $room->check_out_date ); ?></td>
                    <td colspan="4"><?php echo hb_count_nights_two_dates( $room->check_out_date, $room->check_in_date ); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <table class="hb-booking-rooms">
        <thead>
            <tr>
                <th colspan="4">
                    <h3><?php _e( 'Room Details', 'tp-hotel-booking') ?></h3>
                </th>
            </tr>
            <tr>
                <th align="left"><?php _e( 'Name', 'tp-hotel-booking' );?></th>
                <th align="left"><?php _e( 'Type', 'tp-hotel-booking' );?></th>
                <th align="right"><?php _e( 'Number of rooms', 'tp-hotel-booking' );?></th>
                <th align="right"><?php _e( 'Sub total', 'tp-hotel-booking' );?></th>
            </tr>
        </thead>
        <tbody>
        <?php
        $_rooms = get_post_meta( $booking_id, '_hb_room_id' );
        $rooms = array();
        foreach( $_rooms as $room_id ){
            if( empty( $rooms[ $room_id ] ) ){
                $rooms[ $room_id ] = 1;
            }else{
                $rooms[ $room_id ]++;
            }
        }
        $prices = get_post_meta( $booking_id, '_hb_room_price', true );
        foreach( $rooms as $room_id => $num_of_rooms ) {
            $room = HB_Room::instance( $room_id )
                ->set_data(
                    array(
                        'num_of_rooms' => $num_of_rooms,
                        'check_in_date' => get_post_meta( $booking_id, '_hb_check_in_date', true ),
                        'check_out_date' => get_post_meta( $booking_id, '_hb_check_out_date', true )
                    )
                );
            $term = get_term( $room->room_type, 'hb_room_type' );
            if( $term ){
                $room_type = $term->name;
            }else{
                $room_type = __( 'Unknown', 'tp-hotel-booking' );
            }
            ?>
            <tr>
                <td><?php echo $room->name;?></td>
                <td><?php echo sprintf( '%s (%s)', $room_type, $room->capacity_title );?></td>
                <td align="right"><?php echo $num_of_rooms;?></td>
                <td align="right"><?php echo hb_format_price( ! empty( $prices[ $room_id ] ) ? $prices[ $room_id ] : 0, $currency_symbol );?></td>
            </tr>
        <?php
        }
        ?>
        <?php if( $coupon = get_post_meta( $booking_id, '_hb_coupon', true ) ){?>
            <tr>
                <th colspan="3" align="left"><?php printf( __( 'Coupon Applied (%s)', 'tp-hotel-booking' ), $coupon['code'] ); ?></th>
                <td align="right" class="negative-price">-<?php echo hb_format_price( $coupon['value'], $currency_symbol ); ?></td>
            </tr>
        <?php }?>
            <tr>
                <th colspan="3" align="left"><?php _e( 'Sub Total', 'tp-hotel-booking' ); ?></th>
                <td align="right"><?php echo hb_format_price( get_post_meta( $booking_id, '_hb_sub_total', true ), $currency_symbol ); ?></td>
            </tr>
            <tr>
                <th colspan="3" align="left"><?php _e( 'Price including tax', 'tp-hotel-booking' ); ?></th>
                <td align="right"><?php echo get_post_meta( $booking_id, '_hb_price_including_tax', true ) == 'yes' ? __( 'Yes', 'tp-hotel-booking') : __( 'No', 'tp-hotel-booking'); ?></td>
            </tr>
            <tr>
                <th colspan="3" align="left"><?php _e( 'Tax', 'tp-hotel-booking' ); ?></th>
                <td align="right"><?php echo get_post_meta( $booking_id, '_hb_tax', true ) * 100; ?>%</td>
            </tr>
            <tr>
                <th colspan="3" align="left"><?php _e( 'Total', 'tp-hotel-booking' ); ?></th>
                <td align="right"><?php echo hb_format_price( get_post_meta( $booking_id, '_hb_total', true ), $currency_symbol ); ?></td>
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
                    <?php _e( 'Payment Gateway', 'tp-hotel-booking' );?>
                </th>
                <td>
                    <?php echo get_post_meta( $booking_id, '_hb_method_title', true );?>
                </td>
            </tr>
            <tr>
                <th>
                    <?php _e( 'Booking status', 'tp-hotel-booking' );?>
                </th>
                <td>
                    <span class="hb-booking-status <?php echo get_post_status( $booking_id );?>"><?php echo hb_get_booking_status_label( $booking_id );?></span>
                </td>
            </tr>
        </tbody>
    </table>
    <?php if( $addition_information = get_post_field( 'post_content', $booking_id ) ){?>
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
                <?php echo $addition_information;?>
            </td>
        </tr>
        </tbody>
    </table>
    <?php }?>
</div>