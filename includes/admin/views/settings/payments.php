<?php
$settings = hb_settings();
?>
<table class="form-table">
    <tr>
        <th><?php _e( 'Hotel Name', 'tp-hotel-booking' );?></th>
        <td>
            <input type="text" class="regular-text" name="<?php echo $settings->get_field_name('paypal');?>[username]" value="<?php echo $settings->get('paypal');?>" />
        </td>
    </tr>
</table>