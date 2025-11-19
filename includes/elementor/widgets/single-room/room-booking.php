<?php

namespace Elementor;

use WPHB\HBGroupControlTrait;
use Thim_EL_Kit\GroupControlTrait;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Thim_Ekit_Widget_Room_Booking extends Widget_Base
{
    use GroupControlTrait;
    use HBGroupControlTrait;

    public function get_name()
    {
        return 'room-booking';
    }

    public function get_title()
    {
        return esc_html__('Room Booking', 'wp-hotel-booking');
    }

    public function get_icon()
    {
        return 'thim-eicon eicon-product-add-to-cart';
    }

    public function get_categories()
    {
        return array(\WPHB\Elementor::CATEGORY_SINGLE_ROOM);
    }

    protected function register_controls()
    {
        $this->start_controls_section(
			'section_tabs',
			[
				'label' => __('Booking', 'wp-hotel-booking'),
			]
		);

        $this->add_control(
			'layout',
			array(
				'label'   => esc_html__( 'Select Layout', 'wp-hotel-booking' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'base',
				'options' => array(
                    // 'base'    => esc_html__( 'Base (Popup)', 'wp-hotel-booking' ),
                    'form'    => esc_html__( 'Form', 'wp-hotel-booking' ),
				),
			)
		);

        $this->add_control(
			'text_booking',
			[
				'label'         => esc_html__( 'Text Booking', 'wp-hotel-booking' ),
				'type'          => Controls_Manager::TEXT,
                'placeholder'   => esc_html__( 'Add your text here', 'wp-hotel-booking' ),
                'default'       => esc_html__( 'Check Availability', 'wp-hotel-booking' ),
                'condition' => [
					'layout' => 'base',
				]
			]
		);

        $this->end_controls_section();
        $this->_register_style_button_base();
        $this->_register_style_form_booking_head();
        $this->_register_style_form_booking_content();
    }

    protected function _register_style_button_base()
    {
        $this->start_controls_section(
            'section_button',
            array(
                'label' => esc_html__('Button', 'wp-hotel-booking'),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition'     => [
					'layout' => 'base',
				]
            )
        );

        $this->register_button_style( 'button_booking', '.hb-room-single__booking .hb_button' );

        $this->end_controls_section();
    }

    protected function _register_style_form_booking_head()
    {
        $this->start_controls_section(
            'section_form_head',
            array(
                'label' => esc_html__('From Head', 'wp-hotel-booking'),
                'tab'   => Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_responsive_control(
			'table_align',
			array(
				'label'     => esc_html__( 'Alignment', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left' => array(
						'title' => esc_html__( 'Start', 'wp-hotel-booking' ),
						'icon'  => 'eicon-h-align-left',
					),
					'center'     => array(
						'title' => esc_html__( 'Center', 'wp-hotel-booking' ),
						'icon'  => ' eicon-h-align-center',
					),
					'right'   => array(
						'title' => esc_html__( 'End', 'wp-hotel-booking' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'toggle'    => true,
				'selectors' => array(
					'{{WRAPPER}} .hb-room-single__booking .hb-booking-room-form-head, .hotel-booking-single-room-action .hb-booking-room-form-head, .hotel-booking-single-room-action .hb-booking-room-form-head h2' => 'text-align: {{VALUE}};',
				),
			)
		);

        $this->add_responsive_control(
			'radius_item',
			[
				'label'      => esc_html__( 'Radius Item', 'wp-hotel-booking' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .hotel-booking-single-room-action' => '--border-radius-item: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_control(
			'form_title', [
				'label'     => esc_html__( 'Title', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
			]
		);

        $this->register_style_typo_color_margin('room_form_booking_title', '.hb-room-single__booking .title, .hb-booking-room-form-head h2');

        $this->add_control(
			'form_description', [
				'label'     => esc_html__( 'Description', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
			]
		);

        $this->register_style_typo_color_margin('room_form_booking_description', '.hb-room-single__booking .description, .hb-booking-room-form-head .description');

        $this->end_controls_section();
    }

    protected function _register_style_form_booking_content()
    {
        $this->start_controls_section(
            'section_form_content_check',
            array(
                'label' => esc_html__('Check Available', 'wp-hotel-booking'),
                'tab'   => Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_control(
			'form_check_input_head', [
				'label'     => esc_html__( 'Input', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
			]
		);

        $this->register_style_border_padding_margin('form_check_input', '.hb-room-single__booking .hotel-booking-single-room-action .hb-booking-room-form-field input');

        $this->add_control(
			'form_check_button_head', [
				'label'     => esc_html__( 'Button', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
			]
		);

        $this->register_button_style( 'form_check_button', '.hb-room-single__booking #hotel_booking_room_hidden .hb_button' );

        $this->end_controls_section();
    }

    protected function render()
    {
        do_action('WPHB/modules/single-room/before-preview-query');

        $settings        = $this->get_settings_for_display();
        
        $room_id = get_the_ID();
        ?>

        <div class="hb-room-single__booking">
            <div class="hb-room-single__booking__form">
                <?php $this->_render_booking_form($room_id); ?>
            </div>
        </div>

        <?php do_action('WPHB/modules/single-room/after-preview-query');
    }

    protected function _render_booking_form( $room_id )
    {
        $minium_booking_night = \WPHB_Settings::instance()->get( 'minimum_booking_day', 1 );
        // $minium_checkout_date = 1 + $minium_booking_night;

        $check_in_date  = hb_get_request( 'check_in_date', date( 'Y/m/d' ) );
        $check_out_date = hb_get_request( 'check_out_date', date( 'Y/m/d', strtotime( "+{$minium_booking_night} day" ) ) );
        $adults         = hb_get_request( 'adults', 1 );
        $children       = hb_get_request( 'max_child', 0 );
        $room_qty       = hb_get_request( 'room_qty', 1 );

        $room = \WPHB_Room::instance(
            $room_id,
            array(
                'check_in_date'  => $check_in_date,
                'check_out_date' => $check_out_date,
                'quantity'       => $room_qty,
            )
        );
        ob_start();
        hb_get_template( 'single-room/booking-form.php', array( 'room' => $room, 'is_elementor' => true, ) );
        echo ob_get_clean();
    }
}
