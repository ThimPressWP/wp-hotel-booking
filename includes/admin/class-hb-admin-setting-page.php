<?php
/**
 * @Author: ducnvtt
 * @Date:   2016-03-29 15:09:28
 * @Last Modified by:   ducnvtt
 * @Last Modified time: 2016-03-29 17:34:39
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

abstract class HB_Admin_Setting_Page {

	protected $id = null;

	protected $title = null;

	function __construct() {

		add_filter( 'hb_admin_settings_tabs', array( $this, 'setting_tabs' ) );
		add_filter( 'hb_admin_settings_sections_' . $this->id, array( $this, 'setting_sections' ) );
		add_action( 'hb_admin_settings_tab_' . $this->id, array( $this, 'output' ) );
	}

	/**
	 * get_settings field
	 * @return array settings fields
	 */
	public function get_settings() {
		return apply_filters( 'hotel_booking_admin_setting_' . $this->id, array() );
	}

	// filter tab id
	public function setting_tabs( $tabs ) {
		$tabs[ $this->id ] = $this->title;
		return $tabs;
	}

	// output setting page
	public function output() {
		$settings = $this->get_settings();
		HB_Admin_Settings::render_fields( $settings );
	}

	// filter section in tab id
	public function setting_sections( $sections ) {
		return $sections;
	}

	// save setting option
	public function save() {
		$settings = $this->get_settings();
		HB_Admin_Settings::save_fields( $settings );
	}

}