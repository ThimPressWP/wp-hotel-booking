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
			'section_tabs',
			array(
				'label' => __( 'General', 'wp-hotel-booking' ),
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
    }

    protected function render() {
        $settings    = $this->get_settings_for_display();

        if ( $settings['data'] ) { ?>
            <div id="hotel-booking-search-filter" class="hotel-booking-search-filter">
            <form class="search-filter-form" action="">
                <?php 
                foreach ( $settings['data'] as $data ) {
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
                    	hb_get_template( 'search/v2/search-filter/' . $data['meta_field'] . '.php', compact( 'data' ) );
					}
                }
                ?>
            </form>
            </div>
            <?php
        }
    }
}