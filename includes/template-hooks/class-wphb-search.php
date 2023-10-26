<?php

class WPHB_Search_Template_Hook {
	public function __construct() {
		add_action( 'wphb/loop-v2/room-content', array( $this, 'content_section' ) );
		add_action( 'wphb/loop-v2/room-info', array( $this, 'room_info_section' ) );
		add_action( 'wphb/loop-v2/room-meta', array( $this, 'room_meta_section' ) );
	}

	public function content_section( $room ) {
		$sections = apply_filters(
			'wphb/filter/room-content',
			array(
				'search/v2/loop-v2/thumbnail.php',
				'search/v2/loop-v2/room-info.php',
			)
		);

		foreach ( $sections as $section ) {
			hb_get_template(
				$section,
				array(
					'room' => $room,
				)
			);
		}
	}

	public function room_info_section( $room ) {
		$sections = apply_filters(
			'wphb/filter/room-info',
			array(
				'search/v2/loop-v2/room-info/room-name.php',
				'search/v2/loop-v2/room-info/room-meta.php',
			)
		);

		foreach ( $sections as $section ) {
			hb_get_template(
				$section,
				array(
					'room' => $room,
				)
			);
		}
	}

	public function room_meta_section( $room ) {
		$sections = apply_filters(
			'wphb/filter/room-meta',
			array(
				'search/v2/loop-v2/room-info/room-meta/capacity.php',
				'search/v2/loop-v2/room-info/room-meta/max-child.php',
				'search/v2/loop-v2/room-info/room-meta/price.php',
				'search/v2/loop-v2/room-info/room-meta/quanity.php',
				'search/v2/loop-v2/room-info/room-meta/add-to-cart.php',
			)
		);

		foreach ( $sections as $section ) {
			hb_get_template(
				$section,
				array(
					'room' => $room,
				)
			);
		}
	}
}

new WPHB_Search_Template_Hook();
