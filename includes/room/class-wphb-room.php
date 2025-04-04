<?php
/**
 * WP Hotel Booking room.
 *
 * @version       1.9.6
 * @author        ThimPress
 * @package       WP_Hotel_Booking/Classes
 * @category      Classes
 * @author        Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

/**
 * Class HB_Room
 */
class WPHB_Room extends WPHB_Product_Room_Base {

	/**
	 * @var array
	 */
	protected static $_instance = array();

	/**
	 * @var null|WP_Post
	 */
	public $post = null;

	/**
	 * reivew detail
	 *
	 * @return null or array
	 */
	public $_review_details = null;

	/**
	 * Constructor
	 *
	 * @param $post
	 */
	function __construct( $post, $options = null ) {
		if ( is_numeric( $post ) && $post && get_post_type( $post ) == 'hb_room' ) {
			$this->post = get_post( $post );
		} elseif ( $post instanceof WP_Post || is_object( $post ) ) {
			$this->post = $post;
		}
		if ( empty( $this->post ) ) {
			$this->post = hb_create_empty_post();
		}
		global $hb_settings;
		if ( ! $this->_settings ) {
			$this->_settings = $hb_settings;
		}

		if ( $options ) {
			$this->set_data( $options );
		}

		parent::__construct( $this->post, $options );
	}

	static function hb_setup_room_data( $post ) {
		unset( $GLOBALS['hb_room'] );

		if ( is_int( $post ) ) {
			$post = get_post( $post );
		}

		if ( ! $post ) {
			$post = $GLOBALS['post'];
		}

		if ( empty( $post->post_type ) || ! in_array( $post->post_type, array( 'hb_room' ) ) ) {
			return;
		}

		return $GLOBALS['hb_room'] = self::instance( $post );
	}

	/**
	 * Get the room ID.
	 *
	 * @return int
	 * @since 4.1.9
	 * @version 1.0.0
	 */
	public function get_id(): int {
		return (int) ( $this->post ? $this->post->ID : 0 );
	}

	/**
	 * Get the config number of room.
	 *
	 * @return int
	 * @since 4.1.9
	 * @version 1.0.0
	 */
	public function get_num_of_rooms(): int {
		return (int) get_post_meta( $this->get_id(), '_hb_num_of_rooms', true );
	}

	/**
	 * Get dates booked of room.
	 *
	 * @return array|mixed
	 */
	public function get_dates_booked() {
		$dates_booked = get_post_meta( $this->post->ID, '_hb_dates_booked', true );
		if ( ! $dates_booked ) {
			$dates_booked = array();
		}
		return $dates_booked;
	}

	/**
	 * Store list dates booked of room with quantity.
	 * Ex: [ timestamp => 2, timestamp2 => 1 ]
	 *
	 * @return array|mixed
	 */
	public function get_dates_available() {
		$dates_available = get_post_meta( $this->post->ID, '_hb_dates_available', true );
		if ( ! $dates_available ) {
			$dates_available = array();
		}
		return $dates_available;
	}

	/**
	 * Get unique instance of HB_Room
	 *
	 * @param $room
	 * @param null $options
	 *
	 * @return WPHB_Room
	 */
	static function instance( $room, $options = null ) {
		$post = $room;
		if ( $room instanceof WP_Post ) {
			$id = $room->ID;
		} elseif ( is_object( $room ) && isset( $room->ID ) ) {
			$id = $room->ID;
		} else {
			$id = $room;
		}

		if ( empty( self::$_instance[ $id ] ) ) {
			return self::$_instance[ $id ] = new self( $post, $options );
		} else {
			$room = self::$_instance[ $id ];

			if ( isset( $options['check_in_date'], $options['check_out_date'] ) && ( ( $options['check_in_date'] !== $room->get_data( 'check_in_date' ) ) || ( $options['check_out_date'] !== $room->get_data( 'check_out_date' ) ) ) || $room->quantity === false || ( ! isset( $options['quantity'] ) || $room->quantity != $options['quantity'] ) || ( ! isset( $options['extra_packages'] ) || $options['extra_packages'] != $room->extra_packages )
			) {
				return new self( $post, $options );
			}
		}
		return self::$_instance[ $id ];
	}
}
