<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class WPHB_Booking
 */
class WPHB_Booking {

	/**
	 * @var array
	 */
	protected static $_instance = array();

	/**
	 * Store post object
	 *
	 * @var WP_Post
	 */
	public $post = null;

	/**
	 * @var array
	 */
	private $_booking_info = array();

	/**
	 * @var int
	 */
	public $total = 0;
	public $sub_total = 0;
	public $tax_total = 0;

	/**
	 * Order id
	 *
	 * @var int
	 */
	public $id = 0;

	/**
	 * Order Status
	 *
	 * @var string
	 */
	public $post_status = '';

	/**
	 * Constructor
	 *
	 * @param $post
	 */
	function __construct( $post ) {
		if ( is_numeric( $post ) && $post && get_post_type( $post ) == 'hb_booking' ) {
			$this->post = get_post( $post );
		} else if ( $post instanceof WP_Post || is_object( $post ) ) {
			$this->post = $post;
		}
		if ( empty( $this->post ) ) {
			$this->post = hb_create_empty_post( array( 'post_status' => 'hb-pending' ) );
		}

		$this->id = $this->post->ID;
	}

	function __get( $key ) {
		if ( ! isset( $this->{$key} ) || ! method_exists( $this, $key ) ) {
			switch ( $key ) {
				case 'coupon_value':
					$total  = 0;
					$result = get_post_meta( $this->id, '_hb_' . $key );
					if ( $result ) {
						foreach ( $result as $r ) {
							$total = $total + $r;
						}
					}
					$result = $total;
					break;

				default:
					$result = get_post_meta( $this->id, '_hb_' . $key, true );
					break;
			}

			return $result;
		}
	}

	public function total( $with_coupon = true ) {
		if ( ! $this->id ) {
			return;
		}

		return $this->total = $this->sub_total( $with_coupon ) + $this->tax_total( $with_coupon );
	}

	public function sub_total( $with_coupon = true ) {
		if ( ! $this->id ) {
			return;
		}
		global $wpdb;
		$query = $wpdb->prepare( "
                    SELECT SUM( meta.meta_value ) FROM $wpdb->hotel_booking_order_items AS booking
                        RIGHT JOIN $wpdb->posts AS post ON booking.order_id = post.ID
                        INNER JOIN $wpdb->hotel_booking_order_itemmeta AS meta ON meta.hotel_booking_order_item_id = booking.order_item_id
                    WHERE post.ID = %d
                        AND meta.meta_key = %s
                ", $this->id, 'subtotal' );

		$this->sub_total = $wpdb->get_var( $query );
		if ( $with_coupon ) {
			$this->sub_total = $this->sub_total - $this->coupon_value;
		}

		return $this->sub_total;
	}

	public function tax_total( $with_coupon = true ) {
		if ( ! $this->id ) {
			return;
		}
		global $wpdb;
		$query = $wpdb->prepare( "
                    SELECT SUM( meta.meta_value ) FROM $wpdb->hotel_booking_order_items AS booking
                        RIGHT JOIN $wpdb->posts AS post ON booking.order_id = post.ID
                        INNER JOIN $wpdb->hotel_booking_order_itemmeta AS meta ON meta.hotel_booking_order_item_id = booking.order_item_id
                    WHERE post.ID = %d
                        AND booking.order_item_type IN ( %s, %s )
                        AND meta.meta_key = %s
                ", $this->id, 'line_item', 'sub_item', 'tax_total' );

		$this->tax_total = $wpdb->get_var( $query );
		if ( $with_coupon ) {
			$subtotal_without_coupon = $this->sub_total( false );
			if ( $subtotal_without_coupon > 0 ) {
				$this->tax_total = ( ( $subtotal_without_coupon - $this->coupon_value ) * $this->tax_total ) / $subtotal_without_coupon;
			}
		}

		return $this->tax_total;
	}

	/**
	 * Set booking information
	 *
	 * @param $info
	 */
	function set_booking_info( $info ) {
		if ( func_num_args() > 1 ) {
			$this->_booking_info[ $info ] = func_get_arg( 1 );
		} else {
			$this->_booking_info = array_merge( $this->_booking_info, (array) $info );
		}
	}

	/**
	 * Update booking and relevant data
	 *
	 * @return mixed
	 */
	function update( $order_items = array() ) {
		$post_data = get_object_vars( $this->post );
		// ensure the post_type is correct
		$post_data['post_type']             = 'hb_booking';
		$post_data['post_content_filtered'] = $post_data['post_content'];
		$post_data['post_excerpt']          = $post_data['post_content'];
		if ( $this->post->ID ) {
			$booking_id = wp_update_post( $post_data );
		} else {
			$booking_id     = wp_insert_post( $post_data, true );
			$this->post->ID = $booking_id;
		}
		if ( $booking_id ) {
			foreach ( $this->_booking_info as $meta_key => $v ) {
				if ( strpos( $meta_key, '_hb_' ) === 0 ) {
					if ( is_array( $v ) ) {
						delete_post_meta( $booking_id, $meta_key );
						foreach ( $v as $i ) {
							add_post_meta( $booking_id, $meta_key, $i );
						}
					} else {
						update_post_meta( $booking_id, $meta_key, $v );
					}
				}
			}

		}
		$this->id = $this->post->ID;

		if ( ! empty( $order_items ) ) {
			$this->add_order_items( $order_items );
		}

		return $this->post->ID;
	}

	function get_post_meta( $meta_key = null, $val = null, $unique = true ) {
		if ( ! $this->post->ID || ! $meta_key ) {
			return $val;
		}

		return get_post_meta( $this->post->ID, $meta_key, $unique );
	}

	/**
	 * Get current status of booking
	 *
	 * @return mixed
	 */
	public function get_status() {
		$this->post->post_status = get_post_status( $this->id );

		return apply_filters( 'hb_order_get_status', 'hb-' === substr( $this->post->post_status, 0, 3 ) ? substr( $this->post->post_status, 3 ) : $this->post->post_status, $this );
	}

	/**
	 * Checks to see if current booking has status as passed
	 *
	 * @param $status
	 *
	 * @return mixed
	 */
	public function has_status( $status ) {
		return apply_filters( 'hb_booking_has_status', ( is_array( $status ) && in_array( $this->get_status(), $status ) ) || $this->get_status() === $status ? true : false, $this, $status );
	}

	/**
	 * Updates booking to new status if needed
	 *
	 * @param string $new_status
	 */
	function update_status( $new_status = 'pending' ) {
		// Standardise status names.
		$new_status = 'hb-' === substr( $new_status, 0, 3 ) ? substr( $new_status, 3 ) : $new_status;
		$old_status = $this->get_status();

		if ( $new_status !== $old_status || ! in_array( $this->post_status, array_keys( hb_get_booking_statuses() ) ) ) {

			// Update the order
			wp_update_post( array( 'ID' => $this->id, 'post_status' => 'hb-' . $new_status ) );
			$this->post_status = 'hb-' . $new_status;

			// Status was changed
			do_action( 'hb_booking_status_' . $new_status, $this->id );
			do_action( 'hb_booking_status_' . $old_status . '_to_' . $new_status, $this->id );
			do_action( 'hb_booking_status_changed', $this->id, $old_status, $new_status );

			switch ( $new_status ) {

				case 'completed' :
					$payment_complete_date = get_post_meta( $this->post->ID, '_hb_booking_payment_completed', true );
					if ( ! $payment_complete_date ) {
						add_post_meta( $this->post->ID, '_hb_booking_payment_completed', current_time( 'mysql' ) );
					} else {
						update_post_meta( $this->post->ID, '_hb_booking_payment_completed', current_time( 'mysql' ) );
					}
					break;

				case 'processing' :

					break;
			}
		}
	}

	/**
	 * Format booking number id
	 * @return string
	 */
	function get_booking_number() {
		return hb_format_order_number( $this->id );
	}

	/**
	 * Mark booking as complete
	 *
	 * @param string - transaction ID provided payment gateway
	 */
	function payment_complete( $transaction_id = '' ) {
		do_action( 'hb_pre_payment_complete', $this->id );

		delete_transient( 'booking_awaiting_payment' );

		$valid_booking_statuses = apply_filters( 'hb_valid_order_statuses_for_payment_complete', array( 'pending' ), $this );

		if ( $this->id && $this->has_status( $valid_booking_statuses ) ) {

			$this->update_status( 'completed' );

			if ( ! empty( $transaction_id ) ) {
				add_post_meta( $this->id, '_transaction_id', $transaction_id, true );
			}

			do_action( 'hb_payment_complete', $this->id );
		} else {
			do_action( 'hb_payment_complete_order_status_' . $this->get_status(), $this->id );
		}
	}

	/**
	 * Get checkout booking success url
	 *
	 * @return mixed
	 */
	public function get_checkout_booking_received_url() {
		$received_url = hb_get_page_permalink( 'search' );

		return apply_filters( 'hb_get_checkout_booking_received_url', $received_url, $this );
	}

	function add_order_items( $order_items = array() ) {
		// clean order item
		if ( $this->id ) {
			hb_empty_booking_order_items( $this->id );
		}
		if ( ! empty( $order_items ) ) {
			$parents = array();
			// insert line_item
			foreach ( $order_items AS $k => $order_item ) {
				if ( ! isset( $order_item['parent_id'] ) || ! $order_item['parent_id'] ) {
					$product_id    = $order_item['product_id'];
					$order_item_id = hb_add_order_item( $this->id, array(
						'order_item_name' => get_the_title( $product_id ),
						'order_item_type' => 'line_item'
					) );
					$parents[ $k ] = $order_item_id;

					// add order items meta
					foreach ( $order_item as $meta_key => $meta_value ) {
						if ( $meta_key !== 'parent_id' ) {
							hb_add_order_item_meta( $order_item_id, $meta_key, $meta_value );
						}
					}
				}
			}

			// insert sub_item
			foreach ( $order_items AS $k => $order_item ) {
				if ( isset( $order_item['parent_id'] ) && array_key_exists( $order_item['parent_id'], $parents ) ) {
					$product_id    = $order_item['product_id'];
					$order_item_id = hb_add_order_item( $this->id, array(
						'order_item_name'   => get_the_title( $product_id ),
						'order_item_type'   => 'sub_item',
						'order_item_parent' => $parents[ $order_item['parent_id'] ]
					) );
					// add order items meta
					foreach ( $order_item as $meta_key => $meta_value ) {
						if ( $meta_key !== 'parent_id' ) {
							hb_add_order_item_meta( $order_item_id, $meta_key, $meta_value );
						}
					}
				}
			}
		}
	}

	/**
	 * Get an instance of WPHB_Booking by post ID or WP_Post object
	 *
	 * @param $booking
	 *
	 * @return WPHB_Booking
	 */
	static function instance( $booking ) {
		$post = $booking;
		if ( $booking instanceof WP_Post ) {
			$id = $booking->ID;
		} elseif ( is_object( $booking ) && isset( $booking->ID ) ) {
			$id = $booking->ID;
		} else {
			$id = $booking;
		}

		if ( empty( self::$_instance[ $id ] ) ) {
			self::$_instance[ $id ] = new self( $post );
		}

		return self::$_instance[ $id ];
	}
}
