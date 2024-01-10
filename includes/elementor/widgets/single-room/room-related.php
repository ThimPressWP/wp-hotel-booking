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
        return 'thim-eicon eicon-product-related';
    }

    public function get_categories()
    {
        return array(\WPHB\Elementor::CATEGORY_SINGLE_ROOM);
    }

    protected function register_controls()
    {
        parent::register_controls();

        $this->update_control(
			'cat_id',
			array(
				'label'   => esc_html__( 'Related By', 'wp-hotel-booking' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'category',
				'options' => array(
					'category' => esc_html__( 'Category', 'wp-hotel-booking' ),
					'tags'     => esc_html__( 'Tags', 'wp-hotel-booking' ),
				),
			)
		);
    }

    public function render()
    {
        do_action('WPHB/modules/single-room/before-preview-query');

        $settings = $this->get_settings_for_display();

		$query_args = array(
			'post_type'           => 'hb_room',
			'posts_per_page'      => absint( $settings['number_rooms'] ),
			'order'               => ( 'asc' == $settings['order'] ) ? 'asc' : 'desc',
			'ignore_sticky_posts' => true,
			'post__not_in'        => array( get_the_ID() ),
			'category__in'        => wp_get_post_categories( get_the_ID() )
		);

        if ( $settings['cat_id'] == 'tags' ) {
			$tags = wp_get_post_tags( get_the_ID() );
			if ( $tags ) {
				$query_args['tag__in'] = array( $tags[0]->term_id );
			} else {
				// get category if not have tags
				$query_args['category__in'] = wp_get_post_categories( get_the_ID() );
			}
		} else {
			$query_args['category__in'] = wp_get_post_categories( get_the_ID() );
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

		$class       = 'hb-room-list';
		$class_inner = 'hb-room-list__inner';
		$class_item  = 'hb-room-list__article';

		if ( $query_vars->have_posts() ) { 
			if ( isset( $settings['room_layout'] ) && $settings['room_layout'] == 'slider' ) {
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
						$this->current_permalink = get_permalink();?>

						<div <?php post_class( array( $class_item ) ); ?>>
							<?php  
								\Thim_EL_Kit\Utilities\Elementor::instance()->render_loop_item_content( $settings['template_id'] ); 
							?>
						</div>
					<?php } ?>
				</div>
			</div>

			<?php
		} 

		wp_reset_postdata();

        do_action('WPHB/modules/single-room/after-preview-query');
    }
}