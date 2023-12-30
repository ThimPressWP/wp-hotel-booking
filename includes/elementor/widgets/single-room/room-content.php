<?php

namespace Elementor;

// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit;
}

class Thim_Ekit_Widget_Room_Content extends Widget_Base
{
	public function get_name()
	{
		return 'room-content';
	}

	public function get_title()
	{
		return esc_html__('Room Content', 'wp-hotel-booking');
	}

	public function get_icon()
	{
		return 'thim-eicon eicon-post-content';
	}

	public function get_categories()
	{
		return array(\WPHB\Elementor::CATEGORY_SINGLE_ROOM);
	}

	protected function register_controls()
	{
		$this->start_controls_section(
			'section_product_content_style',
			array(
				'label' => esc_html__('Style', 'wp-hotel-booking'),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'alignment',
			array(
				'label'     => esc_html__('Alignment', 'wp-hotel-booking'),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'    => array(
						'title' => esc_html__('Left', 'wp-hotel-booking'),
						'icon'  => 'eicon-text-align-left',
					),
					'center'  => array(
						'title' => esc_html__('Center', 'wp-hotel-booking'),
						'icon'  => 'eicon-text-align-center',
					),
					'right'   => array(
						'title' => esc_html__('Right', 'wp-hotel-booking'),
						'icon'  => 'eicon-text-align-right',
					),
					'justify' => array(
						'title' => esc_html__('Justified', 'wp-hotel-booking'),
						'icon'  => 'eicon-text-align-justify',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .hb-room-single__content' => 'text-align: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'color',
			array(
				'label'     => esc_html__('Color', 'wp-hotel-booking'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .hb-room-single__content' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'text_typography',
				'selector' => '{{WRAPPER}} .hb-room-single__content',
			)
		);

		$this->end_controls_section();
	}

	public function render()
	{
		do_action('WPHB/modules/single-room/before-preview-query');

		?>

		<div class="hb-room-single__content">
			<?php the_content(); ?>
		</div>

		<?php

		do_action('WPHB/modules/single-room/after-preview-query');
	}
}
