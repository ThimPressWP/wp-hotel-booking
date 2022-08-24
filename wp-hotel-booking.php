<?php
/**
 * Plugin Name: WP Hotel Booking
 * Plugin URI: http://thimpress.com/
 * Description: Full of professional features for a booking room system
 * Author: ThimPress
 * Version: 1.10.6
 * Author URI: http://thimpress.com
 * Text Domain: wp-hotel-booking
 * Domain Path: /languages/
 * Requires PHP: 7.0
 *
 * @package wp-hotel-booking
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

const WPHB_FILE = __FILE__;
define( 'WPHB_PLUGIN_PATH', dirname( __FILE__ ) );
define( 'WPHB_PLUGIN_URL', plugins_url( '', __FILE__ ) );
const WPHB_VERSION = '1.10.7';
define( 'WPHB_BLOG_ID', get_current_blog_id() );
const WPHB_TEMPLATES = WPHB_PLUGIN_PATH . '/templates/';
const TP_HB_EXTRA    = __FILE__;
const WPHB_DEBUG     = 0;

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
	public function __construct() {

		if ( self::$_instance ) {
			return;
		}
		$this->includes();

		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'wp_print_scripts', array( $this, 'global_js' ) );
		add_action( 'template_redirect', 'hb_handle_purchase_request', 999 );
		add_action( 'admin_init', array( $this, 'create_tables' ) );
		register_activation_hook( plugin_basename( __FILE__ ), array( $this, 'install' ) );
		register_deactivation_hook( plugin_basename( __FILE__ ), array( $this, 'uninstall' ) );
		//add_action( 'plugin_loaded', array( $this, 'install' ) );

		add_action( 'init', array( $this, 'init' ), 20 );

		// create new blog in multisite
		add_action( 'wpmu_new_blog', array( $this, 'create_new_blog' ), 10, 6 );
		// multisite delete table in multisite
		add_filter( 'wpmu_drop_tables', array( $this, 'delete_blog_table' ) );
	}

	public function init() {
		// cart
		$this->cart = WPHB_Cart::instance();
		// user
		$this->user = hb_get_current_user();
	}

	public function create_tables() {
		WPHB_Install::create_tables();
		WPHB_Install::create_pages();
	}

	// install hook
	public function install() {
		WPHB_Install::install();
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
		$this->_include( 'includes/class-wphb-autoloader.php' );
		$this->_include( 'includes/class-wphb-template-loader.php' );
		$this->_include( 'includes/class-wphb-ajax.php' );
		$this->_include( 'includes/class-wphb-install.php' );

		$this->_include( 'includes/class-wphb-gdpr.php' );
		$this->_include( 'includes/class-wphb-helpers.php' );

		if ( is_admin() ) {
			$this->admin_includes();
		}
		$this->_include( 'includes/class-wphb-settings.php' );
		$this->_include( 'includes/class-wphb-comments.php' );
		$this->_include( 'includes/wphb-template-hooks.php' );
		$this->_include( 'includes/wphb-template-functions.php' );
		$this->_include( 'includes/wphb-widget-functions.php' );

		$this->_include( 'includes/admin/helpers/class-wphb-override-template.php' );

		if ( ! is_admin() ) {
			$this->frontend_includes();
		}
		$this->_include( 'includes/class-wphb-post-types.php' );

		$this->_include( 'includes/wphb-core-functions.php' );
		$this->_include( 'includes/wphb-functions.php' );
		$this->_include( 'includes/class-wphb-resizer.php' );

		// booking
		$this->_include( 'includes/booking/wphb-booking-functions.php' );
		$this->_include( 'includes/booking/wphb-booking-hooks.php' );
		$this->_include( 'includes/booking/class-wphb-booking.php' );

		// users
		$this->_include( 'includes/user/wphb-user-functions.php' );
		$this->_include( 'includes/user/class-wphb-user.php' );
		$this->_include( 'includes/class-wphb-roles.php' );

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
		// end shortcodes

		if ( ! class_exists( 'Aq_Resize' ) ) {
			$this->_include( 'includes/aq_resizer.php' );
		}
	}

	public function admin_includes() {
		$this->_include( 'includes/admin/class-wphb-admin.php' );
	}

	// load payments addons
	public function plugins_loaded() {
		// load text domain
		$this->load_text_domain();
	}

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
		$v_rand = uniqid();

		$dependencies = array(
			'jquery',
			'jquery-ui-sortable',
			'jquery-ui-datepicker',
			'wp-util',
		);

		wp_register_style( 'wp-hotel-booking-libaries-style', $this->plugin_url( 'assets/css/libraries.css' ) );

		// select2
		wp_register_script( 'wp-admin-hotel-booking-select2', $this->plugin_url( 'assets/js/select2.min.js' ) );
		if ( is_admin() ) {
			$dependencies = array_merge( $dependencies, array( 'backbone' ) );
			$screen       = get_current_screen();
			if ( $screen->base === 'edit-tags' && ( $screen->taxonomy === 'hb_room_type' || $screen->taxonomy === 'hb_room_capacity' ) ) {
				wp_register_script( 'wp-admin-hotel-booking', $this->plugin_url( 'assets/js/admin.room-taxonomies.js' ), $dependencies, false, true );
			}

			if ( WPHB_DEBUG ) {
				wp_register_style( 'wp-admin-hotel-booking', $this->plugin_url( 'assets/css/admin.tp-hotel-booking.css' ), array(), $v_rand );
				wp_register_script( 'wp-admin-hotel-booking', $this->plugin_url( 'assets/js/admin.hotel-booking.js' ), $dependencies, $v_rand );
			} else {
				wp_register_style( 'wp-admin-hotel-booking', $this->plugin_url( 'assets/css/admin.tp-hotel-booking.min.css' ), array(), WPHB_VERSION );
				wp_register_script( 'wp-admin-hotel-booking', $this->plugin_url( 'assets/js/admin.hotel-booking.min.js' ), $dependencies, WPHB_VERSION );
			}

			wp_localize_script( 'wp-admin-hotel-booking', 'hotel_booking_i18n', hb_admin_i18n() );
			wp_register_script( 'wp-admin-hotel-booking-fullcalendar', $this->plugin_url( 'assets/js/fullcalendar.min.js' ), array_merge( array( 'moment' ), $dependencies ) );
			wp_register_style( 'wp-admin-hotel-booking-fullcalendar', $this->plugin_url( 'assets/css/fullcalendar.min.css' ) );
		} else {
			if ( WPHB_DEBUG ) {
				wp_register_style( 'wp-hotel-booking', $this->plugin_url( 'assets/css/hotel-booking.css' ), array(), $v_rand );
				wp_register_script( 'wp-hotel-booking', $this->plugin_url( 'assets/js/hotel-booking.js' ), $dependencies, $v_rand, true );
			} else {
				wp_register_style( 'wp-hotel-booking', $this->plugin_url( 'assets/css/hotel-booking.min.css' ), array(), WPHB_VERSION );
				wp_register_script( 'wp-hotel-booking', $this->plugin_url( 'assets/js/hotel-booking.min.js' ), $dependencies, WPHB_VERSION, true );
			}

			wp_localize_script( 'wp-hotel-booking', 'hotel_booking_i18n', hb_i18n() );

			// rooms slider widget
			wp_register_script( 'wp-hotel-booking-gallery', $this->plugin_url( 'includes/libraries/camera/js/gallery.min.js' ), $dependencies );

			// owl carousel
			wp_register_script( 'wp-hotel-booking-owl-carousel', $this->plugin_url( 'includes/libraries/owl-carousel/owl.carousel.min.js' ), $dependencies );
		}

		if ( is_admin() ) {
			wp_enqueue_style( 'wp-admin-hotel-booking' );
			wp_enqueue_script( 'wp-admin-hotel-booking' );
			wp_enqueue_script( 'backbone' );

			// report
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jquery-ui-core' );
			wp_enqueue_script( 'jquery-ui-autocomplete' );

			/* fullcalendar */
			wp_enqueue_style( 'wp-admin-hotel-booking-fullcalendar' );
			wp_enqueue_script( 'wp-admin-hotel-booking-fullcalendar' );
		} else {
			wp_enqueue_style( 'wp-hotel-booking' );
			wp_enqueue_script( 'wp-hotel-booking' );

			// rooms slider widget
			wp_enqueue_script( 'wp-hotel-booking-owl-carousel' );

			// room galleria
			if ( is_singular( 'hb_room' ) ) {
				wp_enqueue_script( 'wp-hotel-booking-gallery' );
			}
		}
		wp_enqueue_style( 'wp-hotel-booking-libaries-style' );

		// select2
		wp_enqueue_script( 'wp-admin-hotel-booking-select2' );
		// wp_enqueue_script( 'colorpicker' );
	}

	/**
	 * Output global js settings
	 */
	public function global_js() {
		$upload_dir       = wp_upload_dir();
		$upload_base_url  = $upload_dir['baseurl'];
		$min_booking_date = get_option( 'tp_hotel_booking_minimum_booking_day' ) ? get_option( 'tp_hotel_booking_minimum_booking_day' ) : 1;
		?>
		<script type="text/javascript">
			var hotel_settings = {
				ajax            : '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',
				settings        : <?php echo WPHB_Settings::instance()->toJson( apply_filters( 'hb_settings_fields', array( 'review_rating_required' ) ) ); ?>,
				upload_base_url : '<?php echo esc_js( $upload_base_url ); ?>',
				meta_key        : {
					prefix: '_hb_'
				},
				nonce           : '<?php echo esc_html( wp_create_nonce( 'hb_booking_nonce_action' ) ); ?>',
				timezone        : '<?php echo esc_html( current_time( 'timestamp' ) ); ?>',
				min_booking_date: <?php echo esc_html( $min_booking_date ); ?>
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
	static function instance() {
		if ( ! self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}
}

$GLOBALS['wp_hotel_booking'] = WP_Hotel_Booking::instance();

