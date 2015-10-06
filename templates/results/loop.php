<?php
$gallery = $room->gallery;
$featured = $gallery ? array_shift( $gallery ) : false;
?>
<li class="hb-room clearfix">

    <form name="hb-search-results" class="hb-search-room-results">

        <h4 class="hb-room-name">
            <?php echo $room->name;?> (<?php echo $room->capacity_title;?>)
        </h4>
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
                                    'name'              => 'hb-num-of-rooms',
                                    'min'               => 1,
                                    'show_option_none'  => __( 'Select', 'tp-hotel-booking' ),
                                    'max'               => $room->post->available_rooms,
                                    'class'             => 'number_room_select'
                                )
                            );?></div>
                    </li>
                    <li><button class="hb_add_to_cart"><?php _e( 'Add To Cart', 'tp-hotel-booking' ) ?></button></li>
                </ul>
                <a href="" class="hb-view-booking-room-details"><?php _e( 'View details', 'tp-hotel-booking' );?></a>
            </div>
            <?php hb_get_template( 'results/booking-room-details.php', array( 'room' => $room ) );?>
        </div>

        <?php wp_nonce_field( 'hb_booking_nonce_action', 'nonce' ); ?>
        <input type="hidden" name="check_in_date" value="<?php echo hb_get_request( 'check_in_date' );?>" />
        <input type="hidden" name="check_out_date" value="<?php echo hb_get_request( 'check_out_date' );?>">
        <input type="hidden" name="room-id" value="<?php echo esc_attr( $room->post->ID ); ?>">
        <input type="hidden" name="hotel-booking" value="cart">
        <input type="hidden" name="action" value="hotel_booking_ajax_add_to_cart" />
    </form>

</li>