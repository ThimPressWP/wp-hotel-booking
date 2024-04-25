<?php

namespace Elementor;

use Thim_EL_Kit\GroupControlTrait;
use Elementor\Widget_Base;
use Elementor\Repeater;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use WPHB\HBGroupControlTrait;
use Elementor\Thim_Ekit_Widget_Filter_Room_Selected;
use WPHB_Settings;
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if (!class_exists('Thim_Ekit_Widget_Filter_Room_Selected')){
	require_once WPHB_PLUGIN_PATH . '/includes/elementor/widgets/global/filter-room-selected.php';
}
class Thim_Ekit_Widget_Filter_Room extends Thim_Ekit_Widget_Filter_Room_Selected {

    use GroupControlTrait;
	use HBGroupControlTrait;

    public function get_name() {
		return 'wphb-filter-room';
	}

	public function get_title() {
		return esc_html__( 'Filter Room', 'wp-hotel-booking' );
	}

	public function get_icon() {
		return 'thim-eicon eicon-taxonomy-filter';
	}

	public function get_categories() {
		return [ \Thim_EL_Kit\Elementor::CATEGORY ];
	}

    public function get_keywords() {
		return [ 'filter', 'room' ];
	}

    public function get_base() {
		return basename( __FILE__, '.php' );
	}

    protected function register_controls() {
        $this->start_controls_section(
			'section_filter',
			[
				'label' => __( 'Filter Area', 'wp-hotel-booking' ),
			]
		);

        $repeater_data = new Repeater();
        $repeater_data->add_control(
            'meta_field',
			[
				'label'   => esc_html__( 'Select Meta', 'wp-hotel-booking' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'types',
				'options' => [
 					'price'     => esc_html__( 'Price', 'wp-hotel-booking' ),
					'rating'    => esc_html__( 'Rating', 'wp-hotel-booking' ),
					'types'     => esc_html__( 'Types', 'wp-hotel-booking' ),
					'clear'     => esc_html__( 'Clear All', 'wp-hotel-booking' )
				]
			]
        );

		$repeater_data->add_control(
			'min_price',
			[
				'label'     => esc_html__( 'Min Price', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::NUMBER,
				'default' 	=> 0,
                'condition'     => [
					'meta_field' => 'price',
				],
			]
		);

		$repeater_data->add_control(
			'max_price',
			[
				'label'     => esc_html__( 'Max Price', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::NUMBER,
				'default' 	=> 1000,
                'condition'     => [
					'meta_field' => 'price',
				],
			]
		);

		$repeater_data->add_control(
			'show_count',
			[
				'label'   => esc_html__( 'Show Count', 'wp-hotel-booking' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition'     => [
					'meta_field' => [ 'types', 'rating' ],
				],
			]
		);

		$repeater_data->add_control(
			'heading_setting',
			[
				'label'        => esc_html__( 'Heading Setting', 'wp-hotel-booking' ),
				'type'         => Controls_Manager::POPOVER_TOGGLE,
				'label_off'    => esc_html__( 'Default', 'wp-hotel-booking' ),
				'label_on'     => esc_html__( 'Custom', 'wp-hotel-booking' ),
				'return_value' => 'yes',
				'condition'     => [
					'meta_field!' => 'clear',
				],
			]
		);

		$repeater_data->start_popover();

		$repeater_data->add_control(
			'enable_heading',
			[
				'label'        => esc_html__( 'Enable Heading', 'wp-hotel-booking' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => esc_html__( 'Show', 'wp-hotel-booking' ),
				'label_off'    => esc_html__( 'Hide', 'wp-hotel-booking' ),
				'return_value' => 'yes',
			]
		);

		$repeater_data->add_control(
			'toggle_content',
			[
				'label'     => esc_html__( 'Show Toggle', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'none',
				'options'   => array(
					'none' 			=> esc_html__( 'None', 'wp-hotel-booking' ),
					'show' 			=> esc_html__( 'Show', 'wp-hotel-booking' ),
					'always_show' 	=> esc_html__( 'Always Show', 'wp-hotel-booking' ),
					'dropdown' 		=> esc_html__( 'Dropdown', 'wp-hotel-booking' ),
				),
				'condition'    => [
					'enable_heading' => 'yes',
				],
			]
		);

		$repeater_data->end_popover();

		$repeater_data->add_responsive_control(
			'width_item',
			[
				'label'     => esc_html__( 'Width Content', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::SLIDER,
				'size_units' => [ '%', 'px' ],
				'default'   => [
                    'unit'  => '%'
                ],
				'selectors' => [
					'{{WRAPPER}} .search-filter-form-el {{CURRENT_ITEM}}' => 'width: {{SIZE}}{{UNIT}};'
				]
			]
		);

        $this->add_control(
			'data',
            [
				'label'       => esc_html__( 'Filter', 'wp-hotel-booking' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater_data->get_controls(),
				'default'     => [
 					[
						'meta_field' => 'types',
					],
					[
						'meta_field' => 'rating',
					],
				],
				'title_field' => '<span style="text-transform: capitalize;">{{{ meta_field.replace("_", " ") }}}</span>',
			]
        );

        $this->end_controls_section();
		$this->register_section_extra();
		$this->register_section_style_field();
		$this->register_section_style_title();
		$this->register_section_style_item();
		$this->register_section_style_clear();
		$this->register_section_style_price();
		$this->register_section_style_form_popup();
		$this->register_section_style_button_popup();
		$this->register_section_style_selected_number();
		$this->register_section_style_item_selected(
			array(
				'selected_list' => 'yes'
			)
		);
		$this->register_section_style_icon_selected(
			array(
				'selected_list' => 'yes'
			)
		);
		$this->register_section_clear_button(
			array(
				'selected_list' => 'yes'
			)
		);
    }

	protected function register_section_extra(){
		$this->start_controls_section(
			'extra_option',
			[
				'label' => esc_html__( 'Extra Option', 'wp-hotel-booking' ),
			]
		);

		$this->add_control(
			'filter_toggle_button',
			[
				'label'        => esc_html__( 'Filter Toggle Button', 'wp-hotel-booking' ),
				'type'         => Controls_Manager::POPOVER_TOGGLE,
				'label_off'    => esc_html__( 'Default', 'wp-hotel-booking' ),
				'label_on'     => esc_html__( 'Custom', 'wp-hotel-booking' ),
				'return_value' => 'yes',
			]
		);

		$this->start_popover();

		$this->add_control(
			'enable_filter_button',
			[
				'label'        => esc_html__( 'Enable Toggle Button', 'wp-hotel-booking' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => esc_html__( 'Show', 'wp-hotel-booking' ),
				'label_off'    => esc_html__( 'Hide', 'wp-hotel-booking' ),
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'enable_filter_button_mobile',
			[
				'label'        => esc_html__( 'Enable Toggle Button Mobile', 'wp-hotel-booking' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => esc_html__( 'Show', 'wp-hotel-booking' ),
				'label_off'    => esc_html__( 'Hide', 'wp-hotel-booking' ),
				'return_value' => 'yes',
				'condition' => [
					'enable_filter_button!' => 'yes',
				]
			]
		);

		$this->add_control(
			'text_filter_button', 
			[
				'label'       => esc_html__( 'Text Button', 'wp-hotel-booking' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Add your {{text here}}', 'wp-hotel-booking' ),
				'default'     => esc_html__( 'Filter', 'wp-hotel-booking' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'icon_filter_button',
			array(
				'label'       => esc_html__( 'Icon', 'wp-hotel-booking' ),
 				'type'        => Controls_Manager::ICONS,
				'skin'        => 'inline',
				'label_block' => false,
			)
		);

		$this->add_control(
			'icon_filter_position',
			[
				'label'     => esc_html__( 'Icon Position', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'left',
				'options'   => [
					'left'  => esc_html__( 'Before', 'wp-hotel-booking' ),
					'right' => esc_html__( 'After', 'wp-hotel-booking' ),
				],
				'condition' => [
					'icon_filter_button[value]!' => '',
				]
			]
		);

		$this->add_control(
			'filter_selected_number',
			[
				'label'        => esc_html__( 'Filter Selected Number', 'wp-hotel-booking' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => esc_html__( 'Show', 'wp-hotel-booking' ),
				'label_off'    => esc_html__( 'Hide', 'wp-hotel-booking' ),
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'selected_list',
			[
				'label'        => esc_html__( 'Selected List', 'wp-hotel-booking' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => esc_html__( 'Show', 'wp-hotel-booking' ),
				'label_off'    => esc_html__( 'Hide', 'wp-hotel-booking' ),
				'return_value' => 'yes',
			]
		);

		$this->add_responsive_control(
			'filter_section_width',
			[
				'label'     => esc_html__( 'Width Form', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'custom' ],
				'default' => [
					'unit' => 'px',
					'size' => 300,
				],
				'selectors' => [
					'{{WRAPPER}} .search-filter-form-el' => 'width: {{SIZE}}{{UNIT}};']
			]
		);

		$this->end_popover();

		$this->end_controls_section();

		$this->register_section_selected_options(
			array(
				'selected_list' => 'yes'
			)
		);
	}

	protected function register_section_style_field(){
		$this->start_controls_section(
			'style_field',
			[
				'label' => esc_html__( 'Field', 'wp-hotel-booking' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'layout_form',
			array(
				'label'     => esc_html__( 'Display', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'block'        => array(
						'title' => esc_html__( 'Default', 'wp-hotel-booking' ),
						'icon'  => 'eicon-editor-list-ul',
					),
					'flex' => array(
						'title' => esc_html__( 'Flex', 'wp-hotel-booking' ),
						'icon'  => 'eicon-ellipsis-h',
					),
				),
				'default'   => 'block',
				'toggle'    => true,
				'selectors' => array(
					'{{WRAPPER}} .search-filter-form-el ' => '--display-form-filter: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'field_border',
				'label'    => esc_html__( 'Border', 'wp-hotel-booking' ),
				'selector' => '{{WRAPPER}} #hotel-booking-search-filter .search-filter-form-el .field-item > div',
			]
		);

		$this->add_responsive_control(
			'field_padding',
			[
				'label'      => esc_html__( 'Padding', 'wp-hotel-booking' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} #hotel-booking-search-filter .search-filter-form-el .field-item > div' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'field_margin',
			[
				'label'      => esc_html__( 'Margin', 'wp-hotel-booking' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} #hotel-booking-search-filter .search-filter-form-el .field-item > div' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'field_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'wp-hotel-booking' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} #hotel-booking-search-filter .search-filter-form-el .field-item > div' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_section_style_title(){
		$this->start_controls_section(
			'style_title',
			[
				'label' => esc_html__( 'Title', 'wp-hotel-booking' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->register_style_typo_color_margin('title_filter', '.hotel-booking-search-filter h4, .hotel-booking-search-filter .title');

		$this->add_responsive_control(
			'icon_toggle_offset_h',
			[
				'label'     => esc_html__( 'Offset Icon X', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .field-item .icon-toggle-filter' => 'right: {{SIZE}}{{UNIT}};']
			]
		);

		$this->add_responsive_control(
			'icon_toggle_offset_v',
			[
				'label'     => esc_html__( 'Offset Icon Y', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .field-item .icon-toggle-filter' => 'top: {{SIZE}}{{UNIT}};']
			]
		);

		$this->end_controls_section();
	}

	protected function register_section_style_item(){
		$this->start_controls_section(
			'style_item',
			[
				'label' => esc_html__( 'Item', 'wp-hotel-booking' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->register_style_typo_color_margin('item_filter', '#hotel-booking-search-filter.hotel-booking-search-filter .list-item');

		$this->add_control(
			'item_color_hover',
			[
				'label'     => esc_html__( 'Color Hover', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .hotel-booking-search-filter .list-item:hover' => 'color: {{VALUE}};'
				],
			]
		);

		$this->add_control(
			'toggle_dropdown',
			[
				'label'     => esc_html__( 'Toggle Dropdown', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'toggle_dropdown_width',
			[
				'label'       => esc_html__( 'Width', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => 'px',
					'size' => 200,
				],
				'selectors'   => [
					'{{WRAPPER}} .search-filter-form-el .field-item.toggle-content.dropdown ul, {{WRAPPER}} .search-filter-form-el .field-item.toggle-content.dropdown .hb-search-price' => 'min-width: {{SIZE}}{{UNIT}};']
			]
		);

		$this->add_control(
			'toggle_dropdown_bg',
			[
				'label'     => esc_html__( 'Background', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .search-filter-form-el .field-item.toggle-content.dropdown ul' => 'background-color: {{VALUE}};'
				],
			]
		);

		$this->add_responsive_control(
			'toggle_dropdown_padding',
			[
				'label'      => esc_html__( 'Padding', 'wp-hotel-booking' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .search-filter-form-el .field-item.toggle-content.dropdown ul, {{WRAPPER}} .search-filter-form-el .field-item.toggle-content.dropdown .hb-search-price' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'toggle_dropdown_margin',
			[
				'label'      => esc_html__( 'Margin', 'wp-hotel-booking' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .search-filter-form-el .field-item.toggle-content.dropdown ul, {{WRAPPER}} .search-filter-form-el .field-item.toggle-content.dropdown .hb-search-price ' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'toggle_dropdown_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'wp-hotel-booking' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .search-filter-form-el .field-item.toggle-content.dropdown ul' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'toggle_dropdown_shadow',
				'selector'  => '{{WRAPPER}} .search-filter-form-el .field-item.toggle-content.dropdown ul',
			]
		);

		$this->end_controls_section();
	}

	protected function register_section_style_clear(){
		$this->start_controls_section(
			'style_clear',
			[
				'label' => esc_html__( 'Clear', 'wp-hotel-booking' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'clear_align',
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
					'{{WRAPPER}} .search-filter-form-el .clear-filter' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->register_button_style( 'clear', '#hotel-booking-search-filter .clear-filter button' );

		$this->add_responsive_control(
			'clear_margin',
			[
				'label'      => esc_html__( 'Margin', 'wp-hotel-booking' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} #hotel-booking-search-filter .clear-filter button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_section_style_price(){
		$this->start_controls_section(
			'style_field_price',
			[
				'label' => esc_html__( 'Price', 'wp-hotel-booking' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'range_price',
			[
				'label'     => esc_html__( 'Range Price', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'range_color',
			[
				'label'     => esc_html__( 'Color', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .search-filter-form-el .noUi-horizontal' => 'background-color: {{VALUE}};'
				],
			]
		);

		$this->add_control(
			'range_color_target',
			[
				'label'     => esc_html__( 'Color Target', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .search-filter-form-el .noUi-horizontal .noUi-connect, {{WRAPPER}} .search-filter-form-el .noUi-horizontal .noUi-handle' => 'background-color: {{VALUE}};'
				],
			]
		);

		$this->add_responsive_control(
			'range_price_height',
			[
				'label'     => esc_html__( 'Height Range', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => 'px',
					'size' => 6,
				],
				'selectors' => [
					'{{WRAPPER}} .search-filter-form-el .noUi-horizontal' => 'height: {{SIZE}}{{UNIT}};']
			]
		);

		$this->add_responsive_control(
			'handle_price_height',
			[
				'label'     => esc_html__( 'Font Size Handle', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => 'px',
					'size' => 12,
				],
				'selectors' => [
					'{{WRAPPER}} .search-filter-form-el .noUi-horizontal .noUi-handle' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};']
			]
		);

		$this->add_responsive_control(
			'handle_price_offset_h',
			[
				'label'       => esc_html__( 'Offset X', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => 'px',
					'size' => -10,
				],
				'selectors'   => [
					'{{WRAPPER}} .search-filter-form-el .noUi-horizontal .noUi-handle' => 'right:{{SIZE}}{{UNIT}};']
			]
		);

		$this->add_responsive_control(
			'handle_price_offset_v',
			[
				'label'       => esc_html__( 'Offset Y', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => 'px',
					'size' => -4,
				],
				'selectors'   => [
					'{{WRAPPER}} .search-filter-form-el .noUi-horizontal .noUi-handle' => 'top:{{SIZE}}{{UNIT}};']
			]
		);

		$this->add_responsive_control(
			'range_margin',
			[
				'label'      => esc_html__( 'Margin', 'wp-hotel-booking' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .search-filter-form-el .noUi-horizontal' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'button_apply',
			[
				'label'     => esc_html__( 'Button Apply', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->register_button_style( 'btn_apply', '#hotel-booking-search-filter .search-filter-form-el button.apply' );

		$this->add_responsive_control(
			'button_apply_margin',
			[
				'label'      => esc_html__( 'Margin', 'wp-hotel-booking' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} #hotel-booking-search-filter .search-filter-form-el button.apply' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_section_style_form_popup(){
		$this->start_controls_section(
			'style_form_popup',
			[
				'label' => esc_html__( 'Form Popup', 'wp-hotel-booking' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'filter_toggle_button' => 'yes',
				]
			]
		);

		$this->add_control(
			'form_popup_bg',
			[
				'label'     => esc_html__( 'Background', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fff',
				'selectors' => [
					'{{WRAPPER}} .search-filter-form-el.hb-filter-popup, {{WRAPPER}} .search-filter-form-el.hb-filter-popup-mobile' => 'background-color: {{VALUE}};'
				],
			]
		);

		$this->add_responsive_control(
			'form_popup_padding',
			[
				'label'      => esc_html__( 'Padding', 'wp-hotel-booking' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .search-filter-form-el.hb-filter-popup, {{WRAPPER}} .search-filter-form-el.hb-filter-popup-mobile' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'form_popup_shadow',
				'selector'  => '{{WRAPPER}} .search-filter-form-el.hb-filter-popup',
				'separator' => 'before',
				'condition' => [
					'enable_filter_button' => 'yes',
				]
			]
		);

		$this->end_controls_section();
	}

	protected function register_section_style_button_popup(){
		$this->start_controls_section(
			'style_button_popup',
			[
				'label' => esc_html__( 'Button Popup', 'wp-hotel-booking' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'filter_toggle_button' => 'yes',
				]
			]
		);

		$this->register_button_style( 'button_popup', '.hotel-booking-search-filter .hb-button-popup' );

		$this->add_responsive_control(
			'button_popup_margin',
			[
				'label'      => esc_html__( 'Margin', 'wp-hotel-booking' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .hotel-booking-search-filter .hb-button-popup' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_popup_size',
			[
				'label'     => esc_html__( 'Font Size Icon', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .hb-button-popup i' => 'font-size: {{SIZE}}{{UNIT}};'
				],
				'condition' => [
					'icon_filter_button[value]!' => '',
				]
			]
		);

		$this->end_controls_section();
	}

	protected function register_section_style_selected_number(){
		$this->start_controls_section(
			'style_selected_number',
			[
				'label' => esc_html__( 'Selected Number', 'wp-hotel-booking' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'filter_selected_number' => 'yes',
					'filter_toggle_button' => 'yes',
				]
			]
		);

		$this->register_button_style( 'selected_number', '.hotel-booking-search-filter .selected-filter-number' );

		$this->add_responsive_control(
			'selected_number_margin',
			[
				'label'      => esc_html__( 'Margin', 'wp-hotel-booking' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .hotel-booking-search-filter .selected-filter-number' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

    public function render() {
		$hb_settings = WPHB_Settings::instance();
        $settings    = $this->get_settings_for_display();
		$extraClass = '';

        if ( $settings['data'] ) { ?>
            <div id="hotel-booking-search-filter" class="hotel-booking-search-filter hb-el">
			<?php 
				if ( $settings['filter_toggle_button'] == 'yes' ) {
					if ( $settings['enable_filter_button'] == 'yes' ) {
						$extraClass .= ' hb-filter-popup';
					}
					if ( $settings['enable_filter_button_mobile'] == 'yes' ) {
						$extraClass .= ' hb-filter-popup-mobile';
					}
					$this->button_popup($settings, $extraClass);
				}
			?>
            <form class="search-filter-form search-filter-form-el <?php echo esc_attr($extraClass) ?>" action="">
                <?php 
				if ( $settings['selected_list'] == 'yes' ) {
					self::render_selected($settings);
				}
                foreach ( $settings['data'] as $data ) {
					$classes = $icon_toggle = '';

					if ( isset($data['show_count']) && $data['show_count'] != 'yes') {
						$classes .= ' count-hide';
					}

					if ( isset($data['enable_heading']) && $data['enable_heading'] != 'yes') {
						$classes .= ' heading-hide';
					}

					if ( isset($data['toggle_content']) && $data['toggle_content'] != 'none') {
						$classes .= ' toggle-content';
						$icon_toggle = '<i class="icon-toggle-filter fas fa-angle-up"></i><i class="icon-toggle-filter fas fa-angle-down"></i>';

						if ( $data['toggle_content'] == 'always_show') {
							$classes .= ' toggle-on';
						}
						if ( $data['toggle_content'] == 'dropdown') {
							$classes .= ' dropdown';
						}
					}

					$classes .= ' elementor-repeater-item-'.$data['_id'];

					$data['step_price'] = $hb_settings->get( 'filter_price_step', 1 );
					$data['min_price']  = $data['min_price'] ?? $hb_settings->get( 'filter_price_min', 0 );
					$data['max_price']  = $data['max_price'] ?? $hb_settings->get( 'filter_price_max', 0 );
					$data['min_value']  = hb_get_request( 'min_price' );
					$data['max_value']  = hb_get_request( 'max_price' );
			
					switch ( $data['meta_field'] ) {
						case 'clear':
							?>
							<div class="clear-filter">
								<button type="button">
									<?php esc_html_e( 'Reset', 'wp-hotel-booking' ); ?>
								</button>
							</div>
							<?php
							break;
						default:
						?> 
							<div class="field-item <?php echo esc_attr($classes) ?>">
						<?php
							if ( $icon_toggle != '' ){
								echo $icon_toggle;
							}
                    		hb_get_template( 'search/v2/search-filter/' . $data['meta_field'] . '.php', compact( 'data' ) );
						?> 
							</div> 
						<?php
					}
                }
                ?>
            </form>
			<div class="filter-bg"></div>
            </div>
            <?php
        }
    }

	protected function button_popup($settings, $extraClass){
		$text_popup = $settings['text_filter_button'] ?? esc_html__('Filter', 'wp-hotel-booking');

		echo '<button class="hb-button-popup ' . esc_attr($extraClass) . '">';
		if (!empty($settings['icon_filter_button'])) {
			Icons_Manager::render_icon(
				$settings['icon_filter_button'],
				array(
					'aria-hidden' => 'true',
					'class'       => 'icon-align-' . esc_attr($settings['icon_filter_position']),
				)
			);
		}
		echo $text_popup;
		if ($settings['filter_selected_number'] == 'yes') {
			echo $this->selected_style_number();
		}
		echo '</button>';
	}

	protected function selected_style_number(){
		$types = $rating = $price = 0;
		$total = '';

		if (!empty($_GET['room_type'])) {
			$types =  count(explode(',', $_GET['room_type']));
		}
		if (!empty($_GET['rating'])) {
			$rating =  count(explode(',', $_GET['rating']));
		}
		if (!empty($_GET['min_price']) || !empty($_GET['max_price'])) {
			$price =  count(explode(',', $_GET['min-price']));
			$price =  count(explode(',', $_GET['max-price']));
		}

		$total = $types + $rating + $price;

		if (!empty($total)) {
			echo '<span class="selected-filter-number">' . $total . '</span>';
		}
	}
}