<div class="hb-booking-room-details">
    <?php
    $details = $room->get_booking_room_details();
    echo '<table>';
    echo '<tr>';
    if( $details ) foreach( $details as $d => $info ){
        echo '<td>';
        echo sprintf( '%d x %s', $info['count'], '[' . hb_date_to_name( $d )  . ']', hb_format_price( $info['price'] ) );
        echo '</td>';
    }
    echo '</tr>';

    echo '<tr>';
    if( $details ) foreach( $details as $d => $info ){
        $room_details_sub_total = round( $info['price'] * $info['count'], 2 );
        echo '<td>';
        echo hb_format_price( $room_details_sub_total );
        echo '</td>';
    }
    echo '</tr>';
    echo '</table>';
    ?>
</div>