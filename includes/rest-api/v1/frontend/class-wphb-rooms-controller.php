<?php
/**
 * Class WPHB_REST_Rooms_Controller
 */
class WPHB_REST_Rooms_Controller extends WPHB_Abstract_REST_Controller {
	/**
	 * WPHB_REST_Rooms_Controller constructor.
	 */
	public function __construct() {
		$this->namespace = 'wphb/v1';
		$this->rest_base = 'rooms';
		parent::__construct();
	}

	/**
	 * Register routes API
	 */
	public function register_routes() {
		$this->routes = array(
			'search-rooms'   => array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'search_rooms' ),
					'permission_callback' => '__return_true',
				),
			),
			'book-rooms'     => array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'book_rooms_page_search' ),
					'permission_callback' => '__return_true',
				),
			),
			'add-extra-cart' => array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'add_extra_to_cart' ),
					'permission_callback' => '__return_true',
				),
			),
			// 'remove-item'    => array(
			// array(
			// 'methods'             => WP_REST_Server::CREATABLE,
			// 'callback'            => array( $this, 'remove_item_cart' ),
			// 'permission_callback' => '__return_true',
			// ),
			// ),
		);

		parent::register_routes();
	}

	/**
	 * It searches for rooms and returns the results in a JSON response
	 *
	 * @param WP_REST_Request request The request object.
	 */
	public function search_rooms( WP_REST_Request $request ) {

		$params           = $request->get_params();
		$response         = new WPHB_REST_RESPONSE();
		$response->status = 'success';

		$check_in_date   = $params['check_in_date'];
		$check_out_date  = $params['check_out_date'];
		$adults_capacity = $params['adults'];
		$max_child       = $params['max_child'];
		$paged           = absint( $params['paged'] ) ?? 1;
		$limit           = hb_settings()->get( 'posts_per_page', 8 );

		try {
			$date_format = get_option('date_format');

			if ( strpos($check_in_date, '/' ) !== false ) {
				$check_in_date = DateTime::createFromFormat( $date_format , $check_in_date)->format('F j, Y');
			}

			if ( strpos( $check_out_date, '/' ) !== false ) {
				$check_out_date = DateTime::createFromFormat( $date_format, $check_out_date)->format('F j, Y');
			}

			$atts = array(
				'check_in_date'  => $check_in_date,
				'check_out_date' => $check_out_date,
				'adults'         => $adults_capacity,
				'max_child'      => $max_child,
				'search_page'    => null,
				'widget_search'  => false,
				'hb_page'        => $paged,
			);
			
			$results    = hb_search_rooms( $atts );
			// print_r($results);die;
			$total_page = ceil( $results['total'] / $limit );

			if ( empty( $results ) || empty( $results['data'] ) ) {
				$response->status = 'error';
				throw new Exception( esc_html__( 'Error: No rooms available!.', 'wp-hotel-booking' ) );
			}

			$response->data->pagination = hb_get_template_content(
				'search/v2/pagination-v2.php',
				array(
					'total' => $total_page,
					'paged' => $results['page'],
				)
			);

			$response->data->content = hb_get_template_content(
				'search/v2/results-v2.php',
				array(
					'results' => $results,
					'atts'    => $atts,
				)
			);
		} catch ( Exception $e ) {
			$response->message = $e->getMessage();
		}
		wp_send_json( $response );
	}

	/**
	 * It adds a room to the cart
	 *
	 * @param WP_REST_Request request The request object.
	 */
	public function book_rooms_page_search( WP_REST_Request $request ) {
		$params           = $request->get_params();
		$response         = new WPHB_REST_RESPONSE();
		$response->status = 'error';

		$room_id        = absint( $params['roomID'] ) ?? 0;
		$check_in_date  = sanitize_text_field( wp_unslash( $params['checkinDate'] ) ) ?? '';
		$check_out_date = sanitize_text_field( wp_unslash( $params['checkoutDate'] ) ) ?? '';
		$num_room       = absint( $params['numRoom'] ) ?? 1;

		// add extra room when disable option :tp_hotel_booking_custom_process
		$extra_data         = $params['extraData'] ?? array();
		$extra_selected     = array();
		$extra_selected_qty = array();

		if ( ! empty( $extra_data ) ) {
			foreach ( $extra_data as $extra ) {
				$extra_selected[ $extra['extraID'] ]     = 'on';
				$extra_selected_qty[ $extra['extraID'] ] = $extra['qty'];
			}
		}

		$date_format = get_option('date_format');

		if ( strpos($check_in_date, '/' ) !== false ) {
			$check_in_date = DateTime::createFromFormat( $date_format , $check_in_date)->format('F j, Y');
		}

		if ( strpos( $check_out_date, '/' ) !== false ) {
			$check_out_date = DateTime::createFromFormat( $date_format, $check_out_date)->format('F j, Y');
		}

		$args_room = array(
			'product_id'                    => $room_id,
			'check_in_date'                 => $check_in_date,
			'check_out_date'                => $check_out_date,
			'hb_optional_quantity_selected' => $extra_selected,
			'hb_optional_quantity'          => $extra_selected_qty,
		);

		try {

			if ( empty( $room_id ) ) {
				throw new Exception( esc_html__( 'Error: Room ID is not exists!.', 'wp-hotel-booking' ) );
			}

			if ( empty( $check_in_date ) || empty( $check_out_date ) ) {
				throw new Exception( esc_html__( 'Error: Date incorrect !.', 'wp-hotel-booking' ) );
			}

			$room = get_post( $room_id );

			if ( ! $room || ! is_a( $room, 'WP_POST' ) || $room->post_type != 'hb_room' ) {
				throw new Exception( esc_html__( 'Error: Room ID is not exists !.', 'wp-hotel-booking' ) );
			}

			if ( ! isset( $num_room ) ) {
				throw new Exception( esc_html__( 'Error: Can not select zero room !.', 'wp-hotel-booking' ) );
			}

			$args_room = apply_filters( 'hotel_booking_add_cart_params', $args_room );

			$cart_item_id = WP_Hotel_Booking::instance()->cart->add_to_cart( $room_id, $args_room, $num_room );

			$cart_item_id = apply_filters( 'hotel_booking_cart_item_id', $cart_item_id, $args_room, $num_room );

			if ( ! is_wp_error( $cart_item_id ) ) {
				$cart_item = WP_Hotel_Booking::instance()->cart->get_cart_item( $cart_item_id );
				$room      = $cart_item->product_data;

				do_action( 'hotel_booking_added_cart_completed', $cart_item_id, $args_room, $num_room );

				$results = array(
					'status'    => 'success',
					'message'   => __( 'Added successfully.', 'wp-hotel-booking' ),
					'id'        => $room_id,
					'permalink' => get_permalink( $room_id ),
					// 'name'      => sprintf( '%s', $room->name ) . ( $room->capacity_title ? sprintf( '(%s)', $room->capacity_title ) : '' ),
					'name'      => sprintf( '%s', $room->name ),
					'quantity'  => $num_room,
					'cart_id'   => $cart_item_id,
					'total'     => hb_format_price( WP_Hotel_Booking::instance()->cart->get_cart_item( $cart_item_id )->amount ),
					'redirect'  => '',
				);

				$pageRedirect = WPHB_Settings::instance()->getPageRedirect();

				$additionPackage = get_post_meta( $room_id, '_hb_room_extra', true );

				if ( (int) get_option( 'tp_hotel_booking_custom_process', 0 ) && ! empty( $additionPackage ) ) {
					// Addition package not null && custom_process
					$results['redirect'] = get_option( 'tp_hotel_booking_custom_process' ) ? add_query_arg(
						array(
							'is_page_room_extra' => 'select-room-extra',
							'cart_id'            => $cart_item_id,
							'room_id'            => $room_id,
						),
						hb_get_search_room_url()
					) : '';
				} else {
					$results['redirect'] = $pageRedirect;
				}

				$results = apply_filters( 'hotel_booking_add_to_cart_results', $results, $room );

				$response->data->results = $results;

			} else {
				throw new Exception( esc_html__( 'Warning: Room selected. Please View Cart to change order!.', 'wp-hotel-booking' ) );
			}

			$response->status = 'success';

		} catch ( Exception $e ) {
			$response->message = $e->getMessage();
		}
		wp_send_json( $response );
	}

	/**
	 * It adds an extra to the cart
	 *
	 * @param WP_REST_Request request The request object.
	 */
	public function add_extra_to_cart( WP_REST_Request $request ) {
		$params           = $request->get_params();
		$response         = new WPHB_REST_RESPONSE();
		$response->status = 'error';

		$cart_id    = $params['cartID'] ?? '';
		$extra_data = $params['extraData'] ?? array();

		// add extra room when disable option :tp_hotel_booking_custom_process
		$extra_selected     = array();
		$extra_selected_qty = array();

		if ( ! empty( $extra_data ) ) {
			foreach ( $extra_data as $extra ) {
				$extra_selected[ $extra['extraID'] ]     = 'on';
				$extra_selected_qty[ $extra['extraID'] ] = $extra['qty'];
			}
		}

		try {
			if ( ! $cart_id ) {
				throw new Exception( esc_html__( 'Error: Cart ID is invalid.', 'wp-hotel-booking' ) );
			}

			$cart       = WPHB_Cart::instance();
			$extra_cart = HB_Extra_Cart::instance();
			$cart_item  = $cart->get_cart_item( $cart_id );
			$extra_cart->ajax_added_cart(
				$cart_id,
				array(
					'product_id'                    => $cart_item->product_id,
					'check_in_date'                 => $cart_item->check_in_date,
					'check_out_date'                => $cart_item->check_out_date,
					'hb_optional_quantity'          => $extra_selected_qty,
					'hb_optional_quantity_selected' => $extra_selected,
				),
				array(),
				false
			);

			$cart_url     = hb_get_cart_url();
			$pageRedirect = WPHB_Settings::instance()->getPageRedirect();

			if ( $pageRedirect == '' ) {
				$pageRedirect = $cart_url;
			}

			$response->status   = 'success';
			$response->redirect = $pageRedirect;

		} catch ( Exception $e ) {
			$response->message = $e->getMessage();
		}
		wp_send_json( $response );
	}
}
