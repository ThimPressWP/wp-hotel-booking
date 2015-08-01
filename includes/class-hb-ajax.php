<?php
class HB_Ajax{
    protected static $_loaded = false;
    function __construct(){
        if( self::$_loaded ) return;

        $ajax_actions = array(
            'fetch_custom_info' => true
        );

        foreach( $ajax_actions as $action => $priv ){
            add_action( "wp_ajax_hotel_booking_{$action}", array( __CLASS__, $action ) );
            if( $priv ){
                add_action( "wp_ajax_nopriv_hotel_booking_{$action}", array( __CLASS__, $action ) );
            }
        }

        self::$_loaded = true;
    }

    static function fetch_custom_info(){
        print_r( $_POST );
        die();
    }
}

new HB_Ajax();