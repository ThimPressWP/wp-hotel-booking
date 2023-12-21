<?php

namespace Elementor;

use Thim_EL_Kit\GroupControlTrait;
use WPHB\HBGroupControlTrait;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Thim_Ekit_Widget_Room_Thumb extends Widget_Base {
    use GroupControlTrait;

    public function get_name() {
		return 'room-thumb';
	}

	public function get_title() {
		return esc_html__( 'Room Thumbnail Gallery', 'wp-hotel-booking' );
	}

	public function get_icon() {
		return 'thim-eicon eicon-gallery-group';
	}

	public function get_categories() {
		return array( \WPHB\Elementor::CATEGORY_SINGLE_ROOM );
	}

	public function get_base() {
		return basename( __FILE__, '.php' );
	}

    protected function register_controls() {
        $this->start_controls_section(
			'section_tabs',
			[
				'label' => __( 'Thumbnail Gallery', 'wp-hotel-booking' ),
			]
		);

        $this->add_control(
			'layout_style',
			array(
				'label'   => esc_html__( 'Select Layout', 'wp-hotel-booking' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'gallery',
				'options' => array(
                    'gallery_popup'      => esc_html__( 'Gallery and Popup Slide', 'wp-hotel-booking' ),
					'slider'             => esc_html__( 'Only Slide', 'wp-hotel-booking' ),
                    'gallery_slide'            => esc_html__( 'Gallery and Slide', 'wp-hotel-booking' ),
				),
			)
		);

        $this->end_controls_section();
    }

    protected function render() {
        wp_enqueue_script( 'magnific-popup' );

        do_action( 'WPHB/modules/single-room/before-preview-query' );

        $settings    	= $this->get_settings_for_display();
        $galleries = get_post_meta( get_the_ID(), '_hb_gallery', true );

        if ( isset($settings['layout_style']) && $settings['layout_style'] == 'gallery_popup' ){ ?>

            <div class="hb-room-thumbnail-gallery-popup"> 
                <?php $this->_render_thumb_gallery_and_popup_slide($galleries); ?> 
            </div>

            <?php
        }elseif ( isset($settings['layout_style']) && $settings['layout_style'] == 'gallery_slide' ){ ?>
            
            <div class="hb-room-thumbnail-gallery-slide"> 
                <?php $this->_render_thumb_gallery_and_slide($galleries); ?> 
            </div>

            <?php
        }else { ?>

            <div class="hb-room-thumbnail-slide"> 
                <?php $this->_render_thumb_slide($galleries); ?> 
            </div>

            <?php
        }
    
        do_action( 'WPHB/modules/single-room/after-preview-query' );
    }

    protected function _render_thumb_gallery_and_popup_slide($galleries){

        if( !empty($galleries) ) {
            $gallery_img = $galleries;
            $class = '';
            echo '<ul class="first-gallery">';
            foreach ( $gallery_img as $key => $image_id ) {
                if ( $key == 0 ) {
                    $image_size = array( 960, 700 );
                } else {
                    $image_size = array( 480, 350 );
                }
                $images   = wp_get_attachment_image( $image_id, $image_size );
                $full_url = wp_get_attachment_image_url( $image_id, 'full' );

                if ( $key == 4 ) {
                    $class       = ' more';
                    $count_image = count( $gallery_img ) - 5;
                    $images      .= ( $count_image > 0 ) ? '<span>+' . $count_image . ' ' . esc_html__( 'photos', 'realpress' ) . '</span>' : '';
                }

                if ( $key > 4 ) {
                    $class  = ' hidden';
                    $images = '';
                }

                echo '<li class="realpress-gallery-item' . $class . '"><a href="' . esc_url( $full_url ) . '" class="gallery-img-item">' . $images . '</a></li>';

                if ( count( $gallery_img ) > 1 && $key == 0 ) {
                    echo '</ul><ul class="thim-gallery-thumbnails d-flex">';
                }
            }
		echo '</ul>';
        }else {
            $full_url = wp_get_attachment_image_url( get_the_ID(), 'full' );
            ?>
				<div class="hb-room-item-post-thumbnail">
					<img src="<?php echo esc_url( $full_url ); ?>" alt="<?php the_title(); ?>">
				</div>
			<?php
        }
    }

    protected function _render_thumb_gallery_and_slide($galleries){

    }

    protected function _render_thumb_slide($galleries){

    }
}