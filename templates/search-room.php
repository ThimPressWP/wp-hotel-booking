<?php
$check_in_date = hb_get_request( 'check_in_date' );
$check_out_date = hb_get_request( 'check_out_date' );
$adults = 2;
$max_child = 2;
?>
<div id="hotel-booking-search-<?php echo uniqid(); ?>" class="hotel-booking-search">
<?php
    // display title widget or shortcode
    $atts = array();
    if( $args && isset($args['atts']) )
        $atts = $args['atts'];
    if ( !isset($atts['show_title']) || strtolower($atts['show_title']) === 'true' ):
?>
    <h3><?php _e( 'Search your room', 'tp-hotel-booking' );?></h3>
<?php endif; ?>
    <form name="hb-search-form" action="<?php echo $search_page;?>">
        <ul class="hb-form-table">
            <li class="hb-form-field">
                <?php hb_render_label_shortcode( $atts, 'show_label', 'Arrival Date', 'true'); ?>
                <div class="hb-form-field-input hb_input_field">
                    <input type="text" name="check_in_date" id="check_in_date" class="hb_input_date_check" value="<?php echo $check_in_date;?>" placeholder="<?php _e( 'Arrival Date', 'tp-hotel-booking' );?>" />
                </div>
            </li>
            <li class="hb-form-field">
                <?php hb_render_label_shortcode( $atts, 'show_label', 'Departure Date', 'true'); ?>
                <div class="hb-form-field-input hb_input_field">
                    <input type="text" name="check_out_date" id="check_out_date" class="hb_input_date_check" value="<?php echo $check_out_date;?>" placeholder="<?php _e( 'Departure Date', 'tp-hotel-booking' );?>" />
                </div>
            </li>
            <li class="hb-form-field">
                <?php hb_render_label_shortcode( $atts, 'show_label', 'Adults', 'true'); ?>
                <div class="hb-form-field-input">
                    <?php
                        hb_dropdown_numbers(
                            array(
                                'name'      => 'adults_capacity',
                                'min'       => 1,
                                'max'       => hb_get_max_capacity_of_rooms(),
                                'show_option_none'  => __( 'Adults', 'tp-hotel-booking' ),
                                'option_none_value' => 0
                            )
                        );
                    ?>
                </div>
            </li>
            <li class="hb-form-field">
                <?php hb_render_label_shortcode( $atts, 'show_label', 'Children', 'true'); ?>
                <div class="hb-form-field-input">
                    <?php
                    hb_dropdown_numbers(
                        array(
                            'name'      => 'max_child',
                            'min'   => 1,
                            'max'   => hb_get_max_child_of_rooms(),
                            'show_option_none'  => __( 'Children', 'tp-hotel-booking' ),
                            'option_none_value' => 0
                        )
                    );
                    ?>
                </div>
            </li>
        </ul>
        <?php //echo $ajax_nonce = wp_create_nonce( "hb_search_nonce_action" );?>
        <?php wp_nonce_field( 'hb_search_nonce_action', 'nonce' ); ?>
        <input type="hidden" name="hotel-booking" value="results" />
        <input type="hidden" name="action" value="hotel_booking_parse_search_params" />
        <p class="hb-submit">
            <button type="submit"><?php _e( 'Check Availability', 'tp-hotel-booking' );?></button>
        </p>
    </form>
</div>