<?php

namespace Elementor;

use Thim_EL_Kit\GroupControlTrait;
use Elementor\Widget_Base;
use Elementor\Repeater;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Typography;
// Exit if accessed directly

class Thim_Ekit_Widget_Filter_Room extends Widget_Base {
    use GroupControlTrait;

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
		return array( \WPHB\Elementor::CATEGORY );
	}

    public function get_keywords() {
		return array( 'filter', 'room' );
	}

    public function get_base() {
		return basename( __FILE__, '.php' );
	}

    protected function register_controls() {
        $this->start_controls_section(
			'section_filter',
			array(
				'label' => __( 'Filter Area', 'wp-hotel-booking' ),
			)
		);

        $repeater_data = new Repeater();
        $repeater_data->add_control(
            'meta_field',
			array(
				'label'   => esc_html__( 'Select Meta', 'wp-hotel-booking' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'types',
				'options' => array(
 					'price'     => esc_html__( 'Price', 'wp-hotel-booking' ),
					'rating'    => esc_html__( 'Rating', 'wp-hotel-booking' ),
					'types'     => esc_html__( 'Types', 'wp-hotel-booking' ),
					'clear'     => esc_html__( 'Clear All', 'wp-hotel-booking' ),
				),
			)
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
			array(
				'label'   => esc_html__( 'Show Count', 'wp-hotel-booking' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition'     => [
					'meta_field' => [ 'types', 'rating' ],
				],
			)
		);

		$repeater_data->add_control(
			'heading_setting',
			array(
				'label'        => esc_html__( 'Heading Setting', 'wp-hotel-booking' ),
				'type'         => Controls_Manager::POPOVER_TOGGLE,
				'label_off'    => esc_html__( 'Default', 'wp-hotel-booking' ),
				'label_on'     => esc_html__( 'Custom', 'wp-hotel-booking' ),
				'return_value' => 'yes',
				'condition'     => [
					'meta_field!' => 'clear',
				],
			)
		);

		$repeater_data->start_popover();

		$repeater_data->add_control(
			'enable_heading',
			array(
				'label'        => esc_html__( 'Enable Heading', 'wp-hotel-booking' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => esc_html__( 'Show', 'wp-hotel-booking' ),
				'label_off'    => esc_html__( 'Hide', 'wp-hotel-booking' ),
				'return_value' => 'yes',
			)
		);

		$repeater_data->add_control(
			'toggle_content',
			array(
				'label'        => esc_html__( 'Toggle Content', 'wp-hotel-booking' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => esc_html__( 'Show', 'wp-hotel-booking' ),
				'label_off'    => esc_html__( 'Hide', 'wp-hotel-booking' ),
				'return_value' => 'yes',
				'condition'    => [
					'enable_heading' => 'yes',
				],
			)
		);

		$repeater_data->add_control(
			'default_toggle_on',
			array(
				'label'        => esc_html__( 'Default Toggle On', 'wp-hotel-booking' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => esc_html__( 'Show', 'wp-hotel-booking' ),
				'label_off'    => esc_html__( 'Hide', 'wp-hotel-booking' ),
				'return_value' => 'yes',
				'condition'    => [
					'enable_heading' => 'yes',
					'toggle_content' => 'yes',
				],
			)
		);

		$repeater_data->end_popover();

        $this->add_control(
			'data',
            array(
				'label'       => esc_html__( 'Filter', 'wp-hotel-booking' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater_data->get_controls(),
				'default'     => array(
 					array(
						'meta_field' => 'types',
					),
					array(
						'meta_field' => 'rating',
					),
				),
				'title_field' => '<span style="text-transform: capitalize;">{{{ meta_field.replace("_", " ") }}}</span>',
			)
        );

        $this->end_controls_section();
		$this->register_section_style_field();
		$this->register_section_style_title();
		$this->register_section_style_item();
		$this->register_section_style_clear();
		$this->register_section_style_price();
    }

	protected function register_section_style_field(){
		$this->start_controls_section(
			'style_field',
			array(
				'label' => esc_html__( 'Field', 'wp-hotel-booking' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'field_border',
				'label'    => esc_html__( 'Border', 'wp-hotel-booking' ),
				'selector' => '{{WRAPPER}} .search-filter-form-el .field-item',
			)
		);

		$this->add_responsive_control(
			'field_padding',
			[
				'label'      => esc_html__( 'Padding', 'wp-hotel-booking' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .search-filter-form-el .field-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .search-filter-form-el .field-item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .search-filter-form-el .field-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
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
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => esc_html__( 'Typography', 'wp-hotel-booking' ),
				'selector' => '{{WRAPPER}} .hotel-booking-search-filter h4, {{WRAPPER}} .hotel-booking-search-filter .title',
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__( 'Color', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .hotel-booking-search-filter h4, {{WRAPPER}} .hotel-booking-search-filter .title' => 'color: {{VALUE}};'
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
					'{{WRAPPER}} .hotel-booking-search-filter h4, {{WRAPPER}} .hotel-booking-search-filter .title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_toggle_offset_h',
			array(
				'label'       => esc_html__( 'Offset X (px)', 'wp-hotel-booking' ),
				'type'        => Controls_Manager::NUMBER,
				'label_block' => false,
				'selectors'   => array(
					'{{WRAPPER}} .field-item .icon-toggle-filter' => 'right:{{VALUE}}px',
				),
			)
		);

		$this->add_responsive_control(
			'icon_toggle_offset_v',
			array(
				'label'       => esc_html__( 'Offset Y (px)', 'wp-hotel-booking' ),
				'type'        => Controls_Manager::NUMBER,
				'label_block' => false,
				'selectors'   => array(
					'{{WRAPPER}} .field-item .icon-toggle-filter' => 'top:{{VALUE}}px',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_section_style_item(){
		$this->start_controls_section(
			'style_item',
			array(
				'label' => esc_html__( 'Item', 'wp-hotel-booking' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'item_typography',
				'label'    => esc_html__( 'Typography', 'wp-hotel-booking' ),
				'selector' => '{{WRAPPER}} .hotel-booking-search-filter .list-item *',
			]
		);

		$this->add_control(
			'item_color',
			[
				'label'     => esc_html__( 'Color', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .hotel-booking-search-filter .list-item *' => 'color: {{VALUE}};'
				],
			]
		);

		$this->add_control(
			'item_color_hover',
			[
				'label'     => esc_html__( 'Color Hover', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .hotel-booking-search-filter .list-item:hover *' => 'color: {{VALUE}};'
				],
			]
		);

		$this->add_responsive_control(
			'item_margin',
			[
				'label'      => esc_html__( 'Margin', 'wp-hotel-booking' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .hotel-booking-search-filter .list-item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_section_style_clear(){
		$this->start_controls_section(
			'style_clear',
			array(
				'label' => esc_html__( 'Clear', 'wp-hotel-booking' ),
				'tab'   => Controls_Manager::TAB_STYLE,
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
					'{{WRAPPER}} .search-filter-form-el .clear-filter' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_section_style_price(){
		$this->start_controls_section(
			'style_field_price',
			array(
				'label' => esc_html__( 'Price', 'wp-hotel-booking' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'range_price',
			array(
				'label'     => esc_html__( 'Range Price', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::HEADING,
			)
		);

		$this->add_responsive_control(
			'range_price_height',
			array(
				'label'     => esc_html__( 'Height Range', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => 'px',
					'size' => 6,
				],
				'selectors' => array(
					'{{WRAPPER}} .search-filter-form-el .noUi-horizontal' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'handle_price_height',
			array(
				'label'     => esc_html__( 'Font Size Handle', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => 'px',
					'size' => 12,
				],
				'selectors' => array(
					'{{WRAPPER}} .search-filter-form-el .noUi-horizontal .noUi-handle' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'handle_price_offset_h',
			array(
				'label'       => esc_html__( 'Offset X (px)', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => 'px',
					'size' => -10,
				],
				'selectors'   => array(
					'{{WRAPPER}} .search-filter-form-el .noUi-horizontal .noUi-handle' => 'right:{{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'handle_price_offset_v',
			array(
				'label'       => esc_html__( 'Offset Y (px)', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => 'px',
					'size' => -4,
				],
				'selectors'   => array(
					'{{WRAPPER}} .search-filter-form-el .noUi-horizontal .noUi-handle' => 'top:{{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'button_apply',
			array(
				'label'     => esc_html__( 'Button Apply', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->register_button_style( 'btn_apply', '.search-filter-form-el button.apply' );

		$this->add_responsive_control(
			'button_apply_margin',
			[
				'label'      => esc_html__( 'Margin', 'wp-hotel-booking' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .search-filter-form-el button.apply' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

    protected function render() {
        $settings    = $this->get_settings_for_display();

        if ( $settings['data'] ) { ?>
            <div id="hotel-booking-search-filter" class="hotel-booking-search-filter">
            <form class="search-filter-form search-filter-form-el" action="">
                <?php 
                foreach ( $settings['data'] as $data ) {
					$classes = $icon_toggle = '';

					if ( isset($data['show_count']) && $data['show_count'] != 'yes') {
						$classes .= ' count-hide';
					}

					if ( isset($data['enable_heading']) && $data['enable_heading'] != 'yes') {
						$classes .= ' heading-hide';
					}

					if ( isset($data['toggle_content']) && $data['toggle_content'] == 'yes') {
						$classes .= ' toggle-content';
						$icon_toggle = '<i class="icon-toggle-filter fas fa-angle-up"></i><i class="icon-toggle-filter fas fa-angle-down"></i>';

						if ( isset($data['default_toggle_on']) && $data['default_toggle_on'] == 'yes') {
							$classes .= ' toggle-on';
						}
					}

					switch ( $data['meta_field'] ) {
						case 'clear':
							?>
							<div class="clear-filter">
								<button type="button">
									<?php esc_html_e( 'Clear all fields', 'wp-hotel-booking' ); ?>
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
            </div>
            <?php
        }
    }
}