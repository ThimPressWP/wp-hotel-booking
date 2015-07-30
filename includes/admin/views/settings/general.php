<?php
$settings = hb_settings();
?>
<table class="form-table">
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
                <option value="left" <?php selected( $settings->get('price_currency_position') == 'left' );?>>Left ( $69.99 )</option>
                <option value="right" <?php selected( $settings->get('price_currency_position') == 'right' );?>>Right ( 69.99$ )</option>
                <option value="left_with_space" <?php selected( $settings->get('price_currency_position') == 'left_with_space' );?>>Left with space ( $ 69.99 )</option>
                <option value="right_with_space" <?php selected( $settings->get('price_currency_position') == 'right_with_space' );?>>Right with space ( 69.99 $ )</option>
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
</table>