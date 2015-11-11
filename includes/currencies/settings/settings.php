<?php

// HB_SW_Curreny_Setting instead of HB_Settings
$settings = HB_SW_Curreny_Setting::instance();

// currencies options
$currencies = wp_parse_args(
	$settings->_options,
	array(
		'enable'				=> 1,
		'is_multi_currency'		=> 1
	)
);

?>
<h3><?php _e( 'Currency settings', 'tp-hotel-booking' );?></h3>
<p class="description">
    <?php _e( 'Currency settings extension', 'tp-hotel-booking' );?>
</p>
<table class="form-table">
	<tr>
        <th><?php _e( 'Enable', 'tp-hotel-booking' ); ?></th>
        <td>
        	<select name="<?php echo esc_attr( $settings->get_field_name('enable') ); ?>" tabindex="-1">
                <option value="1" <?php selected( $currencies['enable'] == 1 );?>><?php _e('Yes', 'tp-hotel-booking') ?></option>
                <option value="0" <?php selected( $currencies['enable'] == 0 );?>><?php _e('No', 'tp-hotel-booking') ?></option>
            </select>
        </td>
    </tr>
    <tr>
        <th><?php _e( 'Is multiple allowed', 'tp-hotel-booking' ); ?></th>
        <td>
        	<select name="<?php echo esc_attr( $settings->get_field_name('is_multi_currency') ); ?>" tabindex="-1">
                <option value="1" <?php selected( $currencies['is_multi_currency'] == 1 );?>><?php _e('Yes', 'tp-hotel-booking') ?></option>
                <option value="0" <?php selected( $currencies['is_multi_currency'] == 0 );?>><?php _e('No', 'tp-hotel-booking') ?></option>
            </select>
        </td>
    </tr>
    <tr>
        <th><?php _e( 'Currency aggregator', 'tp-hotel-booking' ); ?></th>
        <?php $aggregators = apply_filters( 'tp_hotel_booking_currency_aggregator', array() ); ?>
        <td>
            <select name="<?php echo esc_attr( $settings->get_field_name('is_multi_currency') ); ?>" tabindex="-1">
                <?php foreach( $aggregators as $k => $agg ): ?>
                    <option value="<?php echo esc_attr( $k ) ?>" <?php selected( $currencies['is_multi_currency'] == $k );?>><?php printf( '%s', $agg ) ?></option>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>
</table>
