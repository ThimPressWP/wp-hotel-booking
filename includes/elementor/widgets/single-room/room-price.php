<?php

namespace Elementor;

use Thim_EL_Kit\GroupControlTrait;
use WPHB\HBGroupControlTrait;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Thim_Ekit_Widget_Room_Price extends Widget_Base {
	use GroupControlTrait;
    use HBGroupControlTrait;

    public function get_name() {
		return 'room-price';
	}

	public function get_title() {
		return esc_html__( 'Room Price', 'wp-hotel-booking' );
	}

	public function get_icon() {
		return 'thim-eicon eicon-price-list';
	}

	public function get_categories() {
		return array( \WPHB\Elementor::CATEGORY_SINGLE_ROOM );
	}

	public function get_base() {
		return basename( __FILE__, '.php' );
	}

    protected function register_controls() {
        $this->start_controls_section(
			'section_tabs',
			[
				'label' => __( 'General', 'wp-hotel-booking' ),
			]
		);

        $this->add_control(
			'icon_unit',
			[
				'label'         => esc_html__( 'Icon Unit', 'wp-hotel-booking' ),
				'type'          => Controls_Manager::ICONS,
                'skin'          => 'inline',
                'label_block'   => false,
			]
		);

        $this->end_controls_section();
        $this->register_section_style_price();
    }

    protected function register_section_style_price(){
        $this->start_controls_section(
			'style_price',
			[
				'label' => esc_html__( 'Price', 'wp-hotel-booking' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

        $this->register_button_style( 'price_room', '.hb-room-price .price_value' );

        $this->end_controls_section();

        $this->start_controls_section(
			'style_unit',
			[
				'label' => esc_html__( 'Unit', 'wp-hotel-booking' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

        $this->register_style_typo_color_margin('unit_price', '.hb-room-price .price_value .unit');

        $this->end_controls_section();
    }

    protected function render() {

        do_action( 'WPHB/modules/single-room/before-preview-query' );

        $settings    	= $this->get_settings_for_display();
        global $hb_settings;

        $price_display  = apply_filters( 'hotel_booking_loop_room_price_display_style', $hb_settings->get( 'price_display' ) );
        $prices         = hb_room_get_selected_plan( get_the_ID() );
        $prices         = isset( $prices->prices ) ? $prices->prices : array();
        $html_icon_unit = '';

        if ( $settings['icon_unit'] ){
			ob_start();
			Icons_Manager::render_icon( $settings['icon_unit'],
				array(
					'aria-hidden' => 'true',
				)
			);
			$html_icon_unit = ob_get_contents();
			ob_end_clean();
		}

        if ( $prices ) {
            $min_price = is_numeric( min( $prices ) ) ? min( $prices ) : 0;
            $max_price = is_numeric( max( $prices ) ) ? max( $prices ) : 0;
            $min = $min_price + ( hb_price_including_tax() ? ( $min_price * hb_get_tax_settings() ) : 0 );
            $max = $max_price + ( hb_price_including_tax() ? ( $max_price * hb_get_tax_settings() ) : 0 );
            ?>
    
            <div class="hb-room-price">
                <?php if ( $price_display === 'max' ) { ?>
                    <span class="price_value price_max">
                    <?php echo hb_format_price( $max ) ?><span class="unit"><?php echo $html_icon_unit; esc_html_e( 'night', 'wp-hotel-booking' ); ?></span>
                </span>
    
                <?php } elseif ( $price_display === 'min_to_max' && $min !== $max ) { ?>
                    <span class="price_value price_min_to_max">
                    <?php echo hb_format_price( $min ) ?> - <?php echo hb_format_price( $max ) ?>
                        <span class="unit"><?php echo $html_icon_unit; esc_html_e( 'night', 'wp-hotel-booking' ); ?></span>
                </span>
    
                <?php } else { ?>
                    <span class="price_value price_min">
                    <?php echo hb_format_price( $min ) ?><span class="unit"><?php echo $html_icon_unit;  esc_html_e( 'night', 'wp-hotel-booking' ); ?></span>
                </span>
                <?php } ?>
    
            </div>
        <?php }

       do_action( 'WPHB/modules/single-room/after-preview-query' );
    }

}