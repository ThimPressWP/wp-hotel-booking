<?php

namespace Elementor;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Thim_Ekit_Widget_Room_Booking extends Widget_Base
{
    public function get_name()
    {
        return 'room-booking';
    }

    public function get_title()
    {
        return esc_html__('Room Booking', 'wp-hotel-booking');
    }

    public function get_icon()
    {
        return 'thim-eicon eicon-product-add-to-cart';
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
    }

    protected function render()
    {

    }
}
