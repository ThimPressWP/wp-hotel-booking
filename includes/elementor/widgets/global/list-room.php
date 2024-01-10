<?php

namespace Elementor;

use Thim_EL_Kit\GroupControlTrait;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Thim_Ekit_Widget_List_Room extends Widget_Base {

	use GroupControlTrait;
	protected $current_permalink;

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
		$this->start_controls_section(
			'section_content_list',
			array(
				'label' => esc_html__( 'Settings', 'wp-hotel-booking' ),
			)
		);

		$this->add_control(
			'room_layout',
			array(
				'label'   => esc_html__( 'Select Layout', 'wp-hotel-booking' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => array(
					'default' => esc_html__( 'Default', 'wp-hotel-booking' ),
					'slider'  => esc_html__( 'Slider', 'wp-hotel-booking' ),
				),
			)
		);

		$this->add_control(
			'template_id',
			array(
				'label'   => esc_html__( 'Choose a template', 'wp-hotel-booking' ),
				'type'    => Controls_Manager::SELECT2,
				'default' => '0',
				'options' => array( '0' => esc_html__( 'None', 'wp-hotel-booking' ) ) + \Thim_EL_Kit\Functions::instance()->get_pages_loop_item( 'hb_room' ),
			)
		);

		$this->add_control(
			'cat_id',
			array(
				'label'   => esc_html__( 'Select Category', 'wp-hotel-booking' ),
				'default' => 'all',
				'type'    => Controls_Manager::SELECT,
				'options' => \Thim_EL_Kit\Elementor::get_cat_taxonomy( 'hb_room_type', array( 'all' => esc_html__( 'All', 'wp-hotel-booking' ) ), false ),
			)
		);

		$this->add_control(
			'number_rooms',
			array(
				'label'   => esc_html__( 'Number Room', 'wp-hotel-booking' ),
				'default' => '4',
				'type'    => Controls_Manager::NUMBER,
			)
		);

		$this->add_responsive_control(
			'columns',
			array(
				'label'          => esc_html__( 'Columns', 'wp-hotel-booking' ),
				'type'           => Controls_Manager::SELECT,
				'default'        => '3',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options'        => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
				),
				'selectors'      => array(
					'{{WRAPPER}}' => '--hb-room-list-columns: repeat({{VALUE}}, 1fr)',
				),
				'condition'      => array(
					'room_layout!' => 'slider',
				),
			)
		);

		$this->add_control(
			'orderby',
			array(
				'label'   => esc_html__( 'Order by', 'wp-hotel-booking' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'popular' => esc_html__( 'Popular', 'wp-hotel-booking' ),
					'recent'  => esc_html__( 'Date', 'wp-hotel-booking' ),
					'title'   => esc_html__( 'Title', 'wp-hotel-booking' ),
					'random'  => esc_html__( 'Random', 'wp-hotel-booking' ),
				),
				'default' => 'recent',
			)
		);

		$this->add_control(
			'order',
			array(
				'label'   => esc_html__( 'Order', 'wp-hotel-booking' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'asc'  => esc_html__( 'ASC', 'wp-hotel-booking' ),
					'desc' => esc_html__( 'DESC', 'wp-hotel-booking' ),
				),
				'default' => 'asc',
			)
		);

		$this->end_controls_section();
		$this->_register_style_layout();

		$this->_register_settings_slider(
			array(
				'room_layout' => 'slider',
			)
		);

		$this->_register_setting_slider_dot_style(
			array(
				'room_layout'             => 'slider',
				'slider_show_pagination!' => 'none',
			)
		);

		$this->_register_setting_slider_nav_style(
			array(
				'room_layout'       => 'slider',
				'slider_show_arrow' => 'yes',
			)
		);
    }

	protected function _register_style_layout() {
		$this->start_controls_section(
			'section_design_layout',
			array(
				'label'     => esc_html__( 'Layout', 'wp-hotel-booking' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'room_layout!' => 'slider',
				),
			)
		);

		$this->add_responsive_control(
			'column_gap',
			array(
				'label'     => esc_html__( 'Columns Gap', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 30,
				),
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--hb-room-list-column-gap: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'row_gap',
			array(
				'label'     => esc_html__( 'Rows Gap', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 30,
				),
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--hb-room-list-row-gap: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();
	}

    public function render()
    {
		$settings = $this->get_settings_for_display();

		$query_args = array(
			'post_type'           => 'hb_room',
			'posts_per_page'      => absint( $settings['number_rooms'] ),
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
		} else {
			echo '<div class="message-info">' . __( 'No data were found matching your selection, you need to create Post or select Category of Widget.', 'wp-hotel-booking' ) . '</div>';
		}

		wp_reset_postdata();
    }
    
}