<?php

namespace Elementor;

use Elementor\Thim_Ekit_Widget_Product_Image;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( '\Elementor\Thim_Ekit_Widget_Product_Image' ) ) {
	include THIM_EKIT_PLUGIN_PATH . 'inc/elementor/widgets/single-product/product-image.php';
}

class Thim_Ekit_Widget_Room_Thumb extends Thim_Ekit_Widget_Product_Image {

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

        parent::register_controls();
    }


    public function render() {
        do_action( 'WPHB/modules/single-room/before-preview-query' );
        
        $galleries = get_post_meta( get_the_ID(), '_hb_gallery', true );

        $this->ekits_get_slide_image( $galleries );

        do_action( 'WPHB/modules/single-room/after-preview-query' );
    }

    protected function ekits_get_slide_image($galleries) {
        ?>
        <div class="woocommerce-product-gallery ekits-product-gallery--with-images ekits-product-slides__horizontal images">
			<div class="ekits-product-slides__wrapper">
				<ul class="slides">
                <?php
                foreach ( $galleries as $image_id ) {
                    $thumbnail_url = wp_get_attachment_image_url( $image_id, 'full' );
                    ?>
                    <li class="woocommerce-product-gallery__image">
                        <img src="<?php echo esc_url_raw( $thumbnail_url ); ?>"/>
                    </li>
                    <?php
                }
                ?>
                </ul>
            </div>
            <div class="ekits-product-thumbnails__wrapper">
            <ul class="slides">
                <?php
                foreach ( $galleries as $image_id ) {
                    $thumbnail_url = wp_get_attachment_image_url( $image_id, 'full' );
                    ?>
                    <li class="product-image-thumbnail">
                        <img src="<?php echo esc_url_raw( $thumbnail_url ); ?>"/>
                    </li>
                    <?php
                }
                ?>
                </ul>
            </div>
        </div>

        <?php
        $this->ekits_js_slider();
    }
}