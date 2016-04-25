<?php
/**
 * @Author: ducnvtt
 * @Date:   2016-04-25 11:26:10
 * @Last Modified by:   ducnvtt
 * @Last Modified time: 2016-04-25 17:00:34
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class HBIP_Exporter {

	public function __construct() {

		/* export submit */
		add_action( 'admin_init', array( $this, 'export' ) );
	}

	public function export() {
		/* verify nonce */
		if ( ! isset( $_POST['hbtool_export'] ) || ! wp_verify_nonce( $_POST['hbtool_export'], 'hbtool_export' ) || ! current_user_can( 'manage_opions' ) ) {
			return;
		}

		global $wpdb;
		$args = array(
				'export'	=> isset( $_POST[ 'export' ] ) ? $_POST[ 'export' ] : 'all'
			);

		/**
		 * Fires at the beginning of an export, before any headers are sent.
		 *
		 * @since 2.3.0
		 *
		 * @param array $args An array of export arguments.
		 */
		do_action( 'hb_tool_export', $args );

		$sitename = sanitize_key( get_bloginfo( 'name' ) );
		if ( ! empty( $sitename ) ) {
			$sitename .= '.';
		}
		$date = date( 'Y-m-d' );
		$filename = $sitename . 'hotel_data.' . $date . '.xml';
		/**
		 * Filter the export filename.
		 *
		 * @since 4.4.0
		 *
		 * @param string $wp_filename The name of the file for download.
		 * @param string $sitename    The site name.
		 * @param string $date        Today's date, formatted.
		 */
		$filename = apply_filters( 'hb_tool_export_filename', $filename, $sitename, $date );

		// header( 'Content-Description: File Transfer' );
		// header( 'Content-Disposition: attachment; filename=' . $filename );
		// header( 'Content-Type: text/xml; charset=' . get_option( 'blog_charset' ), true ); die();
	}

}

new HBIP_Exporter();
