<?php
/**
 * Admin View: Pricing talbe view.
 *
 * @version     1.9.7
 * @package     WP_Hotel_Booking/Views
 * @category    Views
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

$room_id = $post->ID;
if ( empty( $room_id ) ) {
	return;
}
$week_names   = hb_date_names();
$plans        = hb_room_get_pricing_plans( $room_id );
$regular_plan = null;

foreach ( $plans as $k => $plan ) {
	if ( ! $plan->start && ! $plan->end ) {
		$regular_plan = $plan;
		unset( $plans[ $k ] );
	}
}

$count_plants = count( $plans );
$date_order   = hb_start_of_week_order();
?>

<div class="wrap" id="tp_hotel_booking_pricing">
	<form method="post" name="pricing-table-form">
		<div class="hb-pricing-table regular-price clearfix">
			<h3 class="hb-pricing-table-title">
				<span><?php _e( 'Regular price', 'wp-hotel-booking' ); ?></span>
				<input type="text" class="datepicker"
						name="_hbpricing[date-start][<?php echo esc_attr( sprintf( '%s', $regular_plan ? $regular_plan->ID : 0 ) ); ?>]"
						size="10" readonly="readonly"/>
				<input type="hidden"
						name="_hbpricing[date-start-timestamp][<?php echo esc_attr( sprintf( '%s', $regular_plan ? $regular_plan->ID : 0 ) ); ?>]"/>
				<input type="text" class="datepicker"
						name="_hbpricing[date-end][<?php echo esc_attr( sprintf( '%s', $regular_plan ? $regular_plan->ID : 0 ) ); ?>]"
						size="10" readonly="readonly"/>
				<input type="hidden"
						name="_hbpricing[date-end-timestamp][<?php echo esc_attr( sprintf( '%s', $regular_plan ? $regular_plan->ID : 0 ) ); ?>]"/>
				<input type="hidden" name="_hbpricing[plan_id][]" value="<?php echo esc_attr( sprintf( '%s', $regular_plan ? $regular_plan->ID : 0 ) ); ?>"/>
			</h3>
			<div class="hb-pricing-controls">
				<!-- <a href="" class="dashicons dashicons-edit" data-action="edit"
					title="<?php // _e( 'Edit', 'wp-hotel-booking' ); ?>"></a> -->
				<a href="" class="dashicons dashicons-admin-page" data-action="clone"
					title="<?php esc_attr_e( 'Clone', 'wp-hotel-booking' ); ?>"></a>
				<a href="" class="dashicons dashicons-trash" data-action="remove"
					title="<?php esc_attr_e( 'Remove', 'wp-hotel-booking' ); ?>"></a>
			</div>
			<div class="hb-pricing-list">
				<table>
					<thead>
					<tr>
						<?php foreach ( $date_order as $i ) { ?>
							<th><?php echo esc_html( $week_names[ $i ] ); ?></th>
						<?php } ?>
					</tr>
					</thead>
					<tbody>
					<tr>
						<?php
						$prices  = isset( $regular_plan->prices ) ? $regular_plan->prices : array();
						$plan_id = isset( $regular_plan->ID ) ? $regular_plan->ID : 0;
						?>
						<?php foreach ( $date_order as $i ) { ?>
							<td>
								<?php $price = ! empty( $prices[ $i ] ) ? $prices[ $i ] : ''; ?>
								<input class="hb-pricing-price" type="number" min="0" step="any"
										name="_hbpricing[prices][<?php echo esc_attr( sprintf( '%s', $plan_id ? $plan_id : 0 ) ); ?>][<?php echo esc_attr( $i ); ?>]"
										value="<?php echo esc_attr( $price ); ?>" size="10"/>
							</td>
						<?php } ?>
					</tr>
					</tbody>
				</table>
			</div>
		</div>
		<h3 class="clearfix">
			<?php _e( 'Other plan', 'wp-hotel-booking' ); ?>
			<span class="count"><?php printf( _n( '(%d plan)', '(%d plans)', $count_plants, 'wp-hotel-booking' ), $count_plants ); ?></span>
		</h3>

		<div id="hb-pricing-plan-list">
			<?php if ( $plans ) : ?>
				<?php foreach ( $plans as $plan ) : ?>
					<?php
					$start = strtotime( $plan->start );
					$end   = strtotime( $plan->end );
					?>
					<div class="hb-pricing-table">
						<h3 class="hb-pricing-table-title">
							<span><?php _e( 'Date Range', 'wp-hotel-booking' ); ?></span>
							<input type="text" class="datepicker start_date"
									name="_hbpricing[date-start][<?php echo esc_attr( $plan->ID ); ?>]" size="10"
									value="<?php printf( '%s', date_i18n( hb_get_date_format(), $start ) ); ?>"
									readonly="readonly"/>
							<input type="hidden" name="_hbpricing[date-start-timestamp][<?php echo esc_attr( $plan->ID ); ?>]"
									value="<?php echo esc_attr( $start ); ?>"/>
							<input type="text" class="datepicker end_date"
									name="_hbpricing[date-end][<?php echo esc_attr( $plan->ID ); ?>]" size="10"
									value="<?php printf( '%s', date_i18n( hb_get_date_format(), $end ) ); ?>"
									readonly="readonly"/>
							<input type="hidden" name="_hbpricing[date-end-timestamp][<?php echo esc_attr( $plan->ID ); ?>]"
									value="<?php echo esc_attr( $end ); ?>"/>
							<input type="hidden" name="_hbpricing[plan_id][]" value="<?php echo esc_attr( $plan->ID ); ?>"/>
						</h3>
						<div class="hb-pricing-controls">
							<!-- <a href="" class="dashicons dashicons-edit" data-action="edit"
								title="<?php // _e( 'Edit', 'wp-hotel-booking' ); ?>"></a> -->
							<!-- <a href="" class="dashicons dashicons-admin-page" data-action="clone" title="<?php // _e( 'Clone', 'wp-hotel-booking' ); ?>"></a> -->
							<a href="" class="dashicons dashicons-trash" data-action="remove"
								title="<?php _e( 'Remove', 'wp-hotel-booking' ); ?>"></a>
						</div>

						<div class="hb-pricing-list">
							<table>
								<thead>
								<tr>
									<?php foreach ( $date_order as $i ) { ?>
										<th><?php echo esc_html( $week_names[ $i ] ); ?></th>
									<?php } ?>
								</tr>
								</thead>
								<tbody>
								<tr>
									<?php $prices = $plan->prices; ?>
									<?php foreach ( $date_order as $i ) { ?>
										<td>
											<?php $price = ! empty( $prices[ $i ] ) ? $prices[ $i ] : ''; ?>
											<input class="hb-pricing-price" type="number" min="0" step="any"
													name="_hbpricing[prices][<?php echo esc_attr( $plan->ID ); ?>][<?php echo esc_attr( $i ); ?>]"
													value="<?php echo esc_attr( $price ); ?>" size="10"/>
										</td>
									<?php } ?>
								</tr>
								</tbody>
							</table>
						</div>
					</div>
				<?php endforeach; ?>

			<?php else : ?>
				<p id="hb-no-plan-message"> <?php _e( 'No addition plans', 'wp-hotel-booking' ); ?></p>
			<?php endif; ?>

		</div>
		<p>	
			<div id='calendar_room_pricing'></div>
			<input type="hidden" name="room_id" value="<?php echo esc_attr( $room_id ); ?>"/>
			<button class="button button-primary show-all-plan"><?php _e( 'View All', 'wp-hotel-booking' ); ?></button>
		</p>
		<input type="text" id="all-plan-datepicker" style="display:none">
		<?php wp_nonce_field( 'hb-update-pricing-plan', 'hb-update-pricing-plan-field' ); ?>
	</form>
</div>

<script type="text/html" id="tmpl-hb-pricing-table">
	<div class="hb-pricing-table">
		<h3 class="hb-pricing-table-title">
			<span><?php _e( 'Date Range', 'wp-hotel-booking' ); ?></span>
			<input type="text" class="datepicker" name="_hbpricing[date-start][__INDEX__]" size="10" readonly="readonly"/>
			<input type="hidden" name="_hbpricing[date-start-timestamp][__INDEX__]"/>
			<input type="text" class="datepicker" name="_hbpricing[date-end][__INDEX__]" size="10" readonly="readonly"/>
			<input type="hidden" name="_hbpricing[date-end-timestamp][__INDEX__]"/>
			<input type="hidden" name="_hbpricing[plan_id][]" value="<?php echo '__INDEX__'; ?>" />
		</h3>
		<div class="hb-pricing-controls">
			<!-- <a href="" class="dashicons dashicons-edit" data-action="edit"
			   title="<?php _e( 'Clone', 'wp-hotel-booking' ); ?>"></a> -->

			<# if( typeof data.clone !== 'undefined' && data.clone === true ) { #>
			<a href="" class="dashicons dashicons-admin-page" data-action="clone"
			   title="<?php _e( 'Clone', 'wp-hotel-booking' ); ?>"></a>
			<# } #>

			<a href="" class="dashicons dashicons-trash" data-action="remove"
			   title="<?php _e( 'Remove', 'wp-hotel-booking' ); ?>"></a>
		</div>

		<div class="hb-pricing-list">
			<table>
				<thead>
				<tr>
					<?php foreach ( $date_order as $i ) { ?>
						<th><?php echo esc_html( $week_names[ $i ] ); ?></th>
					<?php } ?>
				</tr>
				</thead>
				<tbody>
				<tr>
					<?php foreach ( $date_order as $i ) { ?>
						<td>
							<input class="hb-pricing-price" type="number" min="0" step="any"
								   name="_hbpricing[prices][__INDEX__][<?php echo esc_attr( $i ); ?>]" value="" size="10"/>
						</td>
					<?php } ?>
				</tr>
				</tbody>
			</table>
		</div>
	</div>
</script>
