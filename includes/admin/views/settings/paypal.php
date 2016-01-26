<?php
$settings = HB_Settings::instance();
$paypal = $settings->get('paypal');
$paypal = wp_parse_args(
    $paypal,
    array(
        'enable'    => 'on',
        'email'     => '',
        'sandbox'   => 'off',
        'sandbox_email' => ''
    )
);

$field_name = $settings->get_field_name('paypal');
?>
<table class="form-table">
    <tr>
        <th><?php _e( 'Enable', 'tp-hotel-booking' ); ?></th>
        <td>
            <input type="hidden" name="<?php echo $field_name; ?>[enable]" value="off" />
            <input type="checkbox" name="<?php echo $field_name; ?>[enable]" <?php checked( $paypal['enable'] == 'on' ? 1 : 0, 1 ); ?> value="on" />
        </td>
    </tr>
    <tr>
        <th><?php _e( 'Paypal email', 'tp-hotel-booking' ); ?></th>
        <td>
            <input type="email" class="regular-text" name="<?php echo $field_name; ?>[email]" value="<?php echo $paypal['email']; ?>" />
        </td>
    </tr>
    <tr>
        <th><?php _e( 'Sandbox Mode', 'tp-hotel-booking' ); ?></th>
        <td>
            <input type="hidden" name="<?php echo $field_name; ?>[sandbox]" value="off" />
            <input type="checkbox" name="<?php echo $field_name; ?>[sandbox]" <?php checked( $paypal['sandbox'] == 'on' ? 1 : 0, 1 ); ?> value="on" />
        </td>
    </tr>
    <tr>
        <th><?php _e( 'Paypal sandbox email', 'tp-hotel-booking' ); ?></th>
        <td>
            <input type="email" class="regular-text" name="<?php echo $field_name; ?>[sandbox_email]" value="<?php echo $paypal['sandbox_email']; ?>" />
        </td>
    </tr>
</table>
