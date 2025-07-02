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
                    'base'    => esc_html__( 'Base (Popup)', 'wp-hotel-booking' ),
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
        global $hb_room;
        $hb_room = \WPHB_Room::instance(get_the_ID()); ?>

        <div class="hb-room-single__booking">
            <?php if ( $settings['layout'] == 'form' ) { ?>

                <div class="hb-room-single__booking__form">
                    <?php $this->_render_booking_form($hb_room); ?>
                </div> 

            <?php } else {
                $external_link = get_post_meta( $hb_room->ID, '_hb_external_link', true );
                $external_link = ! empty( $external_link ) ? $external_link : '#';
                 ?>

                <a href="<?php echo $external_link; ?>" <?php echo ! empty( $external_link ) ? 'target="_blank"' : ''; ?> data-id="<?php echo esc_attr( $hb_room->ID ); ?>" data-name="<?php echo esc_attr( $hb_room->name ); ?>"
                    class="hb_button hb_primary" id="hb_room_load_booking_form">
                    <?php _e( $settings['text_booking'], 'wp-hotel-booking' ); ?>
                </a>

            <?php } ?>
        </div>

        <?php do_action('WPHB/modules/single-room/after-preview-query');
    }

    protected function _render_booking_form($hb_room)
    {
        ?>
        <div id="hotel_booking_room_hidden">
           <form action="POST" name="hb-search-single-room" class="hb-search-room-results hotel-booking-search hotel-booking-single-room-action">
            <div class="hb-booking-room-form-head">
                <h2 class="title"><?php echo get_the_title() ?></h2>
                <p class="description"><?php _e( 'Please set arrival date and departure date before check available.', 'wp-hotel-booking' ); ?></p>
            </div>
            <div class="hb-search-results-form-container">
                <div class="hb-booking-room-form-group">
                    <div class="hb-booking-room-form-field hb-form-field-input">
                        <input type="text" name="check_in_date" value id="check_in_date" placeholder="<?php _e( 'Arrival Date', 'wp-hotel-booking' ); ?>" autocomplete="off"/><input type="text" name="select-date-range" style="display:none;">
                    </div>
                </div>
                <div class="hb-booking-room-form-group">
                    <div class="hb-booking-room-form-field hb-form-field-input">
                        <input type="text" name="check_out_date" value id="check_out_date" placeholder="<?php _e( 'Departure Date', 'wp-hotel-booking' ); ?>" autocomplete="off"/>
                    </div>
                </div>
                <div class="hb-booking-room-form-group">
                    <input type="hidden" name="room-name" value="<?php printf( '%s', $hb_room->post_title ); ?>" />
                    <input type="hidden" name="room-id" value="<?php printf( '%s', $hb_room->ID ); ?>" />
                    <input type="hidden" name="action" value="hotel_booking_single_check_room_available"/>
                    <?php wp_nonce_field( 'hb_booking_single_room_check_nonce_action', 'hb-booking-single-room-check-nonce-action' ); ?>
                    <button type="submit" class="hb_button"><?php _e( 'Check Available', 'wp-hotel-booking' ); ?></button>
                </div>
            </div>
            </form>
        </div>
        <?php
    }
}
