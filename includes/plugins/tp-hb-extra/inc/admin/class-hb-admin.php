<?php

/**
 * admin extra class
 * menu, tab, room setting extra field
 */
class HB_Extra_Admin
{

	function __construct()
	{
		/**
		 * tp_hotel_booking_addon_menus recive addon menus
		 */
		add_filter( 'hotel_booking_addon_menus', array( $this, 'extra_settings' ) );
		add_action( 'hotel_booking_extra', array( $this, 'extra_settings_build' ) );
	}

	/**
	 * other settings tab
	 * @param $settings array
	 * @return array with key is unique
	 */
	function extra_settings( $settings )
	{
		$settings['tp_hotel_booking_extra'] = __( 'Extra Room Packages', 'tp-hb-extra' );
		return $settings;
	}

	function extra_settings_build()
	{
		HB_Extra_Factory::instance()->_include( TP_HB_EXTRA_INC . '/admin/views/extra.php' );
	}

}

new HB_Extra_Admin();