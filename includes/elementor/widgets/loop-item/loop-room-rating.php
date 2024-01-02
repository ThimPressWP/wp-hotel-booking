<?php

namespace Elementor;

use Thim_EL_Kit\Utilities\Widget_Loop_Trait;
use WPHB\HBGroupControlTrait;

defined('ABSPATH') || exit;

if (!class_exists('\Elementor\Thim_Ekit_Widget_Loop_Product_Ratting')) {
    include THIM_EKIT_PLUGIN_PATH . 'inc/elementor/widgets/loop-item/loop-product-ratting.php';
}

class Thim_Ekit_Widget_Loop_Room_Rating extends Thim_Ekit_Widget_Loop_Product_Ratting
{
    use HBGroupControlTrait;
    use Widget_Loop_Trait;

    public function get_name()
    {
        return 'loop-room-rating';
    }

    public function get_title()
    {
        return esc_html__('Room Rating', 'wp-hotel-booking');
    }

    public function get_icon()
    {
        return 'thim-eicon eicon-product-rating';
    }

    protected function get_html_wrapper_class() {
		return 'thim-ekits-loop-ratting';
	}

    public function get_keywords() {
		return array( 'room', 'ratting' );
	}

    protected function register_controls()
    {
        $this->start_controls_section(
			'section_tabs',
			[
				'label' => __('Rating', 'wp-hotel-booking'),
			]
		);

        $this->add_control(
			'layout',
			array(
				'label'   => esc_html__( 'Select Layout', 'wp-hotel-booking' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'star_number',
				'options' => array(
                    'star'          => esc_html__( 'Star', 'wp-hotel-booking' ),
                    'star_number'   => esc_html__( 'Star an number', 'wp-hotel-booking' ),
				),
			)
		);

        $this->end_controls_section();
        parent::register_controls();

        $this->update_control(
			'star_color',
			array(
				'label'     => esc_html__( 'Star Color', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}.thim-ekits-loop-ratting .star-rating span:before, {{WRAPPER}}.thim-ekits-loop-ratting i' => 'color: {{VALUE}}',
				),
			)
		);

        $this->update_control(
			'star_size',
			array(
				'label'     => esc_html__( 'Star Size', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em' ],
				'default'   => array(
					'unit' => 'px',
				),
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}.thim-ekits-loop-ratting .star-rating, {{WRAPPER}}.thim-ekits-loop-ratting i' => 'font-size: {{SIZE}}{{UNIT}}',
				),
			)
		);
        $this->_register_style_average_rating();
    }

    protected function _register_style_average_rating()
    {
        $this->start_controls_section(
            'section_average_rating',
            array(
                'label' => esc_html__('Average Rating', 'wp-hotel-booking'),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition'     => [
					'layout' => 'star_number',
				]
            )
        );

        $this->register_style_typo_color_margin('room_average_rating', '.average-rating');

        $this->end_controls_section();
    }

    public function render()
    {
        $settings = $this->get_settings_for_display();

        $hb_room = \WPHB_Room::instance(get_the_ID());
        if (empty($hb_room)) {
            return;
        }
        $rating_total   = round($hb_room->average_rating(), 1); 

        if ( $settings['layout'] == 'star_number' ) { ?>
            <i class="fas fa-star"></i>
            <span class="average-rating"><?php printf( '%s/5', $rating_total ); ?></span>
        <?php } else {
            $rating = $hb_room->average_rating();
            if ( comments_open( $hb_room->ID ) ) { ?>
                <div class="rating">
                    <?php if ( $rating ) { ?>
                        <div itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating" class="star-rating"
                            title="<?php echo esc_html( sprintf( __( 'Rated %d out of 5', 'wp-hotel-booking' ), $rating ) ); ?>">
                            <span style="width:<?php echo ( ( $rating / 5 ) * 100 ); ?>%"></span>
                        </div>
                    <?php } ?>
                </div>
            <?php }  
        }

        if ( $settings['show_number'] == 'yes' ) {
			$count_review = $hb_room->get_review_count();
			if($count_review > 0){
				echo '<span class="number-ratting">(';
				printf( _n( '%s review', '%s reviews', $count_review, 'thim-elementor-kit' ), $count_review );
				echo ')</span>';
			}
		}
    }
}
