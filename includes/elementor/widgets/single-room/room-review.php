<?php

namespace Elementor;

use WPHB\HBGroupControlTrait;
use Thim_EL_Kit\GroupControlTrait;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Thim_Ekit_Widget_Room_Review extends Widget_Base
{
    use HBGroupControlTrait;
    use GroupControlTrait;

    public function get_name()
    {
        return 'room-review';
    }

    public function get_title()
    {
        return esc_html__('Room Review', 'wp-hotel-booking');
    }

    public function get_icon()
    {
        return 'thim-eicon eicon-review';
    }

    public function get_categories()
    {
        return array(\WPHB\Elementor::CATEGORY_SINGLE_ROOM);
    }

    protected function register_controls()
    {
        $this->_register_style_review_base();
        $this->_register_style_review();
        $this->_register_style_button();
    }

    protected function _register_style_review_base()
    {
        $this->start_controls_section(
            'section_base_title',
            array(
                'label' => esc_html__('Title', 'wp-hotel-booking'),
                'tab'   => Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_control(
			'show_avatar',
			[
				'label'        => esc_html__( 'Avatar', 'wp-hotel-booking' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => esc_html__( 'Show', 'wp-hotel-booking' ),
				'label_off'    => esc_html__( 'Hide', 'wp-hotel-booking' ),
				'return_value' => 'yes',
			]
		);

        $this->register_style_typo_color_margin('room_base_title_review', '.hb-room-single__review h2, .hb-room-single__review .comment-reply-title');

        $this->end_controls_section();
    }

    protected function _register_style_review()
    {
        $this->start_controls_section(
            'section_review',
            array(
                'label' => esc_html__('Content', 'wp-hotel-booking'),
                'tab'   => Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_control(
			'section_review_color_star',
			array(
				'label'     => esc_html__( 'Star Color', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					"{{WRAPPER}} .hb-room-single__review" => '--room-single-rating-star-color: {{VALUE}};',
				),
			)
		);

        $this->add_responsive_control(
			'radius_item',
			[
				'label'      => esc_html__( 'Radius Item', 'wp-hotel-booking' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .hb-room-single__review' => '--border-radius-item: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_control(
			'user_title', [
				'label'     => esc_html__( 'Title', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
			]
		);

        $this->register_style_typo_color_margin('room_title_review', '#reviews #comments .commentlist .comment .hb-room-review-title');

        $this->add_control(
			'user_comment', [
				'label'     => esc_html__( 'User Comment', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
			]
		);

        $this->register_style_typo_color_margin('room_comment_review', '#reviews #comments .commentlist .comment .description p');

        $this->end_controls_section();
    }

    protected function _register_style_button()
    {
        $this->start_controls_section(
            'section_button',
            array(
                'label' => esc_html__('Button', 'wp-hotel-booking'),
                'tab'   => Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_control(
			'button_popup', [
				'label'     => esc_html__( 'Popup', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
			]
		);

        $this->register_button_style( 'button_popup_review', '.review-top-section .header button' );

        $this->end_controls_section();
    }

    protected function render()
    {
        do_action('WPHB/modules/single-room/before-preview-query');

        $settings        = $this->get_settings_for_display();
        global $hb_room;
        $hb_room = \WPHB_Room::instance(get_the_ID());
        if (empty($hb_room)) {
            return;
        }
    
        $extra_class = '';

        if ( $settings['show_avatar'] != 'yes' ) {
            $extra_class = ' hide-avatar';
        }
        ?>
            <div class="hb-room-single__review <?php echo esc_attr($extra_class) ?>">
                <?php 
                echo comments_template(); 
                ?>
            </div>
        <?php

        do_action('WPHB/modules/single-room/after-preview-query');
    }

}