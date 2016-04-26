<?php
/**
 * @Author: ducnvtt
 * @Date:   2016-04-25 11:25:39
 * @Last Modified by:   ducnvtt
 * @Last Modified time: 2016-04-26 16:26:13
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class HBIP_Importer {

	public function __construct() {
		/* import acction */
		add_action( 'admin_init', array( $this, 'import' ) );
	}

	public function import() {
		if ( ! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'import-upload' ) ) {
			return;
		}

		$errors = array();

		$errors[] = __( 'Fuck you' );

		if ( $errors ) {
			$_SESSION[ 'hbip_import_errors' ] = $errors;
		}

		wp_redirect( admin_url( 'admin.php?page=tp-hotel-tools&tab=import' ) ); exit();
	}

}

new HBIP_Importer();
