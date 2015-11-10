<?php
$settings = hb_settings();
?>
<h3><?php _e( 'Currency settings', 'tp-hotel-booking' );?></h3>
<p class="description">
    <?php _e( 'Currency settings extension', 'tp-hotel-booking' );?>
</p>
<table class="form-table">
    <tr>
        <th><?php _e( 'Is multiple allowed', 'tp-hotel-booking' ); ?></th>
        <td>
        	<select name="<?php echo $settings->get_field_name('is_multi_currency'); ?>" tabindex="-1">
                <option value="1" <?php selected( $settings->get('is_multi_currency') == 1 );?>><?php _e('Yes', 'tp-hotel-booking') ?></option>
                <option value="0" <?php selected( $settings->get('is_multi_currency') == 0 );?>><?php _e('No', 'tp-hotel-booking') ?></option>
            </select>
        </td>
    </tr>
</table>
