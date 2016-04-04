<?php
/**
 * @Author: ducnvtt
 * @Date:   2016-03-31 14:55:56
 * @Last Modified by:   ducnvtt
 * @Last Modified time: 2016-04-04 14:07:23
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

abstract class HB_User_Abstract {

	public $user 	= null;

	public $id 		= null;

	function __construct( $user = null ) {

		if ( is_numeric( $user ) && ( $user = get_user_by( 'ID', $user ) ) ) {
			$this->user = $user;
			$this->id 	= $this->user->ID;
		} else if ( $user instanceof WP_User  ) {
			$this->user = $user;
			$this->id 	= $this->user->ID;
		}

		if ( ! $user ) {
			$current_user = wp_get_current_user();
			$this->id = $current_user->ID;
		}

		if ( ! $this->id ) {
			throw new Exception( sprintf( __( 'User %s is not exists.', 'tp-hotel-booking' ), $user ) );
		}
	}

	function __get( $key ) {
		if ( ! isset( $this->{$key} ) || ! method_exists( $this, $key ) ) {
			return get_user_meta( $this->id, '_hb_' . $key, true );
		}
	}

}