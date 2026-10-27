<?php

namespace Elementor;

use WPHB\HBGroupControlTrait;
use Thim_EL_Kit\GroupControlTrait;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;

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
				'default' => 'form',
				'options' => array(
                    'form'    => esc_html__( 'Form', 'wp-hotel-booking' ),
				),
			)
		);

        $this->end_controls_section();

        $this->_register_style_form_booking_head();
        $this->_register_style_form_booking_content();
    }

    protected function _register_style_form_booking_head()
    {
        $this->start_controls_section(
            'section_form_head',
            array(
                'label' => esc_html__('Form Header', 'wp-hotel-booking'),
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
					'{{WRAPPER}} .hb-room-single__booking .hb-booking-room-form-head, {{WRAPPER}} .hotel-booking-single-room-action .hb-booking-room-form-head, {{WRAPPER}} .hotel-booking-single-room-action .hb-booking-room-form-head h2' => 'text-align: {{VALUE}};',
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
                'label' => esc_html__('Form Content', 'wp-hotel-booking'),
                'tab'   => Controls_Manager::TAB_STYLE,
            )
        );

        // ==================== Form Container ====================
        $this->add_control(
            'form_container_heading',
            array(
                'label'     => esc_html__( 'Form Container', 'wp-hotel-booking' ),
                'type'      => Controls_Manager::HEADING,
            )
        );

        $this->add_responsive_control(
            'form_container_display',
            array(
                'label'     => esc_html__( 'Display', 'wp-hotel-booking' ),
                'type'      => Controls_Manager::CHOOSE,
                'options'   => array(
                    'block' => array(
                        'title' => esc_html__( 'Block', 'wp-hotel-booking' ),
                        'icon'  => 'eicon-editor-list-ul',
                    ),
                    'flex'  => array(
                        'title' => esc_html__( 'Flex', 'wp-hotel-booking' ),
                        'icon'  => 'eicon-ellipsis-h',
                    ),
                ),
                'default'   => '',
                'toggle'    => true,
                'selectors' => array(
                    '{{WRAPPER}} .hb-search-results-form-container' => 'display: {{VALUE}};',
                ),
            )
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            array(
                'name'     => 'form_container_background',
                'selector' => '{{WRAPPER}} .hotel-booking-single-room-action, {{WRAPPER}} .hb-room-single__booking__form #hotel_booking_room_hidden',
            )
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            array(
                'name'     => 'form_container_border',
                'selector' => '{{WRAPPER}} .hotel-booking-single-room-action',
            )
        );

        $this->add_responsive_control(
            'form_container_border_radius',
            array(
                'label'      => esc_html__( 'Border Radius', 'wp-hotel-booking' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', '%', 'em' ),
                'selectors'  => array(
                    '{{WRAPPER}} .hotel-booking-single-room-action' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            array(
                'name'     => 'form_container_box_shadow',
                'selector' => '{{WRAPPER}} .hotel-booking-single-room-action',
            )
        );

        $this->add_responsive_control(
            'form_container_padding',
            array(
                'label'      => esc_html__( 'Padding', 'wp-hotel-booking' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', 'em', '%' ),
                'selectors'  => array(
                    '{{WRAPPER}} .hotel-booking-single-room-action' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_responsive_control(
            'form_container_margin',
            array(
                'label'      => esc_html__( 'Margin', 'wp-hotel-booking' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', 'em', '%' ),
                'selectors'  => array(
                    '{{WRAPPER}} .hotel-booking-single-room-action' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        // ==================== Fields & Labels ====================
        $this->add_control(
            'fields_labels_heading',
            array(
                'label'     => esc_html__( 'Fields & Labels', 'wp-hotel-booking' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            )
        );

        $this->add_responsive_control(
            'fields_spacing',
            array(
                'label'      => esc_html__( 'Field Spacing (Bottom Margin)', 'wp-hotel-booking' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array( 'px', 'em' ),
                'range'      => array(
                    'px' => array(
                        'min' => 0,
                        'max' => 60,
                    ),
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .hb-booking-room-form-group' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'fields_label_typography',
                'label'    => esc_html__( 'Label Typography', 'wp-hotel-booking' ),
                'selector' => '{{WRAPPER}} .hb-booking-room-form-group > label',
            )
        );

        $this->add_control(
            'fields_label_color',
            array(
                'label'     => esc_html__( 'Label Color', 'wp-hotel-booking' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .hb-booking-room-form-group > label' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_responsive_control(
            'fields_label_margin',
            array(
                'label'      => esc_html__( 'Label Margin', 'wp-hotel-booking' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', 'em', '%' ),
                'selectors'  => array(
                    '{{WRAPPER}} .hb-booking-room-form-group > label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        // Max Quantity Text
        $this->add_control(
            'fields_max_qty_heading',
            array(
                'label'     => esc_html__( 'Max Quantity Text', 'wp-hotel-booking' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'fields_max_qty_typography',
                'selector' => '{{WRAPPER}} .wphb-max-qty',
            )
        );

        $this->add_control(
            'fields_max_qty_color',
            array(
                'label'     => esc_html__( 'Text Color', 'wp-hotel-booking' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .wphb-max-qty' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'fields_max_qty_num_color',
            array(
                'label'     => esc_html__( 'Number Highlight Color', 'wp-hotel-booking' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .wphb-max-qty .qty-max' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_responsive_control(
            'fields_max_qty_margin',
            array(
                'label'      => esc_html__( 'Margin', 'wp-hotel-booking' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', 'em', '%' ),
                'selectors'  => array(
                    '{{WRAPPER}} .wphb-max-qty' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        // ==================== Inputs ====================
        $this->add_control(
			'form_check_input_head', [
				'label'     => esc_html__( 'Input', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
			]
		);

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'input_typography',
                'selector' => '{{WRAPPER}} .hb-room-single__booking .hotel-booking-single-room-action .hb-booking-room-form-field input',
            )
        );

        $this->add_responsive_control(
            'input_height',
            array(
                'label'      => esc_html__( 'Height', 'wp-hotel-booking' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array( 'px', 'em' ),
                'range'      => array(
                    'px' => array(
                        'min' => 20,
                        'max' => 100,
                    ),
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .hb-room-single__booking .hotel-booking-single-room-action .hb-booking-room-form-field input' => 'height: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->register_style_border_padding_margin('form_check_input', '.hb-room-single__booking .hotel-booking-single-room-action .hb-booking-room-form-field input');

        $this->add_responsive_control(
            'input_border_radius',
            array(
                'label'      => esc_html__( 'Border Radius', 'wp-hotel-booking' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', '%', 'em' ),
                'selectors'  => array(
                    '{{WRAPPER}} .hb-room-single__booking .hotel-booking-single-room-action .hb-booking-room-form-field input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->start_controls_tabs( 'tabs_input_style' );

        $this->start_controls_tab(
            'tab_input_normal',
            array(
                'label' => esc_html__( 'Normal', 'wp-hotel-booking' ),
            )
        );

        $this->add_control(
            'input_text_color',
            array(
                'label'     => esc_html__( 'Text Color', 'wp-hotel-booking' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .hb-room-single__booking .hotel-booking-single-room-action .hb-booking-room-form-field input' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'input_placeholder_color',
            array(
                'label'     => esc_html__( 'Placeholder Color', 'wp-hotel-booking' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .hb-room-single__booking .hotel-booking-single-room-action .hb-booking-room-form-field input::placeholder' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .hb-room-single__booking .hotel-booking-single-room-action .hb-booking-room-form-field input::-webkit-input-placeholder' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'input_bg_color',
            array(
                'label'     => esc_html__( 'Background Color', 'wp-hotel-booking' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .hb-room-single__booking .hotel-booking-single-room-action .hb-booking-room-form-field input' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'input_border_color',
            array(
                'label'     => esc_html__( 'Border Color', 'wp-hotel-booking' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .hb-room-single__booking .hotel-booking-single-room-action .hb-booking-room-form-field input' => 'border-color: {{VALUE}};',
                ),
            )
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            array(
                'name'     => 'input_box_shadow',
                'selector' => '{{WRAPPER}} .hb-room-single__booking .hotel-booking-single-room-action .hb-booking-room-form-field input',
            )
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_input_focus',
            array(
                'label' => esc_html__( 'Focus', 'wp-hotel-booking' ),
            )
        );

        $this->add_control(
            'input_text_color_focus',
            array(
                'label'     => esc_html__( 'Text Color', 'wp-hotel-booking' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .hb-room-single__booking .hotel-booking-single-room-action .hb-booking-room-form-field input:focus' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'input_bg_color_focus',
            array(
                'label'     => esc_html__( 'Background Color', 'wp-hotel-booking' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .hb-room-single__booking .hotel-booking-single-room-action .hb-booking-room-form-field input:focus' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'input_border_color_focus',
            array(
                'label'     => esc_html__( 'Border Color', 'wp-hotel-booking' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .hb-room-single__booking .hotel-booking-single-room-action .hb-booking-room-form-field input:focus' => 'border-color: {{VALUE}};',
                ),
            )
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            array(
                'name'     => 'input_box_shadow_focus',
                'selector' => '{{WRAPPER}} .hb-room-single__booking .hotel-booking-single-room-action .hb-booking-room-form-field input:focus',
            )
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        // ==================== Optional Extras ====================
        $this->add_control(
            'extras_section_heading',
            array(
                'label'     => esc_html__( 'Optional Extras', 'wp-hotel-booking' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            )
        );

        // Section Toggle / Title
        $this->add_control(
            'extras_toggle_heading',
            array(
                'label' => esc_html__( 'Toggle / Title', 'wp-hotel-booking' ),
                'type'  => Controls_Manager::HEADING,
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'extras_toggle_typography',
                'selector' => '{{WRAPPER}} .hb_addition_package_title_toggle a, {{WRAPPER}} .hb_package_toggle',
            )
        );

        $this->start_controls_tabs( 'tabs_extras_toggle' );

        $this->start_controls_tab(
            'tab_extras_toggle_normal',
            array(
                'label' => esc_html__( 'Normal', 'wp-hotel-booking' ),
            )
        );

        $this->add_control(
            'extras_toggle_color',
            array(
                'label'     => esc_html__( 'Color', 'wp-hotel-booking' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .hb_addition_package_title_toggle a, {{WRAPPER}} .hb_package_toggle' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_extras_toggle_hover',
            array(
                'label' => esc_html__( 'Hover', 'wp-hotel-booking' ),
            )
        );

        $this->add_control(
            'extras_toggle_color_hover',
            array(
                'label'     => esc_html__( 'Color', 'wp-hotel-booking' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .hb_addition_package_title_toggle a:hover, {{WRAPPER}} .hb_package_toggle:hover' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
            'extras_toggle_margin',
            array(
                'label'      => esc_html__( 'Margin', 'wp-hotel-booking' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', 'em', '%' ),
                'selectors'  => array(
                    '{{WRAPPER}} .hb_addition_package_title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        // Extra Items
        $this->add_control(
            'extras_item_heading',
            array(
                'label'     => esc_html__( 'Extra Item', 'wp-hotel-booking' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            )
        );

        $this->add_control(
            'extras_item_bg_color',
            array(
                'label'     => esc_html__( 'Background Color', 'wp-hotel-booking' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .hb_addition_packages_ul li' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            array(
                'name'     => 'extras_item_border',
                'selector' => '{{WRAPPER}} .hb_addition_packages_ul li',
            )
        );

        $this->add_responsive_control(
            'extras_item_border_radius',
            array(
                'label'      => esc_html__( 'Border Radius', 'wp-hotel-booking' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', '%', 'em' ),
                'selectors'  => array(
                    '{{WRAPPER}} .hb_addition_packages_ul li' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_responsive_control(
            'extras_item_padding',
            array(
                'label'      => esc_html__( 'Padding', 'wp-hotel-booking' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', 'em', '%' ),
                'selectors'  => array(
                    '{{WRAPPER}} .hb_addition_packages_ul li' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_responsive_control(
            'extras_item_margin',
            array(
                'label'      => esc_html__( 'Margin / Spacing', 'wp-hotel-booking' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', 'em', '%' ),
                'selectors'  => array(
                    '{{WRAPPER}} .hb_addition_packages_ul li' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        // Extra Item Title / Name
        $this->add_control(
            'extras_title_heading',
            array(
                'label'     => esc_html__( 'Item Title', 'wp-hotel-booking' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'extras_title_typography',
                'selector' => '{{WRAPPER}} .hb_package_title label',
            )
        );

        $this->add_control(
            'extras_title_color',
            array(
                'label'     => esc_html__( 'Color', 'wp-hotel-booking' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .hb_package_title label' => 'color: {{VALUE}};',
                ),
            )
        );

        // Extra Item Description
        $this->add_control(
            'extras_desc_heading',
            array(
                'label'     => esc_html__( 'Item Description', 'wp-hotel-booking' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'extras_desc_typography',
                'selector' => '{{WRAPPER}} .hb_extra_title p',
            )
        );

        $this->add_control(
            'extras_desc_color',
            array(
                'label'     => esc_html__( 'Color', 'wp-hotel-booking' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .hb_extra_title p' => 'color: {{VALUE}};',
                ),
            )
        );

        // Extra Item Price
        $this->add_control(
            'extras_price_heading',
            array(
                'label'     => esc_html__( 'Item Price', 'wp-hotel-booking' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'extras_price_typography',
                'selector' => '{{WRAPPER}} .hb_extra_detail_price label, {{WRAPPER}} .hb_extra_detail_price strong',
            )
        );

        $this->add_control(
            'extras_price_color',
            array(
                'label'     => esc_html__( 'Price Color', 'wp-hotel-booking' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .hb_extra_detail_price strong' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'extras_unit_color',
            array(
                'label'     => esc_html__( 'Unit Color', 'wp-hotel-booking' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .hb_extra_detail_price small' => 'color: {{VALUE}};',
                ),
            )
        );

        // Extra Quantity Input
        $this->add_control(
            'extras_qty_heading',
            array(
                'label'     => esc_html__( 'Quantity Input', 'wp-hotel-booking' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            )
        );

        $this->add_responsive_control(
            'extras_qty_width',
            array(
                'label'      => esc_html__( 'Width', 'wp-hotel-booking' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array( 'px', '%' ),
                'range'      => array(
                    'px' => array(
                        'min' => 40,
                        'max' => 150,
                    ),
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .hb_extra_detail_price input.hb_optional_quantity' => 'max-width: {{SIZE}}{{UNIT}}; width: 100%;',
                ),
            )
        );

        $this->add_control(
            'extras_qty_color',
            array(
                'label'     => esc_html__( 'Color', 'wp-hotel-booking' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .hb_extra_detail_price input.hb_optional_quantity' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'extras_qty_bg_color',
            array(
                'label'     => esc_html__( 'Background Color', 'wp-hotel-booking' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .hb_extra_detail_price input.hb_optional_quantity' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            array(
                'name'     => 'extras_qty_border',
                'selector' => '{{WRAPPER}} .hb_extra_detail_price input.hb_optional_quantity',
            )
        );

        $this->add_responsive_control(
            'extras_qty_border_radius',
            array(
                'label'      => esc_html__( 'Border Radius', 'wp-hotel-booking' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', '%', 'em' ),
                'selectors'  => array(
                    '{{WRAPPER}} .hb_extra_detail_price input.hb_optional_quantity' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_responsive_control(
            'extras_qty_padding',
            array(
                'label'      => esc_html__( 'Padding', 'wp-hotel-booking' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', 'em' ),
                'selectors'  => array(
                    '{{WRAPPER}} .hb_extra_detail_price input.hb_optional_quantity' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        // Checkbox
        $this->add_control(
            'extras_checkbox_heading',
            array(
                'label'     => esc_html__( 'Checkbox', 'wp-hotel-booking' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            )
        );

        $this->add_responsive_control(
            'extras_checkbox_size',
            array(
                'label'      => esc_html__( 'Size', 'wp-hotel-booking' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array( 'px' ),
                'range'      => array(
                    'px' => array(
                        'min' => 12,
                        'max' => 32,
                    ),
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .hb_addition_package_extra input[type="checkbox"]' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->add_control(
            'extras_checkbox_accent_color',
            array(
                'label'     => esc_html__( 'Accent Color', 'wp-hotel-booking' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .hb_addition_package_extra input[type="checkbox"]' => 'accent-color: {{VALUE}};',
                ),
            )
        );

        $this->add_responsive_control(
            'extras_checkbox_margin',
            array(
                'label'      => esc_html__( 'Margin', 'wp-hotel-booking' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', 'em' ),
                'selectors'  => array(
                    '{{WRAPPER}} .hb_addition_package_extra input[type="checkbox"]' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        // ==================== Total Price ====================
        $this->add_control(
            'total_price_section_heading',
            array(
                'label'     => esc_html__( 'Total Price', 'wp-hotel-booking' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            )
        );

        $this->add_control(
            'total_price_bg_color',
            array(
                'label'     => esc_html__( 'Background Color', 'wp-hotel-booking' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .hb-booking-room-form-group.hb-room-price' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            array(
                'name'     => 'total_price_border',
                'selector' => '{{WRAPPER}} .hb-booking-room-form-group.hb-room-price',
            )
        );

        $this->add_responsive_control(
            'total_price_border_radius',
            array(
                'label'      => esc_html__( 'Border Radius', 'wp-hotel-booking' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', '%', 'em' ),
                'selectors'  => array(
                    '{{WRAPPER}} .hb-booking-room-form-group.hb-room-price' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            array(
                'name'     => 'total_price_box_shadow',
                'selector' => '{{WRAPPER}} .hb-booking-room-form-group.hb-room-price',
            )
        );

        $this->add_responsive_control(
            'total_price_padding',
            array(
                'label'      => esc_html__( 'Padding', 'wp-hotel-booking' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', 'em', '%' ),
                'selectors'  => array(
                    '{{WRAPPER}} .hb-booking-room-form-group.hb-room-price' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_responsive_control(
            'total_price_margin',
            array(
                'label'      => esc_html__( 'Margin', 'wp-hotel-booking' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', 'em', '%' ),
                'selectors'  => array(
                    '{{WRAPPER}} .hb-booking-room-form-group.hb-room-price' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_responsive_control(
            'total_price_display',
            array(
                'label'     => esc_html__( 'Total Display', 'wp-hotel-booking' ),
                'type'      => Controls_Manager::CHOOSE,
                'options'   => array(
                    'block' => array(
                        'title' => esc_html__( 'Block', 'wp-hotel-booking' ),
                        'icon'  => 'eicon-editor-list-ul',
                    ),
                    'flex'  => array(
                        'title' => esc_html__( 'Flex (Inline)', 'wp-hotel-booking' ),
                        'icon'  => 'eicon-ellipsis-h',
                    ),
                ),
                'default'   => 'flex',
                'selectors' => array(
                    '{{WRAPPER}} .hb-room-price .hb-total-price' => 'display: {{VALUE}}; align-items: center;',
                ),
            )
        );

        $this->add_responsive_control(
            'total_price_justify',
            array(
                'label'     => esc_html__( 'Justify Content', 'wp-hotel-booking' ),
                'type'      => Controls_Manager::CHOOSE,
                'options'   => array(
                    'flex-start' => array(
                        'title' => esc_html__( 'Start', 'wp-hotel-booking' ),
                        'icon'  => 'eicon-text-align-left',
                    ),
                    'center'     => array(
                        'title' => esc_html__( 'Center', 'wp-hotel-booking' ),
                        'icon'  => 'eicon-text-align-center',
                    ),
                    'flex-end'   => array(
                        'title' => esc_html__( 'End', 'wp-hotel-booking' ),
                        'icon'  => 'eicon-text-align-right',
                    ),
                    'space-between' => array(
                        'title' => esc_html__( 'Space Between', 'wp-hotel-booking' ),
                        'icon'  => 'eicon-text-align-justify',
                    ),
                ),
                'default'   => 'space-between',
                'condition' => array(
                    'total_price_display' => 'flex',
                ),
                'selectors' => array(
                    '{{WRAPPER}} .hb-room-price .hb-total-price' => 'justify-content: {{VALUE}};',
                ),
            )
        );

        // Total label
        $this->add_control(
            'total_text_heading',
            array(
                'label'     => esc_html__( 'Total Label', 'wp-hotel-booking' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'total_text_typography',
                'selector' => '{{WRAPPER}} .hb-total-price-text',
            )
        );

        $this->add_control(
            'total_text_color',
            array(
                'label'     => esc_html__( 'Color', 'wp-hotel-booking' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .hb-total-price-text' => 'color: {{VALUE}};',
                ),
            )
        );

        // Total price value
        $this->add_control(
            'total_value_heading',
            array(
                'label'     => esc_html__( 'Total Amount', 'wp-hotel-booking' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'total_value_typography',
                'selector' => '{{WRAPPER}} .hb-total-price-value',
            )
        );

        $this->add_control(
            'total_value_color',
            array(
                'label'     => esc_html__( 'Color', 'wp-hotel-booking' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .hb-total-price-value' => 'color: {{VALUE}};',
                ),
            )
        );

        // View details link
        $this->add_control(
            'view_details_heading',
            array(
                'label'     => esc_html__( 'View Details Link', 'wp-hotel-booking' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'view_details_typography',
                'selector' => '{{WRAPPER}} .hb-single-room-price-details',
            )
        );

        $this->start_controls_tabs( 'tabs_view_details' );

        $this->start_controls_tab(
            'tab_view_details_normal',
            array(
                'label' => esc_html__( 'Normal', 'wp-hotel-booking' ),
            )
        );

        $this->add_control(
            'view_details_color',
            array(
                'label'     => esc_html__( 'Color', 'wp-hotel-booking' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .hb-single-room-price-details' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_view_details_hover',
            array(
                'label' => esc_html__( 'Hover', 'wp-hotel-booking' ),
            )
        );

        $this->add_control(
            'view_details_color_hover',
            array(
                'label'     => esc_html__( 'Color', 'wp-hotel-booking' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .hb-single-room-price-details:hover' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
            'view_details_margin',
            array(
                'label'      => esc_html__( 'Margin', 'wp-hotel-booking' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', 'em', '%' ),
                'selectors'  => array(
                    '{{WRAPPER}} .hb_view_price' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        // ==================== Button ====================
        $this->add_control(
			'form_check_button_head', [
				'label'     => esc_html__( 'Button', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
			]
		);

        $this->add_responsive_control(
            'button_align',
            array(
                'label'     => esc_html__( 'Alignment', 'wp-hotel-booking' ),
                'type'      => Controls_Manager::CHOOSE,
                'options'   => array(
                    'left'    => array(
                        'title' => esc_html__( 'Left', 'wp-hotel-booking' ),
                        'icon'  => 'eicon-text-align-left',
                    ),
                    'center'  => array(
                        'title' => esc_html__( 'Center', 'wp-hotel-booking' ),
                        'icon'  => 'eicon-text-align-center',
                    ),
                    'right'   => array(
                        'title' => esc_html__( 'Right', 'wp-hotel-booking' ),
                        'icon'  => 'eicon-text-align-right',
                    ),
                    'justify' => array(
                        'title' => esc_html__( 'Justified', 'wp-hotel-booking' ),
                        'icon'  => 'eicon-text-align-justify',
                    ),
                ),
                'selectors'    => array(
                    '{{WRAPPER}} .hb-booking-room-form-group:last-child' => 'text-align: {{VALUE}};',
                ),
            )
        );

        $this->add_responsive_control(
            'button_full_width',
            array(
                'label'        => esc_html__( 'Full Width', 'wp-hotel-booking' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Yes', 'wp-hotel-booking' ),
                'label_off'    => esc_html__( 'No', 'wp-hotel-booking' ),
                'return_value' => '100%',
                'default'      => '100%',
                'selectors'    => array(
                    '{{WRAPPER}} .hb-room-single__booking #hotel_booking_room_hidden .hb_button' => 'width: {{VALUE}};',
                ),
            )
        );

        $this->add_responsive_control(
            'button_margin',
            array(
                'label'      => esc_html__( 'Margin', 'wp-hotel-booking' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', 'em', '%' ),
                'selectors'  => array(
                    '{{WRAPPER}} .hb-room-single__booking #hotel_booking_room_hidden .hb_button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->register_button_style( 'form_check_button', '.hb-room-single__booking #hotel_booking_room_hidden .hb_button' );

        // Previous Step Button
        $this->add_control(
            'previous_button_heading',
            array(
                'label'     => esc_html__( 'Previous Step Button', 'wp-hotel-booking' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            )
        );

        $this->register_button_style( 'previous_step_button', '.hb-room-single__booking #hotel_booking_room_hidden .hb_previous_step.hb_button' );

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
        $minium_booking_night = (int) \WPHB_Settings::instance()->get( 'minimum_booking_day', 1 );
        $minium_booking_night = max( 1, $minium_booking_night );
        // $minium_checkout_date = 1 + $minium_booking_night;

        $check_in_date  = hb_get_request( 'check_in_date', date( 'Y/m/d' ) );
        $check_out_date = hb_get_request( 'check_out_date', date( 'Y/m/d', strtotime( "+{$minium_booking_night} day" ) ) );
        if ( strtotime( $check_out_date ) <= strtotime( $check_in_date ) ) {
            $check_out_date = date( 'Y/m/d', strtotime( $check_in_date . " +{$minium_booking_night} day" ) );
        }
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
