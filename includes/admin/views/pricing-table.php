<?php
$week_names = array(
    __( 'Sun', 'tp-hotel-booking' ),
    __( 'Mon', 'tp-hotel-booking' ),
    __( 'Tue', 'tp-hotel-booking' ),
    __( 'Wed', 'tp-hotel-booking' ),
    __( 'Thu', 'tp-hotel-booking' ),
    __( 'Fri', 'tp-hotel-booking' ),
    __( 'Sat', 'tp-hotel-booking' )
);

$capacities = hb_get_room_capacities(
);

$room_type_id = intval( hb_get_request( 'hb-room-type' ) );
$room_type_select = hb_dropdown_room_types(
    array(
        'selected' => hb_get_request('hb-room-type'),
        'show_option_none' => __( '---Select---', 'tp-hotel-booking' ),
        'option_none_value' => 0,
        'echo' => false
    )
);

$pricing_plans = get_posts(
    array(
        'post_type'         => 'hb_pricing_plan',
        'posts_per_page'    => 9999,
        'meta_query' => array(
            array(
                'key'     => '_hb_pricing_plan_room',
                'value'   => $room_type_id
            )
        )
    )
);
if( $pricing_plans ) {
    $regular_plan = array_pop($pricing_plans);
}else{
    $regular_plan = null;
}
?>

<div class="wrap" id="tp_hotel_booking_pricing">
    <h2><?php _e( 'Pricing Plan', 'tp-hotel-booking' );?></h2>
    <form method="post" name="pricing-table-form">
        <p><strong><?php _e( 'Select type of room', 'tp-hotel-booking' );?></strong>&nbsp;&nbsp;<?php echo $room_type_select;?></p>
        <?php if( $room_type_id ){?>
        <div class="hb-pricing-table regular-price">
            <h3 class="hb-pricing-table-title">
                <span><?php _e( 'Regular price', 'tp-hotel-booking' );?></span>
                <input type="text" class="datepicker" name="date-start[<?php echo $regular_plan ? $regular_plan->ID : '__INDEX__';?>]" size="10" />
                <input type="text" class="datepicker" name="date-end[<?php echo $regular_plan ? $regular_plan->ID : '__INDEX__';?>]" size="10" />
            </h3>
            <div class="hb-pricing-controls">
                <a href="" class="dashicons dashicons-admin-page" data-action="clone" title="<?php _e( 'Clone', 'tp-hotel-booking' );?>"></a>
                <a href="" class="dashicons dashicons-trash" data-action="remove" title="<?php _e( 'Remove', 'tp-hotel-booking' );?>"></a>
            </div>
            <?php
            if( $regular_plan ) {
                $regular_prices = get_post_meta($regular_plan->ID, '_hb_pricing_plan_prices', true);
            }else{
                $regular_prices = array();
            }
            ?>
            <div class="hb-pricing-list">
                <table>
                    <thead>
                        <tr>
                            <th><?php _e( 'Capacity', 'tp-hotel-booking' );?></th>
                            <?php for( $i = 0; $i < 7; $i++ ){?>
                            <th><?php echo $week_names[ $i ];?></th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if( $capacities ):?>
                        <?php foreach( $capacities as $capacity ):?>
                        <tr>
                            <th><?php echo $capacity->name;?></th>
                            <?php for( $i = 0; $i < 7; $i++ ){?>
                                <td>
                                    <?php
                                    $price = ! empty( $regular_prices[ $capacity->term_id ][ $i ] ) ? $regular_prices[ $capacity->term_id ][ $i ] : '';
                                    ?>
                                    <input class="hb-pricing-price" type="text" name="price[<?php echo $regular_plan ? $regular_plan->ID : '__INDEX__';?>][<?php echo $capacity->term_id;?>][<?php echo $i;?>]" value="<?php echo $price;?>" size="10" />
                                </td>
                            <?php }?>
                        </tr>
                        <?php endforeach;?>
                        <?php else:?>
                        <tr><td colspan="7"><?php _e( 'No capacities found', 'tp-hotel-booking' );?></td></tr>
                        <?php endif;?>
                    </tbody>
                </table>
            </div>
        </div>
        <h3><?php _e( 'Other plan', 'tp-hotel-booking' );?></h3>
        <div id="hb-pricing-plan-list">
        <?php if( $pricing_plans ): foreach( $pricing_plans as $plan ){?>
            <?php
            $plan_prices = get_post_meta($plan->ID, '_hb_pricing_plan_prices', true);
            $start_date = get_post_meta($plan->ID, '_hb_pricing_plan_start', true);
            $end_date = get_post_meta($plan->ID, '_hb_pricing_plan_end', true);
            ?>
            <div class="hb-pricing-table">
                <h3 class="hb-pricing-table-title">
                    <span><?php _e( 'Date Range', 'tp-hotel-booking' );?></span>
                    <input type="text" class="datepicker" name="date-start[<?php echo $plan->ID;?>]" size="10" value="<?php echo $start_date;?>" />
                    <input type="text" class="datepicker" name="date-end[<?php echo $plan->ID;?>]" size="10" value="<?php echo $end_date;?>" />
                </h3>
                <div class="hb-pricing-controls">
                    <a href="" class="dashicons dashicons-admin-page" data-action="clone" title="<?php _e( 'Clone', 'tp-hotel-booking' );?>"></a>
                    <a href="" class="dashicons dashicons-trash" data-action="remove" title="<?php _e( 'Remove', 'tp-hotel-booking' );?>"></a>
                </div>

                <div class="hb-pricing-list">
                    <table>
                        <thead>
                        <tr>
                            <th><?php _e( 'Capacity', 'tp-hotel-booking' );?></th>
                            <?php for( $i = 0; $i < 7; $i++ ){?>
                                <th><?php echo $week_names[ $i ];?></th>
                            <?php } ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if( $capacities ):?>
                            <?php foreach( $capacities as $capacity ):?>
                                <tr>
                                    <th><?php echo $capacity->name;?></th>
                                    <?php for( $i = 0; $i < 7; $i++ ){?>
                                        <td>
                                            <?php $price = ! empty( $plan_prices[ $capacity->term_id ] ) ? ( array_key_exists( $i, $plan_prices[ $capacity->term_id ] ) ? $plan_prices[ $capacity->term_id ][ $i ] : '' ) : '';?>
                                            <input class="hb-pricing-price" type="text" name="price[<?php echo $plan->ID;?>][<?php echo $capacity->term_id;?>][<?php echo $i;?>]" value="<?php echo $price;?>" size="10" />
                                        </td>
                                    <?php }?>
                                </tr>
                            <?php endforeach;?>
                        <?php else:?>
                            <tr><td colspan="7"><?php _e( 'No capacities found', 'tp-hotel-booking' );?></td></tr>
                        <?php endif;?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php } endif;?>
        </div>
        <?php wp_nonce_field( 'hb-update-pricing-plan', 'hb-update-pricing-plan-field' );?>
        <p>
            <button class="button"><?php _e( 'Add new', 'tp-hotel-booking' );?></button>
            <button class="button button-primary"><?php _e( 'Update', 'tp-hotel-booking' );?></button>
        </p>
        <?php }?>
    </form>
</div>
<script type="text/html" id="tmpl-hb-pricing-table">
    <div class="hb-pricing-table regular-price">
        <h3 class="hb-pricing-table-title">
            <span><?php _e( 'Regular price', 'tp-hotel-booking' );?></span>
            <input type="text" class="datepicker" name="date-start[__INDEX__]" size="10" />
            <input type="text" class="datepicker" name="date-end[__INDEX__]" size="10" />
        </h3>
        <div class="hb-pricing-controls">
            <a href="" class="dashicons dashicons-admin-page" data-action="clone" title="<?php _e( 'Clone', 'tp-hotel-booking' );?>"></a>
            <a href="" class="dashicons dashicons-trash" data-action="remove" title="<?php _e( 'Remove', 'tp-hotel-booking' );?>"></a>
        </div>

        <div class="hb-pricing-list">
            <table>
                <thead>
                <tr>
                    <th><?php _e( 'Capacity', 'tp-hotel-booking' );?></th>
                    <?php for( $i = 0; $i < 7; $i++ ){?>
                        <th><?php echo $week_names[ $i ];?></th>
                    <?php } ?>
                </tr>
                </thead>
                <tbody>
                <?php if( $capacities ):?>
                    <?php foreach( $capacities as $capacity ):?>
                        <tr>
                            <th><?php echo $capacity->name;?></th>
                            <?php for( $i = 0; $i < 7; $i++ ){?>
                                <td>
                                    <input class="hb-pricing-price" type="text" name="price[__INDEX__][<?php echo $capacity->term_id;?>][<?php echo $i;?>]" value="" size="10" />
                                </td>
                            <?php }?>
                        </tr>
                    <?php endforeach;?>
                <?php else:?>
                    <tr><td colspan="7"><?php _e( 'No capacities found', 'tp-hotel-booking' );?></td></tr>
                <?php endif;?>
                </tbody>
            </table>
        </div>
    </div>
</script>