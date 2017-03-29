<?php

if ( !defined( 'ABSPATH' ) )
    exit;

if ( !defined( 'WPHB_PLUGIN_PATH' ) )
    return;

if ( !defined( 'TP_HB_CURRENCY' ) )
    define( 'TP_HB_CURRENCY', dirname( __FILE__ ) );

if ( !defined( 'TP_HB_CURRENCY_URI' ) )
    define( 'TP_HB_CURRENCY_URI', WPHB_PLUGIN_URL . '/includes/plugins/wp-hotel-booking-currencies' );

if ( !defined( 'TP_HB_STORAGE_NAME' ) )
    define( 'TP_HB_STORAGE_NAME', 'tp_hb_sw_currency' );

class WPHB_SW_Factory {

    public function __construct() {
        $this->init();
    }

    function init() {
        require_once TP_HB_CURRENCY . '/class-hb-currencies.php';
    }

}

new WPHB_SW_Factory();
