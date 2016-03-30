<?php
/**
 * @Author: ducnvtt
 * @Date:   2016-03-29 17:13:06
 * @Last Modified by:   ducnvtt
 * @Last Modified time: 2016-03-31 10:40:36
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class HB_Admin_Setting_Room extends HB_Admin_Setting_Page {

	public $id = 'room';

	public $title = null;

	function __construct() {

		$this->title = __( 'Room', 'tp-hotel-booking' );

		parent::__construct();
	}

	public function get_settings() {
		return apply_filters( 'hotel_booking_admin_setting_fields_' . $this->id, array(

				array(
						'type'		=> 'section_start',
						'id'		=> 'catalog_room_setting',
						'title'		=> __( 'Catalog Options', 'tp-hotel-booking' ),
						'desc'		=> __( 'Catalog settings display column number and image size used in room list ( archive page, related room ).', 'tp-hotel-booking' )
					),

				array(
						'id'		=> 'tp_hotel_booking_catalog_number_column',
						'type'		=> 'number',
						'default'	=> 4,
						'title'		=> __( 'Number of column display catalog page', 'tp-hotel-booking' )
					),

				array(
						'id'		=> 'tp_hotel_booking_posts_per_page',
						'type'		=> 'number',
						'default'	=> 8,
						'title'		=> __( 'Number of post display in page', 'tp-hotel-booking' )
					),

				array(
						'id'		=> 'tp_hotel_booking_catalog_image',
						'type'		=> 'image_size',
						'default'	=> array(
								'width'		=> 270,
								'height'	=> 270
							),
						'options'	=> array(
								'width'		=> 270,
								'height'	=> 270
							),
						'title'		=> __( 'Catalog images size', 'tp-hotel-booking' )
					),

				array(
						'id'		=> 'tp_hotel_booking_catalog_display_rating',
						'title'		=> __( 'Display rating', 'tp-hotel-booking' ),
						'type'		=> 'checkbox',
						'default'	=> 1
					),

				array(
						'type'		=> 'section_end',
						'id'		=> 'catalog_room_setting'
					),

				array(
						'type'		=> 'section_start',
						'id'		=> 'room_setting',
						'title'		=> __( 'Room Options', 'tp-hotel-booking' ),
						'desc'		=> __( 'Room settings display column number and image size used in gallery single page', 'tp-hotel-booking' )
					),

				array(
						'id'		=> 'tp_hotel_booking_room_image_gallery',
						'type'		=> 'image_size',
						'default'	=> array(
								'width'		=> 270,
								'height'	=> 270
							),
						'options'	=> array(
								'width'		=> 270,
								'height'	=> 270
							),
						'title'		=> __( 'Room images size gallery', 'tp-hotel-booking' )
					),

				array(
						'id'		=> 'tp_hotel_booking_room_thumbnail',
						'type'		=> 'image_size',
						'default'	=> array(
								'width'		=> 150,
								'height'	=> 150
							),
						'options'	=> array(
								'width'		=> 150,
								'height'	=> 150
							),
						'title'		=> __( 'Room images thumbnail', 'tp-hotel-booking' )
					),

				array(
						'id'		=> 'tp_hotel_booking_display_pricing_plans',
						'title'		=> __( 'Display pricing plans', 'tp-hotel-booking' ),
						'type'		=> 'checkbox',
						'default'	=> 1
					),

				array(
						'id'		=> 'tp_hotel_booking_enable_review_rating',
						'title'		=> __( 'Enable ratings on reviews', 'tp-hotel-booking' ),
						'type'		=> 'checkbox',
						'default'	=> 1,
						'atts'		=> array(
								'onchange'	=> "jQuery('.enable_ratings_on_reviews').toggleClass( 'hide-if-js', ! this.checked );"
							)
					),

				array(
						'id'		=> 'tp_hotel_booking_review_rating_required',
						'title'		=> __( 'Ratings are required to leave a review', 'tp-hotel-booking' ),
						'type'		=> 'checkbox',
						'default'	=> 1,
						'trclass'	=> array( 'enable_ratings_on_reviews' )
					),

				array(
						'id'		=> 'tp_hotel_booking_enable_gallery_lightbox',
						'title'		=> __( 'Enable gallery lightbox', 'tp-hotel-booking' ),
						'type'		=> 'checkbox',
						'default'	=> 1
					),

				array(
						'type'		=> 'section_end',
						'id'		=> 'room_setting'
					),

			) );
	}

}

return new HB_Admin_Setting_Room();