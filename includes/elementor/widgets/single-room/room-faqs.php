<?php

namespace Elementor;

use WPHB\HBGroupControlTrait;
use Elementor\Thim_Ekit_Widget_Accordion;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

if ( ! class_exists( '\Elementor\Thim_Ekit_Widget_Accordion' ) ) {
	include THIM_EKIT_PLUGIN_PATH . 'inc/elementor/widgets/global/accordion.php';
}

class Thim_Ekit_Widget_Room_Faqs extends Thim_Ekit_Widget_Accordion
{
    use HBGroupControlTrait;

    public function get_name()
    {
        return 'room-faqs';
    }

    public function get_title()
    {
        return esc_html__('Room FAQs', 'wp-hotel-booking');
    }

    public function get_icon()
    {
        return 'thim-eicon eicon-accordion';
    }

    public function get_categories()
    {
        return array(\WPHB\Elementor::CATEGORY_SINGLE_ROOM);
    }

    protected function register_controls()
    {
        $this->start_controls_section(
			'section_tabs',
			[
				'label' => __( 'FAQs', 'wp-hotel-booking' ),
			]
		);

        $this->add_control(
			'icon',
			[
				'label'       => esc_html__( 'Icon', 'wp-hotel-booking' ),
				'type'        => Controls_Manager::ICONS,
				'label_block' => false,
				'skin'        => 'inline',
				'default'     => [
					'value'   => 'fas fa-plus',
					'library' => 'fa-solid',
				],
				'recommended' => [
					'fa-solid'   => [
						'chevron-down',
						'angle-down',
						'angle-double-down',
						'caret-down',
						'caret-square-down',
					],
					'fa-regular' => [
						'caret-square-down',
					],
				],
				'separator'   => 'before',
			]
		);

		$this->add_control(
			'icon_active',
			[
				'label'       => esc_html__( 'Active Icon', 'wp-hotel-booking' ),
				'type'        => Controls_Manager::ICONS,
				'label_block' => false,
				'default'     => [
					'value'   => 'fas fa-minus',
					'library' => 'fa-solid',
				],
				'recommended' => [
					'fa-solid'   => [
						'chevron-up',
						'angle-up',
						'angle-double-up',
						'caret-up',
						'caret-square-up',
					],
					'fa-regular' => [
						'caret-square-up',
					],
				],
				'skin'        => 'inline',
				'condition'   => [
					'icon[value]!' => '',
				],
			]
		);

        $this->end_controls_section();

        if ( function_exists('register_controls_style_item') ){
            $this->register_controls_style_item();
        }
       
        if ( function_exists('register_controls_style_title') ){
		    $this->register_controls_style_title();
        }
        
        if ( function_exists('register_controls_style_content') ){
		    $this->register_controls_style_content();
        }
    }

    protected function render()
    {
        do_action('WPHB/modules/single-room/before-preview-query');

        $settings    	= $this->get_settings_for_display();
        $room = \WPHB_Room::instance(get_the_ID());
        if (empty($room)) {
            return;
        }

        $faqs = get_post_meta( $room->ID, '_wphb_room_faq', true );

        if (!empty($faqs)) {
            ?>
            <div class="hb-room-single__faqs thim-accordion-sections">
            <?php
                foreach ( $faqs as $faq ) : ?>
                    <div class="accordion-section">			
                        <div class="accordion-title" aria-selected="false">
                            <?php echo esc_html( $faq[0] );
                            if ( ! empty( $settings['icon'] ) || ! empty( $settings['icon_active'] ) ) {
								?>
								<span class="accordion-icon">
									<span
										class="accordion-icon-closed"><?php Icons_Manager::render_icon( $settings['icon'] ); ?></span>
									<span
										class="accordion-icon-opened"><?php Icons_Manager::render_icon( $settings['icon_active'] ); ?></span>
								</span>
							<?php } ?>
                        </div>
                        <div class="accordion-content">
                            <?php echo wp_kses_post( $faq[1] ); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php
            $this->_add_js_accordion();
        }

        do_action('WPHB/modules/single-room/after-preview-query');
    }

    protected function _add_js_accordion()
    {
        ?>
        <script type="text/javascript">
        jQuery(document).ready(function ($) {
            const contents = $('.accordion-section').find('.accordion-content');
            contents.hide();
            $('.accordion-title').on('click',function (e) {
                e.preventDefault();
                const content = $(this).parent('.accordion-section').find('.accordion-content');
                if ( content.hasClass('active') ) {
                    content.removeClass('active')
                    $(this).removeClass('active');
                    $(this).attr('aria-selected', false);
                    content.fadeOut(300);;
                } else {
                    content.addClass('active');
                    $(this).addClass('active');
                    $(this).attr('aria-selected', true);
                    content.fadeIn(300);;
                }
            });
        });
        </script>
        <?php
    }
}