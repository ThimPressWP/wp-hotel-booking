<?php
/**
 * @Author: ducnvtt
 * @Date:   2016-03-29 17:13:06
 * @Last Modified by:   ducnvtt
 * @Last Modified time: 2016-03-29 17:20:23
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class HB_Admin_Setting_General extends HB_Admin_Setting_Page {

	public $id = 'general';

	public $title = null;

	function __construct() {

		$this->title = __( 'General', 'tp-hotel-booking' );

		parent::__construct();
	}

	public function get_settings() {
		return apply_filters( 'hotel_booking_admin_setting_' . $this->id, array(

					

			) );
	}

}

return new HB_Admin_Setting_General();