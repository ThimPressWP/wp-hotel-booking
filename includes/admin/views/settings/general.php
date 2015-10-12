<?php
$settings = hb_settings();
?>
<h3 class="description"><?php _e( 'General Settings', 'tp-hotel-booking' );?></h3>
<table class="form-table">
    <tr>
        <th><?php _e( 'Search Page', 'tp-hotel-booking' );?></th>
        <td>
            <?php
                wp_dropdown_pages(
                    array(
                        'show_option_none'  => __( '---Select page---', 'tp-hotel-booking' ),
                        'option_none_value' => 0,
                        'name'      => $settings->get_field_name('search_page_id'),
                        'selected'  => $settings->get('search_page_id')
                    )
                );
            ?>
        </td>
    </tr>
    <tr>
        <th><?php _e( 'Install and use page: ', 'tp-hotel-booking' ) ?></th>
        <td>
            <input type="hidden" name="<?php echo $settings->get_field_name('my_room_page');?>" value="0" />
            <input type="checkbox" name="<?php echo $settings->get_field_name('my_room_page');?>" <?php checked( $settings->get('my_room_page') ? 1 : 0, 1 );?> value="1" />
            <label for="<?php echo $settings->get_field_name('my_room_page');?>"><?php _e( 'My Room', 'tp-hotel-booking' ) ?></label>

            <input type="hidden" name="<?php echo $settings->get_field_name('check_out');?>" value="0" />
            <input type="checkbox" name="<?php echo $settings->get_field_name('check_out');?>" <?php checked( $settings->get('check_out') ? 1 : 0, 1 );?> value="1" />
            <label for="<?php echo $settings->get_field_name('check_out');?>"><?php _e( 'My Room', 'tp-hotel-booking' ) ?></label>
        </td>
    </tr>
    <tr>
        <th><?php _e( 'Terms and Conditions Page', 'tp-hotel-booking' );?></th>
        <td>
            <?php
            wp_dropdown_pages(
                array(
                    'show_option_none'  => __( '---Select page---', 'tp-hotel-booking' ),
                    'option_none_value' => 0,
                    'name'      => $settings->get_field_name('terms_page_id'),
                    'selected'  => $settings->get('terms_page_id')
                )
            );
            ?>
        </td>
    </tr>
    <tr>
        <th><?php _e( 'Currency', 'tp-hotel-booking' );?></th>
        <td>
            <select name="<?php echo $settings->get_field_name('currency');?>">
                <?php if( $currencies = hb_payment_currencies() ): foreach( $currencies as $code => $title ){?>
                <option value="<?php echo $code;?>" <?php selected( $code == $settings->get('currency') );?>><?php echo $title;?></option>
                <?php } endif;?>
            </select>
        </td>
    </tr>
    <tr>
        <th><?php _e( 'Currency Position', 'tp-hotel-booking' );?></th>
        <td>
            <select name="<?php echo $settings->get_field_name('price_currency_position');?>" tabindex="-1">
                <option value="left" <?php selected( $settings->get('price_currency_position') == 'left' );?>><?php _e('Left ( $69.99 )', 'tp-hotel-booking') ?></option>
                <option value="right" <?php selected( $settings->get('price_currency_position') == 'right' );?>><?php _e('Right ( 69.99$ )', 'tp-hotel-booking') ?></option>
                <option value="left_with_space" <?php selected( $settings->get('price_currency_position') == 'left_with_space' );?>><?php _e('Left with space ( $ 69.99 )', 'tp-hotel-booking') ?></option>
                <option value="right_with_space" <?php selected( $settings->get('price_currency_position') == 'right_with_space' );?>><?php _e('Right with space ( 69.99 $ )', 'tp-hotel-booking') ?></option>
            </select>
        </td>
    </tr>
    <tr>
        <th><?php _e( 'Thousands Separator', 'tp-hotel-booking' );?></th>
        <td>
            <input type="text" class="regular-text" name="<?php echo $settings->get_field_name('price_thousands_separator');?>" value="<?php echo $settings->get('price_thousands_separator');?>" />
        </td>
    </tr>
    <tr>
        <th><?php _e( 'Decimals Separator', 'tp-hotel-booking' );?></th>
        <td>
            <input type="text" class="regular-text" name="<?php echo $settings->get_field_name('price_decimals_separator');?>" value="<?php echo $settings->get('price_decimals_separator');?>" />
        </td>
    </tr>
    <tr>
        <th><?php _e( 'Number of decimal', 'tp-hotel-booking' );?></th>
        <td>
            <input type="text" class="regular-text" name="<?php echo $settings->get_field_name('price_number_of_decimal');?>" value="<?php echo $settings->get('price_number_of_decimal');?>" />
        </td>
    </tr>
    <tr>
        <th><?php _e( 'Tax', 'tp-hotel-booking' );?></th>
        <td>
            <input type="text" class="regular-text" name="<?php echo $settings->get_field_name('tax');?>" value="<?php echo $settings->get('tax');?>" />%
        </td>
    </tr>
    <tr>
        <th><?php _e( 'Price including tax', 'tp-hotel-booking' );?></th>
        <td>
            <input type="hidden" name="<?php echo $settings->get_field_name('price_including_tax');?>" value="0" />
            <input type="checkbox" name="<?php echo $settings->get_field_name('price_including_tax');?>" <?php checked( $settings->get('price_including_tax') ? 1 : 0, 1 );?> value="1" />
        </td>
    </tr>
    <tr>
        <th><?php _e( 'Price display', 'tp-hotel-booking' );?></th>
        <td>
            <select name="<?php echo $settings->get_field_name('price_display');?>" tabindex="-1">
                <option value="min" <?php selected( $settings->get('price_display') == 'min' );?>><?php _e('Min', 'tp-hotel-booking') ?></option>
                <option value="max" <?php selected( $settings->get('price_display') == 'max' );?>><?php _e('Max', 'tp-hotel-booking') ?></option>
                <option value="min_to_max" <?php selected( $settings->get('price_display') == 'min_to_max' );?>><?php _e('Min to Max', 'tp-hotel-booking') ?></option>
            </select>
        </td>
    </tr>

    <tr>
        <th><?php _e( 'Advance Payment', 'tp-hotel-booking' );?></th>
        <td>
            <input type="text" class="regular-text" name="<?php echo $settings->get_field_name('advance_payment');?>" value="<?php echo $settings->get('advance_payment');?>" />%
        </td>
    </tr>
    <tr>
        <th><?php _e( 'Enable Coupon', 'tp-hotel-booking' );?></th>
        <td>
            <input type="hidden" name="<?php echo $settings->get_field_name('enable_coupon');?>" value="0" />
            <input type="checkbox" name="<?php echo $settings->get_field_name('enable_coupon');?>" <?php checked( $settings->get('enable_coupon') ? 1 : 0, 1 );?> value="1" />
        </td>
    </tr>
</table>