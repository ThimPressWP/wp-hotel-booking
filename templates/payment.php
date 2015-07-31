<?php
$start_date = hb_get_request( 'check_in_date' );
$end_date = hb_get_request( 'check_out_date' );
$rooms = $_REQUEST['hb-num-of-rooms'];
$total_rooms = 0;
if( $rooms ) foreach( $rooms as $room ) {
    $total_rooms += $room;
}
$total_nights = hb_count_nights_two_dates( $end_date, $start_date );
$total = 0;
foreach( $_REQUEST['hb-num-of-rooms'] as $room_id => $num ){
    $total += $num * $_REQUEST['hb-room-details-total'][ $room_id ];
}
$tax = 0.12;
$grand_total = $total + $total * $tax;
?>
<div id="hotel-booking-payment">

    <form name="hb-payment-form">
        <h3><?php _e( 'Booking Details', 'tp-hotel-booking' );?></h3>
        <ul>
            <li>
                <label><?php _e( 'Check-in Date', 'tp-hotel-booking' );?></label>
                <div><?php echo hb_get_request('check_in_date');?></div>
            </li>
            <li>
                <label><?php _e( 'Check-out Date', 'tp-hotel-booking' );?></label>
                <div><?php echo hb_get_request('check_out_date');?></div>
            </li>
            <li>
                <label><?php _e( 'Total Nights', 'tp-hotel-booking' );?></label>
                <div><?php echo $total_rights;?></div>
            </li>
            <li>
                <label><?php _e( 'Total Rooms', 'tp-hotel-booking' );?></label>
                <div><?php echo $total_rooms;?></div>
            </li>
        </ul>
        <h3><?php _e( 'Booking Rooms', 'tp-hotel-booking' );?></h3>
        <table>
            <thead>
                <th><?php _e( 'Number of rooms', 'tp-hotel-booking' );?></th>
                <th><?php _e( 'Room type', 'tp-hotel-booking' );?></th>
                <th><?php _e( 'Capacity', 'tp-hotel-booking' );?></th>
                <th><?php _e( 'Gross Total', 'tp-hotel-booking' );?></th>
            </thead>
        <?php if( $rooms ) foreach( $rooms as $id => $num_of_rooms ){?>
            <?php
            if( ! $num_of_rooms ) continue;
            $room = HB_Room::instance( $id );
            ?>
            <tr>
                <td><?php echo $num_of_rooms;?></td>
                <td><?php echo $room->name;?> (<?php echo $room->capacity_title;?>)</td>
                <td><?php echo sprintf( _n( '%d adult', '%d adults', $room->capacity, 'tp-hotel-booking' ), $room->capacity );?> </td>
                <td><?php echo $room->get_total( $start_date, $total_nights, $num_of_rooms );?></td>
            </tr>
        <?php }?>
            <tr>
                <td colspan="3"><?php _e( 'Sub Total', 'tp-hotel-booking' );?></td>
                <td><?php echo $total;?></td>
            </tr>
            <?php if( $tax ){?>
            <tr>
                <td colspan="3"><?php _e( 'Tax', 'tp-hotel-booking' );?></td>
                <td><?php echo $tax;?></td>
            </tr>
            <?php }?>
            <tr>
                <td colspan="3"><?php _e( 'Grand Total', 'tp-hotel-booking' );?></td>
                <td><?php echo $grand_total;?></td>
            </tr>
        </table>
        <input type="hidden" name="hotel-booking" value="confirm" />
        <input type="hidden" name="check_in_date" value="<?php echo $start_date;?>" />
        <input type="hidden" name="check_out_date" value="<?php echo $end_date;?>">
        <p>
            <button type="submit"><?php _e( 'Confirm', 'tp-hotel-booking' );?></button>
        </p>
    </form>
</div>