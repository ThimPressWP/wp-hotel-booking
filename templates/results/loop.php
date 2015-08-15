<?php
$gallery = $room->gallery;
$featured = $gallery ? array_shift( $gallery ) : false;
?>
<li class="hb-room clearfix">
    <h4 class="hb-room-name"><?php echo $room->name;?> (<?php echo $room->capacity_title;?>)</h4>
    <div class="hb-room-content">
        <div class="hb-room-thumbnail">
            <?php if( $featured ):?>
            <a class="hb-room-gallery" rel="hb-room-gallery-<?php echo $room->post->ID;?>" data-lightbox="hb-room-gallery[<?php echo $room->post->ID;?>]" data-title="<?php echo $featured['alt'];?>" href="<?php echo $featured['src'];?>">
                <img src="<?php echo $featured['thumb'];?>" alt="<?php echo $featured['alt'];?>" data-id="<?php echo $featured['id'];?>" />
            </a>
            <?php endif;?>
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
                    <label><?php _e( 'Select number of room', 'tp-hotel-booking' );?></label>
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
        <div class="hb-room-type-gallery">
            <?php if( $gallery ): foreach( $gallery as $image ){?>
                <a  class="hb-room-gallery" rel="hb-room-gallery-<?php echo $room->post->ID;?>" data-lightbox="hb-room-gallery[<?php echo $room->post->ID;?>]" data-title="<?php echo $image['alt'];?>" href="<?php echo $image['src'];?>">
                    <img src="<?php echo $image['thumb'];?>" alt="<?php echo $image['alt'];?>" data-id="<?php echo $image['id'];?>" />
                </a>
            <?php } endif;?>
        </div>
    </div>
</li>