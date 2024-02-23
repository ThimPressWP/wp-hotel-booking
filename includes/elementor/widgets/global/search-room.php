<?php

namespace Elementor;

use Thim_EL_Kit\GroupControlTrait;
use Elementor\Widget_Base;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Typography;
use WPHB\HBGroupControlTrait;
use Elementor\Repeater;
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class Thim_Ekit_Widget_Search_Room extends Widget_Base {
    use GroupControlTrait;
	use HBGroupControlTrait;

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
		return array( \Thim_EL_Kit\Elementor::CATEGORY );
	}

    public function get_keywords() {
		return array( 'search', 'room' );
	}

	public function get_script_depends() {
		return [ 'wp-hotel-booking-moment', 'wphb-daterangepicker' ];
	}

	public function get_style_depends() {
		return [ 'wphb-multidate-style' ];
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

		$repeater_data = new Repeater();
		$repeater_data->add_control(
            'meta_field',
			[
				'label'   => esc_html__( 'Select Field', 'wp-hotel-booking' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'types',
				'options' => [
 					'date'      	=> esc_html__( 'Date', 'wp-hotel-booking' ),
					'adults'    	=> esc_html__( 'Adults', 'wp-hotel-booking' ),
					'children'     	=> esc_html__( 'Children', 'wp-hotel-booking' ),
					'submit'     	=> esc_html__( 'Submit', 'wp-hotel-booking' )
				]
			]
        );
        $repeater_data->add_control(
			'layout_date',
			array(
				'label'     => esc_html__( 'Layout', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'multidate',
				'options'   => array(
					'single'    => esc_html__( 'Single', 'wp-hotel-booking' ),
					'multidate' => esc_html__( 'Multidate', 'wp-hotel-booking' ),
				),
				'condition'     => [
					'meta_field' => 'date',
				]
			)
		);

		$repeater_data->add_control(
			'layout_guest',
			array(
				'label'     => esc_html__( 'Layout', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'number_box',
				'options'   => array(
					'select'       => esc_html__( 'Select', 'wp-hotel-booking' ),
					'number_box'   => esc_html__( 'Number Box', 'wp-hotel-booking' ),
				),
				'condition'     => [
					'meta_field' => ['adults', 'children'],
				]
			)
		);

		$repeater_data->add_control(
			'label_field_date',
			[
				'label'     => esc_html__( 'Label (Check in text)', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::TEXT,
                'placeholder'   => esc_html__( 'Add your text here', 'wp-hotel-booking' ),
				'condition'     => [
					'meta_field' => 'date',
				]
			]
		);

		$repeater_data->add_control(
			'label_field_check_out',
			[
				'label'     => esc_html__( 'Check out text', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::TEXT,
                'placeholder'   => esc_html__( 'Add your text here', 'wp-hotel-booking' ),
				'condition'     => [
					'meta_field' => 'date',
					'layout_date'=>	'single'
				]
			]
		);

		$repeater_data->add_control(
			'label_field',
			[
				'label'     	=> esc_html__( 'Label', 'wp-hotel-booking' ),
				'type'      	=> Controls_Manager::TEXT,
                'placeholder'   => esc_html__( 'Add your text here', 'wp-hotel-booking' ),
				'condition'     => [
					'meta_field!' => 'date',
				]
			]
		);

		$repeater_data->add_control(
			'icons_field',
			[
				'label'         => esc_html__( 'Icon', 'wp-hotel-booking' ),
				'type'          => Controls_Manager::ICONS,
                'skin'          => 'inline',
                'label_block'   => false,
			]
		);

		$repeater_data->add_responsive_control(
			'width_item',
			[
				'label'     => esc_html__( 'Width Content', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::SLIDER,
				'size_units' => [ '%', 'px' ],
				'default'   => [
					'size' => 25,
                    'unit'  => '%'
                ],
				'selectors' => [
					'{{WRAPPER}} .hotel-booking-search-el {{CURRENT_ITEM}}' => 'width: {{SIZE}}{{UNIT}};']
			]
		);

		$this->add_control(
			'data',
            [
				'label'       => esc_html__( 'Search', 'wp-hotel-booking' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater_data->get_controls(),
				'default'     => [
 					[
						'meta_field' => 'date',
					],
					[
						'meta_field' => 'adults',
					],
					[
						'meta_field' => 'children',
					],
					[
						'meta_field' => 'submit',
					],
				],
				'title_field' => '<span style="text-transform: capitalize;">{{{ meta_field.replace("_", " ") }}}</span>',
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
				'default'     => 'row',
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
					'{{WRAPPER}} form > .hb-form-table' => 'flex-direction: {{VALUE}};display: flex;align-items: center;',
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

		$this->add_control(
			'bg_search_form',
			[
				'label'     => esc_html__( 'Background', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fff',
				'selectors' => [
					'{{WRAPPER}} .hotel-booking-search-el form' => 'background-color: {{VALUE}};'
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
				'condition'     => [
					'layout' => 'single',
				],
			)
		);

		$this->register_style_typo_color_margin('title_search', '.hotel-booking-search-el h3');

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

		$this->register_button_style( 'field_search', '.hotel-booking-search-el .hb-form-field' );

		$this->add_responsive_control(
			'field_margin',
			[
				'label'      => esc_html__( 'Margin', 'wp-hotel-booking' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default'    => array(
					'unit'     => 'px',
					'top'      => 0,
					'right'    => 0,
					'bottom'   => 0,
					'left'     => 0,
				),
				'selectors'  => [
					'{{WRAPPER}} .hotel-booking-search-el .hb-form-field' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_control(
			'label_heading', [
				'label'     => esc_html__( 'Label', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::HEADING,
			]
		);

		$this->register_style_typo_color_margin('label_style', '.hotel-booking-search-el .hb-form-table .hb-form-field .label');

        $this->add_control(
			'input_heading', [
				'label'     => esc_html__( 'Input', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->register_button_style( 'input_search', '.hotel-booking-search-el .hb-form-field input, .hotel-booking-search-el .hb-form-field select' );

        $this->end_controls_section();
    }

	protected function register_section_style_field_list(){
		$this->start_controls_section(
			'style_field_list',
			array(
				'label' => esc_html__( 'Field List', 'wp-hotel-booking' ),
				'tab'   => Controls_Manager::TAB_STYLE,
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
					'{{WRAPPER}} .hotel-booking-search-el .hb-submit button' => 'width: {{SIZE}}{{UNIT}};'
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
				'default'   => [
					'size'	=> 90,
					'unit'	=> 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .hotel-booking-search-el .hb-submit button' => 'height: {{SIZE}}{{UNIT}};'
				]
			]
		);

		$this->register_button_style( 'submit_search_button', '.hotel-booking-search-el .hb-submit button' );

		$this->add_responsive_control(
			'button_margin',
			[
				'label'      => esc_html__( 'Margin', 'wp-hotel-booking' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .hotel-booking-search-el .hb-submit button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

    protected function render() {
        $settings    	= $this->get_settings_for_display();
		$uniqid         = uniqid();

		if ( $settings['data'] ) {
			?>
            <div class="hotel-booking-search-el">
				<form name="hb-search-form" action="<?php echo hb_get_url(); ?>" class="hb-search-form-<?php echo esc_attr($uniqid); ?>">
					<ul class="hb-form-table"> 
					<?php
					foreach ( $settings['data'] as $data ) {
						$classes = 'elementor-repeater-item-'.$data['_id'];

						switch ( $data['meta_field'] ) {
							case 'date':
								$this->hb_render_check_the_date($data, $classes);
								break;
							case 'adults':	
								$this->hb_render_adults($data, $classes);
								break;
							case 'children':
								$this->hb_render_children($data, $classes);	
								break;
							case 'submit':
								$this->hb_render_submit($data, $classes);	
								break;
						}
					}
					?>
					</ul>
					<?php
					wp_nonce_field('hb_search_nonce_action', 'nonce'); ?>
					<input type="hidden" name="hotel-booking" value="results" />
					<input type="hidden" name="action" value="hotel_booking_parse_search_params" />
				</form>
			</div>
			<?php
		}
    }

	protected function hb_render_check_the_date($settings, $classes) {
		$uniqid          = uniqid();
		$datetime 		 = new \DateTime('NOW');
        $tomorrow 		 = new \DateTime('tomorrow');
        $format 		 = get_option('date_format');
		$check_in_date  = hb_get_request( 'check_in_date', $datetime->format($format));
		$check_out_date = hb_get_request( 'check_out_date', $tomorrow->format($format));
		$label_check_in  = $settings['label_field_date'] ?? esc_html__('Arrival Date', 'wp-hotel-booking');
		$label_check_out = $settings['label_field_check_out'] ?? esc_html__('Departure Date', 'wp-hotel-booking');

		if ($settings['layout_date'] == 'single') {
			?>
			<li class="hb-form-field <?php echo esc_attr($classes); ?>">
				<?php if ( $label_check_in != '' ) :?>
					<div class="label"><?php echo $label_check_in; ?></div>
				<?php endif; ?>
				<div class="hb-form-field-input hb_input_field">
					<?php if ( $settings['icons_field'] ) { 
						Icons_Manager::render_icon( $settings['icons_field'], array( 'aria-hidden' => 'true', 'class' => 'icon-custom' ) );        
					} ?>
					<input type="text" name="check_in_date" id="check_in_date_<?php echo esc_attr($uniqid); ?>" class="hb_input_date_check" value="<?php echo esc_attr($check_in_date); ?>" placeholder="<?php echo $label_check_in; ?>" autocomplete="off" />
				</div>
			</li>

			<li class="hb-form-field <?php echo esc_attr($classes); ?>">
				<?php if ( $label_check_out != '' ) :?>
					<div class="label"><?php echo $label_check_out; ?></div>
				<?php endif; ?>
				<div class="hb-form-field-input hb_input_field">
					<?php if ( $settings['icons_field'] ) { 
						Icons_Manager::render_icon( $settings['icons_field'], array( 'aria-hidden' => 'true', 'class' => 'icon-custom' ) );        
					} ?>
					<input type="text" name="check_out_date" id="check_out_date_<?php echo esc_attr($uniqid); ?>" class="hb_input_date_check" value="<?php echo esc_attr($check_out_date); ?>" placeholder="<?php echo $label_check_out; ?>" autocomplete="off" />
				</div>
			</li>
			<?php
		}else {
			?>
			<li class="hb-form-field hb-form-check-in-check-out <?php echo esc_attr($classes); ?>">
				<input type="text" id="multidate" class="multidate" value="<?php echo esc_attr($check_in_date) ?>" readonly />
				<?php if ( $settings['icons_field'] ) { 
					Icons_Manager::render_icon( $settings['icons_field'], array( 'aria-hidden' => 'true', 'class' => 'icon-custom' ) );        
				} ?>
				<?php if ( $label_check_in != '' ) :?>
					<div class="label"><?php echo $label_check_in; ?></div>
				<?php endif; ?>
				<div class="hb-form-field-input hb_input_field">
					<input type="text" name="check_in_date" id="check_in_date_<?php echo  esc_attr($uniqid) ?>" class="check-date" value="<?php echo esc_attr($check_in_date) ?>" readonly />
				</div>
				<div class="hb-form-field-input hb_input_field">
					<input type="text" name="check_out_date" id="check_out_date_<?php echo esc_attr($uniqid) ?>" class="check-date" value="<?php echo esc_attr($check_out_date) ?>" readonly />
				</div>
			</li>
			<?php
		}
	}

	protected function hb_render_adults($settings, $classes) {
		$adults         = hb_get_request('adults', '1');
		$label_adults   = !empty($settings['label_field']) ? $settings['label_field'] : esc_html__('Adults', 'wp-hotel-booking');

		if ( $settings['layout_guest'] == 'select') {
		?>
			<li class="hb-form-field <?php echo esc_attr($classes); ?>">
				<?php if ( $settings['icons_field'] ) { 
					Icons_Manager::render_icon( $settings['icons_field'], array( 'aria-hidden' => 'true', 'class' => 'icon-custom' ) );        
				} ?>
				<?php if ( $label_adults != '' ) :?>
					<div class="label"><?php echo $label_adults; ?></div>
				<?php endif; ?>
				<div class="hb-form-field-input">
					<?php
					hb_dropdown_numbers(
						array(
							'name'              => 'adults_capacity',
							'min'               => 1,
							'max'               => hb_get_max_capacity_of_rooms(),
							'show_option_none'  => $label_adults,
							'selected'          => $adults,
							'option_none_value' => 0,
							'options'           => hb_get_capacity_of_rooms()
						)
					);
					?>
				</div>
			</li>
		<?php
		} else {
			?>
			<li class="hb-form-field hb-form-number <?php echo esc_attr($classes); ?>">
				<?php if ( $settings['icons_field'] ) { 
					Icons_Manager::render_icon( $settings['icons_field'], array( 'aria-hidden' => 'true', 'class' => 'icon-custom' ) );        
				} ?>
				<?php if ( $label_adults != '' ) :?>
					<div class="label"><?php echo $label_adults; ?></div>
				<?php endif; ?>
				<div id="adults" class="hb-form-field-input hb_input_field">
					<input type="text" id="number" class="adults-input" value="<?php echo esc_attr($adults) ?>" readonly />
					<span><?php echo $label_adults; ?></span>
				</div>
				<div class="hb-form-field-list nav-adults">
					<span class="name"><?php echo $label_adults; ?></span>
					<div class="number-box">
						<span class="number-icons goDown"><i class="fa fa-minus"></i></span>
						<span class="hb-adults-field adults-number">
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
			<?php
		}
	}

	protected function hb_render_children($settings, $classes) {
		$max_child      = hb_get_request('max_child', '0');
		$label_child   	= !empty($settings['label_field']) ? $settings['label_field'] : esc_html__('Children', 'wp-hotel-booking');

		if ( $settings['layout_guest'] == 'select') {
			?>
			<li class="hb-form-field <?php echo esc_attr($classes); ?>">
				<?php if ( $settings['icons_field'] ) { 
					Icons_Manager::render_icon( $settings['icons_field'], array( 'aria-hidden' => 'true', 'class' => 'icon-custom' ) );        
				} ?>
				<?php if ( $label_child != '' ) :?>
					<div class="label"><?php echo $label_child; ?></div>
				<?php endif; ?>
				<div class="hb-form-field-input">
					<?php
					hb_dropdown_numbers(
						array(
							'name'              => 'max_child',
							'min'               => 1,
							'max'               => hb_get_max_child_of_rooms(),
							'show_option_none'  => $label_child,
							'option_none_value' => 0,
							'selected'          => $max_child,
						)
					);
					?>
				</div>
			</li>
			<?php
		} else {
			?>
			<li class="hb-form-field hb-form-number <?php echo esc_attr($classes); ?>">
				<?php if ( $settings['icons_field'] ) { 
					Icons_Manager::render_icon( $settings['icons_field'], array( 'aria-hidden' => 'true', 'class' => 'icon-custom' ) );        
				} ?>
				<?php if ( $label_child != '' ) :?>
					<div class="label"><?php echo $label_child; ?></div>
				<?php endif; ?>
				<div id="child" class="hb-form-field-input hb_input_field">
					<input type="text" id="number" class="child-input" value="<?php echo esc_attr($max_child) ?>" readonly />
					<span><?php echo $label_child; ?></span>
				</div>
				<div class="hb-form-field-list nav-children">
					<span class="name"><?php echo $label_child; ?></span>
					<div class="number-box">
						<span class="number-icons goDown"><i class="fa fa-minus"></i></span>
						<span class="hb-children-field children-number">
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
			<?php
		}
	}

	protected function hb_render_submit($settings, $classes) {
		?>
		<li class="hb-submit <?php echo esc_attr($classes); ?>">
			<button type="submit" class="wphb-button">
				<?php if ( $settings['icons_field'] ) { 
					Icons_Manager::render_icon( $settings['icons_field'], array( 'aria-hidden' => 'true' ) );        
				} ?>
				<?php if ( $settings['label_field'] != '' ) :?>
					<?php echo $settings['label_field']; ?>
				<?php else : ?>
					<?php esc_html_e( 'Check Availability', 'wp-hotel-booking' ) ;?>
				<?php endif; ?>
			</button>
		</li>
		<?php
	}
}