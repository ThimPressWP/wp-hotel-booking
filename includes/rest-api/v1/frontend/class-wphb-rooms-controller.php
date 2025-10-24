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
			'room-pricing'   => array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'room_pricing' ),
					'permission_callback' => '__return_true',
				),
			),
			'single-room-price-details'   => array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'single_room_price_details' ),
					'permission_callback' => '__return_true',
				),
			),
			'calculate-booking-price'   => array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'calculate_booking_price' ),
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
			$date_format = get_option( 'date_format' );

			if ( strpos( $check_in_date, '/' ) !== false ) {
				// Strtotime() doesn't work with dd/mm/YYYY format
				if ( $date_format == 'd/m/Y' ) {
					$check_in_date = str_replace( '/', '-', $check_in_date );
				}
				$check_in_date = date( 'F j, Y', strtotime( $check_in_date ) );
			}

			if ( strpos( $check_out_date, '/' ) !== false ) {
				// Strtotime() doesn't work with dd/mm/YYYY format
				if ( $date_format == 'd/m/Y' ) {
					$check_out_date = str_replace( '/', '-', $check_out_date );
				}
				$check_out_date = date( 'F j, Y', strtotime( $check_out_date ) );
			}

			$atts = array(
				'check_in_date'  => $check_in_date,
				'check_out_date' => $check_out_date,
				'adults'         => $adults_capacity,
				'max_child'      => $max_child,
				'search_page'    => null,
				'widget_search'  => false,
				'hb_page'        => $paged,
				'min_price'      => $params['min_price'] ?? '',
				'max_price'      => $params['max_price'] ?? '',
				'rating'         => $params['rating'] ?? '',
				'room_type'      => $params['room_type'] ?? '',
				'sort_by'        => $params['sort_by'] ?? '',
			);

			$results = hb_search_rooms( $atts );
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

			$response->data->show_number = hb_get_show_room_text(
				array(
					'paged'         => $results['page'] ?? 1,
					'total'         => $results['total'],
					'item_per_page' => $limit,
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
		$check_in_date  = sanitize_text_field( wp_unslash( $params['checkinDate'] ?? '' ) );
		$check_out_date = sanitize_text_field( wp_unslash( $params['checkoutDate'] ?? '' ) );
		$num_room       = absint( $params['numRoom'] ?? 1 );
		$adults         = absint( $params['adults'] ) ?? 1;
		$child          = absint( $params['maxChild'] ) ?? 0;

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

		$date_format = get_option( 'date_format' );

		if ( strpos( $check_in_date, '/' ) !== false ) {
			// Strtotime() doesn't work with dd/mm/YYYY format
			if ( $date_format == 'd/m/Y' ) {
				$check_in_date = str_replace( '/', '-', $check_in_date );
			}
			$check_in_date = date( 'F j, Y', strtotime( $check_in_date ) );
		}

		if ( strpos( $check_out_date, '/' ) !== false ) {
			// Strtotime() doesn't work with dd/mm/YYYY format
			if ( $date_format == 'd/m/Y' ) {
				$check_out_date = str_replace( '/', '-', $check_out_date );
			}
			$check_out_date = date( 'F j, Y', strtotime( $check_out_date ) );
		}

		$args_room = array(
			'product_id'                    => $room_id,
			'check_in_date'                 => $check_in_date,
			'check_out_date'                => $check_out_date,
			'hb_optional_quantity_selected' => $extra_selected,
			'hb_optional_quantity'          => $extra_selected_qty,
			'adult_qty'                     => $adults,
			'child_qty'                     => $child,
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

				if ( ! empty( $additionPackage ) ) {
					// Addition package not null && custom_process
					$results['redirect']  = get_option( 'tp_hotel_booking_custom_process' ) ? add_query_arg(
						array(
							'is_page_room_extra' => 'select-room-extra',
							'cart_id'            => $cart_item_id,
							'room_id'            => $room_id,
						),
						hb_get_search_room_url()
					) : '';
					$results['has_extra'] = true;
					ob_start();
					wphb_get_template_no_override(
						'single-room/search/extra-check-dates-room.php',
						array( 'post_id' => $room_id )
					);
					echo sprintf(
						'<div class="room-extra-options">
					        <button class="add-extra-to-cart hb_button" data-cartid="%1$s" type="button">%2$s</button>
					    </div>',
						$cart_item_id,
						__( 'Add extra to cart', 'wp-hotel-booking' )
					);
					// $extra_html = ob_get_clean();
					$results['extra_html'] = ob_get_clean();
				} else {
					$results['has_extra'] = false;
				}
				$results['redirect'] = $pageRedirect;

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
			$extra_arr  = array(
				'product_id'                    => $cart_item->product_id,
				'check_in_date'                 => $cart_item->check_in_date,
				'check_out_date'                => $cart_item->check_out_date,
				'hb_optional_quantity'          => $extra_selected_qty,
				'hb_optional_quantity_selected' => $extra_selected,
			);
			$extra_cart->ajax_added_cart(
				$cart_id,
				$extra_arr,
				array(),
				false
			);

			$cart_url     = hb_get_cart_url();
			$pageRedirect = WPHB_Settings::instance()->getPageRedirect();

			if ( $pageRedirect == '' ) {
				$pageRedirect = $cart_url;
			}

			$response->status         = 'success';
			$response->data->redirect = $pageRedirect;

		} catch ( Exception $e ) {
			$response->message = $e->getMessage();
		}
		wp_send_json( $response );
	}

	/**
	 * get room pricing plan per day. show on single room pricing plan calendar
	 * @param  WP_REST_Request $request
	 * @return WPHB_REST_RESPONSE $response
	 */
	public function room_pricing( WP_REST_Request $request ) {
		$params   = $request->get_params();
		$response = new WPHB_REST_RESPONSE();
		try {
			$room_id = WPHB_Helpers::get_param( 'roomId', 0, 'int' );
			if ( ! $room_id ) {
				throw new Exception( esc_html__( 'roomId is required', 'wp-hotel-booking' ) );
			}
			if ( get_post_type( $room_id ) !== 'hb_room' ) {
				throw new Exception( esc_html__( 'roomId is invalid', 'wp-hotel-booking' ) );
			}
			$first_month = WPHB_Helpers::get_param( 'month', intval( date( 'n' ) ), 'int' );
			$year        = WPHB_Helpers::get_param( 'year', intval( date( 'Y' ) ), 'int' );

			$first_month_date_obj  = DateTime::createFromFormat( 'Y-n', "$year-$first_month");
			$first_month_date_str  = $first_month_date_obj->format( 'm/d/Y' );
			$second_month_date_obj = $first_month_date_obj->modify( '+1 month' );
			
			$first_month_pricing  = $this->get_room_pricing( $room_id, $first_month_date_str );
			$second_month_pricing = $this->get_room_pricing( $room_id, $second_month_date_obj->format('m/d/Y') );

			$response->status        = 'success';
			$response->data->pricing = array_merge( $first_month_pricing, $second_month_pricing );
		} catch (Exception $e) {
			$response->message = $e->getMessage();
		}
		return rest_ensure_response( $response );
	}

	/**
	 * get room pricing in a month
	 * @param  integer $room_id [description]
	 * @param  string $date    [description]
	 * @return array
	 */
	public function get_room_pricing( $room_id = null, $date = null ) {
		$start = date( 'm/01/Y', strtotime( $date ) );
		$end   = date( 'm/t/Y', strtotime( $date ) );

		$pricing = array();
		if ( ! $room_id || ! $date ) {
			return $pricing;
		}

		$month_day = date( 't', strtotime( $end ) );
		$room      = WPHB_Room::instance( $room_id );
		for ( $i = 0; $i < $month_day; $i++ ) {
			$day   = strtotime( $start ) + $i * 24 * HOUR_IN_SECONDS;
			$price = $room->get_price( $day, false );
			$price = $price ? floatval( $price ) : '0';

			$pricing[] = array(
				'price'      => $price,
				'date'       => date( 'Y-m-d\TH:i:s\Z', $day ),
				'price_html' => hb_format_price( $price, true, false ),
			);
		}

		return $pricing;
	}

	public function single_room_price_details( WP_REST_Request $request ) {
		$response = new WPHB_REST_RESPONSE();
		try {
			$qty            = WPHB_Helpers::get_param( 'hb-num-of-rooms', 1, 'int' );
			$room_id        = WPHB_Helpers::get_param( 'room-id', 0, 'int' );
			$check_in_date  = WPHB_Helpers::get_param( 'check_in_date', date( 'Y/m/d' ) );
			$check_out_date = WPHB_Helpers::get_param( 'check_out_date', date( 'Y/m/d', strtotime( '+1 day' ) ) );
			$adult_qty      = WPHB_Helpers::get_param( 'adult_qty', 1, 'int' );
			$child_qty      = WPHB_Helpers::get_param( 'child_qty', 0, 'int' );

			$hb_optional_quantity_selected = WPHB_Helpers::get_param( 'hb_optional_quantity_selected', [] );
			$hb_optional_quantity          = WPHB_Helpers::get_param( 'hb_optional_quantity', [] );
			if ( ! $room_id ) {
				throw new Exception( esc_html__( 'roomId is required', 'wp-hotel-booking' ) );
			}
			if ( get_post_type( $room_id ) !== 'hb_room' ) {
				throw new Exception( esc_html__( 'roomId is invalid', 'wp-hotel-booking' ) );
			}
			
			$room = WPHB_Room::instance( $room_id,
				array(
					'check_in_date'  => $check_in_date,
					'check_out_date' => $check_out_date,
					'quantity'       => $qty,
				)
			);
			$extra_info = array();
			if ( ! empty( $hb_optional_quantity_selected ) && ! empty( $hb_optional_quantity ) ) {
				foreach ( $hb_optional_quantity_selected as $extra_id => $select ) {
					$extra_info[ $extra_id ] = array(
						'extra_id' => $extra_id,
						'quantity' => $hb_optional_quantity[ $extra_id ],
					);
				}
			}

			ob_start();
			hb_get_template(
				'single-room/booking-room-price-details.php',
				array(
					'room'       => $room,
					'extra_info' => $extra_info,
				)
			);
			$content = ob_get_clean();

			$response->data->price_html = $content;
			$response->status           = 'success';
		} catch (Exception $e) {
			$response->message = $e->getMessage();
		}
		return rest_ensure_response( $response );
	}

	public function calculate_booking_price( WP_REST_Request $request ) {
		$response = new WPHB_REST_RESPONSE();
		try {
			$qty            = WPHB_Helpers::get_param( 'hb-num-of-rooms', 1, 'int' );
			$room_id        = WPHB_Helpers::get_param( 'room-id', 0, 'int' );
			$check_in_date  = WPHB_Helpers::get_param( 'check_in_date', date( 'Y/m/d' ) );
			$check_out_date = WPHB_Helpers::get_param( 'check_out_date', date( 'Y/m/d', strtotime( '+1 day' ) ) );
			$adult_qty      = WPHB_Helpers::get_param( 'adult_qty', 1, 'int' );
			$child_qty      = WPHB_Helpers::get_param( 'child_qty', 0, 'int' );

			$hb_optional_quantity_selected = WPHB_Helpers::get_param( 'hb_optional_quantity_selected', [] );
			$hb_optional_quantity          = WPHB_Helpers::get_param( 'hb_optional_quantity', [] );
			if ( ! $room_id ) {
				throw new Exception( esc_html__( 'roomId is required', 'wp-hotel-booking' ) );
			}
			if ( get_post_type( $room_id ) !== 'hb_room' ) {
				throw new Exception( esc_html__( 'roomId is invalid', 'wp-hotel-booking' ) );
			}
			
			$room = WPHB_Room::instance( $room_id,
				array(
					'check_in_date'  => $check_in_date,
					'check_out_date' => $check_out_date,
					'quantity'       => $qty,
				)
			);

			$room_price  = $room->amount_singular * $qty;
			$extra_price = 0;
			if ( ! empty( $hb_optional_quantity_selected ) && ! empty( $hb_optional_quantity ) ) {
				foreach ( $hb_optional_quantity_selected as $extra_id => $select ) {
					$extra_package = hotel_booking_get_product_class( $extra_id,
						array(
							'product_id'     => $extra_id,
							'check_in_date'  => $check_in_date,
							'check_out_date' => $check_out_date,
							'quantity'       => $hb_optional_quantity[ $extra_id ],
						)
					);
					$extra_package_price = $extra_package ? $extra_package->get_price_package() : 0;
					$extra_price         += $extra_package_price;
				}
			}
			$response->status            = 'success';
			$response->data->amount      = $room_price + $extra_price;
			$response->data->amount_html = hb_format_price( $room_price + $extra_price );
		} catch (Exception $e) {
			$response->message = $e->getMessage();
		}
		return rest_ensure_response( $response );
	}
}
