<?php
$settings = hb_settings();
?>
<!-- Email Sender Options block -->
<h3><?php _e( 'Email Sender', 'tp-hotel-booking' );?></h3>
<p class="description"><?php _e( 'The name and email address of the sender displays in email', 'tp-hotel-booking' );?></p>
<table class="form-table">
    <tr>
        <th><?php _e( 'From Name', 'tp-hotel-booking' );?></th>
        <td>
            <input type="text" class="regular-text" name="<?php echo $settings->get_field_name('email_general_from_name');?>" value="<?php echo $settings->get('email_general_from_name');?>" placeholder="<?php _e( 'E.g: John Smith', 'tp-hotel-booking' );?>" />
        </td>
    </tr>
    <tr>
        <th><?php _e( 'From Email', 'tp-hotel-booking' );?></th>
        <td>
            <input type="email" class="regular-text" name="<?php echo $settings->get_field_name('email_general_from_email');?>" value="<?php echo $settings->get('email_general_from_email');?>" placeholder="<?php _e( 'E.g: yourmail@yourdomain.com', 'tp-hotel-booking' );?>" />
        </td>
    </tr>
</table>