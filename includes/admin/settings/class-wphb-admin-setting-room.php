<?php
/**
 * WP Hotel Booking admin setting room.
 *
 * @version     1.9.6
 * @author      ThimPress
 * @package     WP_Hotel_Booking/Classes
 * @category    Classes
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;
if ( ! class_exists( 'WPHB_Admin_Setting_Room' ) ) {

	class WPHB_Admin_Setting_Room extends WPHB_Admin_Setting_Page {

		public $id = 'room';

		public $title = null;

		function __construct() {

			$this->title = __( 'Room', 'wp-hotel-booking' );

			parent::__construct();
		}

		public function get_settings() {
			return apply_filters(
				'hotel_booking_admin_setting_fields_' . $this->id,
				array(

					array(
						'type'  => 'section_start',
						'id'    => 'catalog_room_setting',
						'title' => __( 'Catalog Options', 'wp-hotel-booking' ),
						'desc'  => __( 'Catalog settings display column number and image size used in room list ( archive page, related room ).', 'wp-hotel-booking' ),
					),

					array(
						'id'      => 'tp_hotel_booking_catalog_number_column',
						'type'    => 'number',
						'default' => 4,
						'min'     => 1,
						'title'   => __( 'Number of column display catalog page', 'wp-hotel-booking' ),
						'desc'    => __( 'Catalog settings display column number ( archive page, related room ).', 'wp-hotel-booking' ),
					),

					array(
						'id'      => 'tp_hotel_booking_posts_per_page',
						'type'    => 'number',
						'default' => 8,
						'min'     => 1,
						'title'   => __( 'Number of post display in page', 'wp-hotel-booking' ),
						'desc'    => __( 'Settings limit room show ( search page ).', 'wp-hotel-booking' ),
					),

					array(
						'id'      => 'tp_hotel_booking_catalog_image',
						'type'    => 'image_size',
						'default' => array(
							'width'  => 270,
							'height' => 270,
						),
						'options' => array(
							'width'  => 270,
							'height' => 270,
						),
						'title'   => __( 'Catalog images size', 'wp-hotel-booking' ),
					),

					array(
						'id'      => 'tp_hotel_booking_catalog_display_rating',
						'title'   => __( 'Display rating', 'wp-hotel-booking' ),
						'type'    => 'checkbox',
						'default' => 1,
					),

					array(
						'type' => 'section_end',
						'id'   => 'catalog_room_setting',
					),

					array(
						'type'  => 'section_start',
						'id'    => 'room_setting',
						'title' => __( 'Room Options', 'wp-hotel-booking' ),
						'desc'  => __( 'Room settings display column number and image size used in gallery single page', 'wp-hotel-booking' ),
					),

					array(
						'id'      => 'tp_hotel_booking_max_adults_all_room',
						'title'   => __( 'Max Adults Rooms', 'wp-hotel-booking' ),
						'type'    => 'number',
						'default' => 10,
						'min'     => 1,
						'desc'    => __( 'Set custom quantity, serve the search page.', 'wp-hotel-booking' ),
					),

					array(
						'id'      => 'tp_hotel_booking_room_image_gallery',
						'type'    => 'image_size',
						'default' => array(
							'width'  => 1000,
							'height' => 1000,
						),
						'options' => array(
							'width'  => 1000,
							'height' => 1000,
						),
						'title'   => __( 'Room images size gallery', 'wp-hotel-booking' ),
					),

					array(
						'id'      => 'tp_hotel_booking_room_thumbnail',
						'type'    => 'image_size',
						'default' => array(
							'width'  => 150,
							'height' => 150,
						),
						'options' => array(
							'width'  => 150,
							'height' => 150,
						),
						'title'   => __( 'Room images thumbnail', 'wp-hotel-booking' ),
					),

					array(
						'id'      => 'tp_hotel_booking_display_pricing_plans',
						'title'   => __( 'Display pricing plans', 'wp-hotel-booking' ),
						'type'    => 'checkbox',
						'default' => 1,
					),

					array(
						'id'      => 'tp_hotel_booking_enable_review_rating',
						'title'   => __( 'Enable ratings on reviews', 'wp-hotel-booking' ),
						'type'    => 'checkbox',
						'default' => 1,
						'atts'    => array(
							'onchange' => "jQuery('.enable_ratings_on_reviews').toggleClass( 'hide-if-js', ! this.checked );",
						),
					),
	
					// do not use in plugin
					// array(
					// 	'id'      => 'tp_hotel_booking_review_rating_required',
					// 	'title'   => __( 'Ratings are required to leave a review', 'wp-hotel-booking' ),
					// 	'type'    => 'checkbox',
					// 	'default' => 1,
					// 	'trclass' => array( 'enable_ratings_on_reviews' ),
					// ),
					
					// do not use in plugin
					// array(
					// 	'id'      => 'tp_hotel_booking_enable_gallery_lightbox',
					// 	'title'   => __( 'Enable gallery lightbox', 'wp-hotel-booking' ),
					// 	'type'    => 'checkbox',
					// 	'default' => 1,
					// ),

					array(
						'type' => 'section_end',
						'id'   => 'room_setting',
					),

				)
			);
		}

	}

}
return new WPHB_Admin_Setting_Room();
