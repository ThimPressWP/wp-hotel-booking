<?php
/**
 * Common functions is used both in admin and frontend
 */

/**
 * A short way to include a view file
 *
 * @param      $name
 * @param null $args
 */
function hb_wc_admin_view( $name, $args = null ) {
	require hb_wc_get_admin_view( $name, $args );
}

/**
 * @param      $name
 * @param null $args
 *
 * @return string
 */
function hb_wc_get_admin_view( $name, $args = null ) {
	if ( is_array( $args ) ) {
		extract( $args );
	}
	if ( !preg_match( '!\.php$!', $name ) ) {
		$ext = '.php';
	} else {
		$ext = '';
	}
	return HB_WC_PLUGIN_PATH . "includes/admin/views/{$name}{$ext}";
}

function hb_wc_payment_gateway_enable( $enable, $gateway ){
	if( $gateway instanceof WC_Payment_Gateway ){
		$enable = true;
	}
	return $enable;
}

function hb_wc_payment_gateway_form(){
	$parts = explode( '_wc_', current_filter() );
	if( !empty( $parts[1] ) ){
		if( $wc_payment_gateways = WC()->payment_gateways()->get_available_payment_gateways() ){
			foreach( $wc_payment_gateways as $gateway ){
				if( $gateway->id == $parts[1] ){
					echo $gateway->description;
					return;
				}
			}
		}
	}
}

function hb_wc_payment_gateways( $gateways ){
	$wc_payment_gateways = WC()->payment_gateways()->get_available_payment_gateways();
	if( $wc_payment_gateways ) foreach( $wc_payment_gateways as $payment_gateway ){
		$slug = "woo";
		//$checked = checked( WC()->session->get( 'chosen_payment_method') == $payment_gateway->id ? true : false, true, false );
		$gateways[ 'wc_' . $payment_gateway->id ] = $payment_gateway;

		$payment_gateway->slug = 'wc_' . $payment_gateway->id;
		add_filter( 'hb_payment_gateway_enable', 'hb_wc_payment_gateway_enable', 10, 2 );
		add_action( 'hb_payment_gateway_form_wc_' . $payment_gateway->id, 'hb_wc_payment_gateway_form' );
	}
	return $gateways;
}
// add_filter( 'hb_payment_gateways', 'hb_wc_payment_gateways' );