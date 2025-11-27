<?php
namespace Elementor;
use WPHB_Settings;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Thim_Ekit_Widget_Loop_Room_External_Links extends Widget_Base {

	public function get_name() {
		return 'thim-loop-room-external-links';
	}

	public function get_title() {
		return esc_html__( 'Loop Room External Links', 'thim-elementor-kit' );
	}

	public function get_icon() {
		return 'thim-eicon eicon-link';
	}

	public function get_categories() {
		return array( 'thim_ekit_recommended' );
	}

	public function get_keywords() {
		return array( 'loop', 'external', 'links', 'room', 'ota' );
	}

	protected function register_controls() {

		// Content Tab
		$this->start_controls_section(
			'content_section',
			array(
				'label' => esc_html__( 'Content', 'thim-elementor-kit' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'title',
			array(
				'label'   => esc_html__( 'Title', 'thim-elementor-kit' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'External Links', 'thim-elementor-kit' ),
				'dynamic' => array(
					'active' => true,
				),
			)
		);

		$this->end_controls_section();

		// Settings Section
		$this->start_controls_section(
			'settings_section',
			array(
				'label' => esc_html__( 'Settings', 'thim-elementor-kit' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'rel_attributes',
			array(
				'label'    => esc_html__( 'Rel Attributes', 'thim-elementor-kit' ),
				'type'     => Controls_Manager::SELECT2,
				'multiple' => true,
				'options'  => array(
					'nofollow'   => 'nofollow',
					'noopener'   => 'noopener',
					'noreferrer' => 'noreferrer',
					'sponsored'  => 'sponsored',
					'ugc'        => 'ugc',
				),
				'default'  => array( 'noopener', 'noreferrer' ),
			)
		);

		$this->add_control(
			'enable_lazy_load',
			array(
				'label'        => esc_html__( 'Enable Lazy Loading', 'thim-elementor-kit' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'thim-elementor-kit' ),
				'label_off'    => esc_html__( 'No', 'thim-elementor-kit' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->end_controls_section();

		// Style Tab - Title
		$this->start_controls_section(
			'title_style_section',
			array(
				'label' => esc_html__( 'Title', 'thim-elementor-kit' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'Color', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .external-ota-platform-title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .external-ota-platform-title',
			)
		);

		$this->add_responsive_control(
			'title_margin',
			array(
				'label'      => esc_html__( 'Margin', 'thim-elementor-kit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .external-ota-platform-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// Style Tab - UL List
		$this->start_controls_section(
			'list_style_section',
			array(
				'label' => esc_html__( 'List Container', 'thim-elementor-kit' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'list_style_type',
			array(
				'label'     => esc_html__( 'List Style Type', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'none',
				'options'   => array(
					'none'                 => esc_html__( 'None', 'thim-elementor-kit' ),
					'disc'                 => esc_html__( 'Disc', 'thim-elementor-kit' ),
					'circle'               => esc_html__( 'Circle', 'thim-elementor-kit' ),
					'square'               => esc_html__( 'Square', 'thim-elementor-kit' ),
					'decimal'              => esc_html__( 'Decimal', 'thim-elementor-kit' ),
					'decimal-leading-zero' => esc_html__( 'Decimal Leading Zero', 'thim-elementor-kit' ),
					'lower-roman'          => esc_html__( 'Lower Roman', 'thim-elementor-kit' ),
					'upper-roman'          => esc_html__( 'Upper Roman', 'thim-elementor-kit' ),
					'lower-alpha'          => esc_html__( 'Lower Alpha', 'thim-elementor-kit' ),
					'upper-alpha'          => esc_html__( 'Upper Alpha', 'thim-elementor-kit' ),
					'lower-greek'          => esc_html__( 'Lower Greek', 'thim-elementor-kit' ),
					'lower-latin'          => esc_html__( 'Lower Latin', 'thim-elementor-kit' ),
					'upper-latin'          => esc_html__( 'Upper Latin', 'thim-elementor-kit' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .wphb-partner-links'    => 'list-style-type: {{VALUE}};',
					'{{WRAPPER}} .wphb-partner-links li' => 'list-style-type: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'list_style_position',
			array(
				'label'     => esc_html__( 'List Style Position', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'outside',
				'options'   => array(
					'outside' => esc_html__( 'Outside', 'thim-elementor-kit' ),
					'inside'  => esc_html__( 'Inside', 'thim-elementor-kit' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .wphb-partner-links' => 'list-style-position: {{VALUE}};',
				),
				'condition' => array(
					'list_style_type!' => 'none',
				),
			)
		);

		$this->add_responsive_control(
			'list_gap',
			array(
				'label'      => esc_html__( 'Gap Between Items', 'thim-elementor-kit' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .wphb-partner-links' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'list_padding',
			array(
				'label'      => esc_html__( 'Padding', 'thim-elementor-kit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .wphb-partner-links' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'list_margin',
			array(
				'label'      => esc_html__( 'Margin', 'thim-elementor-kit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .wphb-partner-links' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'list_display',
			array(
				'label'     => esc_html__( 'Display', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'flex',
				'options'   => array(
					'flex'        => esc_html__( 'Flex', 'thim-elementor-kit' ),
					'grid'        => esc_html__( 'Grid', 'thim-elementor-kit' ),
					'block'       => esc_html__( 'Block', 'thim-elementor-kit' ),
					'inline-flex' => esc_html__( 'Inline Flex', 'thim-elementor-kit' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .wphb-partner-links' => 'display: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'list_flex_direction',
			array(
				'label'     => esc_html__( 'Direction', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'row',
				'options'   => array(
					'row'            => esc_html__( 'Row', 'thim-elementor-kit' ),
					'column'         => esc_html__( 'Column', 'thim-elementor-kit' ),
					'row-reverse'    => esc_html__( 'Row Reverse', 'thim-elementor-kit' ),
					'column-reverse' => esc_html__( 'Column Reverse', 'thim-elementor-kit' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .wphb-partner-links' => 'flex-direction: {{VALUE}};',
				),
				'condition' => array(
					'list_display' => array( 'flex', 'inline-flex' ),
				),
			)
		);

		$this->add_responsive_control(
			'list_align_items',
			array(
				'label'     => esc_html__( 'Align Items', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'flex-start',
				'options'   => array(
					'flex-start' => esc_html__( 'Start', 'thim-elementor-kit' ),
					'center'     => esc_html__( 'Center', 'thim-elementor-kit' ),
					'flex-end'   => esc_html__( 'End', 'thim-elementor-kit' ),
					'stretch'    => esc_html__( 'Stretch', 'thim-elementor-kit' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .wphb-partner-links' => 'align-items: {{VALUE}};',
				),
				'condition' => array(
					'list_display' => array( 'flex', 'inline-flex' ),
				),
			)
		);

		$this->add_responsive_control(
			'list_justify_content',
			array(
				'label'     => esc_html__( 'Justify Content', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'flex-start',
				'options'   => array(
					'flex-start'    => esc_html__( 'Start', 'thim-elementor-kit' ),
					'center'        => esc_html__( 'Center', 'thim-elementor-kit' ),
					'flex-end'      => esc_html__( 'End', 'thim-elementor-kit' ),
					'space-between' => esc_html__( 'Space Between', 'thim-elementor-kit' ),
					'space-around'  => esc_html__( 'Space Around', 'thim-elementor-kit' ),
					'space-evenly'  => esc_html__( 'Space Evenly', 'thim-elementor-kit' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .wphb-partner-links' => 'justify-content: {{VALUE}};',
				),
				'condition' => array(
					'list_display' => array( 'flex', 'inline-flex' ),
				),
			)
		);

		$this->add_control(
			'list_background',
			array(
				'label'     => esc_html__( 'Background Color', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wphb-partner-links' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'list_border',
				'selector' => '{{WRAPPER}} .wphb-partner-links',
			)
		);

		$this->add_responsive_control(
			'list_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'thim-elementor-kit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .wphb-partner-links' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// Style Tab - List Items
		$this->start_controls_section(
			'list_item_style_section',
			array(
				'label' => esc_html__( 'List Items', 'thim-elementor-kit' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'item_padding',
			array(
				'label'      => esc_html__( 'Padding', 'thim-elementor-kit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .external-link-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'item_background',
			array(
				'label'     => esc_html__( 'Background Color', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .external-link-item' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'item_border',
				'selector' => '{{WRAPPER}} .external-link-item',
			)
		);

		$this->add_responsive_control(
			'item_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'thim-elementor-kit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .external-link-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// Style Tab - Images
		$this->start_controls_section(
			'image_style_section',
			array(
				'label' => esc_html__( 'Images', 'thim-elementor-kit' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'image_width',
			array(
				'label'      => esc_html__( 'Width', 'thim-elementor-kit' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 500,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 50,
				),
				'selectors'  => array(
					'{{WRAPPER}} .external-link-item img' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'image_height',
			array(
				'label'      => esc_html__( 'Height', 'thim-elementor-kit' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 500,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 50,
				),
				'selectors'  => array(
					'{{WRAPPER}} .external-link-item img' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'image_object_fit',
			array(
				'label'     => esc_html__( 'Object Fit', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'cover',
				'options'   => array(
					'fill'    => esc_html__( 'Fill', 'thim-elementor-kit' ),
					'contain' => esc_html__( 'Contain', 'thim-elementor-kit' ),
					'cover'   => esc_html__( 'Cover', 'thim-elementor-kit' ),
					'none'    => esc_html__( 'None', 'thim-elementor-kit' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .external-link-item img' => 'object-fit: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'image_border',
				'selector' => '{{WRAPPER}} .external-link-item img',
			)
		);

		$this->add_responsive_control(
			'image_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'thim-elementor-kit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .external-link-item img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'image_effects' );

		$this->start_controls_tab(
			'image_normal',
			array(
				'label' => esc_html__( 'Normal', 'thim-elementor-kit' ),
			)
		);

		$this->add_control(
			'image_opacity',
			array(
				'label'     => esc_html__( 'Opacity', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'max'  => 1,
						'min'  => 0,
						'step' => 0.01,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .external-link-item img' => 'opacity: {{SIZE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'image_hover',
			array(
				'label' => esc_html__( 'Hover', 'thim-elementor-kit' ),
			)
		);

		$this->add_control(
			'image_opacity_hover',
			array(
				'label'     => esc_html__( 'Opacity', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'max'  => 1,
						'min'  => 0,
						'step' => 0.01,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .external-link-item a:hover img' => 'opacity: {{SIZE}};',
				),
			)
		);

		$this->add_control(
			'image_hover_transition',
			array(
				'label'     => esc_html__( 'Transition Duration', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'max'  => 3,
						'step' => 0.1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .external-link-item img' => 'transition: all {{SIZE}}s;',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Get merged and filtered external links data
	 */
	private function get_external_links_data( $room_id ) {
		// Get post meta external links
		$external_links_json = get_post_meta( $room_id, '_hb_room_external_link', true );
		$external_links      = ! empty( $external_links_json ) ? json_decode( $external_links_json, true ) : array();

		// Get global settings
		$hb_extenal_link_settings = WPHB_Settings::instance()->get( 'external_link_settings' );
		$global_settings          = ! empty( $hb_extenal_link_settings ) ? json_decode( $hb_extenal_link_settings, true ) : array();

		// Default icon URL
		$default_icon_url = WPHB_PLUGIN_URL . '/assets/images/icon-128x128.png';

		$merged_data = array();

		// Filter enabled links and merge with global settings
		foreach ( $external_links as $key => $link_data ) {
			// Skip if not enabled
			if ( empty( $link_data['enabled'] ) ) {
				continue;
			}

			// Get external link URL
			$external_link = ! empty( $link_data['external_link'] ) ? $link_data['external_link'] : '';

			// If empty, try to get from global settings
			if ( empty( $external_link ) && isset( $global_settings[ $key ]['external_link'] ) ) {
				$external_link = $global_settings[ $key ]['external_link'];
			}

			// Skip if still empty
			if ( empty( $external_link ) ) {
				continue;
			}

			// Get icon URL from global settings
			$icon_url = '';
			if ( isset( $global_settings[ $key ]['icon_url'] ) && ! empty( $global_settings[ $key ]['icon_url'] ) ) {
				$icon_url = $global_settings[ $key ]['icon_url'];
			}

			// Use default icon if empty
			if ( empty( $icon_url ) ) {
				$icon_url = $default_icon_url;
			}

			// Get title from global settings
			$title = isset( $global_settings[ $key ]['title'] ) ? $global_settings[ $key ]['title'] : '';

			// Get order
			$order = isset( $link_data['order'] ) ? intval( $link_data['order'] ) : 999;

			$merged_data[] = array(
				'key'           => $key,
				'external_link' => $external_link,
				'icon_url'      => $icon_url,
				'title'         => $title,
				'order'         => $order,
			);
		}

		// Sort by order
		usort(
			$merged_data,
			function ( $a, $b ) {
				return $a['order'] - $b['order'];
			}
		);

		return $merged_data;
	}

	protected function render() {
		$settings       = $this->get_settings_for_display();
		$title          = $settings['title'];
		$rel_attributes = ! empty( $settings['rel_attributes'] ) ? implode( ' ', $settings['rel_attributes'] ) : '';
		$lazy_load      = $settings['enable_lazy_load'] === 'yes';

		// Get current post ID (room ID) in loop
		$room_id = get_the_ID();

		// Get merged external links data
		$external_links = $this->get_external_links_data( $room_id );

		// Get image dimensions from settings
		$image_width  = ! empty( $settings['image_width']['size'] ) ? $settings['image_width']['size'] : 50;
		$image_height = ! empty( $settings['image_height']['size'] ) ? $settings['image_height']['size'] : 50;
		if ( empty( $external_links ) ) {
			return;
		}
		?>
		<div class="external-ota-platform-container loop-room-external-links">
			<?php if ( ! empty( $title ) ) : ?>
				<p class="external-ota-platform-title"><?php echo esc_html( $title ); ?></p>
			<?php endif; ?>
			
			<?php if ( ! empty( $external_links ) ) : ?>
				<ul class="wphb-partner-links e-external-links">
					<?php foreach ( $external_links as $index => $item ) : ?>
						<li class="external-link-item">
							<a href="<?php echo esc_url( $item['external_link'] ); ?>" 
								target="_blank" 
								<?php
								if ( ! empty( $rel_attributes ) ) :
									?>
									rel="<?php echo esc_attr( $rel_attributes ); ?>"<?php endif; ?>
								<?php
								if ( ! empty( $item['title'] ) ) :
									?>
									title="<?php echo esc_attr( $item['title'] ); ?>"<?php endif; ?>>
								<img 
								<?php
								if ( $lazy_load && $index > 0 ) :
									?>
									loading="lazy"<?php endif; ?>
									src="<?php echo esc_url( $item['icon_url'] ); ?>" 
									alt="<?php echo esc_attr( $item['title'] ); ?>" 
									width="<?php echo esc_attr( $image_width ); ?>" 
									height="<?php echo esc_attr( $image_height ); ?>">
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
		<?php
	}

	protected function content_template() {
		?>
		<#
		var relAttributes = settings.rel_attributes ? settings.rel_attributes.join(' ') : '';
		var lazyLoad = settings.enable_lazy_load === 'yes';
		var imageWidth = settings.image_width && settings.image_width.size ? settings.image_width.size : 50;
		var imageHeight = settings.image_height && settings.image_height.size ? settings.image_height.size : 50;
		#>
		
		<div class="external-ota-platform-container loop-room-external-links">
			<# if (settings.title) { #>
				<p class="external-ota-platform-title">{{{ settings.title }}}</p>
			<# } #>
			
			<ul class="wphb-partner-links e-external-links">
				<li class="external-link-item">
					<a href="#" 
						target="_blank" 
						<# if (relAttributes) { #>rel="{{ relAttributes }}"<# } #>
						title="Preview">
						<img src="<?php echo WPHB_PLUGIN_URL . '/assets/images/icon-128x128.png'; ?>" 
							alt="Preview" 
							width="{{ imageWidth }}" 
							height="{{ imageHeight }}">
					</a>
				</li>
			</ul>
		</div>
		<?php
	}
}
