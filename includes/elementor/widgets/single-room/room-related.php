<?php

namespace Elementor;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Thim_Ekit_Widget_Room_Related extends Thim_Ekit_Widget_List_Room
{
    public function get_name()
    {
        return 'room-related';
    }

    public function get_title()
    {
        return esc_html__('Room Related', 'wp-hotel-booking');
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
        parent::register_controls();
    }

    public function render()
    {
        do_action('WPHB/modules/single-room/before-preview-query');


        do_action('WPHB/modules/single-room/after-preview-query');
    }
}