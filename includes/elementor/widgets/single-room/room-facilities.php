<?php

namespace Elementor;

use WPHB\HBGroupControlTrait;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Thim_Ekit_Widget_Room_Facilities extends Widget_Base
{
    use HBGroupControlTrait;

    public function get_name()
    {
        return 'room-facilities';
    }

    public function get_title()
    {
        return esc_html__('Room Facilities', 'wp-hotel-booking');
    }

    public function get_icon()
    {
        return 'thim-eicon eicon-check-circle';
    }

    public function get_categories()
    {
        return array(\WPHB\Elementor::CATEGORY_SINGLE_ROOM);
    }
    
    protected function register_controls()
    {
        $this->_register_style_facilities_item();
        $this->_register_style_facilities_title();
        $this->_register_style_facilities_detail();
    }

    protected function _register_style_facilities_item()
    {
        $this->start_controls_section(
            'section_facilities',
            array(
                'label' => esc_html__('Facility', 'wp-hotel-booking'),
                'tab'   => Controls_Manager::TAB_STYLE,
            )
        );

        $this->register_style_border_padding_margin('facility_item', '.hb-room-single__facilities .__hb_room_facility__detail');

        $this->end_controls_section();
    }

    protected function _register_style_facilities_title()
    {
        $this->start_controls_section(
            'section_title',
            array(
                'label' => esc_html__('Title', 'wp-hotel-booking'),
                'tab'   => Controls_Manager::TAB_STYLE,
            )
        );

        $this->register_style_typo_color_margin('title_facilities', '.hb-room-single__facilities .__hb_room_facility__label');

        $this->end_controls_section();
    }

    protected function _register_style_facilities_detail()
    {
        $this->start_controls_section(
            'section_facility_detail',
            array(
                'label' => esc_html__('Detail', 'wp-hotel-booking'),
                'tab'   => Controls_Manager::TAB_STYLE,
            )
        ); 

        $this->add_responsive_control(
			'facility_columns',
			array(
				'label'     => esc_html__( 'Columns', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '3',
				'options'   => array(
					'100%'      => '1',
					'50%'       => '2',
					'33.33%'    => '3',
					'25%'       => '4',
                    '20%'       => '5',
				),
				'selectors' => array(
					'{{WRAPPER}} .hb-room-single__facilities .facility_attr' => 'flex-basis: {{VALUE}}',
				),
			)
		);

        $this->register_style_typo_color_margin('detail_facilities', '.hb-room-single__facilities .facility_attr__label');

        $this->end_controls_section();
    }

    protected function render()
    {
        do_action('WPHB/modules/single-room/before-preview-query');

        $room = \WPHB_Room::instance(get_the_ID());
        if (empty($room)) {
            return;
        }

        $facilities = get_post_meta($room->ID, '_wphb_room_facilities', true);

        if (!empty($facilities)) {
        ?>
            <div class="hb-room-single__facilities">
                <?php echo $room->get_facilities() ?>
            </div>
        <?php
        }

        do_action('WPHB/modules/single-room/after-preview-query');
    }
}
