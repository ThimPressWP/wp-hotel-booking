<?php

/**
 * Class WPHB_Setup_Wizard
 *
 * Class hewphber for displaying the Setup Wizard page.
 *
 * @since   3.0.0
 * @author  ThimPress
 * @package wp-hotel-booking/Classes
 */
class WPHB_Setup_Wizard {
	/**
	 * @var string
	 */
	protected $_base_url = 'index.php?page=wphb-setup';

	/**
	 * WPHB_Setup_Wizard constructor.
	 */
	protected function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array( $this, 'setup_wizard' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );

		$actions = array(
			'setup_create_pages' => 'create_pages',
		);

		foreach ( $actions as $action => $callback ) {
			add_action( "wp_ajax_hotel_booking_{$action}", array( $this, $callback ) );
		}
	}

	public function admin_notices() {
		if ( ! get_option( 'wphb_setup_wizard_completed', false ) ) { ?>
			<div id="notice-install" class="wphb-notice notice notice-info">
				<p><?php _e( '<strong>WP Hotel Booking is ready to use.</strong>', 'wp-hotel-booking' ); ?></p>
				<p>
					<a class="button button-primary" href="<?php echo admin_url( 'index.php?page=wphb-setup' ); ?>"><?php _e( 'Quick Setup', 'wp-hotel-booking' ); ?></a>
					<!-- <button class="button" data-dismiss-notice="skip-setup-wizard"><?php // _e( 'Skip', 'wp-hotel-booking' ); ?></button> -->
				</p>
			</div>
			<?php
		}
	}

	/**
	 * Create static pages action
	 */
	public function create_pages() {
		$respon = new stdClass();

		if ( ! wp_verify_nonce( hb_get_request( 'wphb-setup-nonce' ), 'wphb-setup-step-pages' ) ) {
			die();
		}

		$pages    = tp_hotel_booking_pages_required();
		$settings = hb_get_request( 'settings' );

		foreach ( $settings['pages'] as $page => $page_id ) {
			if ( empty( $page_id ) ) {
				if ( array_key_exists( $page, $pages ) ) {
					$page_id = $this->create_page( $pages[ $page ] );
					hb_settings()->set( $page, $page_id );
				}
			}
		}
		$respon->data = hb_get_template_content( 'setup/steps/pages.php', array( 'pages' => $pages ) );

		wp_send_json( $respon );
	}

	/**
	 * Create page by type.
	 *
	 * @param string $page
	 *
	 * @return int|WP_Error
	 */
	public function create_page( $page ) {
		if ( empty( $page ) ) {
			return;
		}

		return wp_insert_post(
			array(
				'post_title'   => $page['name'],
				'post_status'  => 'publish',
				'post_type'    => 'page',
				'post_content' => $page['content'] ?? '',
			)
		);
	}

	/**
	 * Add an empty menu item for validating page.
	 */
	public function admin_menu() {
		if ( 'wphb-setup' !== hb_get_request( 'page' ) || ! current_user_can( 'install_plugins' ) ) {
			return;
		}
		add_dashboard_page( '', '', 'manage_options', 'wphb-setup', '' );
	}

	/**
	 * Display setup page a ignore anything else in the rest
	 */
	public function setup_wizard() {

		$v_rand       = uniqid();
		$dependencies = array(
			'jquery',
		);
		if ( 'wphb-setup' !== hb_get_request( 'page' ) || ! current_user_can( 'install_plugins' ) ) {
			return;
		}

		if ( 'finish' === hb_get_request( 'step' ) ) {
			update_option( 'wphb_setup_wizard_completed', 'yes' );
		}

		$this->save();

		// setup wizard admin
		wp_enqueue_style( 'buttons' );
		wp_enqueue_style( 'common' );
		wp_enqueue_style( 'forms' );
		wp_enqueue_style( 'themes' );
		wp_enqueue_style( 'dashboard' );
		wp_enqueue_style( 'widgets' );

		wp_enqueue_style( 'wp-admin-setup-wizard-style', WPHB_PLUGIN_URL . '/assets/css/admin/admin-setup.css' );
		wp_enqueue_style( 'wp-admin-setup-wizard-select2', WPHB_PLUGIN_URL . '/assets/css/select2.min.css' );

		wp_enqueue_script( 'wphb-utils', WPHB_PLUGIN_URL . '/assets/js/utils/jquery.plugins.js ' );
		wp_enqueue_script( 'wphb-dropdown-pages', WPHB_PLUGIN_URL . '/assets/js/admin/dropdown-pages.js ' );
		wp_enqueue_script( 'wp-admin-setup-wizard-select2', WPHB_PLUGIN_URL . '/assets/js/select2.min.js ' );
		wp_enqueue_script( 'wp-admin-setup-wizard-script', WPHB_PLUGIN_URL . '/assets/js/admin/admin.setup-wizard.js ', $dependencies, $v_rand );

		hb_get_template( 'setup/header.php' );
		hb_get_template( 'setup/content.php', array( 'steps' => $this->get_steps() ) );
		hb_get_template( 'setup/footer.php' );
		die();
	}

	public function save() {

		$step = hb_get_request( 'step' );

		if ( ! wp_verify_nonce( hb_get_request( 'wphb-setup-nonce' ), 'wphb-setup-step-' . $step ) ) {
			return;
		}

		$postdata = hb_get_request( 'settings' );
		$steps    = array( 'payment', 'pages' );

		if ( in_array( $step, $steps ) ) {
			if ( array_key_exists( 'paypal', $postdata ) ) {
				update_option( 'tp_hotel_booking_paypal', $postdata['paypal'] );
			}

			if ( array_key_exists( 'currency', $postdata ) ) {
				foreach ( $postdata['currency'] as $k => $v ) {
					update_option( 'tp_hotel_booking_' . $k, $v );
				}
			}

			if ( array_key_exists( 'pages', $postdata ) ) {
				foreach ( $postdata['pages'] as $k => $v ) {
					update_option( 'tp_hotel_booking_' . $k, $v );
				}
			}
		}

		do_action( 'wphb/setup-wizard/update-settings', $postdata, $step );
	}

	/**
	 * Return array of all steps are available when running setup wizard.
	 *
	 * @return array
	 */
	public function get_steps() {
		static $steps = false;
		if ( ! $steps ) {
			$steps = apply_filters(
				'wphb/setup-wizard/steps',
				array(
					'welcome' => array(
						'title'       => __( 'Welcome', 'wp-hotel-booking' ),
						'callback'    => array( $this, 'step_welcome' ),
						'next_button' => __( 'Run Setup Wizard', 'wp-hotel-booking' ),
					),
					'pages'   => array(
						'title'    => __( 'Pages', 'wp-hotel-booking' ),
						'callback' => array( $this, 'step_pages' ),
					),
					'payment' => array(
						'title'    => __( 'Payment', 'wp-hotel-booking' ),
						'callback' => array( $this, 'step_payment' ),
					),
					'finish'  => array(
						'title'    => __( 'Finish', 'wp-hotel-booking' ),
						'callback' => array( $this, 'step_finish' ),
					),
				)
			);
		}

		return $steps;
	}

	/**
	 * Get all keys of available steps.
	 *
	 * @return array
	 */
	public function get_step_keys() {
		return array_keys( $this->get_steps() );
	}

	/**
	 * Get key of current step.
	 *
	 * @param bool $key
	 *
	 * @return mixed|string
	 */
	public function get_current_step( $key = true ) {
		$current = hb_get_request( 'step' );
		$steps   = $this->get_steps();

		if ( empty( $steps[ $current ] ) ) {
			$key_steps = array_keys( $steps );
			$current   = reset( $key_steps );
		}

		$step = $steps[ $current ];
		if ( empty( $step['slug'] ) ) {
			$step['slug'] = $current;
		}

		return $key ? $current : $step;
	}

	/**
	 * Return true if the first step is viewing.
	 *
	 * @return bool
	 */
	public function is_first_step() {
		$steps = $this->get_step_keys();

		return array_search( $this->get_current_step(), $steps ) === 0;
	}

	/**
	 * Return true if the last steo is viewing.
	 *
	 * @return bool
	 */
	public function is_last_step() {
		$steps = $this->get_step_keys();

		return array_search( $this->get_current_step(), $steps ) === sizeof( $steps ) - 1;
	}

	/**
	 * Get url of next step.
	 *
	 * @return string
	 */
	public function get_next_url() {
		$current = $this->get_current_step();
		$steps   = $this->get_step_keys();
		$at      = array_search( $current, $steps );
		if ( $at < sizeof( $steps ) - 1 ) {
			$at ++;
		}

		return esc_url_raw( add_query_arg( 'step', $steps[ $at ], admin_url( $this->_base_url ) ) );
	}

	/**
	 * Get url of prev step.
	 *
	 * @return string
	 */
	public function get_prev_url() {
		$current = $this->get_current_step();
		$steps   = $this->get_step_keys();
		$at      = array_search( $current, $steps );
		if ( $at > 0 ) {
			$at --;
		}

		return esc_url_raw( add_query_arg( 'step', $steps[ $at ], admin_url( $this->_base_url ) ) );
	}

	/**
	 * Get position number of a step.
	 *
	 * @param string $step - Optional.
	 *
	 * @return mixed
	 */
	public function get_step_position( $step = '' ) {
		if ( ! $step ) {
			$step = $this->get_current_step();
		}

		$steps = $this->get_step_keys();

		return array_search( $step, $steps );
	}

	public function get_payments() {
		return array(
			'paypal' => array(
				'name'     => __( 'PayPal', 'wp-hotel-booking' ),
				'icon'     => WPHB_PLUGIN_URL . '/assets/images/paypal-2.png',
				'callback' => array( $this, 'setup_paypal' ),
			),
		);
	}

	public function setup_paypal() {
		hb_get_template( 'setup/setup-paypal.php' );
	}

	/**
	 * Welcome step content.
	 */
	public function step_welcome() {
		hb_get_template( 'setup/steps/welcome.php' );
	}

	public function step_pages() {
		$pages = tp_hotel_booking_pages_required();
		hb_get_template( 'setup/steps/pages.php', array( 'pages' => $pages ) );
	}

	public function step_payment() {
		hb_get_template( 'setup/steps/payment.php' );
	}

	public function step_finish() {
		hb_get_template( 'setup/steps/finish.php' );
	}

	public function scripts() {
	}

	/**
	 * Get singleton instance
	 *
	 * @return bool|WPHB_Setup_Wizard
	 */
	public static function instance() {
		static $instance = false;

		if ( ! $instance ) {
			$instance = new self();
		}

		return $instance;
	}
}
return WPHB_Setup_Wizard::instance();
