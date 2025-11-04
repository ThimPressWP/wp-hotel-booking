<?php
if ( ! isset( $atts ) ) {
	return;
}
defined( 'ABSPATH' ) || exit();

global $hb_settings;
global $post;

$fields = apply_filters(
	'hotel_booking/shortcode/search-filter-v2/field/fields',
	array(
		'price'  => array(
			'min_price'  => $atts['min_price'] ?? $hb_settings->get( 'filter_price_min', 0 ),
			'max_price'  => $atts['max_price'] ?? $hb_settings->get( 'filter_price_max', 100 ),
			'step_price' => $atts['step_price'] ?? $hb_settings->get( 'filter_price_step', 1 ),
			'min_value'  => hb_get_request( 'min_price' ),
			'max_value'  => hb_get_request( 'max_price' ),
		),
		'rating' => array(),
		'types'  => array(),
	)
);
?>

<div id="hotel-booking-search-filter" class="hotel-booking-search-filter">
	<div class="hotel-booking-search-filter-inner">
	    <h3>
			<?php esc_html_e( 'Filter By', 'wp-hotel-booking' ); ?>
		</h3>

	    <form class="search-filter-form" action="">
	        <div class="hb-form-table">
				<?php
					foreach ( $fields as $key => $data ) {
						hb_get_template( 'search/v2/search-filter/' . $key . '.php', compact( 'data' ) );
					}
				?>

				<div class="clear-reset-filter">
					<?php if ( ! $post || $post->ID != hb_settings()->get( 'search_page_id' ) ) { ?>
						<button type="button" class="hb-room-filter-btn"><?php esc_html_e( 'Filter', 'wp-hotel-booking' ); ?></button>
					<?php } ?>

					<div class="clear-filter">
						<button type="button">
							<?php esc_html_e( 'Reset', 'wp-hotel-booking' ); ?>
						</button>
	            </div>
				</div>
	        </div>
	    </form>
    </div>
</div>
