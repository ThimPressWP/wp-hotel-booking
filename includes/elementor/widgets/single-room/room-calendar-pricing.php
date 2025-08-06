<?php

namespace Elementor;

use WPHB\HBGroupControlTrait;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Thim_Ekit_Widget_Room_Calendar_Pricing extends Widget_Base {

	use HBGroupControlTrait;

	public function get_name() {
		return 'room-calendar-pricing';
	}

	public function get_title() {
		return esc_html__( 'Room Calendar Pricing', 'wp-hotel-booking' );
	}

	public function get_icon() {
		return 'thim-eicon eicon-search-results';
	}

	public function get_categories() {
		return array( \WPHB\Elementor::CATEGORY_SINGLE_ROOM );
	}

	protected function register_controls() {
	}


	protected function render() {
		$room = \WPHB_Room::instance( get_the_ID() );
		if ( empty( $room ) ) {
			return;
		}

		hb_get_template( 'single-room/room-calendar-pricing.php', array( 'room' => $room ) );
	}
}
