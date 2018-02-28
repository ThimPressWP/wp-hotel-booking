<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WPHB_Product_Room_Base extends WPHB_Product_Abstract {

	public $quantity = 1;
	public $check_in_date = 1;
	public $check_out_date = 1;

	/**
	 * @var null
	 */
	public $_plans = null;

	/**
	 * @var null|WP_Post
	 */
	public $post = null;

	/**
	 * @var array
	 */
	public $_external_data = array();

	/**
	 * @var int
	 */
	public $_room_details_total = 0;

	/**
	 * @return setting
	 */
	public $_settings;

	/**
	 * reivew detail
	 * @return null or array
	 */
	public $_review_details = null;

	function __construct( $post, $params = null ) {
		if ( is_numeric( $post ) && $post && get_post_type( $post ) == 'hb_room' ) {
			$this->post = get_post( $post );
		} else if ( $post instanceof WP_Post || is_object( $post ) ) {
			$this->post = $post;
		}
		if ( empty( $this->post ) ) {
			$this->post = hb_create_empty_post();
		}
		global $hb_settings;
		if ( ! $this->_settings ) {
			$this->_settings = $hb_settings;
		}

		if ( $params ) {
			$this->set_data( $params );
		}
	}

	/**
	 * Set extra data form room
	 *
	 * @param      $key
	 * @param null $value
	 *
	 * @return $this
	 */
	function set_data( $key, $value = null ) {
		if ( is_array( $key ) ) {
			foreach ( $key as $k => $v ) {
				$this->set_data( $k, $v );
			}
		} else {
			$this->_external_data[ $key ] = $value;
		}

		return $this;
	}

	/**
	 * Get extra data of room
	 *
	 * @param $key
	 *
	 * @return bool
	 */
	function get_data( $key ) {
		return ! empty( $this->_external_data[ $key ] ) ? $this->_external_data[ $key ] : ( $key === false ? $this->_external_data : false );
	}

	/**
	 * Magic function to get a variable of room
	 *
	 * @param $key
	 *
	 * @return int|string
	 */
	function __get( $key ) {
		static $fields = array();
		$return = '';
		switch ( $key ) {
			case 'ID':
				$return = $this->get_data( 'id' ) ? $this->get_data( 'id' ) : $this->post->ID;
				break;
			case 'room_type':
				// $return = intval( get_post_meta( $this->post->ID, '_hb_room_type', true ) );
				$terms  = get_the_terms( $this->post->ID, 'hb_room_type' );
				$return = array();
				if ( $terms ) {
					foreach ( $terms as $key => $term ) {
						$return[] = $term->term_id;
					}
				}
				break;
			case 'name':
				$return = get_the_title( $this->ID );
				break;
			case 'capacity':
				$term_id = get_post_meta( $this->post->ID, '_hb_room_origin_capacity', true ) ? get_post_meta( $this->post->ID, '_hb_room_origin_capacity', true ) : get_post_meta( $this->post->ID, '_hb_room_capacity', true );
				$return  = get_term_meta( $term_id, 'hb_max_number_of_adults', true );
				if ( ! $return ) {
					$return = (int) get_option( 'hb_taxonomy_capacity_' . $term_id );
				}
				break;
			case 'capacity_title':
				$term_id = get_post_meta( $this->ID, '_hb_room_capacity', true );
				if ( $key == 'capacity_title' ) {
					$term = get_term( $term_id, 'hb_room_capacity' );
					if ( isset( $term->name ) ) {
						$return = $term->name;
					}
				} else {
					$return = get_term_meta( $term_id, 'hb_max_number_of_adults', true );
					if ( ! $return ) {
						$return = (int) get_option( 'hb_taxonomy_capacity_' . $term_id );
					}
				}
				break;
			case 'capacity_id':
				$return = get_post_meta( $this->post->ID, '_hb_room_capacity', true );
				break;
			case 'addition_information':
				$return = get_post_meta( $this->ID, '_hb_room_addition_information', true );
				break;
			case 'thumbnail':
				if ( has_post_thumbnail( $this->ID ) ) {
					$return = get_the_post_thumbnail( $this->ID, 'thumbnail' );
				} else {
					$gallery = get_post_meta( $this->ID, '_hb_gallery', true );
					if ( $gallery ) {
						$attachment_id = array_shift( $gallery );
						$return        = wp_get_attachment_image( $attachment_id, 'thumbnail' );
					} else {
						$return = '<img src="' . esc_url( WPHB_PLUGIN_URL . '/includes/libraries/carousel/default.png' ) . '" alt="' . $this->post->post_title . '"/>';
					}
				}
				break;
			case 'gallery':
				$return = $this->get_galleries();
				break;
			case 'max_child':
				$return = get_post_meta( $this->ID, '_hb_max_child_per_room', true );
				break;

			case 'dropdown_room':
				$max_rooms = get_post_meta( $this->post->ID, '_hb_num_of_rooms', true );
				$return    = '<select name="hb-num-of-rooms[' . $this->post->ID . ']">';
				$return    .= '<option value="0">' . __( 'Select', 'wp-hotel-booking' ) . '</option>';
				for ( $i = 1; $i <= $max_rooms; $i ++ ) {
					$return .= sprintf( '<option value="%1$d">%1$d</option>', $i );
				}
				$return .= '</select>';
				break;
			case 'num_of_rooms':
				$return = get_post_meta( $this->post->ID, '_hb_num_of_rooms', true );
				break;
			case 'room_details_total':
				$return = $this->_room_details_total;
				break;
			case 'price_table':
				$return = __( 'why i am here?', 'wp-hotel-booking' );
				break;
			case 'check_in_date':
				$return = $this->get_data( 'check_in_date' );
				break;
			case 'check_out_date':
				$return = $this->get_data( 'check_out_date' );
				break;
			case 'in_to_out':
				$return = strtotime( $this->get_data( 'check_in_date' ) ) . '_' . strtotime( $this->get_data( 'check_out_date' ) );
				break;
			case 'quantity':
				$return = $this->get_data( 'quantity' );
				break;
			case 'total':
				$return = $this->get_total( $this->get_data( 'check_in_date' ), $this->get_data( 'check_out_date' ), $this->get_data( 'quantity' ), false );
				break;
			case 'total_tax':
				$return = $this->get_total( $this->get_data( 'check_in_date' ), $this->get_data( 'check_out_date' ), $this->get_data( 'quantity' ), true );
				break;
			case 'amount_singular_exclude_tax':
				$return = $this->get_total( $this->get_data( 'check_in_date' ), $this->get_data( 'check_out_date' ), 1, false );
				break;
			case 'amount_singular_include_tax':
				$return = $this->get_total( $this->get_data( 'check_in_date' ), $this->get_data( 'check_out_date' ), 1, true );
				break;
			case 'amount_singular':
				$return = $this->amount_singular();
				break;
			case 'search_key':
				$return = $this->get_data( 'search_key' );
				break;
			case 'extra_packages':
				$return = $this->get_data( 'extra_packages' );
				break;
		}

		return apply_filters( 'hotel_booking_room_get_data', $return, $key, $this );
	}

	function get_galleries( $with_featured = false, $ignore_first = true ) {
		$gallery = array();
		if ( $with_featured && $thumb_id = get_post_thumbnail_id( $this->post->ID ) ) {
			$featured_thumb = wp_get_attachment_image_src( $thumb_id, 'thumbnail' );
			$featured_full  = wp_get_attachment_image_src( $thumb_id, 'full' );
			$alt            = get_post_meta( $thumb_id, '_wp_attachment_image_alt', true );
			$gallery[]      = array(
				'id'    => $thumb_id,
				'src'   => $featured_full[0],
				'thumb' => $featured_thumb[0],
				'alt'   => $alt ? $alt : get_the_title( $thumb_id )
			);
		}

		$galleries = get_post_meta( $this->post->ID, '_hb_gallery', true );
		if ( ! $galleries ) {
			return $gallery;
		}

		foreach ( $galleries as $thumb_id ) {
			$alt = get_post_meta( $thumb_id, '_wp_attachment_image_alt', true );

			$w = $this->_settings->get( 'room_thumbnail_width', 150 );
			$h = $this->_settings->get( 'room_thumbnail_height', 150 );

			$size  = apply_filters( 'hotel_booking_room_thumbnail_size', array( 'width' => $w, 'height' => $h ) );
			$thumb = $this->renderImage( $thumb_id, $size, true, 'thumbnail' );
			if ( ! $thumb ) {
				$thumb_src = wp_get_attachment_image_src( $thumb_id, 'thumbnail' );
				$thumb     = $thumb_src[0];
			}

			$w    = $this->_settings->get( 'room_image_gallery_width', 1000 );
			$h    = $this->_settings->get( 'room_image_gallery_height', 667 );
			$size = apply_filters( 'hotel_booking_room_gallery_size', array( 'width' => $w, 'height' => $h ) );

			$full = $this->renderImage( $thumb_id, $size, true, 'full' );
			if ( ! $full ) {
				$full_src = wp_get_attachment_image_src( $thumb_id, 'full' );
				$full     = $full_src[0];
			}
			$alt       = get_post_meta( $thumb_id, '_wp_attachment_image_alt', true );
			$gallery[] = array(
				'id'    => $thumb_id,
				'src'   => $full,
				'thumb' => $thumb,
				'alt'   => $alt ? $alt : get_the_title( $thumb_id )
			);
		}

		return $gallery;
	}

	/**
	 * @return array
	 */
	function get_booking_room_details() {
		$details            = array();
		$room_details_total = 0;
		$start_date         = $this->get_data( 'check_in_date' );
		$end_date           = $this->get_data( 'check_out_date' );

		$start_date_to_time = strtotime( $start_date );
		$end_date_to_time   = strtotime( $end_date );

		$tax = false;
		if ( hb_price_including_tax() ) {
			$tax = true;
		}

		$nights = hb_count_nights_two_dates( $end_date, $start_date );
		for ( $i = 0; $i < $nights; $i ++ ) {
			$c_date = $start_date_to_time + $i * DAY_IN_SECONDS;
			$date   = date( 'w', $c_date );
			if ( ! isset( $details[ $date ] ) ) {
				$details[ $date ] = array(
					'count' => 0,
					'price' => 0
				);
			}
			$details[ $date ]['count'] ++;
			$details[ $date ]['price'] += $this->get_total( $c_date, 1, 1, $tax );
			$room_details_total        += $details[ $date ]['price'];
		}
		$this->_room_details_total = $room_details_total;

		return apply_filters( 'hotel_booking_get_booking_room_details', $details, $this->post->ID );
	}

	/**
	 * Get room price based on plan settings
	 *
	 * @param null $date
	 * @param bool $including_tax
	 *
	 * @return float
	 */
	function get_price( $date = null, $including_tax = true ) {
		$tax = 0;
		if ( $including_tax ) {
			$settings = WPHB_Settings::instance();
			if ( $settings->get( 'price_including_tax' ) ) {
				$tax = $settings->get( 'tax' );
				$tax = (float) $tax / 100;
			}
		}

		if ( ! $date ) {
			$date = time();
		} elseif ( is_string( $date ) ) {
			$date = @strtotime( $date );
		}

		$plans         = $this->get_pricing_plans();
		$return        = 0;
		$selected_plan = hb_room_get_selected_plan( $this->post->ID, $date );

		if ( $selected_plan ) {
			$prices = $selected_plan->prices;
			if ( $prices && isset( $prices[ date( 'w', $date ) ] ) ) {
				$return = $prices[ date( 'w', $date ) ];
				$return = $return + $return * $tax;
			}
		}

		return floatval( $return );
	}

	/**
	 * Get total price of room
	 *
	 * @param      $from
	 * @param      $to
	 * @param int $num_of_rooms
	 * @param bool $including_tax
	 *
	 * @return float|int
	 */
	function get_total( $from = null, $to = null, $num_of_rooms = 1, $including_tax = true ) {
		$nights = 0;
		$total  = 0;
		if ( is_null( $from ) && is_null( $to ) ) {
			$to_time   = (int) $this->check_out_date;
			$from_time = (int) $this->check_in_date;
		} else {
			if ( ! is_numeric( $from ) ) {
				$from_time = strtotime( $from );
			} else {
				$from_time = $from;
			}
			if ( ! is_numeric( $to ) ) {
				$to_time = strtotime( $to );
			} else {
				if ( $to >= DAY_IN_SECONDS ) {
					$to_time = $to;
				} else {
					$nights = $to;
				}
			}
		}

		if ( ! $num_of_rooms ) {
			$num_of_rooms = intval( $this->get_data( 'quantity' ) );
		}

		if ( ! $nights ) {
			$nights = hb_count_nights_two_dates( $to_time, $from_time );
		}

		$from = mktime( 0, 0, 0, date( 'm', $from_time ), date( 'd', $from_time ), date( 'Y', $from_time ) );
		for ( $i = 0; $i < $nights; $i ++ ) {
			$total_per_night = $this->get_price( $from + $i * DAY_IN_SECONDS, false );
			$total           += $total_per_night * $num_of_rooms;
		}

		$total    = apply_filters( 'hotel_booking_room_total_price_excl_tax', $total, $this );
		$settings = WPHB_Settings::instance();
		// room price include tax
		if ( $including_tax ) {
			// $tax_enbale = apply_filters( 'hotel_booking_extra_tax_enable', hb_price_including_tax() );
			// if ( $tax_enbale ) {
			$tax_price = $total * hb_get_tax_settings();
			$tax_price = apply_filters( 'hotel_booking_room_total_price_incl_tax', $tax_price, $this );
			$total     = $total + $tax_price;
			// }
		}

		return $total;
	}

	/**
	 * Get list of pricing plan of this room type
	 * @return null
	 */
	function get_pricing_plans() {
		if ( ! $this->_plans ) {
			$this->_plans = hb_room_get_pricing_plans( $this->post->ID );
		}

		return $this->_plans;
	}

	function get_related_rooms() {
		$room_types          = get_the_terms( $this->post->ID, 'hb_room_type' );
		$room_capacity       = (int) get_post_meta( $this->post->ID, '_hb_room_capacity', true );
		$max_adults_per_room = get_term_meta( $room_capacity, 'hb_max_number_of_adults', true );
		if ( ! $max_adults_per_room ) {
			$max_adults_per_room = (int) get_option( 'hb_taxonomy_capacity_' . $room_capacity );
		}
		if ( ! $max_adults_per_room ) {
			$max_adults_per_room = (int) get_post_meta( $this->post->ID, '_hb_max_adults_per_room', true );
		}
		$max_child_per_room = (int) get_post_meta( $this->post->ID, '_hb_max_child_per_room', true );

		$taxonomis = array();
		if ( $room_types ) {
			foreach ( $room_types as $key => $tax ) {
				$taxonomis[] = $tax->term_id;
			}
		} else {
			$terms = get_terms( 'hb_room_type' );
			foreach ( $terms as $key => $term ) {
				$taxonomis[] = $term->term_id;
			}
		}

		$args  = array(
			'post_type'    => 'hb_room',
			'status'       => 'publish',
			'meta_query'   => array(
				// array(
				//     'key'       => '_hb_max_adults_per_room',
				//     'value'     => $max_adults_per_room,
				//     'compare'   => '>=',
				// ),
				array(
					'key'     => '_hb_max_child_per_room',
					'value'   => $max_child_per_room,
					'compare' => '<='
				),
			),
			'tax_query'    => array(
				array(
					'taxonomy' => 'hb_room_type',
					'field'    => 'term_id',
					'terms'    => $taxonomis
				),
			),
			'post__not_in' => array( $this->post->ID )
		);
		$query = new WP_Query( $args );
		wp_reset_postdata();

		return $query;
	}

	/**
	 * Get reviews count for a room
	 *
	 * @return mixed
	 */
	function get_review_count( $content = 'view' ) {
		global $wpdb;
		$transient_name = rand() . 'hb_review_count_' . $this->post->ID;
		if ( false === ( $count = get_transient( $transient_name ) ) ) {
			$count = count( $this->get_review_details() );

			//set_transient( $transient_name, $count, DAY_IN_SECONDS * 30 );
		}

		return apply_filters( 'hb_room_review_count', $count, $this );
	}

	function get_review_details() {
		if ( ! $this->_review_details ) {
			return get_comments( array( 'post_id' => $this->post->ID, 'status' => 'approve' ) );
		}

		return $this->_review_details;
	}

	function getImage( $type = 'catalog', $attachID = false, $echo = true ) {
		if ( $type === 'catalog' ) {
			return $this->get_catalog( $attachID = false, $echo = true );
		}

		return $this->get_thumbnail( $attachID = false, $echo = true );
	}

	function average_rating() {
		$comments = $this->get_review_details();
		$total    = 0;
		$i        = 0;
		foreach ( $comments as $key => $comment ) {
			$rating = get_comment_meta( $comment->comment_ID, 'rating', true );
			if ( $rating ) {
				$total = $total + $rating;
				$i ++;
			}
		}
		if ( $comments && $i ) {
			return $total / $i;
		}

		return null;
	}

	/**
	 * get thumbnail
	 * @return html or array atts
	 */
	function get_thumbnail( $attachID = false, $echo = true ) {
		$w = $this->_settings->get( 'room_thumbnail_width', 150 );
		$h = $this->_settings->get( 'room_thumbnail_height', 150 );

		$size = apply_filters( 'hotel_booking_room_thumbnail_size', array( 'width' => $w, 'height' => $h ) );

		if ( $attachID == false ) {
			$attachID = get_post_thumbnail_id( $this->post->ID );
		}

		$alt   = get_post_meta( $attachID, '_wp_attachment_image_alt', true );
		$image = $this->renderImage( $attachID, $size, false, 'thumbnail' );
		// default thumbnail

		if ( $echo && $image ) {
			if ( is_array( $image ) ) {
				echo sprintf( '<img src="%1$s" width="%2$s" height="%3$s" alt="%4$s"/>', esc_url( $image[0] ), esc_attr( $image[1] ), esc_attr( $image[2] ), esc_attr( $alt ) );
			} else {
				sprintf( '<img src="%1$s" width="%2$s" height="%3$s" alt="%4$s"/>', esc_url( $image ), esc_attr( $w ), esc_attr( $h ), esc_attr( $alt ) );
			}
		} else {
			return $image;
		}
	}

	function get_catalog( $attachID = false, $echo = true ) {
		$w = $this->_settings->get( 'catalog_image_width', 270 );
		$h = $this->_settings->get( 'catalog_image_height', 270 );

		$size = apply_filters( 'hotel_booking_room_gallery_size', array( 'width' => $w, 'height' => $h ) );

		if ( $attachID == false ) {
			$attachID = get_post_thumbnail_id( $this->post->ID );
		}

		$alt = get_post_meta( $attachID, '_wp_attachment_image_alt', true );

		$image = $this->renderImage( $attachID, $size, false, 'large' );

		if ( $echo && $image ) {
			if ( is_array( $image ) ) {
				echo sprintf( '<img src="%1$s" width="%2$s" height="%3$s" alt="%4$s"/>', esc_url( $image[0] ), esc_attr( $image[1] ), esc_attr( $image[2] ), esc_attr( $alt ) );
			} else {
				sprintf( '<img src="%1$s" width="%2$s" height="%3$s" alt="%4$s"/>', esc_url( $image ), esc_attr( $w ), esc_attr( $h ), esc_attr( $alt ) );
			}
		} else {
			return $image;
		}
	}

	function renderImage( $attachID = null, $size = array(), $src = true, $default = 'thumbnail' ) {
		$resizer = WPHB_Reizer::getInstance();

		$image = $resizer->process( $attachID, $size, $src );
		if ( $image ) {
			return $image;
		} else {
			$image = wp_get_attachment_image_src( $attachID, $default );
			if ( $src ) {
				return $image[0];
			} else {
				return array(
					$image[0],
					$image[1],
					$image[2]
				);
			}
		}
	}

	function pricing_plan() {
		$prices = array();
		$prices = hb_get_price_plan_room( get_the_ID() );
		if ( $prices ) {
			sort( $prices );
		}

		$sort          = $prices;
		$prices['min'] = current( $sort );
		$prices['max'] = end( $sort );

		return $prices;
	}

	// total include tax
	function amount_include_tax() {
		return apply_filters( 'hotel_booking_room_item_total_include_tax', $this->total_tax, $this );
	}

	// total exclude tax
	function amount_exclude_tax() {
		return apply_filters( 'hotel_booking_room_item_total_exclude_tax', $this->total, $this );
	}

	function amount( $cart = false ) {
		$amount = hb_price_including_tax( $cart ) ? $this->amount_include_tax() : $this->amount_exclude_tax();

		return apply_filters( 'hotel_booking_room_item_amount', $amount, $this );
	}

	function amount_singular_exclude_tax() {
		return apply_filters( 'hotel_booking_room_singular_total_exclude_tax', $this->amount_singular_exclude_tax, $this );
	}

	function amount_singular_include_tax() {
		return apply_filters( 'hotel_booking_room_singular_total_include_tax', $this->amount_singular_include_tax, $this );
	}

	function amount_singular( $cart = false ) {
		$amount = hb_price_including_tax( $cart ) ? $this->amount_singular_include_tax() : $this->amount_singular_exclude_tax();

		return apply_filters( 'hotel_booking_room_amount_singular', $amount, $this );
	}

	function is_taxable( $content = 'view' ) {
		return true;
	}

	function get_tax_class( $content = 'view' ) {
		return '';
	}

	public function is_in_stock() {

	}
}
