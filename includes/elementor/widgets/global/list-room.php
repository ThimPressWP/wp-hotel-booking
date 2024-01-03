<?php

namespace Elementor;

use Thim_EL_Kit\GroupControlTrait;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Thim_Ekit_Widget_List_Room extends Widget_Base {

    use GroupControlTrait;

    public function get_name()
    {
        return 'list-room';
    }

    public function get_title()
    {
        return esc_html__('List Room', 'wp-hotel-booking');
    }

    public function get_icon()
    {
        return 'thim-eicon eicon-posts-group';
    }

    public function get_categories() 
    {
		return array( \Thim_EL_Kit\Elementor::CATEGORY );
	}

    public function get_keywords() {
		return array( 'room', 'list' );
	}

    protected function register_controls() 
    {
        $this->start_controls_section(
			'section_tabs',
			array(
				'label' => __( 'General', 'wp-hotel-booking' ),
			)
		);

        $this->add_control(
			'template_id',
			array(
				'label'         => esc_html__( 'Choose a template', 'wp-hotel-booking' ),
				'type'          => Controls_Manager::SELECT2,
				'default'       => '0',
				'options'       => array( '0' => esc_html__( 'None', 'wp-hotel-booking' ) ) + \Thim_EL_Kit\Functions::instance()->get_pages_loop_item( 'lp_course' ),
				'prevent_empty' => false,
			)
		);

        $this->add_responsive_control(
			'columns',
			array(
				'label'              => esc_html__( 'Columns', 'thim-elementor-kit' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => '3',
				'tablet_default'     => '2',
				'mobile_default'     => '1',
				'options'            => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				),
				'selectors'          => array(
					'{{WRAPPER}}' => '--thim-ekits-room-columns: repeat({{VALUE}}, 1fr)',
				),
				'frontend_available' => true,
			)
		);

        $this->end_controls_section();
    }

    protected function render()
    {

    }
    
}