<?php

namespace Elementor;

use Thim_EL_Kit\GroupControlTrait;
use Thim_EL_Kit\Utilities\Widget_Loop_Trait;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Thim_Ekit_Widget_Loop_Room_Add_To_Cart extends Widget_Base
{
    use GroupControlTrait;
    use Widget_Loop_Trait;

    public function get_name()
    {
        return 'loop-room-add-to-cart';
    }

    public function get_title()
    {
        return esc_html__('Room Add To Cart', 'wp-hotel-booking');
    }

    public function get_icon()
    {
        return 'thim-eicon eicon-product-add-to-cart';
    }

    public function get_keywords() {
		return array( 'room', 'cart' );
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
                'default'     => esc_html__( 'Select this room', 'wp-hotel-booking' ),
				'label_block' => true,
			)
        );

        $this->end_controls_section();
        $this->_register_section_style();
    }

    protected function _register_section_style()
    {
        $this->start_controls_section(
            'style_add_to_cart',
            [
                'label' => esc_html__('Add to cart', 'wp-hotel-booking'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->register_button_style( 'button_add_to_cart', '.hb_add_to_cart' );

        $this->end_controls_section();
    }

    protected function render()
    {
        $room = \WPHB_Room::instance(get_the_ID());

        if ( ! isset( $room ) ) {
            return;
        }

        $settings         = $this->get_settings_for_display();
        $text_add_to_cart = isset( $settings['text'] ) ? $settings['text'] : '';
        $room_qty         = hb_get_request( 'room_qty', 1 );
        ?>

        <?php do_action( 'hotel_booking_loop_before_btn_select_room', $room->post->ID ); ?>
        <div class="hb_search_add_to_cart">
            <button class="hb_add_to_cart"><?php echo esc_html( $text_add_to_cart ); ?></button>
            <div class="hb_search_quantity">
                <?php
                hb_dropdown_numbers(
                    array(
                        'name'             => 'hb-num-of-rooms',
                        'min'              => 1,
                        'show_option_none' => __( 'Select Quantity', 'wp-hotel-booking' ),
                        'selected'         => $room_qty,
                        'max'              => $room->post->available_rooms,
                        'class'            => 'number_room_select',
                    )
                );
                ?>
            </div>
        </div>
        
        <?php
    }
}
