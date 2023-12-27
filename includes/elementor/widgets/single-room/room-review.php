<?php

namespace Elementor;

use WPHB\HBGroupControlTrait;
use WPHB_Room;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Thim_Ekit_Widget_Room_Review extends Widget_Base
{
    use HBGroupControlTrait;

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

    public function get_base()
    {
        return basename(__FILE__, '.php');
    }

    protected function register_controls()
    {
        $this->_register_style_review();
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

        $this->register_style_typo_color_margin('room_review', '.hb-room-single__review');

        $this->end_controls_section();
    }

    protected function render()
    {
        do_action('WPHB/modules/single-room/before-preview-query');
        global $hb_room;
        $hb_room = \WPHB_Room::instance(get_the_ID());
        
        ?>
            <div class="hb-room-single__review">
                <?php echo hb_get_template( 'single-room-reviews.php' ); ?>
            </div>
        <?php

        do_action('WPHB/modules/single-room/after-preview-query');
    }
}