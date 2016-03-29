<?php
/**
 * @Author: ducnvtt
 * @Date:   2016-03-29 14:27:43
 * @Last Modified by:   ducnvtt
 * @Last Modified time: 2016-03-29 17:45:04
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class HB_Admin {

	function __construct(){
		add_action( 'init', array( $this, 'includes' ) );
	}

	function includes(){

		TP_Hotel_Booking::instance()->_include( 'includes/admin/class-hb-admin-settings.php' );
		TP_Hotel_Booking::instance()->_include( 'includes/admin/class-hb-admin-menu.php' );
        TP_Hotel_Booking::instance()->_include( 'includes/class-hb-meta-box.php' );
        TP_Hotel_Booking::instance()->_include( 'includes/admin/hb-admin-functions.php' );
        TP_Hotel_Booking::instance()->_include( 'includes/admin/class-hb-customer.php' );
	}

}

new HB_Admin();
