<?php

namespace Elementor;

use Thim_EL_Kit\GroupControlTrait;
use Thim_EL_Kit\Custom_Post_Type;
use WPHB\HBGroupControlTrait;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Thim_Ekit_Widget_Loop_Room_Price extends Widget_Base
{
    use GroupControlTrait;
    use HBGroupControlTrait;

    public function get_name()
    {
        return 'loop-room-price';
    }

    public function show_in_panel()
    {
        $post_type = get_post_meta( get_the_ID(), 'thim_loop_item_post_type', true );
		$type      = get_post_meta( get_the_ID(), Custom_Post_Type::TYPE, true );

		if ( ! empty( $post_type ) && $post_type == 'hb_room' && $type == 'loop_item' || $type == 'single-room' ) {
			return true;
		}

        return false;
    }

    public function get_title()
    {
        return esc_html__('Room Price', 'wp-hotel-booking');
    }

    public function get_icon()
    {
        return 'thim-eicon eicon-price-list';
    }

    public function get_categories()
    {
        return array(\Thim_EL_Kit\Elementor::CATEGORY_RECOMMENDED);
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
            'layout',
            array(
                'label'   => esc_html__('Select Price', 'wp-hotel-booking'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'regular',
                'options' => array(
                    'regular'           => esc_html__('Regular Price', 'wp-hotel-booking'),
                    'pricing_plans'     => esc_html__('Pricing Plans', 'wp-hotel-booking'),
                ),
            )
        );

        $this->end_controls_section();
        $this->_register_section_style_price_regular();
        $this->_register_section_style_pricing_plans_title();
        $this->_register_section_style_pricing_plans_table();
    }

    protected function _register_section_style_price_regular()
    {
        $this->start_controls_section(
            'style_price',
            [
                'label' => esc_html__('Price', 'wp-hotel-booking'),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'layout' => 'regular',
                ]
            ]
        );

        $this->register_style_typo_color_margin('price_room', '.price .price_value');

        $this->add_control(
			'before_price', [
				'label'     => esc_html__( 'Before Price', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
			]
		);

        $this->register_style_typo_color_margin('before_value_price', '.price .title-price');

        $this->add_control(
			'after_price', [
				'label'     => esc_html__( 'After Price', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
			]
		);

        $this->register_style_typo_color_margin('after_value_price', '.price .unit');

        $this->end_controls_section();
    }

    protected function _register_section_style_pricing_plans_title()
    {
        $this->start_controls_section(
            'style_title',
            [
                'label' => esc_html__('Title', 'wp-hotel-booking'),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'layout' => 'pricing_plans',
                ]
            ]
        );

        $this->register_style_typo_color_margin('title_pricing_plans', '.hb_room_pricing_plan_data');

        $this->add_control(
			'title_pricing_plans_bg',
			[
				'label'     => esc_html__( 'Background', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .hb_room_pricing_plan_data' => 'background-color: {{VALUE}};'
				],
			]
		);

        $this->add_responsive_control(
			'title_pricing_plans_padding',
			[
				'label'      => esc_html__( 'Padding', 'wp-hotel-booking' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .hb_room_pricing_plan_data' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->end_controls_section();
    }

    protected function _register_section_style_pricing_plans_table()
    {
        $this->start_controls_section(
            'style_table',
            [
                'label' => esc_html__('Table', 'wp-hotel-booking'),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'layout' => 'pricing_plans',
                ]
            ]
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
					'{{WRAPPER}} .hb_room_pricing_plans th, {{WRAPPER}} .hb_room_pricing_plans td' => 'text-align: {{VALUE}};',
				),
			)
		);

        $this->register_style_typo_color_margin('table_pricing_plans', '.hb_room_pricing_plans');


        $this->add_responsive_control(
			'table_pricing_plans_item_padding',
			[
				'label'      => esc_html__( 'Padding', 'wp-hotel-booking' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .hb_room_pricing_plans th, {{WRAPPER}} .hb_room_pricing_plans td' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_responsive_control(
			'table_pricing_plans_item_border',
			[
				'label'      => esc_html__( 'Border Width Item', 'wp-hotel-booking' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .hb_room_pricing_plans th, {{WRAPPER}} .hb_room_pricing_plans td' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->end_controls_section();
    }

    protected function render()
    {

        do_action('WPHB/modules/single-room/before-preview-query');

        $settings        = $this->get_settings_for_display(); ?>

        <div class="hb-room-single__price">
            <?php if ( $settings['layout'] == 'regular' ){
                echo hb_get_template('loop/price.php');
            }else {
                echo hb_get_template('loop/pricing_plan.php');
            } ?>
        </div>

        <?php do_action('WPHB/modules/single-room/after-preview-query');
    }
}
