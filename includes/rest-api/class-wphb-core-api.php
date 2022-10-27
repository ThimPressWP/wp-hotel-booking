<?php

/**
 * Class WPHB_Core_API
 *
 * @author Thimpress
 * @version 1.0.1
 * @since 1.10.6
 */
defined( 'ABSPATH' ) || exit;

class WPHB_Core_API extends WPHB_Abstract_API {
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Includes files
	 */
	public function rest_api_includes() {
		parent::rest_api_includes();

		$path_version = DIRECTORY_SEPARATOR . $this->version . DIRECTORY_SEPARATOR . 'frontend';

		include_once dirname( __FILE__ ) . $path_version . '/class-wphb-rooms-controller.php';

		do_action( 'wphb/core-api/includes' );
	}

	public function rest_api_register_routes() {
		$controllers = array(
			'WPHB_REST_Rooms_Controller',
		);

		$this->controllers = apply_filters( 'wphb/core-api/controllers', $controllers );

		parent::rest_api_register_routes();
	}
}

new WPHB_Core_API();
