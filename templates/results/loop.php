<li class="hb-room clearfix">
    <h4 class="hb-room-name"><?php echo $room->name;?> (<?php echo $room->capacity_title;?>)</h4>
    <div class="hb-room-content">
        <div class="hb-room-thumbnail">
            <?php echo $room->thumbnail;?>
        </div>
        <?php ob_start();?>
        <div class="hb-room-">
            <?php
            $details = array();
            $room_details_total = 0;
            $start_date = hb_get_request( 'check_in_date' );
            $end_date = hb_get_request( 'check_out_date' );
            $nights = hb_count_nights_two_dates( $end_date, $start_date );
            for( $i = 0; $i < $nights; $i++ ){
                $c_date = $start_date + $i * HOUR_IN_SECONDS * 24;
                $date = date('w', $c_date );
                //echo $i * HOUR_IN_SECONDS, "---";
                if( ! $details[ $date ] ){
                    $details[ $date ] = array(
                        'count' => 0,
                        'price' => 0
                    );
                }
                $details[ $date ]['count'] ++;
                $details[ $date ]['price'] = $room->get_total( $c_date, 1, 1 );
            }
            echo '<table>';
            echo '<tr>';
            if( $details ) foreach( $details as $d => $info ){
                echo '<td>';
                echo sprintf( '%d x %s', $info['count'], '[' . hb_date_to_name( $d ) . ']', $info['price'] );
                echo '</td>';
            }
            echo '</tr>';

            echo '<tr>';
            if( $details ) foreach( $details as $d => $info ){
                $room_details_sub_total = round( $info['price'] * $info['count'], 2 );
                echo '<td>';
                echo sprintf( '%.2f', $room_details_sub_total );
                echo '</td>';

                $room_details_total += $room_details_sub_total;
            }
            echo '</tr>';
            echo '</table>';
            ?>
        </div>
        <?php $room_details = ob_get_clean();?>
        <div class="hb-room-info">
            <ul class="hb-room-meta">
                <li>
                    <label><?php _e( 'Capacity', 'tp-hotel-booking' );?></label>
                    <div><?php echo $room->capacity;?></div>
                </li>
                <li>
                    <label><?php _e( 'Max Child', 'tp-hotel-booking' );?></label>
                    <div><?php echo $room->max_child;?></div>
                </li>
                <li>
                    <label><?php _e( 'Price', 'tp-hotel-booking' );?></label>
                    <div><?php echo $room_details_total;?></div>
                </li>
                <li>
                    <label><?php _e( 'Select room', 'tp-hotel-booking' );?></label>
                    <div><?php echo $room->dropdown_room;?></div>
                </li>
            </ul>
            <input type="hidden" name="hb-room-details-total[<?php echo $room->post->ID;?>]" value="<?php echo $room_details_total;?>" />
            <a href=""><?php _e( 'View details', 'tp-hotel-booking' );?></a>
        </div>
        <?php echo $room_details;?>
    </div>
</li>