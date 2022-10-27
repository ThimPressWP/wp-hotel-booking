<?php
/**
 * WP Hotel Booking Extra.
 *
 * @version       1.9.6
 * @author        ThimPress
 * @package       WP_Hotel_Booking/Extra/Classes
 * @category      Classes
 * @author        Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'HB_Extra_Field' ) ) {
	/**
	 * Class HB_Extra_Field
	 */
	class HB_Extra_Field {

		/**
		 * @var null
		 */
		protected $_extras_type = null;

		/**
		 * HB_Extra_Field constructor.
		 */
		public function __construct() {

			add_action( 'hotel_booking_loop_after_item', array( $this, 'render_extra' ), 10, 1 );
			add_action( 'hotel_booking_after_add_room_to_cart_form', array( $this, 'render_extra' ), 10, 1 );

			// settings tab single room admin v2
			add_filter( 'wpbh_meta_box_room_settings_tabs', array( $this, 'tabs_setting' ), 10, 1 );
			// single room cart
			add_action( 'hotel_booking_room_after_quantity', array( $this, 'single_room_cart' ) );

			// add package details booking
			add_action( 'hotel_booking_room_details_quantity', array( $this, 'admin_booking_room_details' ), 10, 3 );
		}

		/**
		 * Meta field box render
		 *
		 * @param $fields
		 *
		 * @return array
		 */
		public function tabs_setting( $tabs ) {

			$tabs['extra_settings'] = array(
				'label'    => esc_html__( 'Facilities', 'wp-hotel-booking' ),
				'target'   => 'addition_package',
				'icon'     => 'dashicons-welcome-write-blog',
				'priority' => 30,
				'content'  => $this->wphb_extra_fields(),
			);

			return $tabs;
		}

		/**
		 * It's a filter that adds a new tab to the room edit screen
		 *
		 * @return An array of arrays.
		 */
		public function wphb_extra_fields() {
			$tab_extra = apply_filters(
				'wpbh_meta_box_room_extra_fields',
				array(
					'room_extra' => array(
						'name'        => 'room_extra',
						'label'       => __( 'Facilities', 'wp-hotel-booking' ),
						'type'        => 'multiple',
						'options'     => $this->extra_fields(),
						'filter'      => array( $this, 'meta_value' ),
						'edit_option' => array(
							'post_type' => 'hb_extra_room',
							'admin_url' => 'edit.php',
						),
						'text_edit'   => __( '+ Add new.', 'wp-hotel-booking' ),
						'desc'        => __( 'Additional options for the room.', 'wp-hotel-booking' ),
					),
				)
			);

			return $tab_extra;
		}

		/**
		 * @return array
		 */
		protected function extra_fields() {
			global $hb_extra_settings;
			$options = array();
			$extras  = $hb_extra_settings->get_extra();
			foreach ( $extras as $key => $ex ) {
				if ( $ex->post_status == 'publish' && apply_filters( 'hb_filter_extra_option', true, $ex ) ) {
					$opt        = new stdClass();
					$opt->text  = $ex->post_title;
					$opt->value = $ex->ID;
					$options[]  = $opt;
				}
			}

			return $options;
		}

		/**
		 * @param $val
		 *
		 * @return mixed
		 */
		public function meta_value( $val ) {
			return $val;
		}

		/**
		 * @param $booking_params
		 * @param $search_key
		 * @param $room_id
		 */
		public function admin_booking_room_details( $booking_params, $search_key, $room_id ) {
			if ( ! isset( $booking_params[ $search_key ] ) ||
				 ! isset( $booking_params[ $search_key ][ $room_id ] ) ||
				 ! isset( $booking_params[ $search_key ][ $room_id ]['extra_packages_details'] )
			) {
				return;
			}

			$packages = $booking_params[ $search_key ][ $room_id ]['extra_packages_details'];
			?>
			<ul>
				<?php foreach ( $packages as $id => $package ) : ?>
					<li>
						<small><?php printf( '%s (x%s)', $package['package_title'], $package['package_quantity'] ); ?></small>
					</li>
				<?php endforeach ?>
			</ul>
			<?php
		}

		/**
		 * @param $post_id
		 */
		public function render_extra( $post_id ) {
			tp_hb_extra_get_template( 'loop/extra-search-room.php', array( 'post_id' => $post_id ) );
		}

		/**
		 * Extra single search room
		 *
		 * @param $post
		 */
		public function single_room_cart( $post ) {
			ob_start();
			tp_hb_extra_get_template( 'loop/extra-single-search-room.php', array( 'post' => $post ) );
			echo ob_get_clean();
		}
	}
}

new HB_Extra_Field();
