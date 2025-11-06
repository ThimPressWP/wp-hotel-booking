<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Thim_Ekit_Widget_Check_Avaibility extends \Elementor\Widget_Base {

	public function get_name() {
		return 'wphb-room-check-avaibility';
	}

	public function get_title() {
		return esc_html__( 'Check Availability', 'wp-hotel-booking' );
	}

	public function get_icon() {
		return 'thim-eicon eicon-search';
	}

	public function get_categories() {
		return array( 'general', 'thim_ekit_recommended' );
	}

	public function get_keywords() {
		return array( 'hotel', 'booking', 'search', 'form' );
	}

	public function get_script_depends() {
		return array( 'wphb-room-check-avaibility' );
	}

	protected function register_controls() {

		// Content Section
		$this->start_controls_section(
			'content_section',
			array(
				'label' => esc_html__( 'Content', 'textdomain' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'form_title',
			array(
				'label'       => esc_html__( 'Form Title', 'textdomain' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Check Availability', 'textdomain' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'button_title',
			array(
				'label'   => esc_html__( 'Button Text', 'textdomain' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Search', 'textdomain' ),
			)
		);

		$this->add_control(
			'form_action',
			array(
				'label'       => esc_html__( 'Form Action URL', 'textdomain' ),
				'type'        => \Elementor\Controls_Manager::URL,
				'placeholder' => esc_html__( 'https://your-site.com/results', 'textdomain' ),
				'default'     => array(
					'url' => '',
				),
			)
		);

		$this->add_control(
			'show_toggle',
			array(
				'label'        => esc_html__( 'Enable Toggle', 'textdomain' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'textdomain' ),
				'label_off'    => esc_html__( 'No', 'textdomain' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'toggle_button_text',
			array(
				'label'     => esc_html__( 'Toggle Button Text', 'textdomain' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => esc_html__( 'Search Rooms', 'textdomain' ),
				'condition' => array(
					'show_toggle' => 'yes',
				),
			)
		);

		$this->add_control(
			'toggle_close_text',
			array(
				'label'     => esc_html__( 'Toggle Close Text', 'textdomain' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => esc_html__( 'Close', 'textdomain' ),
				'condition' => array(
					'show_toggle' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		// Field Visibility Section
		$this->start_controls_section(
			'fields_section',
			array(
				'label' => esc_html__( 'Form Fields', 'textdomain' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'show_check_in',
			array(
				'label'   => esc_html__( 'Show Check-in Date', 'textdomain' ),
				'type'    => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'show_check_out',
			array(
				'label'   => esc_html__( 'Show Check-out Date', 'textdomain' ),
				'type'    => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'show_adults',
			array(
				'label'   => esc_html__( 'Show Adults', 'textdomain' ),
				'type'    => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'show_children',
			array(
				'label'   => esc_html__( 'Show Children', 'textdomain' ),
				'type'    => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'show_rooms',
			array(
				'label'   => esc_html__( 'Show Number of Rooms', 'textdomain' ),
				'type'    => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->end_controls_section();

		// Toggle Button Style Section
		$this->start_controls_section(
			'toggle_button_style',
			array(
				'label'     => esc_html__( 'Toggle Button', 'textdomain' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_toggle' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'toggle_button_display',
			array(
				'label'          => esc_html__( 'Display', 'textdomain' ),
				'type'           => \Elementor\Controls_Manager::SELECT,
				'options'        => array(
					'none'         => esc_html__( 'None', 'textdomain' ),
					'block'        => esc_html__( 'Block', 'textdomain' ),
					'inline-block' => esc_html__( 'Inline Block', 'textdomain' ),
				),
				'default'        => 'none',
				'tablet_default' => 'block',
				'mobile_default' => 'block',
				'selectors'      => array(
					'{{WRAPPER}} .hb-toggle-button' => 'display: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'toggle_button_typography',
				'selector' => '{{WRAPPER}} .hb-toggle-button',
			)
		);

		$this->add_control(
			'toggle_button_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'textdomain' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .hb-toggle-button' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'toggle_button_text_color',
			array(
				'label'     => esc_html__( 'Text Color', 'textdomain' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .hb-toggle-button' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'toggle_button_padding',
			array(
				'label'      => esc_html__( 'Padding', 'textdomain' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .hb-toggle-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'toggle_button_border',
				'selector' => '{{WRAPPER}} .hb-toggle-button',
			)
		);

		$this->add_control(
			'toggle_button_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'textdomain' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .hb-toggle-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// Form Container Style
		$this->start_controls_section(
			'form_container_style',
			array(
				'label' => esc_html__( 'Form Container', 'textdomain' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'form_display',
			array(
				'label'          => esc_html__( 'Form Display (Desktop)', 'textdomain' ),
				'type'           => \Elementor\Controls_Manager::SELECT,
				'options'        => array(
					'block' => esc_html__( 'Block', 'textdomain' ),
					'flex'  => esc_html__( 'Inline (Flex)', 'textdomain' ),
				),
				'default'        => 'flex',
				'tablet_default' => 'block',
				'mobile_default' => 'block',
				'selectors'      => array(
					'{{WRAPPER}} .hb-form-table' => 'display: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'form_gap',
			array(
				'label'      => esc_html__( 'Gap Between Fields', 'textdomain' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .hb-form-table' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'form_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'textdomain' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .hb-search-form' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'form_padding',
			array(
				'label'      => esc_html__( 'Padding', 'textdomain' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .hb-search-form' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// Submit Button Style
		$this->start_controls_section(
			'submit_button_style',
			array(
				'label' => esc_html__( 'Submit Button', 'textdomain' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'submit_button_typography',
				'selector' => '{{WRAPPER}} .wphb-button',
			)
		);

		$this->start_controls_tabs( 'submit_button_tabs' );

		$this->start_controls_tab(
			'submit_button_normal',
			array(
				'label' => esc_html__( 'Normal', 'textdomain' ),
			)
		);

		$this->add_control(
			'submit_button_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'textdomain' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wphb-button' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'submit_button_text_color',
			array(
				'label'     => esc_html__( 'Text Color', 'textdomain' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wphb-button' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'submit_button_hover',
			array(
				'label' => esc_html__( 'Hover', 'textdomain' ),
			)
		);

		$this->add_control(
			'submit_button_bg_color_hover',
			array(
				'label'     => esc_html__( 'Background Color', 'textdomain' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wphb-button:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'submit_button_text_color_hover',
			array(
				'label'     => esc_html__( 'Text Color', 'textdomain' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wphb-button:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'submit_button_padding',
			array(
				'label'      => esc_html__( 'Padding', 'textdomain' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .wphb-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator'  => 'before',
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'submit_button_border',
				'selector' => '{{WRAPPER}} .wphb-button',
			)
		);

		$this->add_control(
			'submit_button_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'textdomain' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .wphb-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings    = $this->get_settings_for_display();
		$unique_id   = 'hb_' . $this->get_id();
		$form_action = ! empty( $settings['form_action']['url'] ) ? $settings['form_action']['url'] : '';
		?>
		<div class="hotel-booking-search wphb-elementor-search-room">
			<?php if ( $settings['show_toggle'] === 'yes' ) : ?>
				<button type="button" class="hb-toggle-button" data-toggle-text="<?php echo esc_attr( $settings['toggle_button_text'] ); ?>" data-close-text="<?php echo esc_attr( $settings['toggle_close_text'] ); ?>">
					<?php echo esc_html( $settings['toggle_button_text'] ); ?>
				</button>
			<?php endif; ?>

			<form name="hb-search-form" action="<?php echo esc_url( $form_action ); ?>" method="GET" class="hb-search-form hb-search-form-<?php echo esc_attr( $unique_id ); ?>" <?php echo ( $settings['show_toggle'] === 'yes' ) ? 'style="display:none;"' : ''; ?>>
				<?php if ( ! empty( $settings['form_title'] ) ) : ?>
					<h3><?php echo esc_html( $settings['form_title'] ); ?></h3>
				<?php endif; ?>

				<ul class="hb-form-table">
					<?php if ( $settings['show_check_in'] === 'yes' ) : ?>
						<li class="hb-form-field">
							<label><?php echo esc_html__( 'Check-in Date', 'textdomain' ); ?></label>
							<div class="hb-form-field-input hb_input_field">
								<input type="text" name="check_in_date" id="check_in_date_<?php echo esc_attr( $unique_id ); ?>" class="hb_input_date_check flatpickr-input" value="" placeholder="<?php echo esc_attr__( 'Check-in Date', 'textdomain' ); ?>" autocomplete="off" readonly="readonly">
							</div>
						</li>
					<?php endif; ?>

					<?php if ( $settings['show_check_out'] === 'yes' ) : ?>
						<li class="hb-form-field">
							<label><?php echo esc_html__( 'Check-out Date', 'textdomain' ); ?></label>
							<div class="hb-form-field-input hb_input_field">
								<input type="text" name="check_out_date" id="check_out_date_<?php echo esc_attr( $unique_id ); ?>" class="hb_input_date_check flatpickr-input" value="" placeholder="<?php echo esc_attr__( 'Check-out Date', 'textdomain' ); ?>" autocomplete="off" readonly="readonly">
							</div>
						</li>
					<?php endif; ?>

					<?php if ( $settings['show_adults'] === 'yes' ) : ?>
						<li class="hb-form-field">
							<label><?php echo esc_html__( 'Adults', 'textdomain' ); ?></label>
							<div class="hb-form-field-input">
								<select name="adults_capacity">
									<option value=""><?php echo esc_html__( 'Adults', 'textdomain' ); ?></option>
									<?php for ( $i = 1; $i <= 10; $i++ ) : ?>
										<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
									<?php endfor; ?>
								</select>
							</div>
						</li>
					<?php endif; ?>

					<?php if ( $settings['show_children'] === 'yes' ) : ?>
						<li class="hb-form-field">
							<label><?php echo esc_html__( 'Children', 'textdomain' ); ?></label>
							<div class="hb-form-field-input">
								<select name="max_child">
									<option value=""><?php echo esc_html__( 'Children', 'textdomain' ); ?></option>
									<?php for ( $i = 1; $i <= 4; $i++ ) : ?>
										<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
									<?php endfor; ?>
								</select>
							</div>
						</li>
					<?php endif; ?>

					<?php if ( $settings['show_rooms'] === 'yes' ) : ?>
						<li class="hb-form-field">
							<label><?php echo esc_html__( 'Number of rooms', 'textdomain' ); ?></label>
							<div class="hb-form-field-input">
								<select name="number-of-rooms">
									<option value=""><?php echo esc_html__( 'Number of rooms', 'textdomain' ); ?></option>
									<?php for ( $i = 1; $i <= 20; $i++ ) : ?>
										<option value="<?php echo $i; ?>" <?php selected( $i, 1 ); ?>><?php echo $i; ?></option>
									<?php endfor; ?>
								</select>
							</div>
						</li>
					<?php endif; ?>
				</ul>

				<input type="hidden" name="hotel-booking" value="results">
				<input type="hidden" name="widget-search" value="">
				<input type="hidden" name="action" value="hotel_booking_parse_search_params">
				<input type="hidden" name="paged" value="1">
				<?php wp_nonce_field( 'hotel_booking_search', 'nonce' ); ?>

				<p class="hb-submit">
					<button type="submit" class="wphb-button"><?php echo esc_html( $settings['button_title'] ); ?></button>
				</p>
			</form>
		</div>
		<?php
	}
}
