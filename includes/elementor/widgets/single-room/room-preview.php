<?php

namespace Elementor;

use Thim_EL_Kit\GroupControlTrait;
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Thim_Ekit_Widget_Room_Preview extends Widget_Base
{
    use GroupControlTrait;

    public function get_name()
    {
        return 'room-preview';
    }

    public function get_title()
    {
        return esc_html__('Room Preview', 'wp-hotel-booking');
    }

    public function get_icon()
    {
        return 'thim-eicon eicon-preview-medium';
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
				'label' => __('Preview', 'wp-hotel-booking'),
			]
		);
        $this->add_control(
            'icon_preview',
            [
                'label'         => esc_html__( 'Icon Preview', 'wp-hotel-booking' ),
				'type'          => Controls_Manager::ICONS,
                'skin'          => 'inline',
                'label_block'   => false,
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'style_icon',
            [
                'label' => esc_html__('Icon', 'wp-hotel-booking'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->register_button_style( 'room_preview', '.room-preview' );

        $this->end_controls_section();
    }

    protected function render()
    {
        $hb_room = \WPHB_Room::instance(get_the_ID());
        if (empty($hb_room)) {
            return;
        }

        $settings        = $this->get_settings_for_display(); ?>
        <div id="hb_room_images"> 
        <?php if ( $hb_room->is_preview ) :
			$preview = get_post_meta( $hb_room->ID, '_hb_room_preview_url', true );
			?>
			<span class="room-preview" data-preview="<?php echo ! empty( $preview ) ? esc_attr( $preview ) : ''; ?>">
                <?php Icons_Manager::render_icon( $settings['icon_preview'],['aria-hidden' => 'true']); ?>
            </span>
		<?php endif; ?>
        </div>
        
        <?php
    }
}