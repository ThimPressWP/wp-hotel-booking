<?php
$check_in_date = hb_get_request( 'check_in_date' );
$check_out_date = hb_get_request( 'check_out_date' );
$adults = 2;
$max_child = 2;
?>
<div id="hotel-booking-search">
    <h3><?php _e( 'Search your room', 'tp-hotel-booking' );?></h3>
    <form name="hb-search-form" action="<?php echo $search_page;?>">
        <ul class="hb-form-table">
            <li class="hb-form-field">
                <label><?php _e( 'Check-in date', 'tp-hotel-booking' );?></label>
                <div class="hb-form-field-input">
                    <input type="text" name="check_in_date" id="check_in_date" value="<?php echo $check_in_date;?>" placeholder="<?php _e( 'Check in date', 'tp-hotel-booking' );?>" />
                </div>
            </li>
            <li class="hb-form-field">
                <label><?php _e( 'Check-out date', 'tp-hotel-booking' );?></label>
                <div class="hb-form-field-input">
                    <input type="text" name="check_out_date" id="check_out_date" value="<?php echo $check_out_date;?>" placeholder="<?php _e( 'Check out date', 'tp-hotel-booking' );?>" />
                </div>
            </li>
            <li class="hb-form-field">
                <label><?php _e( 'Adults per room', 'tp-hotel-booking' );?></label>
                <div class="hb-form-field-input">
                    <?php
                        hb_dropdown_numbers(
                            array(
                                'name'      => 'adults_capacity',
                                'min'       => 1,
                                'max'       => hb_get_max_capacity_of_rooms(),
                            )
                        );
                    ?>
                </div>
            </li>
            <li class="hb-form-field">
                <label><?php _e( 'Child per room', 'tp-hotel-booking' );?></label>
                <div class="hb-form-field-input">
                    <?php
                    hb_dropdown_numbers(
                        array(
                            'name'      => 'max_child',
                            'min'   => 1,
                            'max'   => hb_get_max_child_of_rooms(),
                            'show_option_none'  => __( '--Select--', 'tp-hotel-booking' ),
                            'option_none_value' => 0
                        )
                    );
                    ?>
                </div>
            </li>
        </ul>
        <?php //echo $ajax_nonce = wp_create_nonce( "hb_search_nonce_action" );?>
        <?php wp_nonce_field( 'hb_search_nonce_action', 'nonce' );?>
        <input type="hidden" name="hotel-booking" value="results" />
        <input type="hidden" name="action" value="hotel_booking_parse_search_params" />
        <p>
        <button type="submit"><?php _e( 'Search', 'tp-hotel-booking' );?></button>
        </p>
    </form>
</div>