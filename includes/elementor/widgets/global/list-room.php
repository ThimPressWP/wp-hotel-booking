<?php

namespace Elementor;

use Elementor\Thim_Ekit_Widget_List_Blog;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if (!class_exists('\Elementor\Thim_Ekit_Widget_List_Blog')) {
    include THIM_EKIT_PLUGIN_PATH . 'inc/elementor/widgets/global/list-blog.php';
}

class Thim_Ekit_Widget_List_Room extends Thim_Ekit_Widget_List_Blog {

    public function get_name()
    {
        return 'list-room';
    }

    public function get_title()
    {
        return esc_html__('List Room', 'wp-hotel-booking');
    }

    public function get_icon()
    {
        return 'thim-eicon eicon-posts-group';
    }

    public function get_categories() 
    {
		return array( \Thim_EL_Kit\Elementor::CATEGORY );
	}

    public function get_keywords() {
		return array( 'room', 'list' );
	}

    protected function register_controls() 
    {
        parent::register_controls();

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

		$this->update_control(
			'cat_id',
			array(
				'label'   => esc_html__( 'Select Category', 'wp-hotel-booking' ),
				'default' => 'all',
				'type'    => Controls_Manager::SELECT,
				'options' => \Thim_EL_Kit\Elementor::get_cat_taxonomy( 'hb_room_type', array( 'all' => esc_html__( 'All', 'wp-hotel-booking' ) ), false ),
			)
		);
    }

    public function render()
    {
		$settings = $this->get_settings_for_display();

		$query_args = array(
			'post_type'           => 'hb_room',
			'posts_per_page'      => absint( $settings['number_posts'] ),
			'order'               => ( 'asc' == $settings['order'] ) ? 'asc' : 'desc',
			'ignore_sticky_posts' => true,
		);

		if ( $settings['cat_id'] && $settings['cat_id'] != 'all' ) {
			$tax_query = array(
				array(
					'taxonomy' => 'hb_room_type',
					'field'    => 'slug',
					'terms'    => $settings['cat_id'],
				),
			);
			$query_args['tax_query'] = $tax_query;
		}

		switch ( $settings['orderby'] ) {
			case 'recent':
				$query_args['orderby'] = 'post_date';
				break;
			case 'title':
				$query_args['orderby'] = 'post_title';
				break;
			case 'popular':
				$query_args['orderby'] = 'comment_count';
				break;
			default: // random
				$query_args['orderby'] = 'rand';
		}
		$query_vars = new \WP_Query( $query_args );

		$class       = 'thim-ekits-post';
		$class_inner = 'thim-ekits-post__inner';
		$class_item  = 'thim-ekits-post__article';

		if ( $query_vars->have_posts() ) { // It's the global `wp_query` it self. and the loop was started from the theme.
			if ( isset( $settings['blog_layout'] ) && $settings['blog_layout'] == 'slider' ) {
				$swiper_class = \Elementor\Plugin::$instance->experiments->is_feature_active( 'e_swiper_latest' ) ? 'swiper' : 'swiper-container';
				$class       .= ' thim-ekits-sliders ' . $swiper_class;
				$class_inner  = 'swiper-wrapper';
				$class_item  .= ' swiper-slide';

				$this->render_nav_pagination_slider( $settings );
			}
			?>
			<div class="<?php echo esc_attr( $class ); ?>">
				<div class="<?php echo esc_attr( $class_inner ); ?>">
					<?php
					while ( $query_vars->have_posts() ) {
						$query_vars->the_post();
						$this->current_permalink = get_permalink();
						parent::render_post( $settings, $class_item );
					}
					?>
				</div>
			</div>

			<?php
		} else {
			echo '<div class="message-info">' . __( 'No data were found matching your selection, you need to create Post or select Category of Widget.', 'thim-elementor-kit' ) . '</div>';
		}

		wp_reset_postdata();
    }
    
}