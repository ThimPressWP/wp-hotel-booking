<?php

class HB_Extra_Cart
{
	/**
	 * instead of new class()
	 * @var null
	 */
	static $_instance = null;

	function __construct( $cart_id = null )
	{
		/**
		 * new script
		 */
		add_action( 'hotel_booking_added_cart', array( $this, 'ajax_added_cart' ), 10, 3 );
		/**
		 * add filter add to cart results array
		 * render object build mini cart
		 */
		add_filter( 'hotel_booking_add_to_cart_results', array( $this, 'add_to_cart_results' ), 10, 2 );

		/**
		 * after mini cart item loop
		 */
		add_action( 'hotel_booking_before_mini_cart_loop_price', array( $this, 'mini_cart_loop' ), 10, 2 );
		/**
		 * wp.template layout filter
		 */
		add_filter( 'hb_get_template', array( $this, 'mini_cart_layout' ), 10, 5 );

		/**
		 * sortable cart item
		 */
		add_filter( 'hotel_booking_load_cart_from_session', array( $this, 'hotel_booking_load_cart_from_session' ), 10, 1 );

		// /**
		//  * ajax remove packages
		//  */
		add_action( 'wp_ajax_tp_hotel_booking_remove_package', array( $this, 'remove_package' ) );
		add_action( 'wp_ajax_nopriv_tp_hotel_booking_remove_package', array( $this, 'remove_package' ) );

		/**
		 * append package into cart
		 */
		add_action( 'hotel_booking_cart_after_item', array( $this, 'cart_package_after_item' ), 10, 2 );

		/**
		 * append package into cart admin
		 */
		add_action( 'hotel_booking_admin_cart_after_item', array( $this, 'admin_cart_package_after_item' ), 10, 3 );

		// email new booking hook
		add_action( 'hotel_booking_email_new_booking', array( $this, 'email_new_booking' ), 10, 3 );

		add_filter( 'hb_extra_cart_input', array( $this, 'check_respondent' ) );

		// room item in booking details
		add_action( 'hotel_booking_after_room_item', array( $this, 'booking_post_type_extra_item' ), 10, 2 );

		// room item in email
		add_action( 'hotel_booking_email_after_room_item', array( $this, 'email_booking_post_type_extra_item' ), 10, 2 );


		// admin process
		add_filter( 'hotel_booking_check_room_available', array( $this, 'admin_load_package' ) );
		add_filter( 'hotel_booking_admin_load_order_item', array( $this, 'admin_load_package' ) );

		add_action( 'hotel_booking_updated_order_item', array( $this, 'admin_add_package_order' ), 10, 2 );

	}

	// add extra
	function ajax_added_cart( $cart_id, $cart_item, $posts )
	{
		if( empty( $posts[ 'hb_optional_quantity_selected' ] ) || empty( $posts[ 'hb_optional_quantity' ] ) ) {
			return;
		}

		remove_action( 'hotel_booking_added_cart', array( $this, 'ajax_added_cart' ), 10, 3 );

		if( $posts['hb_optional_quantity_selected'] )
		{
			$selected_quantity = $posts['hb_optional_quantity'];
			$turn_on = $posts['hb_optional_quantity_selected'];

			foreach ( $selected_quantity as $extra_id => $qty ) {
				// param
				$param = array(
						'product_id'		=> $extra_id,
						'parent_id'			=> $cart_id,
						'check_in_date'		=> $cart_item['check_in_date'],
						'check_out_date'	=> $cart_item['check_out_date']
					);
				if ( array_key_exists( $extra_id, $turn_on ) ) {
					$extra_cart_item_id = WP_Hotel_Booking::instance()->cart->add_to_cart( $extra_id, $param, $qty );
				}
				else
				{
					$extra_cart_item_id = WP_Hotel_Booking::instance()->cart->generate_cart_id( $param );
					WP_Hotel_Booking::instance()->cart->remove_cart_item( $extra_cart_item_id );
				}
			}
		}
		add_action( 'hotel_booking_added_cart', array( $this, 'ajax_added_cart' ), 10, 3 );
	}

	/**
	 * wp.template hook template
	 * @param  [type] $located       [description]
	 * @param  [type] $template_name [description]
	 * @param  [type] $args          [description]
	 * @param  [type] $template_path [description]
	 * @param  [type] $default_path  [description]
	 * @return [type]                [description]
	 */
	public function mini_cart_layout( $located, $template_name, $args, $template_path, $default_path )
	{
		if( $template_name === 'cart/mini_cart_layout.php' )
		{
			return tp_hb_extra_locate_template( 'shortcodes/mini_cart_layout.php' );
		}

		return $located;
	}

	/**
	 * extra package each cart item
	 * @param
	 * @return
	 */
	public function mini_cart_loop( $room, $cart_id )
	{
		$cart_item = WP_Hotel_Booking::instance()->cart->get_cart_item( $cart_id );
		if( ! $cart_item ) return;

		$packages = array();
		foreach ( WP_Hotel_Booking::instance()->cart->cart_contents as $id => $cart_item ) {
			if( isset( $cart_item->parent_id ) && $cart_item->parent_id === $cart_id )
			{
				$cart_item->cart_id = $id;
				$packages[] = $cart_item;
			}
		}

		ob_start();
		tp_hb_extra_get_template( 'loop/mini-cart-extra.php',
			array(
				'packages' 	=> $packages
		));
		echo ob_get_clean();
	}

	// add extra price
	function hotel_booking_load_cart_from_session( $cart_contents ) {
		foreach ( $cart_contents  as $parent_id => $cart_item ) {
			if ( ! isset( $cart_item->parent_id ) ) {
				foreach ( $cart_contents  as $id => $item ) {
					if ( isset( $item->parent_id ) && $item->parent_id === $parent_id ) {
						$cart_contents[$parent_id]->amount += $item->amount;
					}
				}
			}
		}

		return $cart_contents;
	}

	function remove_package()
	{
		if( ! isset( $_POST ) || ! defined( 'WPHB_BLOG_ID' ) )
			return;

		if( ! isset( $_POST['cart_id'] ) || ! $_POST['cart_id'] ) {
			wp_send_json( array( 'status' => 'success', 'message' => __( 'Cart ID is not exists.', 'wp-hotel-booking' ) ) );
		}

		$cart_id = sanitize_text_field( $_POST['cart_id'] );

		// cart item is exists
		if ( $package_item = WP_Hotel_Booking::instance()->cart->get_cart_item( $cart_id ) ) {
			if ( WP_Hotel_Booking::instance()->cart->remove_cart_item( $cart_id ) ) {
				// room cart item id
				$room = WP_Hotel_Booking::instance()->cart->get_cart_item( $package_item->parent_id );
				$results =  array(
		                    'status'    		=> 'success',
		                    'cart_id'        	=> $package_item->parent_id,
		                    'permalink' 		=> get_permalink( $room->product_id ),
		                    'name'      		=> sprintf( '%s', $room->product_data->name ) . ( $room->product_data->capacity_title ? sprintf( '(%s)', $room->product_data->capacity_title) : '' ),
		                    'quantity'  		=> $room->quantity,
		                    'total'     		=> hb_format_price( $room->amount ),
		                    // use to cart table
		                    'package_id'		=> $cart_id,
		                    'item_total'		=> hb_format_price( $room->amount_include_tax ),
		                    'sub_total'  		=> hb_format_price( WP_Hotel_Booking::instance()->cart->sub_total ),
			                'grand_total'   	=> hb_format_price( WP_Hotel_Booking::instance()->cart->total ),
			                'advance_payment' 	=> hb_format_price( WP_Hotel_Booking::instance()->cart->advance_payment )
		            );

				$extraRoom = WP_Hotel_Booking::instance()->cart->get_extra_packages( $package_item->parent_id );
				$extra_packages = array();
				if( $extraRoom ) {
					foreach ( $extraRoom as $cart_id => $cart_item ) {
						$extra = HB_Extra_Package::instance( $cart_item->product_id );
						$extra_packages[] = array(
								'package_title'				=> sprintf( '%s (%s)', $extra->title, hb_format_price( $extra->amount_singular ) ),
								'cart_id'					=> $cart_id,
								'package_quantity'			=> sprintf( 'x%s', $cart_item->quantity ),
								'package_respondent'		=> $extra->respondent
							);
					}
				}
				$results['extra_packages'] = $extra_packages;

				$results = apply_filters( 'hb_remove_package_results', $results, $package_item );

				do_action( 'hb_extra_removed_package', $package_item );
		        hb_send_json( $results );
			}

		} else {
			wp_send_json( array( 'status' => 'warning', 'message' => __( 'Cart item is not exists.', 'wp-hotel-booking' ) ) );
		}
	}

	/**
	 * add to cart results
	 * @param [array] $results [results]
	 * @param [object] $room    [room object class]
	 */
	function add_to_cart_results( $results, $room )
	{
		if( ! isset( $results[ 'cart_id' ] ) ) return $results;

		$cart_id = $results[ 'cart_id' ];
		$cart_contents = WP_Hotel_Booking::instance()->cart->cart_contents;

		if ( $cart_contents ) {
			$extra_packages = array();
			foreach ( $cart_contents as $cart_item_id => $cart_item ) {
				if ( isset( $cart_item->parent_id ) && $cart_item->parent_id === $cart_id ) {
					// extra class
					$extra = HB_Extra_Package::instance( $cart_item->product_id );
					$extra_packages[] = array(
							'package_title'		=> sprintf( '%s (%s)', $extra->title, hb_format_price( $extra->amount_singular ) ),
							'package_id'		=> $extra->ID,
							'cart_id'			=> $cart_item_id,
							'package_quantity'	=> sprintf( 'x%s', $cart_item->quantity )
						);
				}
			}
			$results[ 'extra_packages' ] = $extra_packages;
		}

		return $results;
	}

	// cart fontend
	function cart_package_after_item( $room, $cart_id )
	{
		$extra_packages = WP_Hotel_Booking::instance()->cart->get_extra_packages( $cart_id );

		if( $extra_packages ) {
			if( is_hb_checkout() ) {
				$page = 'checkout';
			}
			else {
				$page = 'cart';
			}

			tp_hb_extra_get_template( 'loop/addition-services-title.php', array( 'page' => $page, 'room' => $room, 'cart_id' => $cart_id ) );
			foreach ( $extra_packages as $package_cart_id => $cart_item ) {
				tp_hb_extra_get_template( 'loop/cart-extra-package.php', array( 'cart_id' => $package_cart_id, 'package' => $cart_item ) );
			}
		}
	}

	// cart admin
	function admin_cart_package_after_item( $cart_params, $cart_id, $booking )
	{
		$html = array();
		ob_start();
		WP_Hotel_Booking::instance()->_include( 'includes/admin/views/update/admin-addition-services-title.php', true, array( 'room' => $cart_params[ $cart_id ], 'cart_id' => $cart_id ), false );
		$html[] = ob_get_clean();
		foreach ( $cart_params as $id => $cart_item ) {
			if ( isset( $cart_item->parent_id ) && $cart_item->parent_id === $cart_id ) {
				ob_start();
				WP_Hotel_Booking::instance()->_include( 'includes/admin/views/update/admin-cart-extra-package.php', true, array( 'package' => $cart_item, 'booking' => $booking ), false );
				$html[] = ob_get_clean();
			}
		}
		echo implode( '', $html );
	}

	// email new booking
	function email_new_booking( $cart_params, $cart_id, $booking ) {
	?>
		<tr class="hb_addition_services_title hb_table_center">
			<td style="text-align: center;" colspan="7">
				<?php _e( 'Addition Services', 'wp-hotel-booking' ); ?>
			</td>
		</tr>
	<?php
		foreach ( $cart_params as $id => $cart_item ) {
			if ( isset( $cart_item->parent_id ) && $cart_item->parent_id === $cart_id ) {
				?>
					<tr style="background-color: #FFFFFF;">

						<td></td>

						<td>
							<?php echo esc_html( $cart_item->quantity ); ?>
						</td>

						<td colspan="3">
							<?php printf( '%s', $cart_item->product_data->title ) ?>
						</td>

						<td>
							<?php echo hb_format_price( $cart_item->amount_singular_exclude_tax, hb_get_currency_symbol( $booking->currency ) ) ?>
						</td>

					</tr>

				<?php
			}
		}
	}

	function check_respondent( $respondent )
	{
		// remove_filter( 'hb_extra_cart_input', array( $this, 'check_respondent' ) );
		if( is_page( hb_get_page_id( 'checkout' ) ) || hb_get_request( 'hotel-booking' ) === 'checkout' )
			return false;

		if( is_page( hb_get_page_id( 'cart' ) ) || hb_get_request( 'hotel-booking' ) === 'cart' )
		{
			if( $respondent === 'trip' )
				return false;
		}
		add_filter( 'hb_extra_cart_input', array( $this, 'check_respondent' ) );
		return $respondent;
	}

	function booking_post_type_extra_item( $room, $hb_booking ) {
		$packages = hb_get_order_items( $hb_booking->id, 'sub_item', $room->order_item_id );

		if ( ! $packages ) {
			return;
		}

		$html = array();
		foreach ( $packages as $k => $package ) {
			$extra = hotel_booking_get_product_class( hb_get_order_item_meta( $package->order_item_id, 'product_id', true ) );
			// $extra->respondent === 'number'
			$html[] = '<tr data-order-parent="'.esc_attr( $room->order_item_id ).'">';

			$html[] = sprintf( '<td class="center"><input type="checkbox" name="book_item[]" value="%s" /></td>', $package->order_item_id );

			$html[] = sprintf( '<td class="name" colspan="3">%s</td>', $package->order_item_name );

			$html[] = sprintf( '<td class="qty">%s</td>', hb_get_order_item_meta( $package->order_item_id, 'qty', true ) );

			$html[] = sprintf( '<td class="total">%s</td>', hb_format_price( hb_get_order_item_meta( $package->order_item_id, 'subtotal', true ), hb_get_currency_symbol( $hb_booking->currency ) ) );

			$html[] = '<td class="actions">';

			if ( $extra->respondent === 'number' ) {
				$html[] ='<a href="#" class="edit" data-order-id="'.esc_attr( $hb_booking->id ).'" data-order-item-id="'.esc_attr( $package->order_item_id ).'" data-order-item-type="sub_item" data-order-item-parent="'.$package->order_item_parent.'">
							<i class="fa fa-pencil"></i>
						</a>';
			}
			$html[] = '<a href="#" class="remove" data-order-id="'.esc_attr( $hb_booking->id ).'" data-order-item-id="'.esc_attr( $package->order_item_id ).'" data-order-item-type="sub_item" data-order-item-parent="'.$package->order_item_parent.'">
						<i class="fa fa-times-circle"></i>
					</a>
				</td>';

			$html[] = '</tr>';
		}

		printf( '%s', implode( '', $html ) );
	}

	function email_booking_post_type_extra_item( $room, $hb_booking ) {
		$packages = hb_get_order_items( $hb_booking->id, 'sub_item', $room->order_item_id );

		if ( ! $packages ) {
			return;
		}

		$html = array();
		foreach ( $packages as $k => $package ) {
			$html[] = '<tr>';

			$html[] = '<td>' . sprintf( '%s', $package->order_item_name ) . '</td>';
			$html[] = '<td>' . sprintf( '%s', date_i18n( hb_get_date_format(), hb_get_order_item_meta( $package->order_item_id, 'check_in_date', true ) ) ) . '</td>';
			$html[] = '<td>' . sprintf( '%s', date_i18n( hb_get_date_format(), hb_get_order_item_meta( $package->order_item_id, 'check_out_date', true ) ) )  . '</td>';
			$html[] = '<td>' . sprintf( '%s', hb_get_order_item_meta( $package->order_item_id, 'qty', true ) )  . '</td>';
			$html[] = '<td>' . sprintf( '%s', hb_format_price( hb_get_order_item_meta( $package->order_item_id, 'subtotal', true ), hb_get_currency_symbol( $hb_booking->currency ) ) )  . '</td>';

			$html[] = '</tr>';
		}

		printf( '%s', implode( '', $html ) );
	}

	// load package in edit room
	public function admin_load_package( $args ) {

		if ( ! isset( $args[ 'product_id' ] ) ) {
			return $args;
		}

		$product_id = absint( $args['product_id'] );
		if ( get_post_type( $product_id ) !== 'hb_room' ) {
			return $args;
		}

		$room_extra = HB_Room_Extra::instance( $product_id );
		$room_extra = $room_extra->get_extra();

		$order_child_id = array();
		$order_subs = array();
		if ( isset( $args['order_id'], $args['order_item_id'] ) ) {
			$sub_items = hb_get_sub_item_order_item_id( $args['order_item_id'] );
			if ( $sub_items ) {
				foreach ( $sub_items as $it_id ) {
					$order_child_id[ hb_get_order_item_meta( $it_id, 'product_id', true ) ] = hb_get_order_item_meta( $it_id, 'qty', true );
					$order_subs[ hb_get_order_item_meta( $it_id, 'product_id', true ) ] = $it_id;
				}
			}
		}

		if ( $room_extra ) {
			$args['sub_items'] = array();
			foreach ( $room_extra as $k => $extra ) {
				$param = array(
						'ID'			=> $extra->ID,
						'title'			=> $extra->title,
						'respondent' 	=> $extra->respondent,
						'selected'		=> array_key_exists( $extra->ID, $order_child_id ) ? true : false,
						'qty'			=> array_key_exists( $extra->ID, $order_child_id ) ? $order_child_id[ $extra->ID ] : 1
					);
				if ( isset( $order_subs[ $extra->ID ] ) ) {
					$param[ 'order_item_id' ] = $order_subs[ $extra->ID ];
				}
				$args['sub_items'][] = $param;
			}
		}

		return $args;
	}

	public function admin_add_package_order( $order_id, $order_item_id ) {
		if ( ! isset( $_POST ) ) {
			return;
		}

		if ( ! isset( $_POST['sub_items'] ) ) {
			return;
		}

		$sub_items = $_POST['sub_items'];
		$check_in_date = isset( $_POST['check_in_date_timestamp'] ) ? $_POST['check_in_date_timestamp'] : '';
		$check_out_date = isset( $_POST['check_out_date_timestamp'] ) ? $_POST['check_out_date_timestamp'] : '';

		foreach ( $sub_items as $product_id => $optional ) {
			if ( isset( $optional['checked'] ) && $optional['checked'] === 'on' ) {
				$qty = isset( $optional['qty'] ) ? $optional['qty'] : 0;
				$param = array(
							'order_item_name'	=> get_the_title( $product_id ),
							'order_item_type'	=> 'sub_item',
							'order_item_parent'	=> $order_item_id,
							'order_id'			=> $order_id
						);

				$product = hotel_booking_get_product_class( $product_id, array(
						'check_in_date' 	=> $check_in_date,
						'check_out_date'	=> $check_out_date,
						'room_quantity'		=> hb_get_order_item_meta( $order_item_id, 'qty', true ),
						'quantity'			=> isset( $optional['qty'] ) ? $optional['qty'] : 0
					) );

				if ( isset( $optional['order_item_id'] ) ) {
					$sub_order_item_id = absint( $optional['order_item_id'] );
					if ( $qty === 0 ) {
						hb_remove_order_item( $sub_order_item_id );
					} else {
						hb_update_order_item( $sub_order_item_id, $param );
					}
				} else {
					$sub_order_item_id = hb_add_order_item( $order_id, $param );
				}

				if ( $qty ) {
					hb_update_order_item_meta( $sub_order_item_id, 'product_id', $product_id );
					hb_update_order_item_meta( $sub_order_item_id, 'qty', $qty );
					hb_update_order_item_meta( $sub_order_item_id, 'check_in_date', $check_in_date );
					hb_update_order_item_meta( $sub_order_item_id, 'check_out_date', $check_out_date );
					hb_update_order_item_meta( $sub_order_item_id, 'subtotal', $product->price );
					hb_update_order_item_meta( $sub_order_item_id, 'total', $product->price_tax );
					hb_update_order_item_meta( $sub_order_item_id, 'tax_total', $product->price_tax - $product->price );
				}

			} else {
				if ( isset( $optional['order_item_id'] ) ) {
					hb_remove_order_item( $optional['order_item_id'] );
				}
			}
		}
	}

	/**
	 * instead of new class. quickly, helpfully
	 * @param $cart_id [description]
	 * @return object or null
	 */
	static function instance( $cart_id = null )
	{
		if( ! empty( self::$_instance[ $cart_id ] ) ) {
			return self::$_instance[ $cart_id ];
		}

		return new self();
	}

}
new HB_Extra_Cart();