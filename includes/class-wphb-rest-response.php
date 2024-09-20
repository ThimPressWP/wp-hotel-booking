<?php

/**
 * Class WPHB_REST_Response
 *
 * @version 1.0
 * @since 3.2.8
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WPHB_REST_Response {
	/**
	 * Status.
	 *
	 * @var string.
	 */
	public $status = 'error';
	/**
	 * Message.
	 *
	 * @var string .
	 */
	public $message = '';
	/**
	 * Extra data
	 *
	 * @var object
	 */
	public $data;

	/**
	 * WPHB_REST_Response constructor.
	 */
	public function __construct() {
		$this->data = new stdClass();
	}
}
