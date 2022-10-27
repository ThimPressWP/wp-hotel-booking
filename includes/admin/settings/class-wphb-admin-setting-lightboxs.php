<?php
/**
 * WP Hotel Booking admin setting lightboxs.
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

if ( ! class_exists( 'WPHB_Admin_Setting_Lightboxs' ) ) {

	class WPHB_Admin_Setting_Lightboxs extends WPHB_Admin_Setting_Page {

		public $id = 'lightboxs';

		public $title = null;

		function __construct() {

			$this->title = __( 'Lightboxs', 'wp-hotel-booking' );

			parent::__construct();
		}

		public function get_settings() {
			return apply_filters(
				'hotel_booking_admin_setting_fields_' . $this->id,
				array(

					array(
						'type'  => 'section_start',
						'id'    => 'lightbox_settings',
						'title' => __( 'Lightbox options', 'wp-hotel-booking' ),
						'desc'  => __( 'General options for Lightbox system.', 'wp-hotel-booking' ),
					),

					array(
						'type'    => 'select',
						'id'      => 'tp_hotel_booking_lightbox',
						'options' => hb_get_support_lightboxs(),
						'title'   => __( 'Lightbox type', 'wp-hotel-booking' ),
						'default' => 'lightbox2',
					),

					array(
						'type' => 'section_end',
						'id'   => 'lightbox_settings',
					),

				)
			);
		}

	}

}
return new WPHB_Admin_Setting_Lightboxs();
