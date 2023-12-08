<?php

namespace Elementor;

use Thim_EL_Kit\GroupControlTrait;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Thim_Ekit_Widget_Archive_Rooms extends Widget_Base {
	use GroupControlTrait;

	public function get_name() {
		return 'archive-rooms';
	}

	public function get_title() {
		return esc_html__( 'Archive Rooms', 'wp-hotel-booking');
	}

	public function get_icon() {
		return 'thim-eicon eicon-post-excerpt';
	}

	public function get_categories() {
		return array('thim_ekit_archive_room');
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_tabs',
			array(
				'label' => __( 'Global', 'wp-hotel-booking' ),
			)
		);

		// $this->add_control(
		// 	'template_id',
		// 	array(
		// 		'label'              => esc_html__( 'Template ID', 'wp-hotel-booking' ),
		// 		'type'               => Controls_Manager::SELECT,
		// 		'default'            => '0',
		// 		'options'            => array( '0' => esc_html__( 'None', 'wp-hotel-booking' ) ) + \Thim_EL_Kit\Functions::instance()->get_pages_loop_item( 'hb_room' ),
		// 		'frontend_available' => true,
		// 	)
		// );

		$this->end_controls_section();
	}
	protected function render() {
		$settings    = $this->get_settings_for_display();
		
        ?>
            <div class="test-widget">
                <?php echo 'get_the_ID'(); ?>
            </div>
        <?php
	}
}
