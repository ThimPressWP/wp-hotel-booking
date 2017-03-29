<?php

/**
 * @Author: ducnvtt
 * @Date:   2016-03-31 14:45:55
 * @Last Modified by:   ducnvtt
 * @Last Modified time: 2016-03-31 15:24:48
 */
if ( !defined( 'ABSPATH' ) ) {
    exit();
}

// get user
if ( !function_exists( 'hb_get_user' ) ) {

    function hb_get_user( $user = null ) {
        return WPHB_User::get_user( $user );
    }

}

// get current user
if ( !function_exists( 'hb_get_current_user' ) ) {

    function hb_get_current_user() {
        global $hb_curent_user;

        if ( !$hb_curent_user ) {
            $hb_curent_user = WPHB_User::get_current_user();
        }
        return $hb_curent_user;
    }

}
