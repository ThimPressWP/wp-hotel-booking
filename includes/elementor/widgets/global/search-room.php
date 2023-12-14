<?php

namespace Elementor;

use Thim_EL_Kit\GroupControlTrait;
use Elementor\Widget_Base;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Typography;
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class Thim_Ekit_Widget_Search_Room extends Widget_Base {
    use GroupControlTrait;

    public function get_name() {
		return 'wphb-search-room';
	}

	public function get_title() {
		return esc_html__( 'Search Room', 'wp-hotel-booking' );
	}

	public function get_icon() {
		return 'thim-eicon eicon-search-results';
	}

	public function get_categories() {
		return array( \WPHB\Elementor::CATEGORY );
	}

    public function get_keywords() {
		return array( 'search', 'room' );
	}

    public function get_base() {
		return basename( __FILE__, '.php' );
	}

    protected function register_controls() {
        $this->start_controls_section(
			'section_tabs',
			array(
				'label' => __( 'General', 'wp-hotel-booking' ),
			)
		);

        $this->add_control(
			'layout',
			array(
				'label'     => esc_html__( 'Layout', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'base',
				'options'   => array(
					'base'      => esc_html__( 'Base', 'wp-hotel-booking' ),
					'multidate' => esc_html__( 'Multidate', 'wp-hotel-booking' ),
				),
			)
		);

		$this->add_control(
			'icon_date',
			[
				'label'         => esc_html__( 'Icon Date', 'wp-hotel-booking' ),
				'type'          => Controls_Manager::ICONS,
                'skin'          => 'inline',
                'label_block'   => false,
				'condition'     => [
                    'layout' => 'multidate',
				],
			]
		);

		$this->add_control(
			'icon_adults',
			[
				'label'         => esc_html__( 'Icon Adults', 'wp-hotel-booking' ),
				'type'          => Controls_Manager::ICONS,
                'skin'          => 'inline',
                'label_block'   => false,
				'condition'     => [
                    'layout' => 'multidate',
				],
			]
		);

		$this->add_control(
			'icon_children',
			[
				'label'         => esc_html__( 'Icon Children', 'wp-hotel-booking' ),
				'type'          => Controls_Manager::ICONS,
                'skin'          => 'inline',
                'label_block'   => false,
				'condition'     => [
                    'layout' => 'multidate',
				],
			]
		);

        $this->add_control(
			'text_submit',
			[
				'label'     => esc_html__( 'Text Submit', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::TEXT,
                'placeholder'   => esc_html__( 'Add your text here', 'wp-hotel-booking' ),
				'default'       => esc_html__( 'Check Availability', 'wp-hotel-booking' ),
                'condition'     => [
					'layout' => 'multidate',
				],
			]
		);

        $this->add_control(
			'icon_submit',
			[
				'label'         => esc_html__( 'Icon Submit', 'wp-hotel-booking' ),
				'type'          => Controls_Manager::ICONS,
                'skin'          => 'inline',
                'label_block'   => false,
				'condition'     => [
                    'layout' => 'multidate',
				],
			]
		);

        $this->end_controls_section();
        $this->register_section_style_general();
		$this->register_section_style_title();
        $this->register_section_style_field();
		$this->register_section_style_field_list();
		$this->register_section_style_button();
    }

    protected function register_section_style_general(){
        $this->start_controls_section(
			'style_general',
			array(
				'label' => esc_html__( 'General', 'wp-hotel-booking' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

        $this->add_responsive_control(
			'display_base',
			array(
				'label'       => esc_html__( 'Display', 'wp-hotel-booking' ),
				'type'        => Controls_Manager::CHOOSE,
				'toggle'      => false,
				'options'     => array(
					'column'         => array(
						'title' => esc_html__( 'Block', 'wp-hotel-booking' ),
						'icon'  => 'eicon-editor-list-ul',
					),
					'row' => array(
						'title' => esc_html__( 'Inline', 'wp-hotel-booking' ),
						'icon'  => 'eicon-ellipsis-h',
					),
				),
				'selectors'   => array(
					'{{WRAPPER}} form, {{WRAPPER}} form > .hb-form-table' => 'flex-direction: {{VALUE}};display: flex;',
				),
			)
		);

        $this->add_responsive_control(
			'space_between',
			array(
				'label'      => esc_html__( 'Space Between', 'wp-hotel-booking' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => array(
					'unit' => 'px',
				),
				'size_units' => array( '%', 'px' ),
				'range'      => array(
					'%'  => array(
						'min' => 0,
						'max' => 100,
					),
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} form, {{WRAPPER}} form > .hb-form-table' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

        $this->add_responsive_control(
			'form_w', [
				'label'      => esc_html__( 'Form Width', 'wp-hotel-booking' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'%'  => array(
						'min' => 0,
						'max' => 100,
					),
				],
				'selectors'  => [
					'{{WRAPPER}} form > .hb-form-table' => 'flex-basis: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};'
				]
			]
		);

		$this->add_responsive_control(
			'form_padding',
			[
				'label'      => esc_html__( 'Padding Form', 'wp-hotel-booking' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'condition'     => [
					'layout' => 'multidate',
				],
				'selectors'  => [
					'{{WRAPPER}} .hotel-booking-search-el .hb-form-table .hb-form-field' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_responsive_control(
			'submit_w', [
				'label'      => esc_html__( 'Submit Width', 'wp-hotel-booking' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'%'  => array(
						'min' => 0,
						'max' => 100,
					),
				],
				'selectors'  => [
					'{{WRAPPER}} form > .hb-submit' => 'flex-basis: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};'
				]
			]
		);

        $this->end_controls_section();
    }

	protected function register_section_style_title(){
		$this->start_controls_section(
			'style_title',
			array(
				'label' => esc_html__( 'Title', 'wp-hotel-booking' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition'     => [
					'layout' => 'base',
				],
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => esc_html__( 'Typography', 'wp-hotel-booking' ),
				'selector' => '{{WRAPPER}} .hotel-booking-search h3',
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__( 'Color', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .hotel-booking-search h3' => 'color: {{VALUE}};'
				],
			]
		);

		$this->add_responsive_control(
			'title_margin',
			[
				'label'      => esc_html__( 'Margin', 'wp-hotel-booking' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .hotel-booking-search h3' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

    protected function register_section_style_field(){
        $this->start_controls_section(
			'style_field',
			array(
				'label' => esc_html__( 'Field', 'wp-hotel-booking' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'field_w', [
				'label'      => esc_html__( 'Field Width', 'wp-hotel-booking' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'%'  => array(
						'min' => 0,
						'max' => 100,
					),
				],
				'condition'     => [
					'layout' => 'base',
				],
				'selectors'  => [
					'{{WRAPPER}} .hotel-booking-search .hb-form-table .hb-form-field' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .hotel-booking-search .hb-form-table .hb-form-field *' => 'width: 100%'
				]
			]
		);

        $this->add_control(
			'label_heading', [
				'label'     => esc_html__( 'Label', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::HEADING,
			]
		);

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'label_typography',
				'label'    => esc_html__( 'Typography', 'wp-hotel-booking' ),
				'selector' => '{{WRAPPER}} .hotel-booking-search .hb-form-table .hb-form-field .label, {{WRAPPER}} .hotel-booking-search .hb-form-table .hb-form-field label',
			]
		);

        $this->add_control(
			'label_color',
			[
				'label'     => esc_html__( 'Color', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .hotel-booking-search .hb-form-table .hb-form-field .label, {{WRAPPER}} .hotel-booking-search .hb-form-table .hb-form-field label' => 'color: {{VALUE}};'
				],
			]
		);

        $this->add_responsive_control(
			'label_margin',
			[
				'label'      => esc_html__( 'Margin', 'wp-hotel-booking' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .hotel-booking-search .hb-form-table .hb-form-field .label, {{WRAPPER}} .hotel-booking-search .hb-form-table .hb-form-field label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; display: block',
				],
			]
		);

        $this->add_control(
			'input_heading', [
				'label'     => esc_html__( 'Input', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'input_typography',
				'label'    => esc_html__( 'Typography', 'wp-hotel-booking' ),
				'selector' => '{{WRAPPER}} .hotel-booking-search .hb-form-table .hb-form-field .hb-form-field-input, {{WRAPPER}} .hotel-booking-search input, {{WRAPPER}} .hotel-booking-search select',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'field_border_base',
				'label'    => esc_html__( 'Border', 'wp-hotel-booking' ),
				'condition'     => [
					'layout' => 'base',
				],
				'selector' => '{{WRAPPER}} .hotel-booking-search .hb-form-field-input input, {{WRAPPER}} .hotel-booking-search .hb-form-field-input select',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'field_border_multidate',
				'label'    => esc_html__( 'Border', 'wp-hotel-booking' ),
				'condition'     => [
					'layout' => 'multidate',
				],
				'selector' => '{{WRAPPER}} .hotel-booking-search .multidate-layout .hb-form-field',
			)
		);

		$this->add_control(
			'field_border_color_hover',
			[
				'label'     => esc_html__( 'Border Color Hover', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .hotel-booking-search .hb-form-field-input > input:focus, {{WRAPPER}} .hotel-booking-search .hb-form-field-input select:hover, {{WRAPPER}} .hotel-booking-search .multidate-layout .hb-form-field:hover' => 'border-color: {{VALUE}};'
				],
			]
		);

        $this->add_control(
			'input_color',
			[
				'label'     => esc_html__( 'Color', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .hotel-booking-search .hb-form-table .hb-form-field .hb-form-field-input *' => 'color: {{VALUE}} !important;'
				],
			]
		);

        $this->add_responsive_control(
			'input_padding',
			[
				'label'      => esc_html__( 'Padding', 'wp-hotel-booking' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .hotel-booking-search .hb-form-table .hb-form-field .hb-form-field-input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'border_radius_input',
			[
				'label'      => esc_html__( 'Border Radius', 'wp-hotel-booking' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .hotel-booking-search .hb-form-field-input input, {{WRAPPER}} .hotel-booking-search .hb-form-field-input select, {{WRAPPER}} .hotel-booking-search .multidate-layout .hb-form-field' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->end_controls_section();
    }

	protected function register_section_style_field_list(){
		$this->start_controls_section(
			'style_field_list',
			array(
				'label' => esc_html__( 'Field List', 'wp-hotel-booking' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition'     => [
					'layout' => 'multidate',
				],
			)
		);

		$this->add_responsive_control(
			'field_list_padding',
			[
				'label'      => esc_html__( 'Padding', 'wp-hotel-booking' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .hotel-booking-search-el .hb-form-table .hb-form-field .hb-form-field-list' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'field_list_margin',
			[
				'label'      => esc_html__( 'Margin', 'wp-hotel-booking' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .hotel-booking-search-el .hb-form-table .hb-form-field .hb-form-field-list, .show-calendar.daterangepicker.dropdown-menu' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'border_radius_field_list',
			[
				'label'      => esc_html__( 'Border Radius', 'wp-hotel-booking' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .hotel-booking-search-el .hb-form-table .hb-form-field .hb-form-field-list, .show-calendar.daterangepicker.dropdown-menu' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'number_box', [
				'label'     => esc_html__( 'Number Box', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'number_box_typography',
				'selector' => '{{WRAPPER}} .hotel-booking-search-el .hb-form-table .hb-form-field.hb-form-number .hb-form-field-list .name',
			)
		);

		$this->add_control(
			'number_box_color',
			[
				'label'     => esc_html__( 'Color', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .hotel-booking-search-el .hb-form-table .hb-form-field.hb-form-number .hb-form-field-list .name' => 'color: {{VALUE}};'
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'number_box_border',
				'label'    => esc_html__( 'Border', 'wp-hotel-booking' ),
				'selector' => '{{WRAPPER}} .hotel-booking-search-el .hb-form-table .number-box',
			)
		);

		$this->add_responsive_control(
			'number_box_padding',
			[
				'label'      => esc_html__( 'Padding', 'wp-hotel-booking' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .hotel-booking-search-el .hb-form-table .number-box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_section_style_button(){
		$this->start_controls_section(
			'style_button',
			array(
				'label' => esc_html__( 'Button', 'wp-hotel-booking' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'Button_align',
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
					'{{WRAPPER}} .hotel-booking-search .hb-submit' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'submit_button_typography',
				'label'    => esc_html__( 'Typography', 'wp-hotel-booking' ),
				'selector' => '{{WRAPPER}} .hotel-booking-search .hb-submit button',
			]
		);

		$this->add_responsive_control(
			'button_w', [
				'label'      => esc_html__( 'Width', 'wp-hotel-booking' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'%'  => array(
						'min' => 0,
						'max' => 1000,
					),
				],
				'selectors'  => [
					'{{WRAPPER}} .hotel-booking-search .hb-submit button' => 'width: {{SIZE}}{{UNIT}};'
				]
			]
		);

		$this->add_responsive_control(
			'button_h', [
				'label'      => esc_html__( 'Height', 'wp-hotel-booking' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'%'  => array(
						'min' => 0,
						'max' => 1000,
					),
				],
				'selectors'  => [
					'{{WRAPPER}} .hotel-booking-search .hb-submit button' => 'height: {{SIZE}}{{UNIT}};'
				]
			]
		);

		$this->start_controls_tabs( 'tabs_button_style' );
		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => esc_html__( 'Normal', 'wp-hotel-booking' ),
			]
		);

		$this->add_control(
			'button_color',
			[
				'label'     => esc_html__( 'Color', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .hotel-booking-search .hb-submit button'          => 'color: {{VALUE}};',
					'{{WRAPPER}} .hotel-booking-search .hb-submit button svg path' => 'fill: {{VALUE}};'
				],
			]
		);

		$this->add_control(
			'button_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .hotel-booking-search .hb-submit button' => 'background: {{VALUE}};'
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button__hover',
			[
				'label' => esc_html__( 'Hover', 'wp-hotel-booking' ),
			]
		);

		$this->add_control(
			'button_color_hover',
			[
				'label'     => esc_html__( 'Color', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .hotel-booking-search .hb-submit button:hover'    => 'color: {{VALUE}};',
					'{{WRAPPER}} .hotel-booking-search .hb-submit button:hover svg path' => 'fill: {{VALUE}};'
				],
			]
		);

		$this->add_control(
			'button_bg_color_hover',
			[
				'label'     => esc_html__( 'Background Color', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .hotel-booking-search .hb-submit button:hover' => 'background: {{VALUE}};'
				],
			]
		);


		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_responsive_control(
			'button_margin',
			[
				'label'      => esc_html__( 'Margin', 'wp-hotel-booking' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .hotel-booking-search .hb-submit button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; display: block',
				],
			]
		);

		$this->add_responsive_control(
			'border_radius_button',
			[
				'label'      => esc_html__( 'Border Radius', 'wp-hotel-booking' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .hotel-booking-search .hb-submit button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

    protected function render() {
        $settings    = $this->get_settings_for_display();

        if ( isset($settings['layout']) && $settings['layout'] == 'multidate' ) {
            ?>
            <div class="hotel-booking-search hotel-booking-search-el">
                <?php $this->wphb_multidate( $settings ); ?>
            </div>
           <?php
        }else {
            hb_get_template( 'search/search-form.php', $settings );
        }
    }

    protected function wphb_multidate( $settings ){
        $datetime = new \DateTime('NOW');
        $tomorrow = new \DateTime('tomorrow');
        $format = get_option('date_format');

        $check_in_date = $datetime->format($format);
        $check_out_date = $tomorrow->format($format);
        $adults         = hb_get_request('adults', '1');
        $max_child      = hb_get_request('max_child', '0');

        $search         = hb_get_page_permalink( 'search' );
        $page_search    = hb_get_page_id('search');
        $uniqid         = uniqid();

        $label_adults   = esc_html__('Adults', 'wp-hotel-booking');
        $label_child    = esc_html__('Children', 'wp-hotel-booking');
        $text_submit    = $settings['text_submit'] ?? esc_html__('Check Availability', 'wp-hotel-booking');
        ?>
        <form <?php echo is_page($page_search) ? 'id="hb-form-search-page" ' : ''; ?> name="hb-search-form" action="<?php echo hb_get_url(); ?>" class="multidate-layout hb-search-form-<?php echo esc_attr($uniqid); ?>">
            <ul class="hb-form-table">
                <?php 
				wp_enqueue_script( 'wp-hotel-booking-moment' );
                wp_enqueue_script( 'wphb-daterangepicker');
                ?>
                <input type="text" id="multidate" class="multidate" value="<?php echo esc_attr($check_in_date) ?>" readonly />
                <li class="hb-form-field hb-form-check-in-check-out">
					<?php if ( $settings['icon_date'] ) { 
                        Icons_Manager::render_icon( $settings['icon_date'], array( 'aria-hidden' => 'true', 'class' => 'icon-custom' ) );        
                    } ?>
                    <div class="label"><?php echo esc_html__('Check-in, Check-out', 'wp-hotel-booking') ?></div>
                    <div class="hb-form-field-input hb_input_field">
                        <input type="text" name="check_in_date" id="check_in_date_<?php echo  esc_attr($uniqid) ?>" class="check-date" value="<?php echo esc_attr($check_in_date) ?>" readonly />
                    </div>
                    <div class="hb-form-field-input hb_input_field">
                        <input type="text" name="check_out_date" id="check_out_date_<?php echo esc_attr($uniqid) ?>" class="check-date" value="<?php echo esc_attr($check_out_date) ?>" readonly />
                    </div>
                </li>
                <li class="hb-form-field hb-form-number">
					<?php if ( $settings['icon_adults'] ) { 
                        Icons_Manager::render_icon( $settings['icon_adults'], array( 'aria-hidden' => 'true', 'class' => 'icon-custom' ) );        
                    } ?>
                    <div class="label"><?php echo $label_adults; ?></div>
                    <div id="adults" class="hb-form-field-input hb_input_field">
                        <input type="text" id="number" class="adults-input" value="<?php echo esc_attr($adults) ?>" readonly />
                        <span><?php echo $label_adults; ?></span>
                    </div>
                    <div class="hb-form-field-list nav-adults">
                        <span class="name"><?php echo $label_adults; ?></span>
                        <div class="number-box">
                            <span class="number-icons goDown"><i class="fa fa-minus"></i></span>
                            <span class="hb-form-field-input hb-adults-field adults-number">
                                <?php
                                hb_dropdown_numbers(
                                    array(
                                        'name'              => 'adults_capacity',
                                        'min'               => 1,
                                        'max'               => hb_get_max_capacity_of_rooms(),
                                        'selected'          => $adults,
                                        'option_none_value' => '',
                                        'options'           => hb_get_capacity_of_rooms(),
                                    )
                                );
                                ?>
                            </span>
                            <span class="number-icons goUp"><i class="fa fa-plus"></i></span>
                        </div>
                    </div>
                </li>

                <li class="hb-form-field hb-form-number">
					<?php if ( $settings['icon_children'] ) { 
                        Icons_Manager::render_icon( $settings['icon_children'], array( 'aria-hidden' => 'true', 'class' => 'icon-custom' ) );        
                    } ?>
                    <div class="label"><?php echo $label_child; ?></div>
                    <div id="child" class="hb-form-field-input hb_input_field">
                        <input type="text" id="number" class="child-input" value="<?php echo esc_attr($max_child) ?>" readonly />
                        <span><?php echo $label_child; ?></span>
                    </div>
                    <div class="hb-form-field-list nav-children">
                        <span class="name"><?php echo $label_child; ?></span>
                        <div class="number-box">
                            <span class="number-icons goDown"><i class="fa fa-minus"></i></span>
                            <span class="hb-form-field-input hb-children-field children-number">
                                <?php
                                hb_dropdown_numbers(
                                    array(
                                        'name'              => 'max_child',
                                        'min'               => 0,
                                        'max'               => hb_get_max_child_of_rooms(),
                                        'option_none_value' => '',
                                        'selected'          => $max_child,
                                    )
                                );
                                ?>
                            </span>
                            <span class="number-icons goUp"><i class="fa fa-plus"></i></span>
                        </div>
                    </div>
                </li>
            </ul>
            <?php wp_nonce_field('hb_search_nonce_action', 'nonce'); ?>
            <input type="hidden" name="hotel-booking" value="results" />
            <input type="hidden" name="action" value="hotel_booking_parse_search_params" />
            <p class="hb-submit">
                <button type="submit" class="wphb-button">
                    <?php if ( $settings['icon_submit'] ) { 
                        Icons_Manager::render_icon( $settings['icon_submit'], array( 'aria-hidden' => 'true' ) );        
                    } ?>
                    <?php if ( $settings['text_submit'] != '' ) :?>
                        <?php echo $text_submit; ?>
                    <?php endif; ?>
                </button>
            </p>
        </form>
        <?php
    }
}