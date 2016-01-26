<?php
$settings = hb_settings();
?>
<!-- New Booking block -->
<h3><?php _e( 'New Booking', 'tp-hotel-booking' ); ?></h3>
<p class="description"><?php _e( 'New booking emails are sent when a booking is received.', 'tp-hotel-booking'); ?></p>
<table class="form-table">
    <tr>
        <th><?php _e( 'Enable', 'tp-hotel-booking' ); ?></th>
        <td>
            <input type="hidden" name="<?php echo $settings->get_field_name('email_new_booking_enable'); ?>" value="<?php echo $settings->get('email_new_booking_enable') ? 1 : 0; ?>" />
            <input type="checkbox" name="<?php echo $settings->get_field_name('email_new_booking_enable'); ?>" <?php checked( $settings->get('email_new_booking_enable') ? true : false, true ); ?> value="1" />
        </td>
    </tr>
    <tr class="<?php echo $settings->get_field_name('email_new_booking_enable'); ?>">
        <th><?php _e( 'Recipient(s)', 'tp-hotel-booking' ); ?></th>
        <td>
            <input type="text" class="regular-text" name="<?php echo $settings->get_field_name('email_new_booking_recipients'); ?>" value="<?php echo $settings->get('email_new_booking_recipients'); ?>" />
            <p class="description"><?php printf( __( 'Enter recipients (comma separated) for this email. Defaults to <code>%s</code>.', 'tp-hotel-booking' ), get_option( 'admin_email' ) ); ?></p>
        </td>
    </tr>
    <tr class="<?php echo $settings->get_field_name('email_new_booking_enable'); ?>">
        <th><?php _e( 'Subject', 'tp-hotel-booking' ); ?></th>
        <td>
            <input type="text" class="regular-text" name="<?php echo $settings->get_field_name('email_new_booking_subject'); ?>" value="<?php echo $settings->get('email_new_booking_subject'); ?>" />
            <p class="description"><?php _e( 'Subject for email. Leave blank to use the default: <code>[{site_title}] New customer booking ({booking_number}) - {booking_date}</code>.', 'tp-hotel-booking' ); ?></p>
        </td>
    </tr>
    <tr class="<?php echo $settings->get_field_name('email_new_booking_enable'); ?>">
        <th><?php _e( 'Email Heading', 'tp-hotel-booking' ); ?></th>
        <td>
            <input type="text" class="regular-text" name="<?php echo $settings->get_field_name('email_new_booking_heading'); ?>" value="<?php echo $settings->get('email_new_booking_heading'); ?>" />
            <p class="description"><?php _e( 'The main heading displays in the top of email. Default heading: <code>New customer booking</code>.', 'tp-hotel-booking' ); ?></p>
        </td>
    </tr>
    <tr class="<?php echo $settings->get_field_name('email_new_booking_enable'); ?>">
        <th><?php _e( 'Email Format', 'tp-hotel-booking' ); ?></th>
        <td>
            <?php
            $template_formats = array(
                'plain'     => __( 'Plain Text', 'tp-hotel-booking' ),
                'html'      => __( 'HTML', 'tp-hotel-booking' )
            );
            ?>
            <select name="<?php echo $settings->get_field_name('email_new_booking_format'); ?>">
                <?php foreach( $template_formats as $k => $v ){?>
                <option value="<?php echo $k; ?>" <?php selected( $k == $settings->get('email_new_booking_format') ); ?>><?php echo $v; ?></option>
                <?php }?>
            </select>
        </td>
    </tr>
</table>
