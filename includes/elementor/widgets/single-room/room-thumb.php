<?php

namespace Elementor;

use Thim_EL_Kit\GroupControlTrait;
use Elementor\Thim_Ekit_Widget_Product_Image;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( '\Elementor\Thim_Ekit_Widget_Product_Image' ) ) {
	include THIM_EKIT_PLUGIN_PATH . 'inc/elementor/widgets/single-product/product-image.php';
}

class Thim_Ekit_Widget_Room_Thumb extends Thim_Ekit_Widget_Product_Image {
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

    public function get_script_depends() {
		return [ 'wphb-flexslide', 'wphb-magnific-popup' ];
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
				'default' => 'gallery_popup',
				'options' => array(
                    'gallery_popup'      => esc_html__( 'Gallery and Popup Slide', 'wp-hotel-booking' ),
                    'gallery_slide'      => esc_html__( 'Gallery and Slide', 'wp-hotel-booking' ),
                    'slider'             => esc_html__( 'Only Slide', 'wp-hotel-booking' ),
				),
			)
		);

        $this->add_control(
			'icon_gallery_popup',
			[
				'label'         => esc_html__( 'Icon Button Gallery', 'wp-hotel-booking' ),
				'type'          => Controls_Manager::ICONS,
                'skin'          => 'inline',
                'label_block'   => false,
                'condition' => [
					'layout_style' => 'gallery_popup',
				]
			]
		);

        $this->end_controls_section();
        $this->register_section_style_gallery_popup();
        $this->_register_setting_thumb_slider_nav_style(
            esc_html__( 'Nav Slide', 'wp-hotel-booking' ), 'nav_slide', '.hb-room-thumbnail', ['layout_style!' => 'gallery_popup']
        );
    }

    protected function register_section_style_gallery_popup(){
        $this->start_controls_section(
			'style_gallery_popup',
			[
				'label' => esc_html__( 'Gallery Popup', 'wp-hotel-booking' ),
				'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
					'layout_style' => 'gallery_popup',
				]
			]
		);

        $this->add_responsive_control(
			'feature_img_width',
			[
				'label'     => esc_html__( 'Width Feature', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::SLIDER,
				'size_units' => [ '%', 'px' ],
                'default'   => [
                    'unit'  => '%'
                ],
				'selectors' => [
					'{{WRAPPER}} .hb-room-thumbnail-gallery-popup .first-gallery' => 'max-width: {{SIZE}}{{UNIT}};']
			]
		);

        $this->add_responsive_control(
			'list_img_width',
			[
				'label'     => esc_html__( 'Width List', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::SLIDER,
				'size_units' => [ '%', 'px' ],
                'default'   => [
                    'unit'  => '%'
                ],
				'selectors' => [
					'{{WRAPPER}} .hb-room-thumbnail-gallery-popup .hb-gallery-thumbnails' => '--hb-gallery-thumbnails-width: {{SIZE}}{{UNIT}};']
			]
		);

        $this->add_responsive_control(
			'radius_image',
			[
				'label'      => esc_html__( 'Border Radius', 'wp-hotel-booking' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .hb-room-thumbnail-gallery-popup img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_control(
			'gallery_button', [
				'label'     => esc_html__( 'Gallery Button', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
			]
		);

        $this->register_button_style( 'gallery_button_style', '.hb-room-thumbnail-gallery-popup .hb-gallery-thumbnails li.more .button-gallery ' );

        $this->end_controls_section();
    }

    public function render() {
        do_action( 'WPHB/modules/single-room/before-preview-query' );

        $settings    	= $this->get_settings_for_display();
        $galleries = get_post_meta( get_the_ID(), '_hb_gallery', true ); ?>

        <div class="hb-room-thumbnail" > 
        <?php if( !empty($galleries) ) {
            if ( isset($settings['layout_style']) && $settings['layout_style'] == 'gallery_popup' ){ ?>

                <div class="hb-room-thumbnail-gallery-popup"> 
                    <?php $this->_render_thumb_gallery_and_popup_slide($galleries, $settings); ?> 
                </div>

            <?php } elseif ( isset($settings['layout_style']) && $settings['layout_style'] == 'gallery_slide' ){ ?>
                
                <div class="hb-room-thumbnail-gallery-slide"> 
                    <?php $this->_render_thumb_gallery_and_slide($galleries, $settings); ?> 
                </div>

            <?php }else { ?>

                <div class="hb-room-thumbnail-slide"> 
                    <?php $this->_render_thumb_slide($galleries, $settings); ?> 
                </div>

            <?php }
        } else { ?>
				<div class="hb-room-item-post-thumbnail">
					<?php echo get_the_post_thumbnail( get_the_ID(), 'thumbnail' );  ?>
				</div>
		<?php } ?> 
        </div> 
        
        <?php
        do_action( 'WPHB/modules/single-room/after-preview-query' );
    }

    protected function _render_thumb_gallery_and_popup_slide($galleries, $settings){
        $gallery_img = $galleries;
        $class = $html_icon = '';

        if ( isset($settings['icon_gallery_popup']) && $settings['icon_gallery_popup']['value'] != '' ){
			ob_start();
			Icons_Manager::render_icon( $settings['icon_gallery_popup'],['aria-hidden' => 'true',]);
			$html_icon = ob_get_contents();
			ob_end_clean();
		}else {
            $html_icon = '<i aria-hidden="true" class="tk tk-th-large"></i>';
        }
        echo '<ul class="first-gallery">';
            foreach ( $gallery_img as $key => $image_id ) {
                if ( $key == 0 ) {
                    $image_size = array( 960, 700 );
                } else {
                    $image_size = array( 480, 350 );
                }
                $images   = wp_get_attachment_image( $image_id, $image_size );
                $full_url = wp_get_attachment_image_url( $image_id, 'full' );

                if ( $key == 2 ) {
                    $class       = ' more';
                    $count_image = count( $gallery_img ) - 3;
                    $images      .= ( $count_image > 0 ) ? '<span class="button-gallery">' . $html_icon . ' ' . esc_html__( 'Gallery', 'wp-hotel-booking' ) . '</span>' : '';
                }

                if ( $key > 2 ) {
                    $class  = ' hidden';
                    $images = '';
                }

                echo '<li class="hb-gallery-item' . $class . '"><a href="' . esc_url( $full_url ) . '" class="gallery-img-item">' . $images . '</a></li>';

                if ( count( $gallery_img ) > 1 && $key == 0 ) {
                    echo '</ul><ul class="hb-gallery-thumbnails d-flex">';
                }
            }
        echo '</ul>';
    }

    protected function _render_thumb_gallery_and_slide($galleries, $settings){
        ?>
        <div class="hb-main-gallery" id="slider-gallery">
            <ul class="slides">
                <?php
                foreach ( $galleries as $image_id ) {
                    $image_url = wp_get_attachment_image_url( $image_id, 'full' );
                    ?>
                    <li>
                        <img src="<?php echo esc_url_raw( $image_url ); ?>"/>
                    </li>
                <?php } ?>
            </ul>
	    </div>

        <div class="hb-thumbnail-gallery" id="slider-carousel" >
            <ul class="slides">
                <?php
                foreach ( $galleries as $image_id ) {
                    $thumbnail_url = wp_get_attachment_image_url( $image_id, 'thumbnail' );
                    ?>
                    <li>
                        <img src="<?php echo esc_url_raw( $thumbnail_url ); ?>"/>
                    </li>
                    <?php
                }
                ?>
            </ul>
        </div>
        <?php 
        $this->add_js_gallery_and_slide($settings); ?>
        <?php
    }

    protected function _render_thumb_slide($galleries, $settings){
        ?>
        <div class="hb-thumbnail-slide" id="slide-single">
            <ul class="slides">
                <?php
                foreach ( $galleries as $image_id ) {
                    $thumbnail_url = wp_get_attachment_image_url( $image_id, 'full' );
                    ?>
                    <li>
                        <img src="<?php echo esc_url_raw( $thumbnail_url ); ?>"/>
                    </li>
                    <?php
                }
                ?>
            </ul>
        </div>
        <?php
        $this->add_js_slide();
    }

    protected function add_js_gallery_and_slide() {
        ?>
        <script type="text/javascript">
           jQuery(document).ready(function ($) {
                if (jQuery().flexslider) {

                    jQuery('#slider-carousel').flexslider({
                        animation: "slide",
                        controlNav: false,
                        animationLoop: false,
                        slideshow    : false,
                        itemWidth: 150,
                        touch: true,
                        initDelay: 0,
                        itemMargin: 24,
                        asNavFor: "#slider-gallery",
                    });
                }

                if (jQuery().flexslider) {
                    jQuery('#slider-gallery').flexslider({
                        animation: "fade",
                        controlNav: false,
                        directionNav: false,
                        animationLoop: true,
                        animationSpeed: 800,
                        smoothHeight: true,
                        touch: true,
                        maxItems: 1,
                        sync: '#slider-carousel'
                    });
                }
            });
        </script>
        <?php
    }

    protected function add_js_slide() {
        ?>
        <script type="text/javascript">
           jQuery(document).ready(function ($) {
                if (jQuery().flexslider) {
                    jQuery('#slide-single').flexslider({
                        animation: "fade",
                        controlNav: true,
                        directionNav: true,
                        animationLoop: true,
                        animationSpeed: 800,
                        smoothHeight: true,
                        touch: true,
                        maxItems: 1,
                    });
                }
            });
        </script>
        <?php
    }
}