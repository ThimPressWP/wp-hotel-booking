<?php

namespace Elementor;

use Elementor\Thim_Ekit_Widget_Archive_Post;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if (!class_exists('\Elementor\Thim_Ekit_Widget_Archive_Post')) {
    include THIM_EKIT_PLUGIN_PATH . 'inc/elementor/widgets/archive-post/archive-post.php';
}

class Thim_Ekit_Widget_Archive_Room extends Thim_Ekit_Widget_Archive_Post {

    public function get_name() {
		return 'wphb-archive-room';
	}

    public function get_title() {
		return esc_html__( 'Archive Room', 'wp-hotel-booking' );
	}

	public function get_icon() {
		return 'thim-eicon eicon-archive-posts';
	}

	public function get_categories() {
		return array( \WPHB\Elementor::CATEGORY_ARCHIVE_ROOM );
	}

    public function get_keywords() {
		return array( 'room', 'archive' );
	}

	public function get_help_url() {
		return '';
	}

    protected function register_controls() {
        parent::register_options();

        parent::register_style_topbar();
        parent::register_style_layout();

        parent::register_navigation_archive();
        parent::register_style_pagination_archive('.hb-room-archive__pagination');

        $this->update_control(
			'build_loop_item',
			array(
				'label'     => esc_html__( 'Build Loop Item', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before',
			)
		);

        $this->update_control(
			'template_id',
			array(
				'label'   => esc_html__( 'Choose a template', 'wp-hotel-booking' ),
				'type'    => Controls_Manager::SELECT2,
				'default' => '0',
				'options' => array( '0' => esc_html__( 'None', 'wp-hotel-booking' ) ) + \Thim_EL_Kit\Functions::instance()->get_pages_loop_item( 'hb_room' ),
				'condition'   => array(
					'build_loop_item' => 'yes',
				),
			)
		);
    }

    public function render(){

        global $wp_query;

		$query_vars = $wp_query->query_vars;

		$query_vars = apply_filters( 'WPHB/modules/archive_room/query_posts/query_vars', $query_vars );

		if ( $query_vars !== $wp_query->query_vars ) {
			$rooms = new \WP_Query( $query_vars );
		} else {
			$rooms = $wp_query;
		}

		if ( ! $rooms->found_posts ) {
            echo '<p class="message message-error">' . esc_html__( 'No room found !', 'wp-hotel-booking' ) . '</p>';
			return;
		}

        $settings = $this->get_settings_for_display(); 
        $class_item  = 'hb-room-archive__article'; ?>

        <div class="hb-room-archive">
            <?php $this->render_topbar( $rooms, $settings ); ?>

            <div class="hb-room-archive__inner">
                <?php 
                while ( $rooms->have_posts() ) {
                    $rooms->the_post();
                    $this->current_permalink = get_permalink(); ?>

                    <div <?php post_class( array( $class_item ) ); ?>>
                        <?php if ( $settings['build_loop_item'] == 'yes' ) { 
                            \Thim_EL_Kit\Utilities\Elementor::instance()->render_loop_item_content( $settings['template_id'] ); 
                        } else {
                            echo hb_get_template('content-room.php');
                        } ?>
                    </div>
                <?php } ?>
            </div>

            <?php $this->render_loop_footer( $rooms, $settings ); ?>       
        </div>

        <?php
    }
}