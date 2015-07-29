<?php
$settings = hb_settings();
?>
<table class="form-table">
    <tr>
        <th><?php _e( 'Hotel Name', 'tp-hotel-booking' );?></th>
        <td>
            <input type="text" class="regular-text" name="<?php echo $settings->get_field_name('hotel_name');?>" value="<?php echo $settings->get('hotel_name');?>" />
        </td>
    </tr>
    <tr>
        <th><?php _e( 'Address', 'tp-hotel-booking' );?></th>
        <td>
            <input type="text" class="regular-text" name="<?php echo $settings->get_field_name('hotel_address');?>" value="<?php echo $settings->get('hotel_address');?>" />
        </td>
    </tr>
    <tr>
        <th><?php _e( 'City', 'tp-hotel-booking' );?></th>
        <td>
            <input type="text" class="regular-text" name="<?php echo $settings->get_field_name('hotel_city');?>" value="<?php echo $settings->get('hotel_city');?>" />
        </td>
    </tr>
    <tr>
        <th><?php _e( 'State', 'tp-hotel-booking' );?></th>
        <td>
            <input type="text" class="regular-text" name="<?php echo $settings->get_field_name('hotel_state');?>" value="<?php echo $settings->get('hotel_state');?>" />
        </td>
    </tr>
    <tr>
        <th><?php _e( 'Country', 'tp-hotel-booking' );?></th>
        <td>
            <input type="text" class="regular-text" name="<?php echo $settings->get_field_name('hotel_country');?>" value="<?php echo $settings->get('hotel_country');?>" />
        </td>
    </tr>
    <tr>
        <th><?php _e( 'Zip / Postal Code', 'tp-hotel-booking' );?></th>
        <td>
            <input type="text" class="regular-text" name="<?php echo $settings->get_field_name('hotel_zip_code');?>" value="<?php echo $settings->get('hotel_zip_code');?>" />
        </td>
    </tr>
    <tr>
        <th><?php _e( 'Phone Number', 'tp-hotel-booking' );?></th>
        <td>
            <input type="text" class="regular-text" name="<?php echo $settings->get_field_name('hotel_phone_number');?>" value="<?php echo $settings->get('hotel_phone_number');?>" />
        </td>
    </tr>
    <tr>
        <th><?php _e( 'Fax', 'tp-hotel-booking' );?></th>
        <td>
            <input type="text" class="regular-text" name="<?php echo $settings->get_field_name('hotel_fax_number');?>" value="<?php echo $settings->get('hotel_fax_number');?>" />
        </td>
    </tr>
    <tr>
        <th><?php _e( 'Email', 'tp-hotel-booking' );?></th>
        <td>
            <input type="email" class="regular-text" name="<?php echo $settings->get_field_name('hotel_email_address');?>" value="<?php echo $settings->get('hotel_email_address');?>" />
        </td>
    </tr>
</table>