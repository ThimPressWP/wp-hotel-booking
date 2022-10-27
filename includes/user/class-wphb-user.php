<?php
/**
 * WP Hotel Booking urser.
 *
 * @version     1.9.6
 * @author      ThimPress
 * @package     WP_Hotel_Booking/Classes
 * @category    Classes
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

WP_Hotel_Booking::instance()->_include( 'includes/user/class-wphb-abstract-user.php' );

class WPHB_User extends WPHB_User_Abstract {

	static $users = null;

	// get user
	static function get_user( $user_id = null ) {
		if ( ! empty( self::$users[ $user_id ] ) ) {
			return self::$users[ $user_id ];
		}

		return self::$users[ $user_id ] = new self( $user_id );
	}

	// get current user
	static function get_current_user() {
		$user_id = get_current_user_id();
		return self::get_user( $user_id );
	}

}
