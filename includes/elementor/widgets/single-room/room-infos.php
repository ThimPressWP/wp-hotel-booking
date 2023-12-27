<?php

namespace Elementor;

use WPHB\HBGroupControlTrait;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Thim_Ekit_Widget_Room_Infos extends Widget_Base
{
    use HBGroupControlTrait;

    public function get_name()
    {
        return 'room-infos';
    }

    public function get_title()
    {
        return esc_html__('Room Additional Information', 'wp-hotel-booking');
    }

    public function get_icon()
    {
        return 'thim-eicon eicon-single-post';
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
        $this->_register_style_infos();
    }

    protected function _register_style_infos()
    {
        $this->start_controls_section(
            'section_infos',
            array(
                'label' => esc_html__('Content', 'wp-hotel-booking'),
                'tab'   => Controls_Manager::TAB_STYLE,
            )
        );

        $this->register_style_typo_color_margin('additional_information', '.hb-room-single__additional-information');

        $this->end_controls_section();
    }

    protected function render()
    {
        do_action('WPHB/modules/single-room/before-preview-query');

        $room = \WPHB_Room::instance(get_the_ID());
        if (empty($room)) {
            return;
        }

        $infos = get_post_meta( $room->ID, '_hb_room_addition_information', true );

        if (!empty($infos)) {
            ?>
            <div class="hb-room-single__additional-information">
                <?php echo $room->get_addition_information(); ?>
            </div>
            <?php
        }

        do_action('WPHB/modules/single-room/after-preview-query');
    }
}