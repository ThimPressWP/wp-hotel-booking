<?php

namespace Elementor;

use Thim_EL_Kit\GroupControlTrait;
use Thim_EL_Kit\Utilities\Widget_Loop_Trait;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Thim_Ekit_Widget_Loop_Room_Quantity extends Widget_Base
{
    use GroupControlTrait;
    use Widget_Loop_Trait;

    public function get_name()
    {
        return 'loop-room-quantity';
    }

    public function get_title()
    {
        return esc_html__('Room Quantity', 'wp-hotel-booking');
    }

    public function get_icon()
    {
        return 'thim-eicon eicon-select';
    }

    public function get_keywords() {
		return array( 'room', 'cart', 'quantity' );
	}

    protected function register_controls()
    {
        $this->start_controls_section(
            'section_tabs',
            [
                'label' => __('General', 'wp-hotel-booking'),
            ]
        );

        $this->add_control(
			'text',
			array(
				'label'       => esc_html__( 'Text', 'wp-hotel-booking' ),
				'type'        => Controls_Manager::TEXT,
                'default'     => esc_html__( 'Quantity', 'wp-hotel-booking' ),
				'label_block' => true,
			)
        );

        $this->end_controls_section();
        $this->_register_section_style();
    }

    protected function _register_section_style()
    {
        $this->start_controls_section(
            'style_quantity',
            [
                'label' => esc_html__('Quantity', 'wp-hotel-booking'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );


        $this->end_controls_section();
    }

    protected function render()
    {
        $room = \WPHB_Room::instance(get_the_ID());

        if ( ! isset( $room ) ) {
            return;
        }

        $settings          = $this->get_settings_for_display(); 
        $text_quantity  = !empty( $settings['text'] ) ? $settings['text'] : _e( 'Quantity ', 'wp-hotel-booking' ); 
        $max_room = get_post_meta( $room->ID, '_hb_num_of_rooms', true );

        do_action( 'hotel_booking_loop_before_btn_select_room', $room->ID ); 
         ?>
            <li class="hb_search_quantity">
                <div>
                    <?php
                    hb_dropdown_numbers(
                        array(
                            'name'             => 'hb-num-of-rooms',
                            'min'              => 1,
                            'show_option_none' => $text_quantity,
                            'max'              => $max_room,
                            'class'            => 'number_room_select',
                            'selected'         => 1,
                        )
                    );
                    ?>
                </div>
            </li>
        
        <?php
    }
}