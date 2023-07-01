<?php
if ( ! isset( $atts ) ) {
	return;
}
$fields = apply_filters( 'hotel_booking/shortcode/search-filter-v2/field/fields',
	array(
		'price'  => array(
			'min_price' => $atts['min_price'] ?? 0,
			'max_price' => $atts['max_price'] ?? 1500,
			'min_value' => hb_get_request( 'min_price' ),
			'max_value' => hb_get_request( 'max_price' )
		),
		'rating' => array(),
		'types'  => array()
	)
);
?>
<div id="hotel-booking-search-filter" class="hotel-booking-search-filter">
    <h3><?php esc_html_e( 'Filter By', 'wp-hotel-booking' ); ?></h3>
    <form class="search-filter-form" action="">
        <div class="hb-form-table">
			<?php
			foreach ( $fields as $key => $data ) {
				hb_get_template( 'search/v2/search-filter/' . $key . '.php', compact( 'data' ) );
			}
			?>
        </div>
    </form>
</div>
