<?php

namespace Elementor;

use Thim_EL_Kit\GroupControlTrait;
use WPHB\HBGroupControlTrait;
use Thim_EL_Kit\Utilities\Widget_Loop_Trait;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Thim_Ekit_Widget_Loop_Room_Price extends Widget_Base
{
    use GroupControlTrait;
    use HBGroupControlTrait;
    use Widget_Loop_Trait;

    public function get_name()
    {
        return 'loop-room-price';
    }

    public function get_title()
    {
        return esc_html__('Room Price', 'wp-hotel-booking');
    }

    public function get_icon()
    {
        return 'thim-eicon eicon-price-list';
    }

    public function get_keywords() {
		return array( 'room', 'price' );
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
                    'price_breakdown'   => esc_html__('Price Breakdown', 'wp-hotel-booking'),
                ),
            )
        );

        $this->add_control(
			'text_before',
			array(
				'label'       => esc_html__( 'Custom Text Before', 'wp-hotel-booking' ),
				'type'        => Controls_Manager::TEXT,
                'default'     => esc_html__( 'Price from', 'wp-hotel-booking' ),
				'label_block' => true,
                'condition' => array(
					'layout' => 'regular',
				),
			)
        );

        $this->add_control(
			'text_unit',
			array(
				'label'       => esc_html__( 'Custom Unit', 'wp-hotel-booking' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
                'default'     => esc_html__( 'Night', 'wp-hotel-booking' ),
                'condition' => array(
					'layout' => 'regular',
				),
			)
        );

        $this->add_control(
			'text_price_breakdown',
			array(
				'label'       => esc_html__( 'Custom Text', 'wp-hotel-booking' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
                'default'     => esc_html__( 'View price breakdown', 'wp-hotel-booking' ),
                'condition' => array(
					'layout' => 'price_breakdown',
				),
			)
        );

        $this->end_controls_section();
        $this->_register_section_style_price_regular();
        $this->_register_section_style_pricing_plans_title();
        $this->_register_section_style_pricing_plans_table();
        $this->_register_section_style_pricing_breakdown();
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

    protected function _register_section_style_pricing_breakdown()
    {
        $this->start_controls_section(
            'style_breakdown',
            [
                'label' => esc_html__('Breakdown', 'wp-hotel-booking'),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'layout' => 'price_breakdown',
                ]
            ]
        ); 

        $this->register_button_style( 'price_breakdown', '.hb-view-booking-room-details' );

        $this->end_controls_section();
    }

    protected function render()
    {
        global $hb_settings;
        $room = \WPHB_Room::instance(get_the_ID());

        $settings        = $this->get_settings_for_display(); ?>

        <div class="hb-room-single__price">
            <?php if ( $settings['layout'] == 'regular' ){
                $price_display = apply_filters( 'hotel_booking_loop_room_price_display_style', $hb_settings->get( 'price_display' ) );
                $prices        = hb_room_get_selected_plan( get_the_ID() );
                $prices        = isset( $prices->prices ) ? $prices->prices : array();
                $text_before   = isset( $settings['text_before'] ) ? $settings['text_before'] : '';
                $text_after    = isset( $settings['text_unit'] ) ? $settings['text_unit'] : '';

                if ( $prices ) {
                    $min_price = is_numeric( min( $prices ) ) ? min( $prices ) : 0;
                    $max_price = is_numeric( max( $prices ) ) ? max( $prices ) : 0;
                    $min       = $min_price + ( hb_price_including_tax() ? ( $min_price * hb_get_tax_settings() ) : 0 );
                    $max       = $max_price + ( hb_price_including_tax() ? ( $max_price * hb_get_tax_settings() ) : 0 );
                    ?>
                
                    <div class="price">
                        <span class="title-price"><?php echo $text_before; ?></span>
                
                        <?php if ( $price_display === 'max' ) { ?>
                            <span class="price_value price_max"><?php echo hb_format_price( $max ); ?></span>
                
                        <?php } elseif ( $price_display === 'min_to_max' && $min !== $max ) { ?>
                            <span class="price_value price_min_to_max">
                                <?php echo hb_format_price( $min ); ?> - <?php echo hb_format_price( $max ); ?>
                            </span>
                
                        <?php } else { ?>
                            <span class="price_value price_min"><?php echo hb_format_price( $min ); ?></span>
                        <?php } ?>
                
                        <span class="unit"><?php echo $text_after; ?></span>
                    </div>
                <?php }
            }elseif ( $settings['layout'] == 'pricing_plans' ){
                echo hb_get_template('loop/pricing_plan.php');
            }else { 
                $text_price_breakdown   = isset( $settings['text_price_breakdown'] ) ? $settings['text_price_breakdown'] : ''; ?>
                <div class="hb_view_price hb-room-content">
                    <a href="" class="hb-view-booking-room-details"><?php echo $text_price_breakdown; ?></a>
                    <?php hb_get_template( 'search/booking-room-details.php', array( 'room' => $room ) ); ?>
                </div>
            <?php
            } ?>
        </div>
        <?php
    }
}
