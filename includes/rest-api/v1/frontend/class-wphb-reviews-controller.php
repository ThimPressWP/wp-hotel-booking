<?php

/**
 * Class WPHB_REST_Rooms_Controller
 */
class WPHB_REST_Reviews_Controller extends WPHB_Abstract_REST_Controller {
	/**
	 * WPHB_REST_Rooms_Controller constructor.
	 */
	public function __construct() {
		$this->namespace = 'wphb/v1';
		$this->rest_base = 'reviews';
		parent::__construct();
	}

	/**
	 * Register routes API
	 */
	public function register_routes() {
		$this->routes = array(
			'upload-images'   => array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'upload_images' ),
					'permission_callback' => '__return_true',
				),
			),
		);

		parent::register_routes();
	}

	/**
	 * @param WP_REST_Request $request
	 *
	 * @return void
	 */
	public function upload_images( WP_REST_Request $request ) {
//		echo '<pre>';
//		print_r($_FILES);
//		echo '</pre>';
//		die;
		$params           = $request->get_params();
		$response         = new WPHB_REST_RESPONSE();
		$response->status = 'success';
		$response->aaa = 'aaa';
		try {

		} catch ( Exception $e ) {
			$response->message = $e->getMessage();
		}

		wp_send_json( $response );
	}
}

