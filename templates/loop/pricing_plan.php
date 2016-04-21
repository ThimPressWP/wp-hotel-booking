<?php
/**
 * Pricing Plan
 *
 * @author 		ThimPress
 * @package 	Tp-hotel-booking/Templates
 * @version     1.1.4
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

$week_names = hb_date_names();
$plans = hb_room_get_pricing_plans( get_the_ID() );
?>

<?php foreach ( $plans as $plan ) : ?>

	<h4 class="hb_room_pricing_plan_data">
		<?php if ( $plan->start && $plan->end ): ?>
				<?php printf( '%1$s', date_i18n( hb_get_date_format(), strtotime( $plan->start ) ) ) ?>
				<span><?php _e( 'to', 'tp-hotel-booking' ) ?></span>
				<?php printf( '%1$s', date_i18n( hb_get_date_format(), strtotime( $plan->end ) ) ) ?>
		<?php else: ?>
			<?php _e( 'Regular plan', 'tp-hotel-booking' ) ?>
		<?php endif; ?>
	</h4>

	<table class="hb_room_pricing_plans">
		<thead>
			<tr>
	            <?php for( $i = 0; $i < 7; $i++ ){?>
	                <th><?php echo esc_html( $week_names[ $i ] ); ?></th>
	            <?php } ?>
	        </tr>
		</thead>
		<tbody>
			<tr>
				<?php $prices = $plan->prices ?>
				<?php for( $i = 0; $i < 7; $i++ ){?>
                    <td>
                        <?php $price = isset( $prices[ $i ] ) ? $prices[ $i ] : ''; ?>
                        <?php printf( '%s', hb_format_price( $price ) ) ?>
                    </td>
                <?php } ?>
			</tr>
		</tbody>
	</table>

<?php endforeach; ?>