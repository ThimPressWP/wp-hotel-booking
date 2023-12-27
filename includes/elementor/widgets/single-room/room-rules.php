<?php

namespace Elementor;

use WPHB\HBGroupControlTrait;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Thim_Ekit_Widget_Room_Rules extends Widget_Base
{
    use HBGroupControlTrait;

    public function get_name()
    {
        return 'room-rules';
    }

    public function get_title()
    {
        return esc_html__('Room Rules', 'wp-hotel-booking');
    }

    public function get_icon()
    {
        return 'thim-eicon eicon-integration';
    }

    public function get_categories()
    {
        return array(\WPHB\Elementor::CATEGORY_SINGLE_ROOM);
    }

    public function get_base()
    {
        return basename(__FILE__, '.php');
    }

    protected function register_controls()
    {
        $this->_register_style_rules();
    }

    protected function _register_style_rules()
    {
        $this->start_controls_section(
            'section_rules',
            array(
                'label' => esc_html__('Content', 'wp-hotel-booking'),
                'tab'   => Controls_Manager::TAB_STYLE,
            )
        );

        $this->register_style_typo_color_margin('room_rules', '.hb-room-single__rules');

        $this->end_controls_section();
    }

    protected function render()
    {
        do_action('WPHB/modules/single-room/before-preview-query');

        $room = \WPHB_Room::instance(get_the_ID());
        if (empty($room)) {
            return;
        }

        $rules = get_post_meta( $room->ID, '_hb_wphb_rule_room', true );

        if (!empty($rules)) {
            ?>
            <div class="hb-room-single__rules">
                <?php echo $room->get_rules(); ?>
            </div>
            <?php
        }

        do_action('WPHB/modules/single-room/after-preview-query');
    }
}