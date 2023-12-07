<?php

namespace Elementor;

use Thim_EL_Kit\GroupControlTrait;
use Elementor\Widget_Base;
use Elementor\Icons_Manager;
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Thim_Ekit_Widget_Search_Room extends Widget_Base {
    use GroupControlTrait;

    public function get_name() {
		return 'wphb-search-room';
	}

	public function get_title() {
		return esc_html__( 'Search Room', 'wp-hotel-booking' );
	}

	public function get_icon() {
		return 'thim-eicon eicon-taxonomy-filter';
	}

	public function get_categories() {
		return array( \WPHB\Elementor::CATEGORY );
	}

    public function get_keywords() {
		return array( 'search', 'room' );
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

        $this->add_control(
			'layout',
			array(
				'label'     => esc_html__( 'Layout', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'base',
				'options'   => array(
					'base'      => esc_html__( 'Base', 'wp-hotel-booking' ),
					'multidate' => esc_html__( 'Multidate', 'wp-hotel-booking' ),
				),
			)
		);

        $this->add_control(
			'label_adults',
			[
				'label'         => esc_html__( 'Label Adults', 'wp-hotel-booking' ),
				'type'          => Controls_Manager::TEXT,
                'placeholder'   => esc_html__( 'Add your text here', 'wp-hotel-booking' ),
				'default'       => esc_html__( 'Adults', 'wp-hotel-booking' ),
                'condition'     => [
					'layout' => 'multidate',
				],
			]
		);

        $this->add_control(
			'label_child',
			[
				'label'     => esc_html__( 'Label Children', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::TEXT,
                'placeholder'   => esc_html__( 'Add your text here', 'wp-hotel-booking' ),
				'default'       => esc_html__( 'Children', 'wp-hotel-booking' ),
                'condition'     => [
					'layout' => 'multidate',
				],
			]
		);

        $this->add_control(
			'text_submit',
			[
				'label'     => esc_html__( 'Text Submit', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::TEXT,
                'placeholder'   => esc_html__( 'Add your text here', 'wp-hotel-booking' ),
				'default'       => esc_html__( 'Check Availability', 'wp-hotel-booking' ),
                'condition'     => [
					'layout' => 'multidate',
				],
			]
		);

        $this->add_control(
			'show_icon',
			array(
				'label'   => esc_html__( 'Show Icon', 'wp-hotel-booking' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
                'condition'     => [
					'layout' => 'multidate',
				],
			)
		);

        $this->add_control(
			'icon_submit',
			[
				'label'         => esc_html__( 'Select Icon', 'wp-hotel-booking' ),
				'type'          => Controls_Manager::ICONS,
                'skin'          => 'inline',
                'label_block'   => false,
				'condition'     => [
                    'layout' => 'multidate',
					'show_icon' => 'yes',
				],
			]
		);

        $this->end_controls_section();
    }

    protected function render() {
        $settings    = $this->get_settings_for_display();

        if ( isset($settings['layout']) && $settings['layout'] == 'multidate' ) {
            ?>
            <div class="hotel-booking-search">
                <?php $this->wphb_multidate( $settings ); ?>
            </div>
           <?php
        }else {
            hb_get_template( 'search/search-form.php', $settings );
        }
    }

    protected function wphb_multidate( $settings ){
        $datetime = new \DateTime('NOW');
        $tomorrow = new \DateTime('tomorrow');
        $format = get_option('date_format');

        $check_in_date = $datetime->format($format);
        $check_out_date = $tomorrow->format($format);
        $adults         = hb_get_request('adults', '1');
        $max_child      = hb_get_request('max_child', '0');

        $search         = hb_get_page_permalink( 'search' );
        $page_search    = hb_get_page_id('search');
        $uniqid         = uniqid();

        $label_adults   = $settings['label_adults'] ?? esc_html__('Adults', 'wp-hotel-booking');
        $label_child    = $settings['label_child'] ?? esc_html__('Children', 'wp-hotel-booking');
        $text_submit    = $settings['text_submit'] ?? esc_html__('Check Availability', 'wp-hotel-booking');
        ?>
        <form <?php echo is_page($page_search) ? 'id="hb-form-search-page" ' : ''; ?> name="hb-search-form" action="<?php echo hb_get_url(); ?>" class="layout-multidate hb-search-form-<?php echo esc_attr($uniqid); ?>">
            <ul class="hb-form-table">
                <?php 
                wp_enqueue_script( 'wp-daterangepicker');
                ?>
                <input type="text" id="multidate" class="multidate" value="<?php echo esc_attr($check_in_date) ?>" readonly />
                <li class="hb-form-field hb-form-check-in-check-out">
                    <div class="label"><?php echo esc_html__('Check-in, Check-out', 'wp-hotel-booking') ?> <span>*</span></div>
                    <div class="hb-form-field-input hb_input_field">
                        <input type="text" name="check_in_date" id="check_in_date_<?php echo  esc_attr($uniqid) ?>" class="check-date" value="<?php echo esc_attr($check_in_date) ?>" readonly />
                    </div>
                    <div class="hb-form-field-input hb_input_field">
                        <input type="text" name="check_out_date" id="check_out_date_<?php echo esc_attr($uniqid) ?>" class="check-date" value="<?php echo esc_attr($check_out_date) ?>" readonly />
                    </div>
                </li>
                <li class="hb-form-field hb-form-number">
                    <div class="label"><?php echo $label_adults; ?></div>
                    <div id="adults" class="hb-form-field-input hb_input_field">
                        <input type="text" id="number" class="adults-input" value="<?php echo esc_attr($adults) ?>" readonly />
                        <span><?php echo $label_adults; ?></span>
                    </div>
                    <div class="hb-form-field-list nav-guest">
                        <span class="name"><?php echo esc_html__( 'Adults', 'wp-hotel-booking' ) ?></span>
                        <div class="number-box">
                            <span class="number-icons goDown"><i class="ion-minus"></i></span>
                            <span class="hb-form-field-input hb-guest-field guests-number">
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
                            <span class="number-icons goUp"><i class="ion-plus"></i></span>
                        </div>
                    </div>
                </li>

                <li class="hb-form-field hb-form-number">
                    <div class="label"><?php echo $label_child; ?></div>
                    <div id="child" class="hb-form-field-input hb_input_field">
                        <input type="text" id="number" class="child-input" value="<?php echo esc_attr($max_child) ?>" readonly />
                        <span><?php echo $label_child; ?></span>
                    </div>
                    <div class="hb-form-field-list nav-child">
                        <span class="name"><?php echo esc_html__( 'Children', 'wp-hotel-booking' ) ?></span>
                        <div class="number-box">
                            <span class="number-icons goDown"><i class="ion-minus"></i></span>
                            <span class="hb-form-field-input hb-guest-field child-number">
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
                            <span class="number-icons goUp"><i class="ion-plus"></i></span>
                        </div>
                    </div>
                </li>
            </ul>
            <?php wp_nonce_field('hb_search_nonce_action', 'nonce'); ?>
            <input type="hidden" name="hotel-booking" value="results" />
            <input type="hidden" name="action" value="hotel_booking_parse_search_params" />
            <p class="hb-submit">
                <button type="submit" class="wphb-button">
                    <?php if ( $settings['text_submit'] != '' ) :?>
                        <?php echo $text_submit; ?>
                    <?php endif; ?>
                    <?php if ( $settings['icon_submit'] ) { 
                        Icons_Manager::render_icon( $settings['icon_submit'], array( 'aria-hidden' => 'true' ) );        
                    } ?>
                </button>
            </p>
        </form>
        <?php
    }
}