<?php
/**
 * Plugin Name: WP Hotel Booking
 * Plugin URI: http://thimpress.com/
 * Description: Full of professional features for a booking room system
 * Author: ThimPress
 * Version: 2.2.1
 * Author URI: http://thimpress.com
 * Text Domain: wp-hotel-booking
 * Domain Path: /languages/
 * Requires PHP: 7.4
 * Requires at least: 6.0
 * @package wp-hotel-booking
 */

use WPHB\TemplateHooks\CheckRoomsTemplate;
use WPHB\TemplateHooks\ArchiveRoomTemplate;

defined( 'ABSPATH' ) || exit;

const WPHB_FILE        = __FILE__;
const WPHB_PLUGIN_PATH = __DIR__;
$default_headers       = array(
	'Version'    => 'Version',
	'TextDomain' => 'Text Domain',
);
$plugin_info           = get_file_data( __FILE__, $default_headers, 'plugin' );
define( 'WPHB_VERSION', $plugin_info['Version'] );
define( 'WPHB_PLUGIN_URL', plugins_url( '', WPHB_FILE ) );
define( 'WPHB_BLOG_ID', get_current_blog_id() );
define( 'WPHB_TEMPLATES', WPHB_PLUGIN_PATH . '/templates/' );
const TP_HB_EXTRA    = __FILE__;
const WPHB_DEBUG     = 1;
const WPHB_API_V2    = 1;
const WPHB_SHOW_FORM = 0;
const WPHB_ROOM_CT   = 'hb_room';

/**
 * Class WP_Hotel_Booking
 */
class WP_Hotel_Booking {

	/**
	 * Hold the instance of main class
	 *
	 * @var object
	 */
	protected static $_instance = null;

	/**
	 * Plugin path
	 *
	 * @var string
	 */
	protected $_plugin_path = null;

	/**
	 * Plugin URL
	 *
	 * @var string
	 */
	protected $_plugin_url = null;

	/**
	 * @var WPHB_Cart
	 */
	public $cart = null;

	/**
	 * @var null
	 */
	public $user = null;

	/**
	 * Construction
	 */
	private function __construct() {
		$this->includes();

		global $wpdb;
		$wpdb->hotel_booking_order_items = $wpdb->prefix . 'hotel_booking_order_items';

		//add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'wp_print_scripts', array( $this, 'global_js' ) );
		add_action( 'template_redirect', 'hb_handle_purchase_request', 999 );
		// add_action( 'admin_init', array( $this, 'create_tables' ) );
		register_activation_hook( plugin_basename( __FILE__ ), array( $this, 'install' ) );
		register_deactivation_hook( plugin_basename( __FILE__ ), array( $this, 'uninstall' ) );
		// add_action( 'plugin_loaded', array( $this, 'install' ) );

		add_action( 'init', array( $this, 'init' ), 20 );

		// create new blog in multisite
		add_action( 'wpmu_new_blog', array( $this, 'create_new_blog' ), 10, 6 );
		// multisite delete table in multisite
		add_filter( 'wpmu_drop_tables', array( $this, 'delete_blog_table' ) );

		add_action( 'admin_init', array( $this, 'deactivate_plugins_old' ) );

		/**
		 * Load Widgets support Elementor
		 *
		 * This hook has from THIM_EKIT_VERSION 1.3.1
		 */
		add_action(
			'thim_ekit/modules/handle',
			function () {
				$this->_include( '/includes/elementor/modules/class-init.php' );
			}
		);
	}

	public function init() {
		// load text domain
		$this->load_text_domain();
		// cart
		$this->cart = WPHB_Cart::instance();
		// user
		$this->user = hb_get_current_user();

		// Check Elementor, Thim El Kit is active.
		if ( class_exists( 'Thim_EL_Kit' ) && defined( 'ELEMENTOR_VERSION' )
			&& version_compare( THIM_EKIT_VERSION, '1.3.0', '<=' ) ) {
			// Load Widgets support Elementor
			$this->_include( '/includes/elementor/modules/class-init.php' );
		}
	}

	// public function create_tables() {
	// WPHB_Install::create_tables();
	// WPHB_Install::create_pages();
	// }

	// install hook
	public function install() {
		WPHB_Install::install();
		$this->_include( 'includes/class-wphb-roles.php' );
	}

	// uninstall hook
	public function uninstall() {
		WPHB_Install::uninstall();
	}

	// create new blog table
	public function create_new_blog( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {
		WPHB_Install::create_new_blog( $blog_id, $user_id, $domain, $path, $site_id, $meta );
	}

	// delete table when delete blog, multisite
	public function delete_blog_table( $tables ) {
		return WPHB_Install::delete_tables( $tables );
	}

	/**
	 * Deactive plugin already merge to core wp-hotel-booking
	 */
	public function deactivate_plugins_old() {

		$flag = version_compare( get_option( 'hotel_booking_version' ), WPHB_VERSION, '>=' );
		if ( $flag ) {
			$plugins = apply_filters(
				'_hb_deactivate_plugins_old',
				array(
					'wp-hotel-booking-block-room/wp-hotel-booking-block.php',
					'wp-hotel-booking-coupon/wp-hotel-booking-coupon.php',
					'wp-hotel-booking-report/wp-hotel-booking-report.php',
					'wp-hotel-booking-booking-room/wp-hotel-booking-room.php',
				)
			);
			foreach ( $plugins as $plugin ) {
				if ( in_array( $plugin, apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
					deactivate_plugins( $plugin );
					if ( isset( $_GET['activate'] ) ) {
						unset( $_GET['activate'] );
					}
				}
			}
		}
	}

	/**
	 * Include a file
	 *
	 * @param string
	 * @param bool
	 * @param array
	 */
	public function _include( $file, $root = true, $args = array(), $unique = true ) {
		if ( $root ) {
			$file = $this->plugin_path( $file );
		}
		if ( is_array( $args ) ) {
			extract( $args );
		}

		if ( file_exists( $file ) ) {
			if ( $unique ) {
				require_once $file;
			} else {
				require $file;
			}
		}
	}

	/**
	 * Get the full path of a file
	 *
	 * @param string
	 *
	 * @return string
	 */
	public function locate( $file ) {
		return $this->_plugin_path . '/' . $file;
	}

	/**
	 * Includes common files and libraries
	 */
	public function includes() {
		include_once WPHB_PLUGIN_PATH . '/vendor/autoload.php';
		$this->include_files_global();

		if ( is_admin() ) {
			$this->admin_includes();
		}

		if ( ! is_admin() ) {
			$this->frontend_includes();
		}

		CheckRoomsTemplate::instance()->init();
		ArchiveRoomTemplate::instance()->init();
	}


	public function include_files_global() {
		$this->_include( 'includes/class-wphb-autoloader.php' );
		$this->_include( 'includes/class-wphb-template-loader.php' );
		$this->_include( 'includes/class-wphb-ajax.php' );
		$this->_include( 'includes/class-wphb-install.php' );
		$this->_include( 'includes/class-wphb-rest-response.php' );

		$this->_include( 'includes/class-wphb-gdpr.php' );
		$this->_include( 'includes/class-wphb-helpers.php' );

		$this->_include( 'includes/class-wphb-post-types.php' );
		$this->_include( 'includes/wphb-core-functions.php' );
		$this->_include( 'includes/wphb-functions.php' );
		$this->_include( 'includes/class-wphb-resizer.php' );

		$this->_include( 'includes/class-wphb-settings.php' );
		$this->_include( 'includes/class-wphb-comments.php' );
		$this->_include( 'includes/wphb-template-hooks.php' );
		$this->_include( 'includes/wphb-template-functions.php' );
		$this->_include( 'includes/wphb-widget-functions.php' );
		$this->_include( 'includes/admin/helpers/class-wphb-override-template.php' );

		// booking
		$this->_include( 'includes/booking/wphb-booking-functions.php' );
		$this->_include( 'includes/booking/wphb-booking-hooks.php' );
		$this->_include( 'includes/booking/class-wphb-booking.php' );
		$this->_include( 'includes/booking/class-wphb-booking-block.php' );
		$this->_include( 'includes/booking/class-wphb-booking-room-available.php' );

		// users
		$this->_include( 'includes/user/wphb-user-functions.php' );
		$this->_include( 'includes/user/class-wphb-user.php' );
		// $this->_include( 'includes/class-wphb-roles.php' );

		// products
		$this->_include( 'includes/products/class-wphb-abstract-product.php' );
		$this->_include( 'includes/products/class-wphb-product-room.php' );
		// end products

		$this->_include( 'includes/room/wphb-room-functions.php' );
		$this->_include( 'includes/room/class-wphb-room.php' );
		$this->_include( 'includes/plugins/wp-hotel-booking-extra/wp-hotel-booking-extra.php' );
		// // end addon

		$this->_include( 'includes/class-wphb-sessions.php' );
		// cart
		$this->_include( 'includes/cart/wphb-cart-functions.php' );
		$this->_include( 'includes/cart/class-wphb-cart.php' );
		$this->_include( 'includes/gateways/class-wphb-payment-gateway-base.php' );

		$this->_include( 'includes/wphb-webhooks.php' );

		// rest api
		$this->_include( 'includes/abstracts/class-wphb-abstract-rest-api.php' );
		$this->_include( 'includes/abstracts/class-wphb-abstract-rest-controller.php' );
		$this->_include( 'includes/rest-api/class-wphb-core-api.php' );
		$this->_include( 'includes/rest-api/class-wphb-admin-core-api.php' );

		// wphb booking single rooms
		$this->_include( 'includes/room/class-wphb-booking-room.php' );

		// coupon hooks
		$this->_include( 'includes/coupons/class-wphb-coupon-hooks.php' );

		// block template
		$this->_include( 'includes/abstracts/class-wphb-asbtract-block-template.php' );
		$this->_include( 'includes/class-wphb-block-template-config.php' );

		//meta boxes
		$this->_include( 'includes/class-wphb-meta-box.php' );

		//template-hook
		$this->_include( 'includes/template-hooks/class-wphb-search.php' );
	}


	public function frontend_includes() {
		// shortcodes
		$this->_include( 'includes/shortcodes/class-wphb-abstract-shortcodes.php' );
		$this->_include( 'includes/shortcodes/class-wphb-shortcode-hotel-booking-cart.php' );
		$this->_include( 'includes/shortcodes/class-wphb-shortcode-hotel-booking-account.php' );
		$this->_include( 'includes/shortcodes/class-wphb-shortcode-hotel-booking-checkout.php' );
		$this->_include( 'includes/shortcodes/class-wphb-shortcode-hotel-booking-thankyou.php' );
		$this->_include( 'includes/shortcodes/class-wphb-shortcode-hotel-booking-lastest-reviews.php' );
		$this->_include( 'includes/shortcodes/class-wphb-shortcode-hotel-booking-best-reviews.php' );
		$this->_include( 'includes/shortcodes/class-wphb-shortcode-hotel-booking-rooms.php' );
		$this->_include( 'includes/shortcodes/class-wphb-shortcode-hotel-booking-mini-cart.php' );
		$this->_include( 'includes/shortcodes/class-wphb-shortcode-hotel-booking-slider.php' );
		$this->_include( 'includes/shortcodes/class-wphb-shortcode-hotel-booking.php' );
		$this->_include( 'includes/shortcodes/class-wphb-shortcode-hotel-booking-filter.php' );
		// end shortcodes

		if ( ! class_exists( 'Aq_Resize' ) ) {
			$this->_include( 'includes/aq_resizer.php' );
		}
	}

	public function admin_includes() {
		$this->_include( 'includes/admin/class-wphb-admin.php' );
	}

	// load payments addons
	/*public function plugins_loaded() {
		// load text domain
		$this->load_text_domain();
	}*/

	/**
	 * Get the path of the plugin with sub path
	 *
	 * @param string $sub
	 *
	 * @return string
	 */
	public function plugin_path( $sub = '' ) {
		if ( ! $this->_plugin_path ) {
			$this->_plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );
		}

		return $this->_plugin_path . '/' . $sub;
	}

	/**
	 * Get the url of the plugin with sub path
	 *
	 * @param string $sub
	 *
	 * @return string
	 */
	public function plugin_url( $sub = '' ) {
		if ( ! $this->_plugin_url ) {
			$this->_plugin_url = untrailingslashit( plugins_url( '/', __FILE__ ) );
		}

		return $this->_plugin_url . '/' . $sub;
	}

	/**
	 * Load language for the plugin
	 */
	public function load_text_domain() {
		// prefix
		$prefix = basename( dirname( plugin_basename( __FILE__ ) ) );
		$locale = get_locale();
		$dir    = $this->plugin_path( 'languages' );
		$mofile = false;

		$global_file = WP_LANG_DIR . '/plugins/' . $prefix . '-' . $locale . '.mo';
		$plugin_file = $dir . '/' . $prefix . '-' . $locale . '.mo';

		if ( file_exists( $global_file ) ) {
			$mofile = $global_file;
		} elseif ( file_exists( $plugin_file ) ) {
			$mofile = $plugin_file;
		}

		if ( $mofile ) {
			// In themes/plugins/mu-plugins directory
			load_textdomain( 'wp-hotel-booking', $mofile );
		}
	}

	/**
	 * Enqueue assets for the plugin
	 */
	public function enqueue_assets() {
		$v_rand  = uniqid();
		$version = WPHB_VERSION;
		$min     = '.min';
		if ( WPHB_Settings::is_debug() ) {
			$min     = '';
			$version = $v_rand;
		}

		$dependencies = array(
			'jquery',
			'jquery-ui-sortable',
			'jquery-ui-datepicker',
			'wp-util',
			'wp-api-fetch',
		);

		// Register styles
		wp_register_style( 'wphb-ui-slider', $this->plugin_url( 'assets/lib/slider/nouislider.min.css' ) );
		wp_register_style( 'wp-hotel-booking-libaries-style', $this->plugin_url( 'assets/css/libraries.css' ) );
		wp_register_style( 'wp-hotel-booking-review-gallery', $this->plugin_url( "assets/css/review-gallery{$min}.css" ), [], $version );
		wp_register_style( 'wp-admin-hotel-booking', $this->plugin_url( "assets/css/admin/admin.tp-hotel-booking{$min}.css" ), [], $version );
		wp_register_style( 'wp-admin-single-room-v2', $this->plugin_url( "assets/css/admin/admin-single-room{$min}.css" ) );
		wp_register_style( 'wp-admin-review-image', $this->plugin_url( "assets/css/admin/review-image{$min}.css" ), [], $v_rand );
		wp_register_style( 'wp-admin-hotel-booking-fullcalendar', $this->plugin_url( 'assets/css/fullcalendar.min.css' ) );
		wp_register_style( 'wp-hotel-booking', $this->plugin_url( 'assets/css/hotel-booking.css' ), [], WPHB_VERSION );
		wp_register_style( 'wp-hotel-booking-theme-default', $this->plugin_url( 'assets/css/theme-default.css' ), [], WPHB_VERSION );
		wp_register_style( 'wp-admin-hotel-booking-calendar-v2', $this->plugin_url( 'assets/css/admin/main.min.css' ) );
		wp_register_style( 'tingle-css', $this->plugin_url( 'assets/lib/tingle.css' ) );
		wp_register_style( 'flatpickr-css', $this->plugin_url( 'assets/lib/flatpickr.min.css' ) );
		wp_register_style( 'wphb-single-room-css', WPHB_PLUGIN_URL . '/assets/css/booking-single-room.css', [], $version );
		// End Register styles

		// Register scripts
		// select2
		wp_register_script( 'wp-admin-hotel-booking-select2', $this->plugin_url( 'assets/js/select2.min.js' ) );
		// dropdown pages
		wp_register_script( 'wphb-dropdown-pages', $this->plugin_url( 'assets/js/admin/dropdown-pages.js' ) );
		// moment
		wp_register_script( 'wp-hotel-booking-moment', $this->plugin_url( 'assets/js/moment.min.js' ) );
		//nouiSlider
		wp_register_script( 'wphb-ui-slider', $this->plugin_url( 'assets/lib/slider/nouislider.min.js' ) );

		$dependencies = array_merge( $dependencies, array( 'backbone' ) );
		if ( is_admin() ) {
			$screen = get_current_screen();
			if ( $screen->base === 'edit-tags' && ( $screen->taxonomy === 'hb_room_type' || $screen->taxonomy === 'hb_room_capacity' ) ) {
				wp_register_script(
					'wp-admin-hotel-booking',
					$this->plugin_url( 'assets/js/admin/admin.room-taxonomies.js' ),
					$dependencies,
					false,
					true
				);
			}
		}

		wp_register_script(
			'wp-admin-hotel-booking',
			$this->plugin_url( "assets/js/admin/admin.hotel-booking{$min}.js" ),
			array_merge( $dependencies, array( 'wphb-dropdown-pages' ) ),
			$v_rand
		);
		wp_register_script(
			'wp-admin-room-filter',
			$this->plugin_url( "assets/js/admin/room-filter{$min}.js" ),
			array_merge( $dependencies, array() ),
			$v_rand
		);

		wp_register_script( 'wp-admin-hotel-booking-fullcalendar', $this->plugin_url( 'assets/js/fullcalendar.min.js' ), $dependencies );

		wp_register_script(
			'wp-hotel-booking',
			$this->plugin_url( "assets/dist/js/frontend/hotel-booking{$min}.js" ),
			$dependencies,
			$version,
			array(
				'strategy' => 'defer',
			)
		);
		wp_register_script(
			'wp-hotel-booking-v2',
			$this->plugin_url( "assets/dist/js/frontend/hotel-booking-v2{$min}.js" ),
			$dependencies,
			$version,
			array(
				'strategy' => 'defer',
			)
		);
		wp_register_script(
			'wp-hotel-booking-sort-by',
			$this->plugin_url( "assets/dist/js/frontend/sort-by{$min}.js" ),
			array(),
			$version,
			array(
				'strategy' => 'defer',
			)
		);
		wp_register_script(
			'wp-hotel-booking-filter-by',
			$this->plugin_url( "assets/dist/js/frontend/filter-by{$min}.js" ),
			array(),
			$version,
			array(
				'strategy' => 'defer',
			)
		);
		wp_register_script(
			'wp-hotel-booking-room-review',
			$this->plugin_url( "assets/dist/js/frontend/room-review{$min}.js" ),
			array(),
			$version,
			array(
				'strategy' => 'defer',
			)
		);

		wp_localize_script( 'wp-hotel-booking', 'hotel_booking_i18n', hb_i18n() );

		// rooms slider widget
		wp_register_script( 'wp-hotel-booking-gallery', $this->plugin_url( 'includes/libraries/camera/js/gallery.min.js' ), $dependencies ); // old camera
		wp_register_script( 'flexslider', $this->plugin_url( 'includes/libraries/flexslider/jquery.flexslider.min.js' ), $dependencies, WPHB_VERSION ); // new flexslider

		// owl carousel
		wp_register_script( 'wp-hotel-booking-owl-carousel', $this->plugin_url( 'includes/libraries/owl-carousel/owl.carousel.min.js' ), $dependencies );

		// calendar v2 : move addon to single rooms
		wp_register_script( 'wp-admin-hotel-booking-calendar-v2', $this->plugin_url( 'assets/js/admin/main.min.js' ), $dependencies );
		wp_register_script( 'wp-admin-hotel-booking-v2', $this->plugin_url( 'assets/js/admin/admin.hotel-booking-v2.js' ), $dependencies, WPHB_VERSION );

		// Single room script.
		wp_register_script(
			'wpdb-single-room-js',
			WPHB_PLUGIN_URL . "/assets/dist/js/frontend/wphb-single-room{$min}.js",
			[],
			$version,
			[ 'strategy' => 'defer' ]
		);
		// End Register scripts

		if ( is_admin() ) {
			wp_enqueue_style( 'wp-admin-hotel-booking' );
			wp_localize_script( 'wp-admin-hotel-booking', 'hotel_booking_i18n', hb_admin_i18n() );
			wp_enqueue_script( 'wp-admin-hotel-booking' );
			wp_enqueue_script( 'wp-admin-room-filter' );
			wp_enqueue_script( 'backbone' );
			wp_enqueue_style( 'wp-admin-single-room-v2' );
			wp_enqueue_style( 'wp-admin-review-image' );

			// report
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jquery-ui-core' );
			wp_enqueue_script( 'jquery-ui-autocomplete' );

			/* fullcalendar */
			wp_enqueue_script( 'wp-hotel-booking-moment' );
			wp_enqueue_style( 'wp-admin-hotel-booking-fullcalendar' );
			wp_enqueue_script( 'wp-admin-hotel-booking-fullcalendar' );

		} else {
			wp_enqueue_style( 'wphb-ui-slider' );
			wp_enqueue_style( 'wp-hotel-booking' );
			wp_enqueue_style( 'wp-hotel-booking-theme-default' );
			wp_enqueue_style( 'wp-hotel-booking-review-gallery' );

			wp_enqueue_script( 'wp-hotel-booking' );
			wp_enqueue_script( 'wp-hotel-booking-v2' );
			wp_enqueue_script( 'wp-hotel-booking-sort-by' );
			wp_enqueue_script( 'wp-hotel-booking-filter-by' );
			wp_enqueue_script( 'wp-hotel-booking-room-review' );
			wp_enqueue_style( 'flatpickr-css' );

			// Load scripts and styles for single room
			if ( is_singular( 'hb_room' ) ) {
				wp_enqueue_style( 'tingle-css' );
				wp_enqueue_style( 'wphb-single-room-css' );
				wp_enqueue_script( 'wpdb-single-room-js' );
				wp_enqueue_script( 'wp-hotel-booking-gallery' );
				wp_enqueue_script( 'flexslider' );

				global $post;

				$max_images = hb_settings()->get( 'max_review_image_number' );
				if ( empty( $max_images ) ) {
					$max_images = 10;
				}

				$max_file_size = hb_settings()->get( 'max_review_image_file_size' );

				if ( empty( $max_file_size ) ) {
					$max_file_size = 1000000;
				}

				$is_enable = intval( hb_settings()->get( 'enable_advanced_review' ) ) === 1;

				wp_localize_script(
					'wp-hotel-booking-room-review',
					'HB_ROOM_REVIEW_GALLERY',
					array(
						'room_id'             => $post->ID,
						'is_enable'           => $is_enable,
						'max_images'          => $max_images,
						'max_file_size'       => $max_file_size,
						'max_image_error'     => sprintf( esc_html__( 'The image number is greater than %s', 'wp-hotel-booking' ), $max_images ),
						'file_type_error'     => esc_html__( 'The image file type is invalid', 'wp-hotel-booking' ),
						'max_file_size_error' => sprintf( esc_html__( 'The maximum file size is %s KB', 'wp-hotel-booking' ), $max_file_size ),
						$max_file_size,
					)
				);
			}

			// rooms slider widget
			wp_enqueue_script( 'wp-hotel-booking-owl-carousel' );

			// booking in single rooms
			//wp_enqueue_style( 'wp-hotel-booking-magnific-popup-css' );
			wp_enqueue_script( 'wphb-ui-slider' );
			//wp_enqueue_script( 'wp-hotel-booking-magnific-popup-js' );
		}
		wp_enqueue_style( 'wp-hotel-booking-libaries-style' );

		// select2
		wp_enqueue_script( 'wp-admin-hotel-booking-select2' );
		// wp_enqueue_script( 'colorpicker' );

		/* calendar v2 */
		wp_enqueue_script( 'wp-admin-hotel-booking-calendar-v2' );
		wp_enqueue_style( 'wp-admin-hotel-booking-calendar-v2' );
		wp_enqueue_script( 'wp-admin-hotel-booking-v2' );
	}

	/**
	 * Output global js settings
	 */
	public function global_js() {

		if ( is_user_logged_in() && is_admin() ) {
			$screen = get_current_screen();
		}
		$upload_dir          = wp_upload_dir();
		$upload_base_url     = $upload_dir['baseurl'];
		$min_booking_date    = get_option( 'tp_hotel_booking_minimum_booking_day' ) ? get_option( 'tp_hotel_booking_minimum_booking_day' ) : 0;
		$cart_page_url       = ! empty( hb_settings()->get( 'cart_page_id' ) ) ? get_permalink( hb_settings()->get( 'cart_page_id' ) ) : '';
		$checkout_page_url   = ! empty( hb_settings()->get( 'checkout_page_id' ) ) ? get_permalink( hb_settings()->get( 'checkout_page_id' ) ) : '';
		$currency            = get_option( 'tp_hotel_booking_currency', 'USD' );
		$currency_symbol     = hb_get_currency_symbol( $currency );
		$thousands_separator = get_option( 'tp_hotel_booking_price_thousands_separator' ) ? get_option( 'tp_hotel_booking_price_thousands_separator' ) : ',';
		$decimals_separator  = get_option( 'tp_hotel_booking_price_decimals_separator' ) ? get_option( 'tp_hotel_booking_price_decimals_separator' ) : '.';
		$number_decimal      = get_option( 'tp_hotel_booking_price_number_of_decimal' ) ? get_option( 'tp_hotel_booking_price_number_of_decimal' ) : '0';
		?>
		<script type="text/javascript">
			var hotel_settings = {
				cart_page_url: '<?php echo esc_url( $cart_page_url ); ?>',
				checkout_page_url: '<?php echo esc_url( $checkout_page_url ); ?>',
				site_url: '<?php echo esc_url( site_url() ); ?>',
				ajax: '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',
				settings: <?php echo WPHB_Settings::instance()->toJson( apply_filters( 'hb_settings_fields', array( 'review_rating_required' ) ) ); ?>,
				upload_base_url: '<?php echo esc_js( $upload_base_url ); ?>',
				meta_key: {
					prefix: '_hb_'
				},
				date_format: '<?php echo get_option( 'date_format' ); ?>',
				nonce: '<?php echo esc_html( wp_create_nonce( 'hb_booking_nonce_action' ) ); ?>',
				timezone: '<?php echo esc_html( current_time( 'timestamp' ) ); ?>',
				min_booking_date: <?php echo esc_html( $min_booking_date ); ?>,
				wphb_rest_url: '<?php echo get_rest_url(); ?>',
				is_page_search: <?php echo is_page( hb_get_page_id( 'search' ) ) ? 1 : 0; ?>,
				url_page_search: '<?php echo get_permalink( hb_get_page_id( 'search' ) ); ?>',
				room_id: <?php echo isset( $screen->id ) && $screen->id == 'hb_room' ? get_the_ID() : 0; ?>,
				block_dates:
				<?php
				$room_id        = get_the_ID();
				$selected_block = array();
				if ( $room_id ) {
					$calendar_id = get_post_meta( $room_id, 'hb_blocked_id', true );
					if ( $calendar_id ) {
						$selected_block = get_post_meta( $calendar_id, 'hb_blocked_time' );
					}
				}
				echo json_encode( $selected_block );
				?>
				,
				currency: '<?php echo $currency; ?>',
				currency_symbol: '<?php echo $currency_symbol; ?>',
				currency_position: '<?php echo get_option( 'tp_hotel_booking_price_currency_position', 'left' ); ?>',
				thousands_separator: '<?php echo $thousands_separator; ?>',
				decimals_separator: '<?php echo $decimals_separator; ?>',
				number_decimal: '<?php echo $number_decimal; ?>',
				user_id: '<?php echo get_current_user_id(); ?>',
			}
		</script>
		<?php
	}

	/**
	 * Create an instance of the plugin if it is not created
	 *
	 * @static
	 * @return object|WP_Hotel_Booking
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}
}

WP_Hotel_Booking::instance();

