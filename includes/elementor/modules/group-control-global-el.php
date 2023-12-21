<?php
namespace WPHB;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;

trait HBGroupControlTrait {

    protected function register_style_typo_color_margin(string $prefix_name, string $selector) {
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => $prefix_name.'_typography',
				'selector' => "{{WRAPPER}} $selector",
			)
		);

        $this->add_responsive_control(
			$prefix_name.'_margin',
			array(
				'label'      => esc_html__( 'Margin', 'realpress' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					"{{WRAPPER}} $selector" => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			$prefix_name.'_color',
			array(
				'label'     => esc_html__( 'Color', 'realpress' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					"{{WRAPPER}} $selector" => 'color: {{VALUE}};',
				),
			)
		);
	}
}