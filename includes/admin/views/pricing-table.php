<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

$week_names = hb_date_names();

$room_id = intval( hb_get_request( 'hb-room' ) );

$plans = hb_room_get_pricing_plans( $room_id );

$regular_plan = null;

foreach ( $plans as $k => $plan ) {
    if ( ! $plan->start && ! $plan->end ) {
        $regular_plan = $plan;
        unset( $plans[$k] );
    }
}

$count_plants = count( $plans );

?>

<div class="wrap"  id="tp_hotel_booking_pricing">
    <h2><?php _e( 'Pricing Plans', 'tp-hotel-booking' ); ?></h2>
    <form method="post" name="pricing-table-form">
        <p>
            <strong><?php _e( 'Select name of room', 'tp-hotel-booking' ); ?></strong>
            &nbsp;&nbsp;<?php echo hb_dropdown_rooms( array( 'selected' => $room_id ) ); ?>
        </p>
        <?php if( $room_id ) : ?>
            <div class="hb-pricing-table regular-price clearfix">
                <h3 class="hb-pricing-table-title">
                    <span><?php _e( 'Regular price', 'tp-hotel-booking' ); ?></span>
                    <input type="text" class="datepicker" name="date-start[<?php echo sprintf( '%s', $regular_plan ? $regular_plan->ID : '__INDEX__' ); ?>]" size="10" readonly="readonly" />
                    <input type="hidden" name="date-start-timestamp[<?php echo sprintf( '%s', $regular_plan ? $regular_plan->ID : '__INDEX__' ); ?>]"/>

                    <input type="text" class="datepicker" name="date-end[<?php echo sprintf( '%s', $regular_plan ? $regular_plan->ID : '__INDEX__' ); ?>]" size="10" readonly="readonly" />
                    <input type="hidden" name="date-end-timestamp[<?php echo sprintf( '%s', $regular_plan ? $regular_plan->ID : '__INDEX__' ); ?>]"/>
                </h3>
                <div class="hb-pricing-controls">
                    <a href="" class="dashicons dashicons-edit" data-action="edit" title="<?php _e( 'Edit', 'tp-hotel-booking' ); ?>"></a>
                    <a href="" class="dashicons dashicons-admin-page" data-action="clone" title="<?php _e( 'Clone', 'tp-hotel-booking' ); ?>"></a>
                    <a href="" class="dashicons dashicons-trash" data-action="remove" title="<?php _e( 'Remove', 'tp-hotel-booking' ); ?>"></a>
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
                                <?php $prices = $regular_plan->prices; ?>
                                <?php for( $i = 0; $i < 7; $i++ ){ ?>
                                    <td>
                                        <?php $price = ! empty( $prices[ $i ] ) ? $prices[ $i ] : ''; ?>
                                        <input class="hb-pricing-price" type="number" min="0" step="any" name="price[<?php echo sprintf( '%s', $regular_plan ? $regular_plan->ID : '__INDEX__' ); ?>][<?php echo esc_attr( $i ); ?>]" value="<?php echo esc_attr( $price ); ?>" size="10" readonly="readonly" />
                                    </td>
                                <?php } ?>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="clearfix"></div>
            <h3 class="clearfix">
                <?php _e( 'Other plan', 'tp-hotel-booking' ); ?>
                <span class="count"><?php printf( _n( '(%d plan)', '(%d plans)', $count_plants, 'tp-hotel-booking' ), $count_plants ); ?></span>
            </h3>

            <div id="hb-pricing-plan-list">
                <?php if( $plans ): ?>
                    <?php foreach( $plans as $plan ) : ?>
                            <?php
                                $start = strtotime( $plan->start );
                                $end = strtotime( $plan->end );
                            ?>
                            <div class="hb-pricing-table">
                                <h3 class="hb-pricing-table-title">
                                    <span><?php _e( 'Date Range', 'tp-hotel-booking' ); ?></span>
                                    <input type="text" class="datepicker" name="date-start[<?php echo esc_attr( $plan->ID ); ?>]" size="10" value="<?php printf( '%s', date_i18n( hb_get_date_format(), $start ) ); ?>" readonly="readonly" />
                                    <input type="hidden" name="date-start-timestamp[<?php echo esc_attr( $plan->ID ); ?>]" value="<?php echo esc_attr( $start ); ?>" />

                                    <input type="text" class="datepicker" name="date-end[<?php echo esc_attr( $plan->ID ); ?>]" size="10" value="<?php printf( '%s', date_i18n( hb_get_date_format(), $end ) ); ?>" readonly="readonly" />
                                    <input type="hidden" name="date-end-timestamp[<?php echo esc_attr( $plan->ID ); ?>]" value="<?php echo esc_attr( $end ); ?>" />
                                </h3>
                                <div class="hb-pricing-controls">
                                    <a href="" class="dashicons dashicons-edit" data-action="edit" title="<?php _e( 'Edit', 'tp-hotel-booking' ); ?>"></a>
                                    <!-- <a href="" class="dashicons dashicons-admin-page" data-action="clone" title="<?php //_e( 'Clone', 'tp-hotel-booking' ); ?>"></a> -->
                                    <a href="" class="dashicons dashicons-trash" data-action="remove" title="<?php _e( 'Remove', 'tp-hotel-booking' ); ?>"></a>
                                </div>

                                <div class="hb-pricing-list">
                                    <table>
                                        <thead>
                                        <tr>
                                            <?php for( $i = 0; $i < 7; $i++ ){?>
                                                <th><?php echo esc_html( $week_names[ $i ] ); ?></th>
                                            <?php } ?>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <?php $prices = $plan->prices; ?>
                                                <?php for( $i = 0; $i < 7; $i++ ){?>
                                                    <td>
                                                        <?php $price = ! empty( $prices[ $i ] ) ? $prices[ $i ] : ''; ?>
                                                        <input class="hb-pricing-price" type="number" min="0" step="any" name="price[<?php echo esc_attr( $plan->ID ); ?>][<?php echo esc_attr( $i ); ?>]" value="<?php echo esc_attr( $price ); ?>" size="10" readonly="readonly" />
                                                    </td>
                                                <?php } ?>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endforeach; ?>

                <?php else: ?>
                    <p id="hb-no-plan-message"> <?php _e( 'No addition plans', 'tp-hotel-booking' ); ?></p>
                <?php endif; ?>

            </div>
            <p>
                <input type="hidden" name="room_id" value="<?php echo esc_attr( $room_id ) ?>" />
                <button class="button hb-add-new-plan" type="button"><?php _e( 'Add Plan', 'tp-hotel-booking' ); ?></button>
                <button class="button button-primary"><?php _e( 'Update', 'tp-hotel-booking'); ?></button>
            </p>
            <?php wp_nonce_field( 'hb-update-pricing-plan', 'hb-update-pricing-plan-field' ); ?>
            <!-- <p>
                <button type="button" class="button hb-add-new-plan"><?php //_e( 'Add Plan', 'tp-hotel-booking' ); ?></button>
                <button class="button button-primary"><?php //_e( 'Update', 'tp-hotel-booking' ); ?></button>
            </p> -->
        <?php endif; ?>
    </form>
</div>

<script type="text/html" id="tmpl-hb-pricing-table">
    <div class="hb-pricing-table">
        <h3 class="hb-pricing-table-title">
            <span><?php _e( 'Date Range', 'tp-hotel-booking' ); ?></span>
            <input type="text" class="datepicker" name="date-start[__INDEX__]" size="10" readonly="readonly" />
            <input type="hidden" name="date-start-timestamp[__INDEX__]" />
            <input type="text" class="datepicker" name="date-end[__INDEX__]" size="10" readonly="readonly" />
            <input type="hidden" name="date-end-timestamp[__INDEX__]" />
        </h3>
        <div class="hb-pricing-controls">
            <a href="" class="dashicons dashicons-edit" data-action="edit" title="<?php _e( 'Clone', 'tp-hotel-booking' ); ?>"></a>

            <# if( typeof data.clone !== 'undefined' && data.clone === true ) { #>
                <a href="" class="dashicons dashicons-admin-page" data-action="clone" title="<?php _e( 'Clone', 'tp-hotel-booking' ); ?>"></a>
            <# } #>

            <a href="" class="dashicons dashicons-trash" data-action="remove" title="<?php _e( 'Remove', 'tp-hotel-booking' ); ?>"></a>
        </div>

        <div class="hb-pricing-list">
            <table>
                <thead>
                <tr>
                    <?php for( $i = 0; $i < 7; $i++ ){?>
                        <th><?php echo esc_html( $week_names[ $i ] ); ?></th>
                    <?php } ?>
                </tr>
                </thead>
                <tbody>
                    <tr>
                        <?php for( $i = 0; $i < 7; $i++ ){?>
                            <td>
                                <input class="hb-pricing-price" type="number" min="0" step="any" name="price[__INDEX__][<?php echo esc_attr( $i ); ?>]" value="" size="10" readonly="readonly" />
                            </td>
                        <?php } ?>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</script>
