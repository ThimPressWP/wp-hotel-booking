<?php
/**
 * Class WPHB_REST_Admin_Update_Controller
 */
class WPHB_REST_Admin_Update_Controller extends WPHB_Abstract_REST_Controller {
	/**
	 * WPHB_REST_Admin_Update_Controller constructor.
	 */
	public function __construct() {
		$this->namespace = 'wphb/v1/admin';
		$this->rest_base = 'rooms';
		parent::__construct();
	}

	/**
	 * Register routes API
	 */
	public function register_routes() {
		$this->routes = array(
			'update-field' => array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'update_field_room' ),
					'permission_callback' => '__return_true',
				),
			),
		);

		parent::register_routes();
	}

	/**
	 * Check user is Admin
	 *
	 * @return bool
	 */
	public function check_admin_permission(): bool {
		return WPHB_REST_Authentication::check_admin_permission();
	}

	/**
	 *
	 *
	 * @param WP_REST_Request request The request object.
	 */
	public function update_field_room( WP_REST_Request $request ) {
		$params            = $request->get_params();
		$response          = new WPHB_REST_RESPONSE();
		$response->status  = 'error';
		$response->message = __( 'Update Database Error', 'wp-hotel-booking' );
		try {
			// change value max aldult of room
			$args      = array(
				'post_type'      => 'hb_room',
				'posts_per_page' => -1,
			);
			$the_query = new WP_Query( $args );

			if ( $the_query->have_posts() ) :
				while ( $the_query->have_posts() ) :
					$the_query->the_post();
					$max_adult = get_post_meta( get_the_ID(), '_hb_room_capacity_adult', true );
					if ( empty( $max_adult ) ) {
						$room_id = get_the_ID();
						$term_id = get_post_meta( $room_id, '_hb_room_origin_capacity', true ) ? get_post_meta( $room_id, '_hb_room_origin_capacity', true ) : get_post_meta( $room_id, '_hb_room_capacity', true );
						$value   = get_term_meta( $term_id, 'hb_max_number_of_adults', true );
						update_post_meta( $room_id, '_hb_room_capacity_adult', ! empty( $value ) ?: 1 );
					}
				endwhile;
			endif;
			wp_reset_postdata();

			// update option after change value max aldult of room
			update_option( 'hotel_booking_version', WPHB_VERSION );
			$response->status  = 'success';
			$response->message = __( 'Update Success ! ', 'wp-hotel-booking' );

		} catch ( Exception $e ) {
			$response->message = $e->getMessage();
		}
		wp_send_json( $response );
	}
}
