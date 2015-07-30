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
                    <div><?php echo $room->price;?></div>
                </li>
                <li>
                    <label><?php _e( 'Select room', 'tp-hotel-booking' );?></label>
                    <div><?php echo $room->dropdown_room;?></div>
                </li>
            </ul>
            <a href=""><?php _e( 'View details', 'tp-hotel-booking' );?></a>
        </div>
    </div>
</li>