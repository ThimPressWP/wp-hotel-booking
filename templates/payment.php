<?php
$start_date = hb_get_request( 'check_in_date' );
$end_date = hb_get_request( 'check_out_date' );
$rooms = $_REQUEST['hb-num-of-rooms'];
$total_rooms = 0;
if( $rooms ) foreach( $rooms as $room ) {
    $total_rooms += $room;
}
$total_rights = hb_count_nights_to_dates( $end_date, $start_date );
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
        <?php if( $rooms ) foreach( $rooms as $room ){?>
        <?php }?>
        <input type="hidden" name="hotel-booking" value="confirm" />
        <input type="hidden" name="check_in_date" value="<?php echo $start_date;?>" />
        <input type="hidden" name="check_out_date" value="<?php echo $end_date;?>">
        <p>
            <button type="submit"><?php _e( 'Confirm', 'tp-hotel-booking' );?></button>
        </p>
    </form>
</div>