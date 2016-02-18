<?php
$settings = HB_Settings::instance();
$authorize = $settings->get('authorize');
$authorize = wp_parse_args(
    $authorize,
    array(
        'enable'    => 'on',
        'sandbox'   => 'off',
        'api_login_id'     => '',
        'transaction_key' => ''
    )
);

$field_name = $settings->get_field_name('authorize');
?>
<table class="form-table">
    <tr>
        <th><?php _e( 'Enable', 'tp-hotel-booking-authorize-sim' ); ?></th>
        <td>
            <input type="hidden" name="<?php echo $field_name; ?>[enable]" value="off" />
            <input type="checkbox" name="<?php echo $field_name; ?>[enable]" <?php checked( $authorize['enable'] == 'on' ? 1 : 0, 1 ); ?> value="on" />
        </td>
    </tr>
    <tr>
        <th><?php _e( 'Sandbox Mode', 'tp-hotel-booking-authorize-sim' ); ?></th>
        <td>
            <input type="hidden" name="<?php echo $field_name; ?>[sandbox]" value="off" />
            <input type="checkbox" name="<?php echo $field_name; ?>[sandbox]" <?php checked( $authorize['sandbox'] == 'on' ? 1 : 0, 1 ); ?> value="on" />
        </td>
    </tr>
    <tr>
        <th><?php _e( 'API Login ID', 'tp-hotel-booking-authorize-sim' ); ?></th>
        <td>
            <input type="text" class="regular-text" name="<?php echo $field_name; ?>[api_login_id]" value="<?php echo $authorize['api_login_id']; ?>" />
        </td>
    </tr>
    <tr>
        <th><?php _e( 'Transaction Key', 'tp-hotel-booking-authorize-sim' ); ?></th>
        <td>
            <input type="text" class="regular-text" name="<?php echo $field_name; ?>[transaction_key]" value="<?php echo $authorize['transaction_key']; ?>" />
        </td>
    </tr>
</table>
