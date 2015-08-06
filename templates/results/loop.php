<li class="hb-room clearfix">
    <h4 class="hb-room-name"><?php echo $room->name;?> (<?php echo $room->capacity_title;?>)</h4>
    <div class="hb-room-content">
        <div class="hb-room-thumbnail">
            <?php echo $room->thumbnail;?>
        </div>

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
                    <div><?php echo hb_format_price( $room->room_details_total );?></div>
                </li>
                <li>
                    <label><?php _e( 'Select room', 'tp-hotel-booking' );?></label>
                    <div>
                        <?php
                        hb_dropdown_numbers(
                            array(
                                'name'              => 'hb-num-of-rooms[' . $room->post->ID . ']',
                                'min'               => 1,
                                'show_option_none'  => __( '--Select--', 'tp-hotel-booking' ),
                                'max'               => $room->post->available_rooms
                            )
                        );?></div>
                </li>
            </ul>
            <a href="" class="hb-view-booking-room-details"><?php _e( 'View details', 'tp-hotel-booking' );?></a>
        </div>
        <?php hb_get_template( 'results/booking-room-details.php', array( 'room' => $room ) );?>
    </div>
</li>