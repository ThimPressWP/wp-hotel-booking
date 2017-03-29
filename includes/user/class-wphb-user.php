<?php

/**
 * @Author: ducnvtt
 * @Date:   2016-03-31 14:44:36
 * @Last Modified by:   ducnvtt
 * @Last Modified time: 2016-03-31 15:19:01
 */
if ( !defined( 'ABSPATH' ) ) {
    exit();
}

WP_Hotel_Booking::instance()->_include( 'includes/user/class-wphb-abstract-user.php' );

class WPHB_User extends WPHB_User_Abstract {

    static $users = null;

    // get user
    static function get_user( $user_id = null ) {
        if ( !empty( self::$users[$user_id] ) ) {
            return self::$users[$user_id];
        }

        return self::$users[$user_id] = new self( $user_id );
    }

    // get current user
    static function get_current_user() {
        $user_id = get_current_user_id();
        return self::get_user( $user_id );
    }

}
