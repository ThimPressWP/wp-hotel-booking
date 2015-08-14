<?php
$settings = HB_Settings::instance();
$payment = $settings->get('offline-payment');
$payment = wp_parse_args(
    $payment,
    array(
        'enable'        => 'off',
        'email_subject' => 'Offline payment email subject',
        'email_content' => 'Offline payment email content'
    )
);

$field_name = $settings->get_field_name('offline-payment');
?>
<table class="form-table">
    <tr>
        <th><?php _e( 'Enable', 'tp-hotel-booking' );?></th>
        <td>
            <input type="hidden" name="<?php echo $field_name;?>[enable]" value="off" />
            <input type="checkbox" name="<?php echo $field_name;?>[enable]" <?php checked( $payment['enable'] == 'on' ? 1 : 0, 1 );?> value="on" />
        </td>
    </tr>
    <tr>
        <th><?php _e( 'Email Subject', 'tp-hotel-booking' );?></th>
        <td>
            <input type="text" class="regular-text" name="<?php echo $field_name;?>[email_subject]" value="<?php echo esc_attr( $payment['email_subject'] );?>" />
        </td>
    </tr>
    <tr>
        <th><?php _e( 'Email Content', 'tp-hotel-booking' );?></th>
        <td>
        <?php wp_editor( $payment['email_content'], "{$field_name}_email_content" );?>
        <textarea style="display: none;" name="<?php echo $field_name;?>[email_content]"><?php echo $payment['email_content'];?></textarea>
        </td>
    </tr>
</table>
<script type="text/javascript">
    jQuery(function($){
        $('form[name="hb-admin-settings-form"]').submit(function(){
            tinymce.triggerSave();
            $('textarea[name^="tp_hotel_booking_offline-payment"]').val( $( '#tp_hotel_booking_offline-payment_email_content').val() );
        });
    })
</script>