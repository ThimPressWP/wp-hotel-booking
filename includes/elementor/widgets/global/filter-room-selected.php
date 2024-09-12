<?php

namespace Elementor;

use Thim_EL_Kit\GroupControlTrait;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Thim_Ekit_Widget_Filter_Room_Selected extends Widget_Base {
    use GroupControlTrait;

    public function get_name() {
		return 'wphb-filter-room-selected';
	}

	public function get_title() {
		return esc_html__( 'Filter Room Selected', 'wp-hotel-booking' );
	}

	public function get_icon() {
		return 'thim-eicon eicon-form-vertical';
	}

	public function get_categories() {
		return [ \Thim_EL_Kit\Elementor::CATEGORY ];
	}

    public function get_keywords() {
		return [ 'filter', 'room', 'selected' ];
	}

    public function get_base() {
		return basename( __FILE__, '.php' );
	}

	public function get_script_depends() {
		return [ 'wphb-filter-el' ];
	}

    protected function register_controls() {
        $this->register_section_selected_options();
		$this->register_section_style_item_selected();
		$this->register_section_style_icon_selected();
		$this->register_section_clear_button();
    }

	protected function register_section_selected_options( $condition = null ) {
		$section_args = [
			'label' => __( 'Selected Area', 'wp-hotel-booking' ),
		];

		if ( is_array( $condition ) ) {
			$section_args['condition'] = $condition;
		}

		$this->start_controls_section(
			'section_selected', $section_args
		);

        $this->add_control(
			'title_selected',
			[
				'label'       => esc_html__( 'Title', 'wp-hotel-booking' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Add your {{text here}}', 'wp-hotel-booking' ),
				'default'     => esc_html__( 'Selected', 'wp-hotel-booking' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'icon_remove',
			array(
				'label'       => esc_html__( 'Icon', 'wp-hotel-booking' ),
 				'type'        => Controls_Manager::ICONS,
				'skin'        => 'inline',
				'label_block' => false,
			)
		);

        $this->add_control(
			'text_reset',
			[
				'label'       => esc_html__( 'Clear Text', 'wp-hotel-booking' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Add your {{text here}}', 'wp-hotel-booking' ),
				'default'     => esc_html__( 'Clear', 'wp-hotel-booking' ),
				'label_block' => true,
			]
		);

        $this->end_controls_section();
	}
	protected function register_section_style_item_selected( $condition = null ){

		$section_args = [
			'label' => esc_html__( 'Selected Item', 'wp-hotel-booking' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		];

		if ( is_array( $condition ) ) {
			$section_args['condition'] = $condition;
		}

		$this->start_controls_section(
			'selected_style_item', $section_args
		);

		$this->add_responsive_control(
			'selected_space_between',
			array(
				'label'      => esc_html__( 'Space Between', 'wp-hotel-booking' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => array(
					'unit' => 'px',
				),
				'size_units' => array( '%', 'px' ),
				'range'      => array(
					'%'  => array(
						'min' => 1,
						'max' => 100,
					),
					'px' => array(
						'min' => 1,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .selected-list' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->register_button_style( 'item_selected', '.hb-filter-room-selected .selected-item' );

		$this->add_responsive_control(
			'selected_item_selected_margin',
			[
				'label'      => esc_html__( 'Margin', 'wp-hotel-booking' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .hb-filter-room-selected .selected-item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_section_style_icon_selected( $condition = null ){

		$section_args = [
			'label' => esc_html__( 'Selected Icon', 'wp-hotel-booking' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		];

		if ( is_array( $condition ) ) {
			$section_args['condition'] = $condition;
		}

		$this->start_controls_section(
			'selected_style_icon', $section_args
		);

		$this->add_responsive_control(
			'selected_icon_size',
			[
				'label'     => esc_html__( 'Font Size', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .icon-remove-selected' => 'font-size: {{SIZE}}{{UNIT}};'
				],
			]
		);

		$this->add_control(
			'selected_icon_color',
			[
				'label'     => esc_html__( 'Color', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .icon-remove-selected' => 'color: {{VALUE}};'
				],
			]
		);

		$this->add_control(
			'selected_icon_color_hover',
			[
				'label'     => esc_html__( 'Color Hover', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .selected-item:hover .icon-remove-selected' => 'color: {{VALUE}};'
				],
			]
		);

		$this->add_responsive_control(
			'selected_icon_margin',
			[
				'label'      => esc_html__( 'Margin', 'wp-hotel-booking' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .icon-remove-selected' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_section_clear_button( $condition = null ){

		$section_args = [
			'label' => esc_html__( 'Selected Clear Button', 'wp-hotel-booking' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		];

		if ( is_array( $condition ) ) {
			$section_args['condition'] = $condition;
		}

		$this->start_controls_section(
			'selected_style_clear_button', $section_args
		);

		$this->register_button_style( 'clear_selected', '.hb-filter-room-selected .clear-selected-list' );

		$this->add_responsive_control(
			'clear_selected_margin',
			[
				'label'      => esc_html__( 'Margin', 'wp-hotel-booking' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .hb-filter-room-selected .clear-selected-list' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}
	public function render(){
		$settings    = $this->get_settings_for_display();
		$this->render_selected($settings);
	}

    protected static function render_selected($settings) {

        $text_reset         = $settings['text_reset'] ?? esc_html__('Clear', 'wp-hotel-booking');
        $title_selected     = $settings['title_selected'] ?? esc_html__('Selected', 'wp-hotel-booking');

		if (!empty($_GET['room_type']) || !empty($_GET['rating']) || !empty($_GET['min_price']) || !empty($_GET['max_price']) || Plugin::$instance->editor->is_edit_mode()) {
			echo '<div class="hb-filter-room-selected">';
				echo '<h4 class="title">'. $title_selected .'</h4>';
				echo '<div class="selected-list">';
					self::selected_style_list($settings);
				echo '</div>';
				echo '<button class="clear-selected-list">'. $text_reset .'</button>';
			echo '</div>';
		}
    }

	protected static function selected_style_list($settings){
		$types = $ratings = $html_icon = '';
		$classListItem = 'selected-item';
		$currency            = get_option( 'tp_hotel_booking_currency', 'USD' );
		$currency_symbol     = hb_get_currency_symbol( $currency );

		if ( $settings['icon_remove'] ){
			ob_start();
			Icons_Manager::render_icon( $settings['icon_remove'],
				array(
					'aria-hidden' => 'true',
					'class'       => 'icon-remove-selected',
				)
			);
			$html_icon = ob_get_contents();
			ob_end_clean();
		}

		if ( $html_icon != '' ){
			$icon_move = $html_icon;
		}else {
			$icon_move = '<i class="icon-remove-selected fas fa-times"></i>';
		}

		if ( Plugin::$instance->editor->is_edit_mode() ) {
			echo sprintf(
				'<span class="preview selected-item" >%s %s</span>
				<span class="preview selected-item" >%s %s</span>',
				esc_html__( 'Preview 1', 'wp-hotel-booking' ),
				wp_kses_post( $icon_move ),
				esc_html__( 'Preview 2', 'wp-hotel-booking' ),
				wp_kses_post( $icon_move )
			);
		}

		if ( ! empty( $_GET['room_type'] ) ) {
			$types = explode( ',', $_GET['room_type'] );
			foreach ( $types as $type ) {
				sprintf(
					'<span class="%s" data-name="room_type" data-value="%s">%s%s</span>',
					esc_attr( $classListItem ),
					esc_attr( $type ),
					get_term( $type, 'hb_room_type' )->name,
					wp_kses_post( $icon_move )
				);
			}
		}

		if ( ! empty( $_GET['rating'] ) ) {
			$ratings = explode( ',', $_GET['rating'] );
			foreach ( $ratings as $rating ) {
				if ( $rating == 'unrated' ) {
					sprintf(
						'<span class="%s" data-name="rating" data-value="%s">%s%s</span>',
						esc_attr( $classListItem ),
						esc_attr( $rating ),
						esc_html__( 'Unrated', 'wp-hotel-booking' ),
						wp_kses_post( $icon_move )
					);
				} else {
					sprintf(
						'<span class="%s" data-name="rating" data-value="%s">%s%s</span>',
						esc_attr( $classListItem ),
						esc_attr( $rating ),
						sprintf( esc_html( _n( '%s star', '%s stars', $rating, 'wp-hotel-booking' ) ), $rating ),
						wp_kses_post( $icon_move )
					);
				}
			}
		}

		if ( ! empty( $_GET['min_price'] ) || ! empty( $_GET['max_price'] ) ) {
			echo sprintf(
				'<span class="%s" data-name="price" data-value="price">%s: %s - %s%s</span>',
				esc_attr( $classListItem ),
				esc_html__( 'Price', 'wp-hotel-booking' ),
				esc_attr( hb_format_price( $_GET['min_price'], $currency_symbol ) ),
				esc_attr( hb_format_price( $_GET['max_price'], $currency_symbol ) ),
				wp_kses_post( $icon_move )
			);
		}
	}

}
