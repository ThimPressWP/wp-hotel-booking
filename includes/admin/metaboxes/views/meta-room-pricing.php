<?php
/**
 * @Author: ducnvtt
 * @Date:   2016-04-13 13:30:10
 * @Last Modified by:   ducnvtt
 * @Last Modified time: 2016-04-19 09:08:55
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

$week_names = hb_date_names();
$regular_plan = hb_room_get_regular_plan( $post->ID );
$plan_id = isset( $regular_plan->ID ) ? $regular_plan->ID : 0;
?>

<div class="hb-pricing-table regular-price clearfix">
    <h3 class="hb-pricing-table-title">
        <input type="hidden" name="_hbpricing[plan_id][]" value="<?php echo esc_attr( $plan_id ) ?>" />
    </h3>
    <div class="hb-pricing-controls">
        <a href="" class="dashicons dashicons-edit" data-action="edit" title="<?php _e( 'Edit', 'tp-hotel-booking' ); ?>"></a>
    </div>
    <div class="hb-pricing-list">
        <table>
            <thead>
                <tr>
                    <?php for ( $i = 0; $i < 7; $i++ ) { ?>
                    	<th><?php echo esc_html( $week_names[ $i ] ); ?></th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <?php $prices = isset( $regular_plan->prices ) ? $regular_plan->prices : array(); ?>
                    <?php for( $i = 0; $i < 7; $i++ ){ ?>
                        <td>
                            <?php $price = ! empty( $prices[ $i ] ) ? $prices[ $i ] : ''; ?>
                            <input class="hb-pricing-price" type="number" min="0" step="any" name="_hbpricing[prices][<?php echo sprintf( '%s', $plan_id ); ?>][<?php echo esc_attr( $i ); ?>]" value="<?php echo esc_attr( $price ); ?>" size="10" readonly="readonly" />
                        </td>
                    <?php } ?>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php wp_nonce_field( 'hotel_booking_room_pricing_nocne', 'hotel-booking-room-pricing-nonce' ); ?>
