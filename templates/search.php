<div id="hotel-booking-search">
    <h3><?php _e( 'Search your room', 'tp-hotel-booking' );?></h3>
    <form name="hb-search-form">
        <ul class="hb-form-table">
            <li class="hb-form-field">
                <label><?php _e( 'Check-in date', 'tp-hotel-booking' );?></label>
                <div class="hb-form-field-input">
                    <input type="text" name="check_in_date" />
                </div>
            </li>
            <li class="hb-form-field">
                <label><?php _e( 'Check-in date', 'tp-hotel-booking' );?></label>
                <div class="hb-form-field-input">
                    <input type="text" name="check_out_date" />
                </div>
            </li>
            <li class="hb-form-field">
                <label><?php _e( 'Adults', 'tp-hotel-booking' );?></label>
                <div class="hb-form-field-input">
                    <?php
                        hb_dropdown_room_capacities(
                            array(
                                'name'      => 'adults',
                                'selected'  => ! empty( $_REQUEST['adults'] ) ? $_REQUEST['adults'] : ''
                            )
                        );
                    ?>
                </div>
            </li>
            <li class="hb-form-field">
                <label><?php _e( 'Child', 'tp-hotel-booking' );?></label>
                <div class="hb-form-field-input">
                    <?php
                    hb_dropdown_child_per_room(
                        array(
                            'name'      => 'max_child',
                            'selected'  => ! empty( $_REQUEST['max_child'] ) ? $_REQUEST['max_child'] : ''
                        )
                    );
                    ?>
                </div>
            </li>
        </ul>
        <p>
        <button type="submit"><?php _e( 'Search', 'tp-hotel-booking' );?></button>
        </p>
    </form>
</div>