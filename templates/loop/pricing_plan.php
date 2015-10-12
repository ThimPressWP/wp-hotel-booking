<?php
/**
 * Pricing Plan
 *
 * @author 		ThimPress
 * @package 	Tp-hotel-booking/Templates
 * @version     0.9
 */

global $hb_room;
$pricings = $hb_room->pricing_plans_data();
$week_names = $pricings['week'];
$capacitiyID = $pricings['capacity'];
unset($pricings['week']);
unset($pricings['capacity']);
?>

<?php foreach ($pricings['data'] as $key => $prices): ?>
	<h4 class="hb_room_pricing_plan_data">
		<?php if ( isset($prices['plans']) && count($prices['plans']) > 1 ): ?>
				<?php printf( '%1$s', $prices['plans']['start'] ) ?>
				<span><?php _e( 'to', 'Tp-hotel-booking' ) ?></span>
				<?php printf( '%1$s', $prices['plans']['end'] ) ?>
		<?php else: ?>
			<?php _e( 'Regular plan', 'Tp-hotel-booking' ) ?>
		<?php endif; ?>
	</h4>

	<table class="hb_room_pricing_plans">
		<thead>
			<tr>
	            <?php for( $i = 0; $i < 7; $i++ ){?>
	                <th><?php echo $week_names[ $i ];?></th>
	            <?php } ?>
	        </tr>
		</thead>
		<tbody>
			<tr>
				<?php $prices = $prices['price'] ?>
				<?php for( $i = 0; $i < 7; $i++ ){?>
                    <td>
                        <?php $price = ! empty( $prices[ $capacitiyID ] ) ? ( array_key_exists( $i, $prices[ $capacitiyID ] ) ? $prices[ $capacitiyID ][ $i ] : '' ) : '';?>
                        <?php printf( '%1$s%2$s', hb_get_currency_symbol(), $price ) ?>
                    </td>
                <?php }?>
			</tr>
		</tbody>
	</table>

<?php endforeach; ?>