<?php
if ( ! isset( $data ) ) {
	return;
}

if ( ! is_numeric( $data['min_price'] ) || ! is_numeric( $data['max_price'] ) ) {
	return;
}
$min_formatted_price = number_format( $data['min_price'], 2, '.', ',' );
$max_formatted_price = number_format( $data['max_price'], 2, '.', ',' );
?>
    <div class="hb-price-field" data-min="<?php echo esc_attr( $data['min_price'] ); ?>"
         data-max="<?php echo esc_attr( $data['max_price'] ); ?>"
         data-step="<?php echo esc_attr( $data['step_price'] ); ?>">
        <div class=" title"><?php esc_html_e( 'Price', 'wp-hotel-booking' ); ?></div>
        <input type="hidden" class="hb-min-price" name="min-price"
               value="<?php echo esc_attr( $data['min_value'] ); ?>">
        <input type="hidden" class="hb-max-price" name="max-price"
               value="<?php echo esc_attr( $data['max_value'] ); ?>">
        <div class="hb-search-price">
            <div class="hb-price-range"></div>
            <div>
                <span class="min"><?php echo esc_html( $min_formatted_price ); ?></span>
                -
                <span class="max"><?php echo esc_html( $max_formatted_price ); ?></span>
            </div>
        </div>

        <button class="apply "><?php esc_html_e( 'Apply', 'wp-hotel-booking' ); ?></button>
    </div>
<?php
