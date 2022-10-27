<?php
/**
 * HB_Coupon
 *
 * @author   ThimPress
 * @package  WP-Hotel-Booking/Coupon/Classes
 * @version  1.7.2
 */

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

if ( ! defined( 'TP_HOTEL_COUPON' ) ) {
	define( 'TP_HOTEL_COUPON', true );
}

if ( ! class_exists( 'WPHB_COUPON_HOOKS' ) ) {
	/**
	 * Class HB_Coupon
	 */
	class WPHB_COUPON_HOOKS {
		/**
		 * @var array
		 */
		static $_instance = null;

		public function __construct() {
			$this->_init_hooks();
			require_once WPHB_PLUGIN_PATH . '/includes/coupons/class-wphb-coupon.php';
		}

		/**
		 * It adds a new tab to the admin settings page.
		 */
		public function _init_hooks() {
			add_action( 'hb_admin_settings_tab_after', array( $this, 'admin_settings' ) );
			add_action( 'hotel_booking_before_cart_total', array( $this, 'hotel_booking_before_cart_total' ) );
			add_action( 'init', array( $this, 'register_post_types_coupon' ) );
		}

		/**
		 * It registers a new post type called "hb_coupon" with the label "Coupon" and the singular label
		 * "Coupon"
		 */
		public function register_post_types_coupon() {
			$args = array(
				'labels'             => array(
					'name'               => _x( 'Coupons', 'Coupons', 'wp-hotel-booking' ),
					'singular_name'      => _x( 'Coupon', 'Coupon', 'wp-hotel-booking' ),
					'menu_name'          => __( 'Coupons', 'wp-hotel-booking' ),
					'parent_item_colon'  => __( 'Parent Item:', 'wp-hotel-booking' ),
					'all_items'          => __( 'Coupons', 'wp-hotel-booking' ),
					'view_item'          => __( 'View Coupon', 'wp-hotel-booking' ),
					'add_new_item'       => __( 'Add New Coupon', 'wp-hotel-booking' ),
					'add_new'            => __( 'Add New', 'wp-hotel-booking' ),
					'edit_item'          => __( 'Edit Coupon', 'wp-hotel-booking' ),
					'update_item'        => __( 'Update Coupon', 'wp-hotel-booking' ),
					'search_items'       => __( 'Search Coupon', 'wp-hotel-booking' ),
					'not_found'          => __( 'No coupon found', 'wp-hotel-booking' ),
					'not_found_in_trash' => __( 'No coupon found in Trash', 'wp-hotel-booking' ),
				),
				'public'             => false,
				'query_var'          => true,
				'publicly_queryable' => false,
				'show_ui'            => true,
				'has_archive'        => false,
				'capability_type'    => 'hb_room',
				'map_meta_cap'       => true,
				'show_in_menu'       => 'tp_hotel_booking',
				'show_in_admin_bar'  => true,
				'show_in_nav_menus'  => true,
				'supports'           => array( 'title' ),
				// 'can_export'         => false,
				'hierarchical'       => false,
			);
			$args = apply_filters( 'hotel_booking_register_post_type_coupon_arg', $args );
			register_post_type( 'hb_coupon', $args );
		}

		/**
		 * It adds a checkbox to the general settings page in the admin area.
		 *
		 * @param settings The settings page you want to add your field to.
		 *
		 * @return the value of the coupon code.
		 */
		public function admin_settings( $settings ) {
			if ( $settings !== 'general' ) {
				return;
			}
			$settings = hb_settings(); ?>
			<table class="form-table">
				<tr>
					<th><?php _e( 'Enable Coupon', 'wp-hotel-booking' ); ?></th>
					<td>
						<input type="hidden"
							   name="<?php echo esc_attr( $settings->get_field_name( 'enable_coupon' ) ); ?>"
							   value="0"/>
						<input type="checkbox"
							   name="<?php echo esc_attr( $settings->get_field_name( 'enable_coupon' ) ); ?>" <?php checked( $settings->get( 'enable_coupon' ) ? 1 : 0, 1 ); ?>
							   value="1"/>
					</td>
				</tr>
			</table>
			<?php
		}

		/**
		 * It displays the coupon code in the cart page.
		 */
		public function hotel_booking_before_cart_total() {
			$settings = hb_settings();
			if ( defined( 'TP_HOTEL_COUPON' ) && TP_HOTEL_COUPON && $settings->get( 'enable_coupon' ) ) {
				// if( $coupon = get_transient( 'hb_user_coupon_' . session_id() ) ) {
				if ( $coupon = WP_Hotel_Booking::instance()->cart->coupon ) {
					$coupon = HB_Coupon::instance( $coupon );
					?>
					<tr class="hb_coupon">
						<td class="hb_coupon_remove" colspan="9">
							<p class="hb-remove-coupon" align="right">
								<a href="" id="hb-remove-coupon"><i class="fa fa-times"></i></a>
							</p>
							<span class="hb-remove-coupon_code"><?php printf( __( 'Coupon applied: %s', 'wp-hotel-booking' ), $coupon->coupon_code ); ?></span>
							<span class="hb-align-right">
							-<?php echo hb_format_price( $coupon->discount_value ); ?>
						</span>
						</td>
					</tr>
				<?php } else { ?>
					<tr class="hb_coupon">
						<td colspan="9" class="hb-align-center">
							<input type="text" name="hb-coupon-code" value=""
								   placeholder="<?php _e( 'Coupon', 'wp-hotel-booking' ); ?>"
								   style="width: 150px; vertical-align: top;"/>
							<button type="button"
									id="hb-apply-coupon"><?php _e( 'Apply Coupon', 'wp-hotel-booking' ); ?></button>
						</td>
					</tr>
					<?php
				}
			}
		}

		/**
		 * @param null $coupon
		 *
		 * @return HB_Coupon|mixed
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}
	}

	WPHB_COUPON_HOOKS::instance();
}
