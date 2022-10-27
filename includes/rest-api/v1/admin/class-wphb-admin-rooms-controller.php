<?php
/**
 * Class WPHB_REST_Admin_Rooms_Controller
 */
class WPHB_REST_Admin_Rooms_Controller extends WPHB_Abstract_REST_Controller {
	/**
	 * WPHB_REST_Rooms_Controller constructor.
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
			'pricing-plans'    => array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'pricing_plans' ),
					'permission_callback' => '__return_true',
				),
			),
			'block-date'       => array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'block_date' ),
					'permission_callback' => '__return_true',
				),
			),
			'manager-bookings' => array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'manager_bookings' ),
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
	 * It returns a JSON object containing the pricing plans for a given room ID and date
	 *
	 * @param WP_REST_Request request The request object.
	 */
	public function pricing_plans( WP_REST_Request $request ) {
		$params           = $request->get_params();
		$response         = new WPHB_REST_RESPONSE();
		$response->status = 'error';
		$date             = $params['date'] ? date( 'm/d/Y', strtotime( $params['date'] ) ) : array();
		$room_id          = $params['roomID'] ?? 0;
		$data             = array();

		try {
			if ( empty( $room_id ) ) {
				throw new Exception( esc_html__( 'Error: Room ID is invalid.', 'wp-hotel-booking' ) );
			}
			$data             = hotel_booking_print_pricing_json( $room_id, $date );
			$response->data   = $data;
			$response->status = 'success';

		} catch ( Exception $e ) {
			$response->message = $e->getMessage();
		}
		wp_send_json( $response );
	}

	/**
	 * A function that is used to block the date.
	 *
	 * @param WP_REST_Request request The request object.
	 */
	public function block_date( WP_REST_Request $request ) {
		$params           = $request->get_params();
		$response         = new WPHB_REST_RESPONSE();
		$response->status = 'error';

		$args        = $params['argsBlock'] ?? array();
		$removeMonth = isset( $params['removeMonth'] ) ? $params['removeMonth'] : false;
		$room_id     = $params['roomID'] ?? 0;

		try {
			if ( empty( $room_id ) ) {
				throw new Exception( esc_html__( 'Error: Room ID is invalid.', 'wp-hotel-booking' ) );
			}
			$block_id = get_post_meta( $room_id, 'hb_blocked_id', true );

			if ( empty( $block_id ) ) {
				// new data v2
				$block_id = wp_insert_post(
					array(
						'post_type'    => 'hb_blocked',
						'post_status'  => 'publish',
						'post_title'   => __( 'Block item', 'wp-hotel-booking' ),
						'post_content' => __( 'Block item', 'wp-hotel-booking' ),
					)
				);
				if ( ! is_wp_error( $block_id ) ) {
					add_post_meta( $room_id, 'hb_blocked_id', $block_id );
				}
			}
			// compare data old
			$args_block = get_post_meta( $block_id, 'hb_blocked_time' );
			if ( $removeMonth ) {
				if ( ! empty( $args_block ) ) {
					if ( ! empty( $args ) ) {
						foreach ( $args as $value ) {
							if ( in_array( $value, $args_block ) ) {
								delete_post_meta( $block_id, 'hb_blocked_time', $value );
							}
						}
					} else {
						delete_post_meta( $block_id, 'hb_blocked_time' );
					}
				}
				$response->message = esc_html__( 'Open success', 'wp-hotel-booking' );
			} else {
				delete_post_meta( $block_id, 'hb_blocked_time' );
				if ( ! empty( $args ) ) {

					foreach ( $args as $block ) {
						add_post_meta( $block_id, 'hb_blocked_time', $block );
					}
					$response->message = esc_html__( 'Block success', 'wp-hotel-booking' );
				}
			}

			$response->status = 'success';

		} catch ( Exception $e ) {
			$response->message = $e->getMessage();
		}
		wp_send_json( $response );
	}

	/**
	 * Getting the list of bookings for a specific date range
	 *
	 * @param WP_REST_Request request The request object.
	 */
	public function manager_bookings( WP_REST_Request $request ) {
		$params           = $request->get_params();
		$response         = new WPHB_REST_RESPONSE();
		$response->status = 'error';
		$start            = $params['startDay'] ?? '';
		$end              = $params['endDay'] ?? '';
		$data             = array();

		$color_status = array(
			'hb-processing' => '#ffb316',
			'hb-cancelled'  => '#b55b5b',
			'hb-completed'  => '#2eb0d1',
			'hb-pending'    => '#cccccc',
		);

		try {
			if ( empty( $start ) || empty( $end ) ) {
				throw new Exception( esc_html__( 'Error: Timer invalid.', 'wp-hotel-booking' ) );
			}
			$args      = array(
				'post_type'  => 'hb_booking',
				'posts_per_page' => -1
			);

			$format_time = get_option('date_format');
			$the_query = new WP_Query( $args );
			if ( $the_query->have_posts() ) :
				while ( $the_query->have_posts() ) :
					$the_query->the_post();
					$order = get_post( get_the_ID() );
					if ( ! empty( $order ) ) {
						$rooms = hb_get_order_items( get_the_ID() );
						foreach ( $rooms as $room ) {
							$data_item = hb_get_order_item_meta( $room->order_item_id );
							$status    = get_post_status( $room->ID ?? 0 );
							// if ( $data_item['check_in_date'][0] < strtotime($start) || $data_item['check_out_date'][0] > strtotime($end) ) {
							// 	continue;
							// } 
							$data[]    = array(
								'title'           => $room->order_item_name,
								'start'           => date( 'Y-m-d', ( $data_item['check_in_date'][0] ) ),
								'end'             => date( 'Y-m-d', ( $data_item['check_out_date'][0] + ( 24 * 60 * 60 ) ) ),
								'data_item'       => $data_item,
								'backgroundColor' => array_key_exists( $status, $color_status ) ? $color_status[ $status ] : '#2eb0d1',
								'borderColor'     => array_key_exists( $status, $color_status ) ? $color_status[ $status ] : '#2eb0d1',
								'data_order'      => array(
									'id'         => $order->ID,
									'link_edit'  => get_edit_post_link( $order->ID ),
									'order_date' => gmdate( $format_time, strtotime( $order->post_date ) ),
									'title'      => $room->order_item_name,
									'total'      => hb_format_price( hb_get_order_item_meta( $room->order_item_id, 'total', true ), hb_get_currency_symbol( get_option( 'tp_hotel_booking_currency', 'USD' ) ) ),
									'start_date_popup' => gmdate( $format_time, $data_item['check_in_date'][0] ),
									'end_date_popup'   => gmdate( $format_time, $data_item['check_out_date'][0] ),
								),
							);
						};
					}
				endwhile;
			endif;

			if ( ! empty( $data ) ) {
				$response->data   = $data;
				$response->status = 'success';
			}
		} catch ( Exception $e ) {
			$response->message = $e->getMessage();
		}

		wp_send_json( $response );
	}
}
