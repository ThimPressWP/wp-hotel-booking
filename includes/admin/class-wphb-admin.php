<?php
/**
 * @Author: ducnvtt
 * @Date:   2016-03-29 14:27:43
 * @Last Modified by:   ducnvtt
 * @Last Modified time: 2016-04-13 09:40:09
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class WPHB_Admin {

	function __construct(){
		add_action( 'init', array( $this, 'includes' ) );

		// update pricing
		add_action( 'admin_init', array( $this, 'update_pricing' ) );
	}

	function includes(){

		WP_Hotel_Booking::instance()->_include( 'includes/admin/class-wphb-admin-settings.php' );
		WP_Hotel_Booking::instance()->_include( 'includes/admin/class-wphb-admin-menu.php' );
        WP_Hotel_Booking::instance()->_include( 'includes/class-wphb-meta-box.php' );
        WP_Hotel_Booking::instance()->_include( 'includes/admin/wphb-admin-functions.php' );
        WP_Hotel_Booking::instance()->_include( 'includes/admin/class-wphb-customer.php' );
	}

	function update_pricing() {
		if ( ! isset( $_POST['hb-update-pricing-plan-field'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['hb-update-pricing-plan-field'] ), 'hb-update-pricing-plan' ) ){
	        return;
	    }

	    if( empty( $_POST['price'] ) ) {
	    	return;
	    }

	    if ( ! isset( $_POST['room_id'] ) ) {
	    	return;
	    }

	    $room_id = absint( $_POST['room_id'] );
	    $plans = hb_room_get_pricing_plans( $room_id );

	    $ignore = array();
        foreach ( (array)$_POST['price'] as $t => $v ) {
            $start  = isset( $_POST['date-start-timestamp'][ $t ] ) ? sanitize_text_field( $_POST['date-start-timestamp'][ $t ] ) : '';
            $end    = isset( $_POST['date-end-timestamp'][ $t ] ) ? sanitize_text_field( $_POST['date-end-timestamp'][ $t ] ) : '';
            $prices = (array)$_POST['price'][ $t ];

            $plan_id = hb_room_set_pricing_plan( array(
					'start_time'		=> $start,
					'end_time'			=> $end,
					'pricing'			=> $prices,
					'room_id'			=> $room_id,
					'plan_id'			=> $t
				) );
            $ignore[] = $plan_id;
        }

        foreach ( $plans as $id => $plan ) {
        	if ( ! in_array( $id, $ignore ) ) {
        		hb_room_remove_pricing( $id );
        	}
        }
	}

}

new WPHB_Admin();
