<?php
/**
 * WP Hotel Booking admin class.
 *
 * @class       WPHB_Admin
 * @version     1.9.7.4
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPHB_Admin' ) ) {
	/**
	 * Class WPHB_Admin
	 */
	class WPHB_Admin {

		private static $instance;

		public static function instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * WPHB_Admin constructor.
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'includes' ) );

			// update pricing
			// add_action( 'admin_init', array( $this, 'update_pricing' ) );
			// update field Max Adult new version
			add_action( 'wpbh_meta_box_room_general_fields', array( $this, 'wphb_update_field_max_adult' ), 10, 2 );
			add_action( 'admin_notices', array( $this, 'wphb_update_field_max_adult_notice' ) );
		}

		/**
		 * Include files.
		 */
		public function includes() {
			WP_Hotel_Booking::instance()->_include( 'includes/admin/class-wphb-admin-settings.php' );
			WP_Hotel_Booking::instance()->_include( 'includes/admin/class-wphb-admin-menu.php' );
			WP_Hotel_Booking::instance()->_include( 'includes/admin/class-wphb-admin-tools.php' );
			WP_Hotel_Booking::instance()->_include( 'includes/admin/wphb-admin-functions.php' );

			// metabox single room
			WP_Hotel_Booking::instance()->_include( 'includes/admin/metaboxes/rooms/class-wphb-admin-metabox-rooms.php' );
			WP_Hotel_Booking::instance()->_include( 'includes/admin/metaboxes/rooms/class-wphb-admin-metabox-extra-options.php' );
			WP_Hotel_Booking::instance()->_include( 'includes/admin/metaboxes/rooms/class-wphb-admin-metabox-coupons.php' );

			// metabox field tabs
			WP_Hotel_Booking::instance()->_include( 'includes/admin/metaboxes/rooms/tabs/class-wphb-admin-metabox-room-faq.php' );
			WP_Hotel_Booking::instance()->_include( 'includes/admin/metaboxes/rooms/tabs/class-wphb-admin-metabox-room-price.php' );
			WP_Hotel_Booking::instance()->_include( 'includes/admin/metaboxes/rooms/tabs/class-wphb-admin-metabox-room-block-date.php' );

			// setup wizard
			WP_Hotel_Booking::instance()->_include( 'includes/admin/setup/class-wphb-setup-wizard.php' );

		}

		/**
		 * Update pricing.
		 */
		public function update_pricing() {

			if ( ! isset( $_POST['hb-update-pricing-plan-field'] ) || ! wp_verify_nonce(
				sanitize_text_field( $_POST['hb-update-pricing-plan-field'] ),
				'hb-update-pricing-plan'
			) ) {
				return;
			}

			if ( empty( $_POST['price'] ) ) {
				return;
			}

			if ( ! isset( $_POST['room_id'] ) ) {
				return;
			}

			$room_id = absint( $_POST['room_id'] );
			$plans   = hb_room_get_pricing_plans( $room_id );

			$ignore = array();

			$prices = (array) $_POST['price'];
			foreach ( array_keys( $prices ) as $key ) {
				$key         = sanitize_text_field( $key );
				$start       = isset( $_POST['date-start-timestamp'][ $key ] ) ? sanitize_text_field( $_POST['date-start-timestamp'][ $key ] ) : '';
				$end         = isset( $_POST['date-end-timestamp'][ $key ] ) ? sanitize_text_field( $_POST['date-end-timestamp'][ $key ] ) : '';
				$prices_post = (array) $_POST['price'][ $key ];
				$prices      = WPHB_Helpers::sanitize_params_submitted( $prices_post );

				$plan_id  = hb_room_set_pricing_plan(
					array(
						'start_time' => $start,
						'end_time'   => $end,
						'pricing'    => $prices,
						'room_id'    => $room_id,
						'plan_id'    => $key,
					)
				);
				$ignore[] = $plan_id;
			}

			foreach ( $plans as $id => $plan ) {
				if ( ! in_array( $id, $ignore ) ) {
					hb_room_remove_pricing( $id );
				}
			}
		}

		/**
		 * It updates the field max adult.
		 *
		 * @param settings The array of settings for the room type.
		 * @param room_id The ID of the room being edited.
		 *
		 * @return the  array.
		 */
		public function wphb_update_field_max_adult( $settings, $room_id ) {
			$flag = version_compare( get_option( 'hotel_booking_version' ), WPHB_VERSION, '>=' );

			if ( $flag ) {
				$max_adult = get_post_meta( $room_id, '_hb_room_capacity_adult', true );
				unset( $settings['room_origin_capacity'] );

				$agrs_meta = array(
					'room_capacity_adult' => array(
						'name'  => 'room_capacity_adult',
						'label' => __( 'Room Capacities', 'wp-hotel-booking' ),
						'type'  => 'number',
						'std'   => ! empty( $max_adult ) ?: 1,
						'min'   => 1,
					),
				);
				array_splice( $settings, 1, 0, $agrs_meta );
			}
			return $settings;
		}

		/**
		 * It displays a warning message to the user if the database needs to be updated.
		 */
		public function wphb_update_field_max_adult_notice() {
			$flag = version_compare( get_option( 'hotel_booking_version' ), WPHB_VERSION, '<' );
			if ( $flag ) {
				echo '<div class="notice notice-warning">';
				printf(
					'<p>' . __( '<strong>Warning:</strong> Plugin <strong>WP Hotel Booking </strong> database needs to be updated to function properly. <a href="%s" target="_blank">Click</a> to go to update page. ', 'wp-hotel-booking' ) . '</p>',
					admin_url( 'admin.php?page=wphb-tools&tab=wphb_update' )
				);
				echo '</div>';
			}
		}
	}
}

WPHB_Admin::instance();
