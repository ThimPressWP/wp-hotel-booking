<?php

if( ! class_exists( 'HB_Settings' ) )
	return;

class HB_WC_Settings extends HB_Settings{
	function __construct(){
		add_filter( 'hb_admin_settings_tabs', array( $this, 'register_settings' ), 101 );
		add_action( 'hb_admin_settings_tab_woocommerce', array( $this, 'admin_settings' ) );
	}
	/**
	 * Register new settings tab with TP Hotel Booking
	 */
	public function register_settings( $tabs ){
		$tabs[ 'woocommerce' ] = __( 'WooCommerce', 'hb-woocommerce' );
		return $tabs;
	}

	public function admin_settings(){
		include hb_wc_get_admin_view( 'wc-settings' );
	}
}
return new HB_WC_Settings();