<?php
/**
 * WP Hotel Booking functions.
 *
 * @version       1.9.6
 * @author        ThimPress
 * @package       WP_Hotel_Booking/Functions
 * @category      Functions
 * @author        Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;


// if ( ! function_exists( 'hb_get_max_capacity_of_rooms' ) ) {
// function hb_get_max_capacity_of_rooms() {
// static $max = null;
// if ( !is_null( $max ) ) {
// return $max;
// }
// $terms = get_terms( 'hb_room_capacity', array( 'hide_empty' => false ) );
// if ( $terms ) {
// foreach ( $terms as $term ) {
// $cap = get_term_meta( $term->term_id, 'hb_max_number_of_adults', true );
// **
// * @since  1.1.2
// * use term meta
// */
// if ( ! $cap ) {
// $cap = get_option( "hb_taxonomy_capacity_{$term->term_id}" );
// }
// if ( intval( $cap ) > $max ) {
// $max = $cap;
// }
// }
// }
// if ( ! $max ) {
// global $wpdb;
// $results = $wpdb->get_results( "SELECT MAX(meta_value) as max FROM $wpdb->termmeta WHERE meta_key = 'hb_max_number_of_adults'", ARRAY_A );
// $max     = $results[0]['max'];
// }

// return apply_filters( 'get_max_capacity_of_rooms', $max );
// }
// }

if ( ! function_exists( 'hb_get_max_capacity_of_rooms' ) ) {
	function hb_get_max_capacity_of_rooms() {
		static $max = null;

		$args      = array(
			'post_type'      => 'hb_room',
			'posts_per_page' => -1,
		);
		$the_query = new WP_Query( $args );

		if ( $the_query->have_posts() ) :
			while ( $the_query->have_posts() ) :
				$the_query->the_post();
				$adult = get_post_meta( get_the_ID(), '_hb_room_capacity_adult', true );
				if ( intval( $adult ) > $max ) {
					$max = $adult;
				}
		endwhile;
		endif;
		wp_reset_postdata();

		if ( ! $max ) {
			global $wpdb;
			$results = $wpdb->get_results( "SELECT MAX(meta_value) as max FROM $wpdb->postmeta WHERE meta_key = '_hb_room_capacity_adult'", ARRAY_A );
			$max     = $results[0]['max'];
		}

		return apply_filters( 'get_max_capacity_of_rooms', $max );
	}
}

// if ( ! function_exists( 'hb_get_min_capacity_of_rooms' ) ) {
// function hb_get_min_capacity_of_rooms() {
// static $min = null;
// if ( !is_null( $max ) ) {
// return $max;
// }
// $terms = get_terms( 'hb_room_capacity', array( 'hide_empty' => false ) );
// if ( $terms ) {
// foreach ( $terms as $term ) {
// $cap = get_term_meta( $term->term_id, 'hb_max_number_of_adults', true );
// **
// * @since  1.1.2
// * use term meta
// */
// if ( ! $cap ) {
// $cap = get_option( "hb_taxonomy_capacity_{$term->term_id}" );
// }
// if ( intval( $cap ) < $min ) {
// $min = $cap;
// }
// }
// }
// if ( ! $min ) {
// global $wpdb;
// $results = $wpdb->get_results( "SELECT MIN(meta_value) as min FROM $wpdb->termmeta WHERE meta_key = 'hb_max_number_of_adults'", ARRAY_A );
// $min     = $results[0]['min'];
// }

// return apply_filters( 'get_min_capacity_of_rooms', $min );
// }
// }

if ( ! function_exists( 'hb_get_min_capacity_of_rooms' ) ) {
	function hb_get_min_capacity_of_rooms() {
		static $min = null;

		$args      = array(
			'post_type'      => 'hb_room',
			'posts_per_page' => -1,
		);
		$the_query = new WP_Query( $args );

		if ( $the_query->have_posts() ) :
			while ( $the_query->have_posts() ) :
				$the_query->the_post();
				$adult = get_post_meta( get_the_ID(), '_hb_room_capacity_adult', true );
				if ( intval( $adult ) < $min ) {
					$min = $adult;
				}
		endwhile;
		endif;
		wp_reset_postdata();

		if ( ! $min ) {
			global $wpdb;
			$results = $wpdb->get_results( "SELECT MIN(meta_value) as min FROM $wpdb->postmeta WHERE meta_key = '_hb_room_capacity_adult'", ARRAY_A );
			$min     = $results[0]['min'];
		}

		return apply_filters( 'get_min_capacity_of_rooms', $min );
	}
}




// if ( ! function_exists( 'hb_get_capacity_of_rooms' ) ) {
// get array search
// function hb_get_capacity_of_rooms() {
// $terms  = get_terms( 'hb_room_capacity', array( 'hide_empty' => false ) );
// $return = array();
// if ( $terms ) {
// foreach ( $terms as $term ) {
// $qty = get_term_meta( $term->term_id, 'hb_max_number_of_adults', true );
// **
// * @since  1.1.2
// * use term meta
// */
// if ( ! $qty ) {
// get_option( 'hb_taxonomy_capacity_' . $term->term_id );
// }
// if ( $qty ) {
// $return[$qty] = array(
// 'value' => $qty,
// 'text'  => $qty
// );
// }
// }
// }

// if ( !$return ) {
// global $wpdb;
// $return = $wpdb->get_results( "SELECT term_id as value,meta_value as text FROM wp_termmeta WHERE meta_key = 'hb_max_number_of_adults'", ARRAY_A );
// asort( $return );
// }

// ksort( $return );

// return $return;
// }
// }
if ( ! function_exists( 'hb_get_capacity_of_rooms' ) ) {
	// get array search
	function hb_get_capacity_of_rooms() {
		global $hb_settings;
		$max_adult = $hb_settings->get( 'max_adults_all_room' );
		$return    = array();
		if ( $max_adult ) {
			for ( $i = 1; $i <= $max_adult; $i++ ) {
				$return[ $i ] = array(
					'value' => $i,
					'text'  => $i,
				);
			}
		}

		// if ( !$return ) {
		// global $wpdb;
		// $return = $wpdb->get_results( "SELECT term_id as value,meta_value as text FROM wp_termmeta WHERE meta_key = 'hb_max_number_of_adults'", ARRAY_A );
		// asort( $return );
		// }

		ksort( $return );

		return $return;
	}
}

/**
 * minhpd : 11-7-2022 : check is not use
 * List room capacities into dropdown select
 *
 * @param array
 *
 * @return string
 */
if ( ! function_exists( 'hb_dropdown_room_capacities' ) ) {
	function hb_dropdown_room_capacities( $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'echo' => true,
			)
		);
		ob_start();
		wp_dropdown_categories(
			array_merge(
				$args,
				array(
					'taxonomy'   => 'hb_room_capacity',
					'hide_empty' => false,
					'name'       => 'hb-room-capacities',
				)
			)
		);

		$output = ob_get_clean();
		if ( $args['echo'] ) {
			WPHB_Helpers::print( sprintf( '%s', $output ) );
		}

		return $output;
	}
}

/**
 *
 * minhpd : 11-7-2022 : check is not use
 * List room types into dropdown select
 *
 * @param array $args
 *
 * @return string
 */
if ( ! function_exists( 'hb_dropdown_room_types' ) ) {
	function hb_dropdown_room_types( $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'echo' => true,
			)
		);
		ob_start();
		wp_dropdown_categories(
			array_merge(
				$args,
				array(
					'taxonomy'   => 'hb_room_type',
					'hide_empty' => false,
					'name'       => 'hb-room-types',
					'orderby'    => 'term_group',
					'echo'       => true,
				)
			)
		);
		$output = ob_get_clean();

		if ( $args['echo'] ) {
			WPHB_Helpers::print( sprintf( '%s', $output ) );
		}

		return $output;
	}
}

/**
 * List room types into dropdown select
 *
 * @param array $args
 *
 * @return string
 */
if ( ! function_exists( 'hb_dropdown_rooms' ) ) {
	function hb_dropdown_rooms( $args = array( 'selected' => '' ) ) {
		global $wpdb;
		$posts = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT ID, post_title FROM {$wpdb->posts} WHERE `post_type` = %s AND `post_status` = %s",
				'hb_room',
				'publish'
			),
			OBJECT
		);

		$output                    = '<select name="hb-room" id="hb-room-select">';
		$emptySelected             = new stdClass();
		$emptySelected->ID         = '';
		$emptySelected->post_title = __( '---Select Room---', 'wp-hotel-booking' );
		/* filter rooms dropdown list */
		$posts = apply_filters( 'hotel_booking_rooms_dropdown', $posts );
		$posts = array_merge( array( $emptySelected ), $posts );
		if ( $posts && is_array( $posts ) ) {
			foreach ( $posts as $key => $post ) {
				$output .= '<option value="' . $post->ID . '"' . ( $post->ID == $args['selected'] ? ' selected' : '' ) . '>' . $post->post_title . '</option>';
			}
		}
		$output .= '</select>';

		return $output;
	}
}

/**
 * Get room types taxonomy
 * minhpd : 11-7-2022 : check is not use
 *
 * @param array $args
 *
 * @return array
 */
if ( ! function_exists( 'hb_get_room_types' ) ) {
	function hb_get_room_types( $args = array() ) {
		$args  = wp_parse_args(
			$args,
			array(
				'taxonomy'   => 'hb_room_type',
				'hide_empty' => 0,
				'orderby'    => 'term_group',
				'map_fields' => null,
			)
		);
		$terms = (array) get_terms( 'hb_room_type', $args );
		if ( is_array( $args['map_fields'] ) ) {
			$types = array();
			foreach ( $terms as $term ) {
				$type = new stdClass();
				foreach ( $args['map_fields'] as $from => $to ) {
					if ( ! empty( $term->{$from} ) ) {
						$type->{$to} = $term->{$from};
					} else {
						$type->{$to} = null;
					}
				}
				$types[] = $type;
			}
		} else {
			$types = $terms;
		}

		return $types;
	}
}

/**
 * Get room capacities taxonomy
 *
 * @param array $args
 *
 * @return array
 */
if ( ! function_exists( 'hb_get_room_capacities' ) ) {
	function hb_get_room_capacities( $args = array() ) {
		$args  = wp_parse_args(
			$args,
			array(
				'taxonomy'   => 'hb_room_capacity',
				'hide_empty' => 0,
				'orderby'    => 'term_group',
				'map_fields' => null,
			)
		);
		$terms = (array) get_terms( 'hb_room_capacity', $args );
		if ( is_array( $args['map_fields'] ) ) {
			$types = array();
			foreach ( $terms as $term ) {
				$type = new stdClass();
				foreach ( $args['map_fields'] as $from => $to ) {
					if ( ! empty( $term->{$from} ) ) {
						$type->{$to} = $term->{$from};
					} else {
						$type->{$to} = null;
					}
				}
				$types[] = $type;
			}
		} else {
			$types = $terms;
		}

		return $types;
	}
}

/**
 * Get list of child per each room with all available rooms
 *
 * @return mixed
 */
if ( ! function_exists( 'hb_get_child_per_room' ) ) {
	function hb_get_child_per_room() {
		global $wpdb;
		$query = $wpdb->prepare(
			"
        SELECT DISTINCT meta_value
        FROM {$wpdb->postmeta} pm
        INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
        WHERE p.post_type=%s
          AND p.post_status LIKE 'publish'
          AND meta_key=%s
          AND meta_value <> 0
        ORDER BY meta_value ASC
    ",
			'hb_room',
			'_hb_max_child_per_room'
		);

		return $wpdb->get_col( $query );
	}
}

/**
 * Get dropdown select children in search room form
 *
 * @return mixed
 */
if ( ! function_exists( 'hb_get_children_of_rooms' ) ) {
	function hb_get_children_of_rooms() {
		$children = hb_get_child_per_room();
		$return   = array();
		if ( $children ) {
			foreach ( $children as $key => $child ) {
				$return[ $key ] = array(
					'value' => $child,
					'text'  => $child,
				);
			}
		}

		ksort( $return );

		return $return;
	}
}

/**
 * Get list of child per each room with all available rooms
 *
 * @return mixed
 */
if ( ! function_exists( 'hb_get_max_child_of_rooms' ) ) {
	function hb_get_max_child_of_rooms() {
		$rows = hb_get_child_per_room();
		if ( $rows ) {
			sort( $rows );

			return $rows ? end( $rows ) : - 1;
		}
	}
}

/**
 * List child of room into dropdown select
 *
 * @param array $args
 */
if ( ! function_exists( 'hb_dropdown_child_per_room' ) ) {
	function hb_dropdown_child_per_room( $args = array() ) {
		$args      = wp_parse_args(
			$args,
			array(
				'name'     => '',
				'selected' => '',
			)
		);
		$max_child = hb_get_max_child_of_rooms();
		$output    = '<select name="' . $args['name'] . '">';
		$output   .= '<option value="0">' . __( 'Select', 'wp-hotel-booking' ) . '</option>';
		if ( $max_child > 0 ) {
			for ( $i = 1; $i <= $max_child; $i ++ ) {
				$output .= sprintf( '<option value="%1$d"%2$s>%1$d</option>', $i, $args['selected'] == $i ? ' selected="selected"' : '' );
			}
		}
		$output .= '</select>';
		WPHB_Helpers::print( sprintf( '%s', $output ) );
	}
}

/**
 * Get capacity of a room type
 *
 * @param $type_id
 *
 * @return int
 */
if ( ! function_exists( 'hb_get_room_type_capacities' ) ) {
	function hb_get_room_type_capacities( $type_id ) {
		return intval( get_option( "hb_taxonomy_capacity_{$type_id}" ) );
	}
}

/**
 * Parse a param from request has encoded
 */
if ( ! function_exists( 'hb_parse_request' ) ) {
	function hb_parse_request() {
		$params = hb_get_request( 'hotel-booking-params' );
		if ( $params ) {
			$params = json_decode( base64_decode( $params ) );

			if ( $params && is_object( $params ) ) {
				foreach ( $params as $key => $value ) {
					$_GET[ $key ]     = $value;
					$_POST[ $key ]    = $value;
					$_REQUEST[ $key ] = $value;
				}
			}
			if ( isset( $_GET['hotel-booking-params'] ) ) {
				unset( $_GET['hotel-booking-params'] );
			}
			if ( isset( $_POST['hotel-booking-params'] ) ) {
				unset( $_POST['hotel-booking-params'] );
			}
			if ( isset( $_REQUEST['hotel-booking-params'] ) ) {
				unset( $_REQUEST['hotel-booking-params'] );
			}
		}
	}
}

add_action( 'init', 'hb_parse_request' );

/**
 * Get the list of common currencies
 *
 * @return mixed
 */
if ( ! function_exists( 'hb_payment_currencies' ) ) {
	function hb_payment_currencies() {
		$currencies = array(
			'AED' => __( 'United Arab Emirates dirham', 'wp-hotel-booking' ),
			'AFN' => __( 'Afghan afghani', 'wp-hotel-booking' ),
			'ALL' => __( 'Albanian lek', 'wp-hotel-booking' ),
			'AMD' => __( 'Armenian dram', 'wp-hotel-booking' ),
			'ANG' => __( 'Netherlands Antillean guilder', 'wp-hotel-booking' ),
			'AOA' => __( 'Angolan kwanza', 'wp-hotel-booking' ),
			'ARS' => __( 'Argentine peso', 'wp-hotel-booking' ),
			'AUD' => __( 'Australian dollar', 'wp-hotel-booking' ),
			'AWG' => __( 'Aruban florin', 'wp-hotel-booking' ),
			'AZN' => __( 'Azerbaijani manat', 'wp-hotel-booking' ),
			'BAM' => __( 'Bosnia and Herzegovina convertible mark', 'wp-hotel-booking' ),
			'BBD' => __( 'Barbadian dollar', 'wp-hotel-booking' ),
			'BDT' => __( 'Bangladeshi taka', 'wp-hotel-booking' ),
			'BGN' => __( 'Bulgarian lev', 'wp-hotel-booking' ),
			'BHD' => __( 'Bahraini dinar', 'wp-hotel-booking' ),
			'BIF' => __( 'Burundian franc', 'wp-hotel-booking' ),
			'BMD' => __( 'Bermudian dollar', 'wp-hotel-booking' ),
			'BND' => __( 'Brunei dollar', 'wp-hotel-booking' ),
			'BOB' => __( 'Bolivian boliviano', 'wp-hotel-booking' ),
			'BRL' => __( 'Brazilian real', 'wp-hotel-booking' ),
			'BSD' => __( 'Bahamian dollar', 'wp-hotel-booking' ),
			'BTC' => __( 'Bitcoin', 'wp-hotel-booking' ),
			'BTN' => __( 'Bhutanese ngultrum', 'wp-hotel-booking' ),
			'BWP' => __( 'Botswana pula', 'wp-hotel-booking' ),
			'BYR' => __( 'Belarusian ruble (old)', 'wp-hotel-booking' ),
			'BYN' => __( 'Belarusian ruble', 'wp-hotel-booking' ),
			'BZD' => __( 'Belize dollar', 'wp-hotel-booking' ),
			'CAD' => __( 'Canadian dollar', 'wp-hotel-booking' ),
			'CDF' => __( 'Congolese franc', 'wp-hotel-booking' ),
			'CHF' => __( 'Swiss franc', 'wp-hotel-booking' ),
			'CLP' => __( 'Chilean peso', 'wp-hotel-booking' ),
			'CNY' => __( 'Chinese yuan', 'wp-hotel-booking' ),
			'COP' => __( 'Colombian peso', 'wp-hotel-booking' ),
			'CRC' => __( 'Costa Rican col&oacute;n', 'wp-hotel-booking' ),
			'CUC' => __( 'Cuban convertible peso', 'wp-hotel-booking' ),
			'CUP' => __( 'Cuban peso', 'wp-hotel-booking' ),
			'CVE' => __( 'Cape Verdean escudo', 'wp-hotel-booking' ),
			'CZK' => __( 'Czech koruna', 'wp-hotel-booking' ),
			'DJF' => __( 'Djiboutian franc', 'wp-hotel-booking' ),
			'DKK' => __( 'Danish krone', 'wp-hotel-booking' ),
			'DOP' => __( 'Dominican peso', 'wp-hotel-booking' ),
			'DZD' => __( 'Algerian dinar', 'wp-hotel-booking' ),
			'EGP' => __( 'Egyptian pound', 'wp-hotel-booking' ),
			'ERN' => __( 'Eritrean nakfa', 'wp-hotel-booking' ),
			'ETB' => __( 'Ethiopian birr', 'wp-hotel-booking' ),
			'EUR' => __( 'Euro', 'wp-hotel-booking' ),
			'FJD' => __( 'Fijian dollar', 'wp-hotel-booking' ),
			'FKP' => __( 'Falkland Islands pound', 'wp-hotel-booking' ),
			'GBP' => __( 'Pound sterling', 'wp-hotel-booking' ),
			'GEL' => __( 'Georgian lari', 'wp-hotel-booking' ),
			'GGP' => __( 'Guernsey pound', 'wp-hotel-booking' ),
			'GHS' => __( 'Ghana cedi', 'wp-hotel-booking' ),
			'GIP' => __( 'Gibraltar pound', 'wp-hotel-booking' ),
			'GMD' => __( 'Gambian dalasi', 'wp-hotel-booking' ),
			'GNF' => __( 'Guinean franc', 'wp-hotel-booking' ),
			'GTQ' => __( 'Guatemalan quetzal', 'wp-hotel-booking' ),
			'GYD' => __( 'Guyanese dollar', 'wp-hotel-booking' ),
			'HKD' => __( 'Hong Kong dollar', 'wp-hotel-booking' ),
			'HNL' => __( 'Honduran lempira', 'wp-hotel-booking' ),
			'HRK' => __( 'Croatian kuna', 'wp-hotel-booking' ),
			'HTG' => __( 'Haitian gourde', 'wp-hotel-booking' ),
			'HUF' => __( 'Hungarian forint', 'wp-hotel-booking' ),
			'IDR' => __( 'Indonesian rupiah', 'wp-hotel-booking' ),
			'ILS' => __( 'Israeli new shekel', 'wp-hotel-booking' ),
			'IMP' => __( 'Manx pound', 'wp-hotel-booking' ),
			'INR' => __( 'Indian rupee', 'wp-hotel-booking' ),
			'IQD' => __( 'Iraqi dinar', 'wp-hotel-booking' ),
			'IRR' => __( 'Iranian rial', 'wp-hotel-booking' ),
			'IRT' => __( 'Iranian toman', 'wp-hotel-booking' ),
			'ISK' => __( 'Icelandic kr&oacute;na', 'wp-hotel-booking' ),
			'JEP' => __( 'Jersey pound', 'wp-hotel-booking' ),
			'JMD' => __( 'Jamaican dollar', 'wp-hotel-booking' ),
			'JOD' => __( 'Jordanian dinar', 'wp-hotel-booking' ),
			'JPY' => __( 'Japanese yen', 'wp-hotel-booking' ),
			'KES' => __( 'Kenyan shilling', 'wp-hotel-booking' ),
			'KGS' => __( 'Kyrgyzstani som', 'wp-hotel-booking' ),
			'KHR' => __( 'Cambodian riel', 'wp-hotel-booking' ),
			'KMF' => __( 'Comorian franc', 'wp-hotel-booking' ),
			'KPW' => __( 'North Korean won', 'wp-hotel-booking' ),
			'KRW' => __( 'South Korean won', 'wp-hotel-booking' ),
			'KWD' => __( 'Kuwaiti dinar', 'wp-hotel-booking' ),
			'KYD' => __( 'Cayman Islands dollar', 'wp-hotel-booking' ),
			'KZT' => __( 'Kazakhstani tenge', 'wp-hotel-booking' ),
			'LAK' => __( 'Lao kip', 'wp-hotel-booking' ),
			'LBP' => __( 'Lebanese pound', 'wp-hotel-booking' ),
			'LKR' => __( 'Sri Lankan rupee', 'wp-hotel-booking' ),
			'LRD' => __( 'Liberian dollar', 'wp-hotel-booking' ),
			'LSL' => __( 'Lesotho loti', 'wp-hotel-booking' ),
			'LYD' => __( 'Libyan dinar', 'wp-hotel-booking' ),
			'MAD' => __( 'Moroccan dirham', 'wp-hotel-booking' ),
			'MDL' => __( 'Moldovan leu', 'wp-hotel-booking' ),
			'MGA' => __( 'Malagasy ariary', 'wp-hotel-booking' ),
			'MKD' => __( 'Macedonian denar', 'wp-hotel-booking' ),
			'MMK' => __( 'Burmese kyat', 'wp-hotel-booking' ),
			'MNT' => __( 'Mongolian t&ouml;gr&ouml;g', 'wp-hotel-booking' ),
			'MOP' => __( 'Macanese pataca', 'wp-hotel-booking' ),
			'MRU' => __( 'Mauritanian ouguiya', 'wp-hotel-booking' ),
			'MUR' => __( 'Mauritian rupee', 'wp-hotel-booking' ),
			'MVR' => __( 'Maldivian rufiyaa', 'wp-hotel-booking' ),
			'MWK' => __( 'Malawian kwacha', 'wp-hotel-booking' ),
			'MXN' => __( 'Mexican peso', 'wp-hotel-booking' ),
			'MYR' => __( 'Malaysian ringgit', 'wp-hotel-booking' ),
			'MZN' => __( 'Mozambican metical', 'wp-hotel-booking' ),
			'NAD' => __( 'Namibian dollar', 'wp-hotel-booking' ),
			'NGN' => __( 'Nigerian naira', 'wp-hotel-booking' ),
			'NIO' => __( 'Nicaraguan c&oacute;rdoba', 'wp-hotel-booking' ),
			'NOK' => __( 'Norwegian krone', 'wp-hotel-booking' ),
			'NPR' => __( 'Nepalese rupee', 'wp-hotel-booking' ),
			'NZD' => __( 'New Zealand dollar', 'wp-hotel-booking' ),
			'OMR' => __( 'Omani rial', 'wp-hotel-booking' ),
			'PAB' => __( 'Panamanian balboa', 'wp-hotel-booking' ),
			'PEN' => __( 'Sol', 'wp-hotel-booking' ),
			'PGK' => __( 'Papua New Guinean kina', 'wp-hotel-booking' ),
			'PHP' => __( 'Philippine peso', 'wp-hotel-booking' ),
			'PKR' => __( 'Pakistani rupee', 'wp-hotel-booking' ),
			'PLN' => __( 'Polish z&#x142;oty', 'wp-hotel-booking' ),
			'PRB' => __( 'Transnistrian ruble', 'wp-hotel-booking' ),
			'PYG' => __( 'Paraguayan guaran&iacute;', 'wp-hotel-booking' ),
			'QAR' => __( 'Qatari riyal', 'wp-hotel-booking' ),
			'RON' => __( 'Romanian leu', 'wp-hotel-booking' ),
			'RSD' => __( 'Serbian dinar', 'wp-hotel-booking' ),
			'RUB' => __( 'Russian ruble', 'wp-hotel-booking' ),
			'RWF' => __( 'Rwandan franc', 'wp-hotel-booking' ),
			'SAR' => __( 'Saudi riyal', 'wp-hotel-booking' ),
			'SBD' => __( 'Solomon Islands dollar', 'wp-hotel-booking' ),
			'SCR' => __( 'Seychellois rupee', 'wp-hotel-booking' ),
			'SDG' => __( 'Sudanese pound', 'wp-hotel-booking' ),
			'SEK' => __( 'Swedish krona', 'wp-hotel-booking' ),
			'SGD' => __( 'Singapore dollar', 'wp-hotel-booking' ),
			'SHP' => __( 'Saint Helena pound', 'wp-hotel-booking' ),
			'SLL' => __( 'Sierra Leonean leone', 'wp-hotel-booking' ),
			'SOS' => __( 'Somali shilling', 'wp-hotel-booking' ),
			'SRD' => __( 'Surinamese dollar', 'wp-hotel-booking' ),
			'SSP' => __( 'South Sudanese pound', 'wp-hotel-booking' ),
			'STN' => __( 'S&atilde;o Tom&eacute; and Pr&iacute;ncipe dobra', 'wp-hotel-booking' ),
			'SYP' => __( 'Syrian pound', 'wp-hotel-booking' ),
			'SZL' => __( 'Swazi lilangeni', 'wp-hotel-booking' ),
			'THB' => __( 'Thai baht', 'wp-hotel-booking' ),
			'TJS' => __( 'Tajikistani somoni', 'wp-hotel-booking' ),
			'TMT' => __( 'Turkmenistan manat', 'wp-hotel-booking' ),
			'TND' => __( 'Tunisian dinar', 'wp-hotel-booking' ),
			'TOP' => __( 'Tongan pa&#x2bb;anga', 'wp-hotel-booking' ),
			'TRY' => __( 'Turkish lira', 'wp-hotel-booking' ),
			'TTD' => __( 'Trinidad and Tobago dollar', 'wp-hotel-booking' ),
			'TWD' => __( 'New Taiwan dollar', 'wp-hotel-booking' ),
			'TZS' => __( 'Tanzanian shilling', 'wp-hotel-booking' ),
			'UAH' => __( 'Ukrainian hryvnia', 'wp-hotel-booking' ),
			'UGX' => __( 'Ugandan shilling', 'wp-hotel-booking' ),
			'USD' => __( 'United States (US) dollar', 'wp-hotel-booking' ),
			'UYU' => __( 'Uruguayan peso', 'wp-hotel-booking' ),
			'UZS' => __( 'Uzbekistani som', 'wp-hotel-booking' ),
			'VEF' => __( 'Venezuelan bol&iacute;var', 'wp-hotel-booking' ),
			'VES' => __( 'Bol&iacute;var soberano', 'wp-hotel-booking' ),
			'VND' => __( 'Vietnamese &#x111;&#x1ed3;ng', 'wp-hotel-booking' ),
			'VUV' => __( 'Vanuatu vatu', 'wp-hotel-booking' ),
			'WST' => __( 'Samoan t&#x101;l&#x101;', 'wp-hotel-booking' ),
			'XAF' => __( 'Central African CFA franc', 'wp-hotel-booking' ),
			'XCD' => __( 'East Caribbean dollar', 'wp-hotel-booking' ),
			'XOF' => __( 'West African CFA franc', 'wp-hotel-booking' ),
			'XPF' => __( 'CFP franc', 'wp-hotel-booking' ),
			'YER' => __( 'Yemeni rial', 'wp-hotel-booking' ),
			'ZAR' => __( 'South African rand', 'wp-hotel-booking' ),
			'ZMW' => __( 'Zambian kwacha', 'wp-hotel-booking' ),
		);

		return apply_filters( 'hb_payment_currencies', $currencies );
	}
}

/**
 * Checks to see if is enable overwrite templates from theme
 *
 * @return bool
 */
if ( ! function_exists( 'hb_enable_overwrite_template' ) ) {
	function hb_enable_overwrite_template() {
		return WPHB_Settings::instance()->get( 'overwrite_templates' ) == 'on';
	}
}


/**
 * Get a variable from request
 *
 * @param string
 * @param mixed
 * @param mixed
 *
 * @return mixed
 */
if ( ! function_exists( 'hb_get_request' ) ) {
	function hb_get_request( $name, $default = null, $var = '' ) {
		$return = $default;

		switch ( strtolower( $var ) ) {
			case 'post':
				if ( ! empty( $_POST[ $name ] ) ) {
					$return = WPHB_Helpers::sanitize_params_submitted( $_POST[ $name ] );
				}
				break;
			case 'get':
				if ( ! empty( $_GET[ $name ] ) ) {
					$return = WPHB_Helpers::sanitize_params_submitted( $_GET[ $name ] );
				}
				break;
			default:
				if ( ! empty( $_REQUEST[ $name ] ) ) {
					$return = WPHB_Helpers::sanitize_params_submitted( $_REQUEST[ $name ] );
				}
		}

		return $return;
	}
}

/**
 * Calculate the nights between to dates
 *
 * @param null $end
 * @param      $start
 *
 * @return float
 */
if ( ! function_exists( 'hb_count_nights_two_dates' ) ) {
	function hb_count_nights_two_dates( $end = null, $start = null ) {
		if ( ! $end ) {
			$end = time();
		} elseif ( is_numeric( $end ) ) {
			$end = $end;
		} elseif ( is_string( $end ) ) {
			$end = @strtotime( $end );
		}

		if ( is_numeric( $start ) ) {
			$start = $start;
		} elseif ( is_string( $start ) ) {
			$start = strtotime( $start );
		}
		$datediff = $end - $start;
		// check if room enale booking only day
		$booking_only = 0;
		if ( $end == $start ) {
			$booking_only = 1;
		}

		return ! empty( $booking_only ) ? $booking_only : floor( $datediff / ( 60 * 60 * 24 ) );
	}
}

if ( ! function_exists( 'hb_date_names' ) ) {
	function hb_date_names() {
		$date_names = array(
			__( 'Sun', 'wp-hotel-booking' ),
			__( 'Mon', 'wp-hotel-booking' ),
			__( 'Tue', 'wp-hotel-booking' ),
			__( 'Wed', 'wp-hotel-booking' ),
			__( 'Thu', 'wp-hotel-booking' ),
			__( 'Fri', 'wp-hotel-booking' ),
			__( 'Sat', 'wp-hotel-booking' ),
		);

		return apply_filters( 'hb_date_names', $date_names );
	}
}

if ( ! function_exists( 'hb_start_of_week_order' ) ) {
	function hb_start_of_week_order() {
		$start = get_option( 'start_of_week' );

		$order = array();

		for ( $i = (int) $start; $i < 7; $i ++ ) {
			$order[] = $i;
		}

		for ( $j = 0; $j < $start; $j ++ ) {
			$order[] = $j;
		}

		return $order;
	}
}

if ( ! function_exists( 'hb_date_to_name' ) ) {
	function hb_date_to_name( $date ) {
		$date_names = hb_date_names();

		return $date_names[ $date ];
	}
}

if ( ! function_exists( 'hb_get_common_titles' ) ) {
	function hb_get_common_titles() {
		return apply_filters(
			'hb_customer_titles',
			array(
				'mr'   => __( 'Mr.', 'wp-hotel-booking' ),
				'ms'   => __( 'Ms.', 'wp-hotel-booking' ),
				'mrs'  => __( 'Mrs.', 'wp-hotel-booking' ),
				'miss' => __( 'Miss.', 'wp-hotel-booking' ),
				'dr'   => __( 'Dr.', 'wp-hotel-booking' ),
				'prof' => __( 'Prof.', 'wp-hotel-booking' ),
			)
		);
	}
}


if ( ! function_exists( 'hb_get_title_by_slug' ) ) {
	function hb_get_title_by_slug( $slug ) {
		$titles = hb_get_common_titles();

		return ! empty( $titles[ $slug ] ) ? $titles[ $slug ] : '';
	}
}


if ( ! function_exists( 'hb_dropdown_titles' ) ) {
	function hb_dropdown_titles( $args = array() ) {
		$args              = wp_parse_args(
			$args,
			array(
				'name'              => 'title',
				'selected'          => '',
				'show_option_none'  => __( 'Select', 'wp-hotel-booking' ),
				'option_none_value' => '',
				'echo'              => true,
				'required'          => false,
			)
		);
		$name              = '';
		$selected          = '';
		$echo              = false;
		$required          = false;
		$show_option_none  = false;
		$option_none_value = '';
		extract( $args );
		$titles = hb_get_common_titles();
		$output = '<select name="' . $name . '" ' . ( $required ? 'required' : '' ) . '>';
		if ( $show_option_none ) {
			$output .= sprintf( '<option value="%s">%s</option>', $option_none_value, $show_option_none );
		}
		if ( $titles ) {
			foreach ( $titles as $slug => $title ) {
				$output .= sprintf( '<option value="%s"%s>%s</option>', $slug, $slug == $selected ? ' selected="selected"' : '', $title );
			}
		}
		$output .= '</select>';
		if ( $echo ) {
			echo sprintf( '%s', $output );
		}

		return $output;
	}
}

/**
 * Create an empty object with all fields as a WP_Post object
 *
 * @return stdClass
 */
if ( ! function_exists( 'hb_create_empty_post' ) ) {
	function hb_create_empty_post( $args = array() ) {
		$posts = get_posts(
			array(
				'post_type'      => 'any',
				'posts_per_page' => 1,
			)
		);

		if ( $posts ) {
			foreach ( get_object_vars( $posts[0] ) as $key => $value ) {
				if ( ! in_array( $key, $args ) ) {
					$posts[0]->{$key} = null;
				} else {
					$posts[0]->{$key} = $args[ $key ];
				}
			}

			return $posts[0];
		}

		return new stdClass();
	}
}

/**
 * Localize script for front-end
 *
 * @return mixed
 */
if ( ! function_exists( 'hb_i18n' ) ) {
	function hb_i18n() {
		$translation = array(
			'invalid_email'                  => __( 'Your email address is invalid.', 'wp-hotel-booking' ),
			'no_payment_method_selected'     => __( 'Please select your payment method.', 'wp-hotel-booking' ),
			'confirm_tos'                    => __( 'Please accept our Terms and Conditions.', 'wp-hotel-booking' ),
			'no_rooms_selected'              => __( 'Please select at least one the room.', 'wp-hotel-booking' ),
			'empty_customer_title'           => __( 'Please select your title.', 'wp-hotel-booking' ),
			'empty_customer_first_name'      => __( 'Please enter your first name.', 'wp-hotel-booking' ),
			'empty_customer_last_name'       => __( 'Please enter your last name.', 'wp-hotel-booking' ),
			'empty_customer_address'         => __( 'Please enter your address.', 'wp-hotel-booking' ),
			'empty_customer_city'            => __( 'Please enter your city name.', 'wp-hotel-booking' ),
			'empty_customer_state'           => __( 'Please enter your state.', 'wp-hotel-booking' ),
			'empty_customer_postal_code'     => __( 'Please enter your postal code.', 'wp-hotel-booking' ),
			'empty_customer_country'         => __( 'Please select your country.', 'wp-hotel-booking' ),
			'empty_customer_phone'           => __( 'Please enter your phone number.', 'wp-hotel-booking' ),
			'customer_email_invalid'         => __( 'Your email is invalid.', 'wp-hotel-booking' ),
			'customer_email_not_match'       => __( 'Your email does not match with existing email! Ok to create a new customer information.', 'wp-hotel-booking' ),
			'empty_check_in_date'            => __( 'Please select check in date.', 'wp-hotel-booking' ),
			'empty_check_out_date'           => __( 'Please select check out date.', 'wp-hotel-booking' ),
			'check_in_date_must_be_greater'  => __( 'Check in date must be greater than the current.', 'wp-hotel-booking' ),
			'check_out_date_must_be_greater' => __( 'Check out date must be greater than the check in.', 'wp-hotel-booking' ),
			'enter_coupon_code'              => __( 'Please enter coupon code.', 'wp-hotel-booking' ),
			'review_rating_required'         => __( 'Please select a rating.', 'wp-hotel-booking' ),
			'review_content_required'        => __( 'Please enter a review.', 'wp-hotel-booking' ),
			'review_author_required'         => __( 'Please enter your author.', 'wp-hotel-booking' ),
			'review_email_required'          => __( 'Please enter your email.', 'wp-hotel-booking' ),
			'waring'                         => array(
				'room_select' => __( 'Please select room number.', 'wp-hotel-booking' ),
				'try_again'   => __( 'Please try again!', 'wp-hotel-booking' ),
			),
			'date_time_format'               => hb_date_time_format_js(),
			'monthNames'                     => hb_month_name_js(),
			'monthNamesShort'                => hb_month_name_short_js(),
			'dayNames'                       => hb_day_name_js(),
			'dayNamesShort'                  => hb_day_name_short_js(),
			'dayNamesMin'                    => hb_day_name_min_js(),
			'date_start'                     => get_option( 'start_of_week' ),
			'view_cart'                      => __( 'View Cart', 'wp-hotel-booking' ),
			'cart_url'                       => hb_get_cart_url(),
		);

		return apply_filters( 'hb_i18n', $translation );
	}
}

// date time format
if ( ! function_exists( 'hb_date_time_format_js' ) ) {
	function hb_date_time_format_js() {
		// set detault datetime format datepicker
		$dateFormat = hb_get_date_format();

		switch ( $dateFormat ) {
			case 'Y-m-d':
				$return = 'yy-mm-dd';
				break;

			case 'Y/m/d':
				$return = 'yy/mm/dd';
				break;

			case 'd/m/Y':
				$return = 'dd/mm/yy';
				break;

			case 'd-m-Y':
				$return = 'dd-mm-yy';
				break;

			case 'm/d/Y':
				$return = 'mm/dd/yy';
				break;

			case 'm-d-Y':
				$return = 'mm-dd-yy';
				break;

			case 'F j, Y':
				$return = 'MM dd, yy';
				break;
				
			case 'j F Y':
				$return = 'dd MM yy';
				break;

			case 'd.m.Y':
				$return = 'dd.mm.yy';
				break;

			case 'j.n.Y':
				$return = 'dd.m.yy';
				break;

			default:
				$return = 'mm/dd/yy';
				break;
		}

		return $return;
	}
}

if ( ! function_exists( 'hb_month_name_js' ) ) {

	function hb_month_name_js() {
		return apply_filters(
			'hotel_booking_month_name_js',
			array(
				__( 'January', 'wp-hotel-booking' ),
				__( 'February', 'wp-hotel-booking' ),
				__( 'March', 'wp-hotel-booking' ),
				__( 'April', 'wp-hotel-booking' ),
				__( 'May', 'wp-hotel-booking' ),
				__( 'June', 'wp-hotel-booking' ),
				__( 'July', 'wp-hotel-booking' ),
				__( 'August', 'wp-hotel-booking' ),
				__( 'September', 'wp-hotel-booking' ),
				__( 'October', 'wp-hotel-booking' ),
				__( 'November', 'wp-hotel-booking' ),
				__( 'December', 'wp-hotel-booking' ),
			)
		);
	}
}

if ( ! function_exists( 'hb_month_name_short_js' ) ) {

	function hb_month_name_short_js() {
		return apply_filters(
			'hotel_booking_month_name_short_js',
			array(
				__( 'Jan', 'wp-hotel-booking' ),
				__( 'Feb', 'wp-hotel-booking' ),
				__( 'Mar', 'wp-hotel-booking' ),
				__( 'Apr', 'wp-hotel-booking' ),
				__( 'Maj', 'wp-hotel-booking' ),
				__( 'Jun', 'wp-hotel-booking' ),
				__( 'Jul', 'wp-hotel-booking' ),
				__( 'Aug', 'wp-hotel-booking' ),
				__( 'Sep', 'wp-hotel-booking' ),
				__( 'Oct', 'wp-hotel-booking' ),
				__( 'Nov', 'wp-hotel-booking' ),
				__( 'Dec', 'wp-hotel-booking' ),
			)
		);
	}
}

if ( ! function_exists( 'hb_day_name_js' ) ) {

	function hb_day_name_js() {
		return apply_filters(
			'hotel_booking_day_name_js',
			array(
				__( 'Sunday', 'wp-hotel-booking' ),
				__( 'Monday', 'wp-hotel-booking' ),
				__( 'Tuesday', 'wp-hotel-booking' ),
				__( 'Wednesday', 'wp-hotel-booking' ),
				__( 'Thursday', 'wp-hotel-booking' ),
				__( 'Friday', 'wp-hotel-booking' ),
				__( 'Saturday', 'wp-hotel-booking' ),
			)
		);
	}
}

if ( ! function_exists( 'hb_day_name_short_js' ) ) {

	function hb_day_name_short_js() {
		return apply_filters( 'hotel_booking_day_name_short_js', hb_date_names() );
	}
}
if ( ! function_exists( 'hb_day_name_min_js' ) ) {

	function hb_day_name_min_js() {
		return apply_filters(
			'hotel_booking_day_name_min_js',
			array(
				__( 'Su', 'wp-hotel-booking' ),
				__( 'Mo', 'wp-hotel-booking' ),
				__( 'Tu', 'wp-hotel-booking' ),
				__( 'We', 'wp-hotel-booking' ),
				__( 'Th', 'wp-hotel-booking' ),
				__( 'Fr', 'wp-hotel-booking' ),
				__( 'Sa', 'wp-hotel-booking' ),
			)
		);
	}
}

/**
 * Get tax setting
 *
 * @return float|mixed
 */
if ( ! function_exists( 'hb_get_tax_settings' ) ) {

	function hb_get_tax_settings() {
		$settings = WPHB_Settings::instance();
		if ( $tax = $settings->get( 'tax' ) ) {
			$tax = (float) $settings->get( 'tax' ) / 100;
		}

		if ( hb_price_including_tax() ) {
			$tax = $tax;
		}

		$tax = apply_filters( 'hotel_booking_room_tax', $tax );

		return $tax;
	}
}

if ( ! function_exists( 'hb_price_including_tax' ) ) {

	function hb_price_including_tax( $cart = false ) {
		$settings = WPHB_Settings::instance();

		return apply_filters( 'hb_price_including_tax', $settings->get( 'price_including_tax' ), $cart );
	}
}

if ( ! function_exists( 'hb_dropdown_numbers' ) ) {

	function hb_dropdown_numbers( $args = array() ) {
		$args              = wp_parse_args(
			$args,
			array(
				'min'               => 0,
				'max'               => 100,
				'selected'          => 0,
				'name'              => '',
				'class'             => '',
				'echo'              => true,
				'show_option_none'  => '',
				'option_none_value' => '',
				'options'           => array(),
			)
		);
		$min               = 0;
		$max               = 100;
		$selected          = 0;
		$name              = '';
		$id                = '';
		$class             = '';
		$echo              = true;
		$show_option_none  = false;
		$option_none_value = '';

		extract( $args );

		$id     = ! empty( $id ) ? $id : '';
		$output = '<select name="' . $name . '" ' . ( $id ? 'id="' . $id . '"' : '' ) . '' . ( $class ? ' class="' . $class . '"' : '' ) . '>';
		if ( $show_option_none ) {
			$output .= '<option value="' . $option_none_value . '">' . $show_option_none . '</option>';
		}
		if ( empty( $options ) ) {

			for ( $i = $min; $i <= $max; $i ++ ) {
				$output .= sprintf( '<option value="%1$d"%2$s>%1$d</option>', $i, $selected == $i ? ' selected="selected"' : '' );
			}
		} else {
			foreach ( $options as $option ) {
				$output .= sprintf( '<option value="%1$d"%2$s>%3$d</option>', $option['value'], $selected == $option['value'] ? ' selected="selected"' : '', $option['text'] );
			}
		}

		$output .= '</select>';
		if ( $echo ) {
			WPHB_Helpers::print( sprintf( '%s', $output ) );
		}

		return $output;
	}
}

/**
 * @param $data
 */
if ( ! function_exists( 'hb_send_json' ) ) {

	function hb_send_json( $data ) {
		echo '<!-- HB_AJAX_START -->';
		@header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
		echo wp_json_encode( $data );
		echo '<!-- HB_AJAX_END -->';
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			wp_die();
		} else {
			die;
		}
	}
}

if ( ! function_exists( 'hb_is_ajax' ) ) {

	function hb_is_ajax() {
		return defined( 'DOING_AJAX' ) && DOING_AJAX;
	}
}

if ( ! function_exists( 'hb_customer_place_order' ) ) {
	/**
	 * Place order for a booking
	 *
	 * @throws Exception
	 */
	function hb_customer_place_order() {
		WPHB_Checkout::instance()->process_checkout();
		exit();
	}
}

// add_action( 'init', 'hb_customer_place_order' );

if ( ! function_exists( 'hb_get_currency' ) ) {

	function hb_get_currency() {
		$currencies     = hb_payment_currencies();
		$currency_codes = array_keys( $currencies );
		$currency       = reset( $currency_codes );

		return apply_filters( 'hb_currency', WPHB_Settings::instance()->get( 'currency', $currency ) );
	}
}

if ( ! function_exists( 'hb_get_currency_symbols' ) ) {
	function hb_get_currency_symbols() {
		$symbols = apply_filters(
			'wphb_currency_symbols',
			array(
				'AED' => '&#x62f;.&#x625;',
				'AFN' => '&#x60b;',
				'ALL' => 'L',
				'AMD' => 'AMD',
				'ANG' => '&fnof;',
				'AOA' => 'Kz',
				'ARS' => '&#36;',
				'AUD' => '&#36;',
				'AWG' => 'Afl.',
				'AZN' => 'AZN',
				'BAM' => 'KM',
				'BBD' => '&#36;',
				'BDT' => '&#2547;&nbsp;',
				'BGN' => '&#1083;&#1074;.',
				'BHD' => '.&#x62f;.&#x628;',
				'BIF' => 'Fr',
				'BMD' => '&#36;',
				'BND' => '&#36;',
				'BOB' => 'Bs.',
				'BRL' => '&#82;&#36;',
				'BSD' => '&#36;',
				'BTC' => '&#3647;',
				'BTN' => 'Nu.',
				'BWP' => 'P',
				'BYR' => 'Br',
				'BYN' => 'Br',
				'BZD' => '&#36;',
				'CAD' => '&#36;',
				'CDF' => 'Fr',
				'CHF' => '&#67;&#72;&#70;',
				'CLP' => '&#36;',
				'CNY' => '&yen;',
				'COP' => '&#36;',
				'CRC' => '&#x20a1;',
				'CUC' => '&#36;',
				'CUP' => '&#36;',
				'CVE' => '&#36;',
				'CZK' => '&#75;&#269;',
				'DJF' => 'Fr',
				'DKK' => 'kr.',
				'DOP' => 'RD&#36;',
				'DZD' => '&#x62f;.&#x62c;',
				'EGP' => 'EGP',
				'ERN' => 'Nfk',
				'ETB' => 'Br',
				'EUR' => '&euro;',
				'FJD' => '&#36;',
				'FKP' => '&pound;',
				'GBP' => '&pound;',
				'GEL' => '&#x20be;',
				'GGP' => '&pound;',
				'GHS' => '&#x20b5;',
				'GIP' => '&pound;',
				'GMD' => 'D',
				'GNF' => 'Fr',
				'GTQ' => 'Q',
				'GYD' => '&#36;',
				'HKD' => '&#36;',
				'HNL' => 'L',
				'HRK' => 'kn',
				'HTG' => 'G',
				'HUF' => '&#70;&#116;',
				'IDR' => 'Rp',
				'ILS' => '&#8362;',
				'IMP' => '&pound;',
				'INR' => '&#8377;',
				'IQD' => '&#x62f;.&#x639;',
				'IRR' => '&#xfdfc;',
				'IRT' => '&#x062A;&#x0648;&#x0645;&#x0627;&#x0646;',
				'ISK' => 'kr.',
				'JEP' => '&pound;',
				'JMD' => '&#36;',
				'JOD' => '&#x62f;.&#x627;',
				'JPY' => '&yen;',
				'KES' => 'KSh',
				'KGS' => '&#x441;&#x43e;&#x43c;',
				'KHR' => '&#x17db;',
				'KMF' => 'Fr',
				'KPW' => '&#x20a9;',
				'KRW' => '&#8361;',
				'KWD' => '&#x62f;.&#x643;',
				'KYD' => '&#36;',
				'KZT' => '&#8376;',
				'LAK' => '&#8365;',
				'LBP' => '&#x644;.&#x644;',
				'LKR' => '&#xdbb;&#xdd4;',
				'LRD' => '&#36;',
				'LSL' => 'L',
				'LYD' => '&#x644;.&#x62f;',
				'MAD' => '&#x62f;.&#x645;.',
				'MDL' => 'MDL',
				'MGA' => 'Ar',
				'MKD' => '&#x434;&#x435;&#x43d;',
				'MMK' => 'Ks',
				'MNT' => '&#x20ae;',
				'MOP' => 'P',
				'MRU' => 'UM',
				'MUR' => '&#x20a8;',
				'MVR' => '.&#x783;',
				'MWK' => 'MK',
				'MXN' => '&#36;',
				'MYR' => '&#82;&#77;',
				'MZN' => 'MT',
				'NAD' => 'N&#36;',
				'NGN' => '&#8358;',
				'NIO' => 'C&#36;',
				'NOK' => '&#107;&#114;',
				'NPR' => '&#8360;',
				'NZD' => '&#36;',
				'OMR' => '&#x631;.&#x639;.',
				'PAB' => 'B/.',
				'PEN' => 'S/',
				'PGK' => 'K',
				'PHP' => '&#8369;',
				'PKR' => '&#8360;',
				'PLN' => '&#122;&#322;',
				'PRB' => '&#x440;.',
				'PYG' => '&#8370;',
				'QAR' => '&#x631;.&#x642;',
				'RMB' => '&yen;',
				'RON' => 'lei',
				'RSD' => '&#1088;&#1089;&#1076;',
				'RUB' => '&#8381;',
				'RWF' => 'Fr',
				'SAR' => '&#x631;.&#x633;',
				'SBD' => '&#36;',
				'SCR' => '&#x20a8;',
				'SDG' => '&#x62c;.&#x633;.',
				'SEK' => '&#107;&#114;',
				'SGD' => '&#36;',
				'SHP' => '&pound;',
				'SLL' => 'Le',
				'SOS' => 'Sh',
				'SRD' => '&#36;',
				'SSP' => '&pound;',
				'STN' => 'Db',
				'SYP' => '&#x644;.&#x633;',
				'SZL' => 'E',
				'THB' => '&#3647;',
				'TJS' => '&#x405;&#x41c;',
				'TMT' => 'm',
				'TND' => '&#x62f;.&#x62a;',
				'TOP' => 'T&#36;',
				'TRY' => '&#8378;',
				'TTD' => '&#36;',
				'TWD' => '&#78;&#84;&#36;',
				'TZS' => 'Sh',
				'UAH' => '&#8372;',
				'UGX' => 'UGX',
				'USD' => '&#36;',
				'UYU' => '&#36;',
				'UZS' => 'UZS',
				'VEF' => 'Bs F',
				'VES' => 'Bs.S',
				'VND' => '&#8363;',
				'VUV' => 'Vt',
				'WST' => 'T',
				'XAF' => 'CFA',
				'XCD' => '&#36;',
				'XOF' => 'CFA',
				'XPF' => 'Fr',
				'YER' => '&#xfdfc;',
				'ZAR' => '&#82;',
				'ZMW' => 'ZK',
			)
		);
		return $symbols;
	}
}
if ( ! function_exists( 'hb_get_currency_symbol' ) ) {
	function hb_get_currency_symbol( $currency = '' ) {
		if ( ! $currency ) {
			$currency = hb_get_currency();
		}
		$symbols         = hb_get_currency_symbols();
		$currency_symbol = isset( $symbols[ $currency ] ) ? $symbols[ $currency ] : '';

		return apply_filters( 'hb_currency_symbol', $currency_symbol, $currency );
	}
}

if ( ! function_exists( 'hb_format_price' ) ) {
	function hb_format_price( $price, $with_currency = true ) {
		$settings                  = WPHB_Settings::instance();
		$position                  = $settings->get( 'price_currency_position' );
		$price_thousands_separator = $settings->get( 'price_thousands_separator' );
		$price_decimals_separator  = $settings->get( 'price_decimals_separator' );
		$price_number_of_decimal   = $settings->get( 'price_number_of_decimal' );
		if ( ! is_numeric( $price ) ) {
			$price = 0;
		}

		$price  = apply_filters( 'hotel_booking_price_switcher', $price );
		$before = $after = '';
		if ( $with_currency ) {
			if ( gettype( $with_currency ) != 'string' ) {
				$currency = hb_get_currency_symbol();
			} else {
				$currency = $with_currency;
			}

			switch ( $position ) {
				default:
					$before = $currency;
					break;
				case 'left_with_space':
					$before = $currency . ' ';
					break;
				case 'right':
					$after = $currency;
					break;
				case 'right_with_space':
					$after = ' ' . $currency;
			}
		}

		$price_format = $before
			. number_format(
				$price,
				$price_number_of_decimal,
				$price_decimals_separator,
				$price_thousands_separator
			) . $after;

		return apply_filters( 'hb_price_format', $price_format, $price, $with_currency );
	}
}

// if ( ! function_exists( 'hb_search_rooms' ) ) {
// function hb_search_rooms( $args = array() ) {
// global $wpdb;
// $adults_term = hb_get_request( 'adults', 0 );
// $adults      = $adults_term ? get_term_meta( $adults_term, 'hb_max_number_of_adults', true ) : hb_get_min_capacity_of_rooms();
// if ( ! $adults ) {
// $adults = $adults_term ? (int) get_option( 'hb_taxonomy_capacity_' . $adults_term ) : 0;
// }
// $max_child = hb_get_request( 'max_child', 0 );
//
// $args = wp_parse_args(
// $args, array(
// 'check_in_date'  => date( 'm/d/Y' ),
// 'check_out_date' => date( 'm/d/Y' ),
// 'adults'         => $adults,
// 'max_child'      => 0
// )
// );
//
// $check_in_time          = strtotime( $args['check_in_date'] );
// $check_out_time         = strtotime( $args['check_out_date'] );
// $check_in_date_to_time  = mktime( 0, 0, 0, date( 'm', $check_in_time ), date( 'd', $check_in_time ), date( 'Y', $check_in_time ) );
// $check_out_date_to_time = mktime( 0, 0, 0, date( 'm', $check_out_time ), date( 'd', $check_out_time ), date( 'Y', $check_out_time ) );
//
// $results = array();
//
// $rooms = $wpdb->get_results( $wpdb->prepare(
// "SELECT ID FROM {$wpdb->posts} WHERE `post_type` = %s AND `post_status` = %s", 'hb_room', 'publish'
// ), OBJECT );
//
// if ( ! $rooms ) {
// return array();
// }
//
// $data = array();
// foreach ( $rooms as $room ) {
// $room_id = $room->ID;
//
// $not = $wpdb->get_results( $wpdb->prepare( "
// (
// SELECT booking.ID as booking_id, checkin.meta_value as checkin, checkout.meta_value as checkout, meta.meta_value as qty FROM {$wpdb->hotel_booking_order_itemmeta} AS meta
// LEFT JOIN {$wpdb->hotel_booking_order_items} AS order_item ON order_item.order_item_id = meta.hotel_booking_order_item_id AND meta.meta_key = %s
// LEFT JOIN {$wpdb->hotel_booking_order_itemmeta} AS itemmeta ON order_item.order_item_id = itemmeta.hotel_booking_order_item_id AND itemmeta.meta_key = %s
// LEFT JOIN {$wpdb->hotel_booking_order_itemmeta} AS checkin ON order_item.order_item_id = checkin.hotel_booking_order_item_id AND checkin.meta_key = %s
// LEFT JOIN {$wpdb->hotel_booking_order_itemmeta} AS checkout ON order_item.order_item_id = checkout.hotel_booking_order_item_id AND checkout.meta_key = %s
// LEFT JOIN {$wpdb->posts} AS booking ON booking.ID = order_item.order_id
// WHERE
// itemmeta.meta_value = %d
// AND (
// ( checkin.meta_value >= %d AND checkin.meta_value < %d )
// OR  ( checkout.meta_value > %d AND checkout.meta_value <= %d )
// OR  ( checkin.meta_value <= %d AND checkout.meta_value > %d )
// )
// AND booking.post_type = %s
// AND booking.post_status IN ( %s, %s, %s )
// ORDER BY checkin
// )
// ", 'qty', 'product_id', 'check_in_date', 'check_out_date', $room_id, $check_in_date_to_time, $check_out_date_to_time, $check_in_date_to_time, $check_out_date_to_time, $check_in_date_to_time, $check_out_date_to_time, 'hb_booking', 'hb-completed', 'hb-processing', 'hb-pending'
// ), ARRAY_A );
//
// $booked = 0;
// if ( $not ) {
// $range = array();
// foreach ( $not as $key => $booking ) {
// if ( ! $key ) {
// $range = array(
// 'checkin'  => $booking['checkin'],
// 'checkout' => $booking['checkout'],
// 'qty'      => $booking['qty']
// );
// } else {
// if ( $range['checkin'] < $booking['checkin'] && $range['checkout'] < $booking['checkout'] ) {
// $range['qty'] = max( $range['qty'], $booking['qty'] );
// } else {
// $range['qty'] += $booking['qty'];
// }
// }
// }
//
// $booked = $range['qty'];
// }
//
// $data[] = array( 'room_id' => $room_id, 'booking_qty' => $booked );
// }
//
// $query = array();
// foreach ( $data as $_data ) {
// $query[] = $wpdb->get_results(
// $wpdb->prepare( "
// SELECT rooms.*, ( number.meta_value - {$_data['booking_qty']} ) AS available_rooms FROM $wpdb->posts AS rooms
// LEFT JOIN {$wpdb->postmeta} AS number ON rooms.ID = number.post_id AND number.meta_key = %s
// LEFT JOIN {$wpdb->postmeta} AS pm1 ON pm1.post_id = rooms.ID AND pm1.meta_key = %s
// LEFT JOIN {$wpdb->termmeta} AS term_cap ON term_cap.term_id = pm1.meta_value AND term_cap.meta_key = %s
// LEFT JOIN {$wpdb->postmeta} AS pm2 ON pm2.post_id = rooms.ID AND pm2.meta_key = %s
// WHERE
// rooms.ID = %s
// AND rooms.post_status = %s
// AND term_cap.meta_value >= %d
// AND pm2.meta_value >= %d
// GROUP BY rooms.post_name
// HAVING available_rooms > 0
// ORDER BY term_cap.meta_value ASC
// ", '_hb_num_of_rooms', '_hb_room_capacity', 'hb_max_number_of_adults', '_hb_max_child_per_room', $_data['room_id'], 'publish', $adults, $max_child )
// );
// }
//
// $query = apply_filters( 'hb_search_query', $query, array(
// 'check_in'  => $check_in_date_to_time,
// 'check_out' => $check_out_date_to_time,
// 'adults'    => $adults,
// 'child'     => $max_child
// ) );
//
// if ( $query ) {
// if ( is_array( $query ) ) {
// foreach ( $query as $k => $p ) {
// if ( $p[0] ) {
// $room                        = WPHB_Room::instance( $p[0], array(
// 'check_in_date'  => date( 'm/d/Y', $check_in_date_to_time ),
// 'check_out_date' => date( 'm/d/Y', $check_out_date_to_time ),
// 'quantity'       => 1
// ) );
// $room->post->available_rooms = (int) $p[0]->available_rooms;
//
// $room = apply_filters( 'hotel_booking_query_search_parser', $room );
//
// if ( $room && $room->post->available_rooms > 0 ) {
// $results[ $k ] = $room;
// }
// }
// }
// }
// }
//
// if ( WP_Hotel_Booking::instance()->cart->cart_contents && $query ) {
// $selected_id = array();
// foreach ( WP_Hotel_Booking::instance()->cart->cart_contents as $k => $cart ) {
// $selected_id[ $cart->product_id ] = $cart->quantity;
// }
//
// foreach ( $results as $k => $room ) {
// if ( array_key_exists( $room->post->ID, $selected_id ) ) {
// $in  = $room->get_data( 'check_in_date' );
// $out = $room->get_data( 'check_out_date' );
// if (
// ( $in < $check_in_date_to_time && $check_out_date_to_time < $out ) || ( $in < $check_in_date_to_time && $check_out_date_to_time < $out )
// ) {
// $total                                = $query[ $k ]->available_rooms;
// $results[ $k ]->post->available_rooms = (int) $total - (int) $selected_id[ $room->post->ID ];
// }
// }
// }
// }
//
// $results = apply_filters( 'hb_search_available_rooms', $results, array(
// 'check_in'  => $check_in_date_to_time,
// 'check_out' => $check_out_date_to_time,
// 'adults'    => $adults,
// 'child'     => $max_child
// ) );
// global $hb_settings;
// $total          = count( $results );
// $posts_per_page = (int) apply_filters( 'hb_number_search_rooms_per_page', $hb_settings->get( 'posts_per_page', 8 ) );
// $page           = isset( $_GET['hb_page'] ) ? absint( $_GET['hb_page'] ) : 1;
// $offset         = ( $page * $posts_per_page ) - $posts_per_page;
// $max_num_pages  = ceil( $total / $posts_per_page );
//
// $data = array_slice( $results, $offset, $posts_per_page );
//
// $GLOBALS['hb_search_rooms'] = array(
// 'max_num_pages'  => $max_num_pages,
// 'data'           => $max_num_pages > 1 ? array_slice( $results, $offset, $posts_per_page ) : $results,
// 'total'          => $total,
// 'posts_per_page' => $posts_per_page,
// 'offset'         => $offset,
// 'page'           => $page,
// );
//
// return apply_filters( 'hb_search_results', $GLOBALS['hb_search_rooms'], $args );
// }
// }

if ( ! function_exists( 'hb_search_rooms' ) ) {
	function hb_search_rooms( $args = array() ) {
		global $wpdb;
		$adults_term = hb_get_request( 'adults', 0 );
		$adults      = $adults_term ? get_term_meta( $adults_term, 'hb_max_number_of_adults', true ) : hb_get_min_capacity_of_rooms();
		if ( ! $adults ) {
			$adults = $adults_term ? (int) get_option( 'hb_taxonomy_capacity_' . $adults_term ) : 0;
		}
		$max_child = hb_get_request( 'max_child', 0 );

		$args   = wp_parse_args(
			$args,
			array(
				'check_in_date'  => date( 'm/d/Y' ),
				'check_out_date' => date( 'm/d/Y' ),
				'adults'         => $adults,
				'max_child'      => 0,
			)
		);
		$adults = $args['adults'];

		$check_in_time          = strtotime( $args['check_in_date'] );
		$check_out_time         = strtotime( $args['check_out_date'] );
		$check_in_date_to_time  = mktime( 0, 0, 0, date( 'm', $check_in_time ), date( 'd', $check_in_time ), date( 'Y', $check_in_time ) );
		$check_out_date_to_time = mktime( 0, 0, 0, date( 'm', $check_out_time ), date( 'd', $check_out_time ), date( 'Y', $check_out_time ) );

		$results = array();

		$not = $wpdb->prepare(
			"
			(
				SELECT COALESCE( SUM( meta.meta_value ), 0 ) FROM {$wpdb->hotel_booking_order_itemmeta} AS meta
					LEFT JOIN {$wpdb->hotel_booking_order_items} AS order_item ON order_item.order_item_id = meta.hotel_booking_order_item_id AND meta.meta_key = %s
					LEFT JOIN {$wpdb->hotel_booking_order_itemmeta} AS itemmeta ON order_item.order_item_id = itemmeta.hotel_booking_order_item_id AND itemmeta.meta_key = %s
					LEFT JOIN {$wpdb->hotel_booking_order_itemmeta} AS checkin ON order_item.order_item_id = checkin.hotel_booking_order_item_id AND checkin.meta_key = %s
					LEFT JOIN {$wpdb->hotel_booking_order_itemmeta} AS checkout ON order_item.order_item_id = checkout.hotel_booking_order_item_id AND checkout.meta_key = %s
					LEFT JOIN {$wpdb->posts} AS booking ON booking.ID = order_item.order_id
				WHERE
						itemmeta.meta_value = rooms.ID
					AND (
							( checkin.meta_value >= %d AND checkin.meta_value < %d )
						OR 	( checkout.meta_value > %d AND checkout.meta_value < %d )
						OR 	( checkin.meta_value <= %d AND checkout.meta_value > %d )
					)
					AND booking.post_type = %s
					AND booking.post_status IN ( %s, %s, %s )
			)
		",
			'qty',
			'product_id',
			'check_in_date',
			'check_out_date',
			$check_in_date_to_time,
			$check_out_date_to_time,
			$check_in_date_to_time,
			$check_out_date_to_time,
			$check_in_date_to_time,
			$check_out_date_to_time,
			'hb_booking',
			'hb-completed',
			'hb-processing',
			'hb-pending'
		);

		$query = $wpdb->prepare(
			"
			SELECT rooms.*, ( number.meta_value - {$not} ) AS available_rooms FROM $wpdb->posts AS rooms
                                LEFT JOIN {$wpdb->postmeta} AS number ON rooms.ID = number.post_id AND number.meta_key = %s
				LEFT JOIN {$wpdb->postmeta} AS pm2 ON pm2.post_id = rooms.ID AND pm2.meta_key = %s
				LEFT JOIN {$wpdb->postmeta} AS pm3 ON pm3.post_id = rooms.ID AND pm3.meta_key = %s
			WHERE
				rooms.post_type = %s
				AND rooms.post_status = %s
				AND pm2.meta_value >= %d
				AND pm3.meta_value >= %d
			GROUP BY rooms.post_name
			HAVING available_rooms > 0
			ORDER BY rooms.post_title ASC
		",
			'_hb_num_of_rooms',
			'_hb_max_child_per_room',
			'_hb_room_capacity_adult',
			'hb_room',
			'publish',
			$max_child,
			$adults
		);

		$query = apply_filters(
			'hb_search_query',
			$query,
			array(
				'check_in'  => $check_in_date_to_time,
				'check_out' => $check_out_date_to_time,
				'adults'    => $adults,
				'child'     => $max_child,
			)
		);

		if ( $search = $wpdb->get_results( $query ) ) {
			foreach ( $search as $k => $p ) {
				$blocked_id = get_post_meta( $p->ID, 'hb_blocked_id', true );
				if ( ! empty( $blocked_id ) ) {
					$flag = false;
					$date_blocked = get_post_meta( $blocked_id, 'hb_blocked_time', false );
					if ( ! empty( $date_blocked ) ) {
						foreach ( $date_blocked as $date ) {
							if ( $date >= $check_in_date_to_time && $date <= $check_out_date_to_time ){
								$flag = true;
								break;
							}
						}
					}
					if ( $flag ) {
						unset( $search[$k] );
						continue;
					}
				}

				$room = WPHB_Room::instance(
					$p,
					array(
						'check_in_date'  => date( 'm/d/Y', $check_in_date_to_time ),
						'check_out_date' => date( 'm/d/Y', $check_out_date_to_time ),
						'quantity'       => 1,
					)
				);
				$room->post->available_rooms = (int) $p->available_rooms;

				$room = apply_filters( 'hotel_booking_query_search_parser', $room, $args );

				if ( $room && $room->post->available_rooms > 0 ) {
					$results[ $k ] = $room;
				}
			}
		}

		if ( WP_Hotel_Booking::instance()->cart->cart_contents && $search ) {
			$selected_id = array();
			foreach ( WP_Hotel_Booking::instance()->cart->cart_contents as $k => $cart ) {
				$selected_id[ $cart->product_id ] = $cart->quantity;
			}

			foreach ( $results as $k => $room ) {
				if ( array_key_exists( $room->post->ID, $selected_id ) ) {
					$in  = $room->get_data( 'check_in_date' );
					$out = $room->get_data( 'check_out_date' );
					if (
						( $in < $check_in_date_to_time && $check_out_date_to_time < $out ) || ( $in < $check_in_date_to_time && $check_out_date_to_time < $out )
					) {
						$total                                = $search[ $k ]->available_rooms;
						$results[ $k ]->post->available_rooms = (int) $total - (int) $selected_id[ $room->post->ID ];
					}
				}
			}
		}

		$results = apply_filters(
			'hb_search_available_rooms',
			$results,
			array(
				'check_in'  => $check_in_date_to_time,
				'check_out' => $check_out_date_to_time,
				'adults'    => $adults,
				'child'     => $max_child,
			)
		);
		global $hb_settings;
		$total          = count( $results );
		$posts_per_page = (int) apply_filters( 'hb_number_search_rooms_per_page', $hb_settings->get( 'posts_per_page', 8 ) );
		$page           = ! empty( $args['hb_page'] ) ? absint( $args['hb_page'] ) : 1;
		$offset         = ( $page * $posts_per_page ) - $posts_per_page;
		$max_num_pages  = ceil( $total / $posts_per_page );

		$data = array_slice( $results, $offset, $posts_per_page );

		$GLOBALS['hb_search_rooms'] = array(
			'max_num_pages'  => $max_num_pages,
			'data'           => $max_num_pages > 1 ? array_slice( $results, $offset, $posts_per_page ) : $results,
			'total'          => $total,
			'posts_per_page' => $posts_per_page,
			'offset'         => $offset,
			'page'           => $page,
		);

		return apply_filters( 'hb_search_results', $GLOBALS['hb_search_rooms'], $args );
	}
}

if ( ! function_exists( 'hb_get_payment_gateways' ) ) {
	function hb_get_payment_gateways( $args = array() ) {
		static $payment_gateways = array();
		if ( ! $payment_gateways ) {
			$defaults         = array(
				'offline-payment' => new WPHB_Payment_Gateway_Offline_Payment(),
				'paypal'          => new WPHB_Payment_Gateway_Paypal(),
			);
			$payment_gateways = apply_filters( 'hb_payment_gateways', $defaults );
		}

		$args = wp_parse_args(
			$args,
			array(
				'enable' => false,
			)
		);

		if ( $args['enable'] ) {
			$gateways = array();
			foreach ( $payment_gateways as $k => $gateway ) {
				$is_enable = is_callable( array( $gateway, 'is_enable' ) ) && $gateway->is_enable();
				if ( apply_filters( 'hb_payment_gateway_enable', $is_enable, $gateway ) ) {
					$gateways[ $k ] = $gateway;
				}
			}
		} else {
			$gateways = $payment_gateways;
		}

		return $gateways;
	}
}

if ( ! function_exists( 'hb_get_user_payment_method' ) ) {
	function hb_get_user_payment_method( $slug ) {
		$methods = hb_get_payment_gateways( array( 'enable' => true ) );
		$method  = false;
		if ( $methods && ! empty( $methods[ $slug ] ) ) {
			$method = $methods[ $slug ];
		}

		return $method;
	}
}

if ( ! function_exists( 'hb_get_page_id' ) ) {
	function hb_get_page_id( $name ) {
		$settings = hb_settings();

		return apply_filters( 'hb_get_page_id', $settings->get( "{$name}_page_id" ) );
	}
}

if ( ! function_exists( 'hb_get_page_permalink' ) ) {
	function hb_get_page_permalink( $name ) {
		return get_the_permalink( hb_get_page_id( $name ) );
	}
}

if ( ! function_exists( 'hb_get_endpoint_url' ) ) {
	function hb_get_endpoint_url( $endpoint, $value = '', $permalink = '' ) {
		if ( ! $permalink ) {
			$permalink = get_permalink();
		}

		if ( get_option( 'permalink_structure' ) ) {
			if ( strstr( $permalink, '?' ) ) {
				$query_string = '?' . parse_url( $permalink, PHP_URL_QUERY );
				$permalink    = current( explode( '?', $permalink ) );
			} else {
				$query_string = '';
			}
			$url = trailingslashit( $permalink ) . $endpoint . '/' . $value . $query_string;
		} else {
			$url = add_query_arg( $endpoint, $value, $permalink );
		}

		return apply_filters( 'hb_get_endpoint_url(', $url, $endpoint, $value, $permalink );
	}
}

if ( ! function_exists( 'hb_get_advance_payment' ) ) {
	function hb_get_advance_payment() {
		$advance_payment = WPHB_Settings::instance()->get( 'advance_payment' );

		return apply_filters( 'hb_advance_payment', $advance_payment );
	}
}

if ( ! function_exists( 'hb_do_transaction' ) ) {
	function hb_do_transaction( $method, $transaction = false ) {
		do_action( 'hb_do_transaction_' . $method, $transaction );
	}
}

/**
 * Process purchase request
 */
if ( ! function_exists( 'hb_handle_purchase_request' ) ) {
	function hb_handle_purchase_request() {
		$method_var   = 'hb-transaction-method';
		$cart_content = WP_Hotel_Booking::instance()->cart->cart_contents;
		if ( ! empty( $_REQUEST[ $method_var ] ) ) {
			hb_get_payment_gateways();
			$requested_transaction_method = sanitize_text_field( wp_unslash( $_REQUEST[ $method_var ] ) );
			hb_do_transaction( $requested_transaction_method );
		} elseif ( hb_get_page_id( 'checkout' ) && is_page( hb_get_page_id( 'checkout' ) ) && empty( $cart_content ) ) {
			wp_redirect( hb_get_cart_url() );
			exit();
		} elseif ( hb_get_page_id( 'thankyou' ) && is_page( hb_get_page_id( 'thankyou' ) ) && hb_get_thank_you_url() ) {
			wp_redirect( hb_get_cart_url() );
			exit();
		}
	}
}

if ( ! function_exists( 'hb_get_bookings' ) ) {
	function hb_get_bookings( $args = array() ) {
		$defaults = array(
			'post_type' => 'hb_booking',
		);
		$args     = wp_parse_args( $args, $defaults );
		$bookings = get_posts( $args );

		return apply_filters( 'hb_get_bookings', $bookings, $args );
	}
}

/**
 *
 */
if ( ! function_exists( 'hb_maybe_modify_page_content' ) ) {
	function hb_maybe_modify_page_content( $content ) {
		global $post;
		if ( is_page() && ( $post->ID == hb_get_page_id( 'search' ) || has_shortcode( $content, 'hotel_booking' ) ) ) {

			// params search result
			$page       = hb_get_request( 'hotel-booking' );
			$start_date = hb_get_request( 'check_in_date' );
			$end_date   = hb_get_request( 'check_out_date' );
			$adults     = hb_get_request( 'adults' );
			$max_child  = hb_get_request( 'max_child' );

			$content = '[hotel_booking page="' . $page . '" check_in_date="' . $start_date . '" check_in_date="' . $end_date . '" adults="' . $adults . '" max_child="' . $max_child . '"]';
		}

		return $content;
	}
}

add_filter( 'the_content', 'hb_maybe_modify_page_content' );

/**
 * Init some task when wp init
 */
if ( ! function_exists( 'hb_init' ) ) {
	function hb_init() {
		hb_get_payment_gateways();
	}
}

add_action( 'init', 'hb_init' );

if ( ! function_exists( 'hb_format_order_number' ) ) {
	function hb_format_order_number( $order_number ) {
		return '#' . sprintf( '%d', $order_number );
	}
}

if ( ! function_exists( 'hb_get_support_lightboxs' ) ) {
	function hb_get_support_lightboxs() {
		$lightboxs = array(
			'lightbox2' => 'Lightbox 2',
		);

		return apply_filters( 'hb_lightboxs', $lightboxs );
	}
}

if ( ! function_exists( 'hb_get_countries' ) ) {
	function hb_get_countries() {
		$countries = array(
			'AF' => __( 'Afghanistan', 'wp-hotel-booking' ),
			'AX' => __( '&#197;land Islands', 'wp-hotel-booking' ),
			'AL' => __( 'Albania', 'wp-hotel-booking' ),
			'DZ' => __( 'Algeria', 'wp-hotel-booking' ),
			'AD' => __( 'Andorra', 'wp-hotel-booking' ),
			'AO' => __( 'Angola', 'wp-hotel-booking' ),
			'AI' => __( 'Anguilla', 'wp-hotel-booking' ),
			'AQ' => __( 'Antarctica', 'wp-hotel-booking' ),
			'AG' => __( 'Antigua and Barbuda', 'wp-hotel-booking' ),
			'AR' => __( 'Argentina', 'wp-hotel-booking' ),
			'AM' => __( 'Armenia', 'wp-hotel-booking' ),
			'AW' => __( 'Aruba', 'wp-hotel-booking' ),
			'AU' => __( 'Australia', 'wp-hotel-booking' ),
			'AT' => __( 'Austria', 'wp-hotel-booking' ),
			'AZ' => __( 'Azerbaijan', 'wp-hotel-booking' ),
			'BS' => __( 'Bahamas', 'wp-hotel-booking' ),
			'BH' => __( 'Bahrain', 'wp-hotel-booking' ),
			'BD' => __( 'Bangladesh', 'wp-hotel-booking' ),
			'BB' => __( 'Barbados', 'wp-hotel-booking' ),
			'BY' => __( 'Belarus', 'wp-hotel-booking' ),
			'BE' => __( 'Belgium', 'wp-hotel-booking' ),
			'PW' => __( 'Belau', 'wp-hotel-booking' ),
			'BZ' => __( 'Belize', 'wp-hotel-booking' ),
			'BJ' => __( 'Benin', 'wp-hotel-booking' ),
			'BM' => __( 'Bermuda', 'wp-hotel-booking' ),
			'BT' => __( 'Bhutan', 'wp-hotel-booking' ),
			'BO' => __( 'Bolivia', 'wp-hotel-booking' ),
			'BQ' => __( 'Bonaire, Saint Eustatius and Saba', 'wp-hotel-booking' ),
			'BA' => __( 'Bosnia and Herzegovina', 'wp-hotel-booking' ),
			'BW' => __( 'Botswana', 'wp-hotel-booking' ),
			'BV' => __( 'Bouvet Island', 'wp-hotel-booking' ),
			'BR' => __( 'Brazil', 'wp-hotel-booking' ),
			'IO' => __( 'British Indian Ocean Territory', 'wp-hotel-booking' ),
			'VG' => __( 'British Virgin Islands', 'wp-hotel-booking' ),
			'BN' => __( 'Brunei', 'wp-hotel-booking' ),
			'BG' => __( 'Bulgaria', 'wp-hotel-booking' ),
			'BF' => __( 'Burkina Faso', 'wp-hotel-booking' ),
			'BI' => __( 'Burundi', 'wp-hotel-booking' ),
			'KH' => __( 'Cambodia', 'wp-hotel-booking' ),
			'CM' => __( 'Cameroon', 'wp-hotel-booking' ),
			'CA' => __( 'Canada', 'wp-hotel-booking' ),
			'CV' => __( 'Cape Verde', 'wp-hotel-booking' ),
			'KY' => __( 'Cayman Islands', 'wp-hotel-booking' ),
			'CF' => __( 'Central African Republic', 'wp-hotel-booking' ),
			'TD' => __( 'Chad', 'wp-hotel-booking' ),
			'CL' => __( 'Chile', 'wp-hotel-booking' ),
			'CN' => __( 'China', 'wp-hotel-booking' ),
			'CX' => __( 'Christmas Island', 'wp-hotel-booking' ),
			'CC' => __( 'Cocos (Keeling) Islands', 'wp-hotel-booking' ),
			'CO' => __( 'Colombia', 'wp-hotel-booking' ),
			'KM' => __( 'Comoros', 'wp-hotel-booking' ),
			'CG' => __( 'Congo (Brazzaville)', 'wp-hotel-booking' ),
			'CD' => __( 'Congo (Kinshasa)', 'wp-hotel-booking' ),
			'CK' => __( 'Cook Islands', 'wp-hotel-booking' ),
			'CR' => __( 'Costa Rica', 'wp-hotel-booking' ),
			'HR' => __( 'Croatia', 'wp-hotel-booking' ),
			'CU' => __( 'Cuba', 'wp-hotel-booking' ),
			'CW' => __( 'Cura&Ccedil;ao', 'wp-hotel-booking' ),
			'CY' => __( 'Cyprus', 'wp-hotel-booking' ),
			'CZ' => __( 'Czech Republic', 'wp-hotel-booking' ),
			'DK' => __( 'Denmark', 'wp-hotel-booking' ),
			'DJ' => __( 'Djibouti', 'wp-hotel-booking' ),
			'DM' => __( 'Dominica', 'wp-hotel-booking' ),
			'DO' => __( 'Dominican Republic', 'wp-hotel-booking' ),
			'EC' => __( 'Ecuador', 'wp-hotel-booking' ),
			'EG' => __( 'Egypt', 'wp-hotel-booking' ),
			'SV' => __( 'El Salvador', 'wp-hotel-booking' ),
			'GQ' => __( 'Equatorial Guinea', 'wp-hotel-booking' ),
			'ER' => __( 'Eritrea', 'wp-hotel-booking' ),
			'EE' => __( 'Estonia', 'wp-hotel-booking' ),
			'ET' => __( 'Ethiopia', 'wp-hotel-booking' ),
			'FK' => __( 'Falkland Islands', 'wp-hotel-booking' ),
			'FO' => __( 'Faroe Islands', 'wp-hotel-booking' ),
			'FJ' => __( 'Fiji', 'wp-hotel-booking' ),
			'FI' => __( 'Finland', 'wp-hotel-booking' ),
			'FR' => __( 'France', 'wp-hotel-booking' ),
			'GF' => __( 'French Guiana', 'wp-hotel-booking' ),
			'PF' => __( 'French Polynesia', 'wp-hotel-booking' ),
			'TF' => __( 'French Southern Territories', 'wp-hotel-booking' ),
			'GA' => __( 'Gabon', 'wp-hotel-booking' ),
			'GM' => __( 'Gambia', 'wp-hotel-booking' ),
			'GE' => __( 'Georgia', 'wp-hotel-booking' ),
			'DE' => __( 'Germany', 'wp-hotel-booking' ),
			'GH' => __( 'Ghana', 'wp-hotel-booking' ),
			'GI' => __( 'Gibraltar', 'wp-hotel-booking' ),
			'GR' => __( 'Greece', 'wp-hotel-booking' ),
			'GL' => __( 'Greenland', 'wp-hotel-booking' ),
			'GD' => __( 'Grenada', 'wp-hotel-booking' ),
			'GP' => __( 'Guadeloupe', 'wp-hotel-booking' ),
			'GT' => __( 'Guatemala', 'wp-hotel-booking' ),
			'GG' => __( 'Guernsey', 'wp-hotel-booking' ),
			'GN' => __( 'Guinea', 'wp-hotel-booking' ),
			'GW' => __( 'Guinea-Bissau', 'wp-hotel-booking' ),
			'GY' => __( 'Guyana', 'wp-hotel-booking' ),
			'HT' => __( 'Haiti', 'wp-hotel-booking' ),
			'HM' => __( 'Heard Island and McDonald Islands', 'wp-hotel-booking' ),
			'HN' => __( 'Honduras', 'wp-hotel-booking' ),
			'HK' => __( 'Hong Kong', 'wp-hotel-booking' ),
			'HU' => __( 'Hungary', 'wp-hotel-booking' ),
			'IS' => __( 'Iceland', 'wp-hotel-booking' ),
			'IN' => __( 'India', 'wp-hotel-booking' ),
			'ID' => __( 'Indonesia', 'wp-hotel-booking' ),
			'IR' => __( 'Iran', 'wp-hotel-booking' ),
			'IQ' => __( 'Iraq', 'wp-hotel-booking' ),
			'IE' => __( 'Republic of Ireland', 'wp-hotel-booking' ),
			'IM' => __( 'Isle of Man', 'wp-hotel-booking' ),
			'IL' => __( 'Israel', 'wp-hotel-booking' ),
			'IT' => __( 'Italy', 'wp-hotel-booking' ),
			'CI' => __( 'Ivory Coast', 'wp-hotel-booking' ),
			'JM' => __( 'Jamaica', 'wp-hotel-booking' ),
			'JP' => __( 'Japan', 'wp-hotel-booking' ),
			'JE' => __( 'Jersey', 'wp-hotel-booking' ),
			'JO' => __( 'Jordan', 'wp-hotel-booking' ),
			'KZ' => __( 'Kazakhstan', 'wp-hotel-booking' ),
			'KE' => __( 'Kenya', 'wp-hotel-booking' ),
			'KI' => __( 'Kiribati', 'wp-hotel-booking' ),
			'KW' => __( 'Kuwait', 'wp-hotel-booking' ),
			'KG' => __( 'Kyrgyzstan', 'wp-hotel-booking' ),
			'LA' => __( 'Laos', 'wp-hotel-booking' ),
			'LV' => __( 'Latvia', 'wp-hotel-booking' ),
			'LB' => __( 'Lebanon', 'wp-hotel-booking' ),
			'LS' => __( 'Lesotho', 'wp-hotel-booking' ),
			'LR' => __( 'Liberia', 'wp-hotel-booking' ),
			'LY' => __( 'Libya', 'wp-hotel-booking' ),
			'LI' => __( 'Liechtenstein', 'wp-hotel-booking' ),
			'LT' => __( 'Lithuania', 'wp-hotel-booking' ),
			'LU' => __( 'Luxembourg', 'wp-hotel-booking' ),
			'MO' => __( 'Macao S.A.R., China', 'wp-hotel-booking' ),
			'MK' => __( 'Macedonia', 'wp-hotel-booking' ),
			'MG' => __( 'Madagascar', 'wp-hotel-booking' ),
			'MW' => __( 'Malawi', 'wp-hotel-booking' ),
			'MY' => __( 'Malaysia', 'wp-hotel-booking' ),
			'MV' => __( 'Maldives', 'wp-hotel-booking' ),
			'ML' => __( 'Mali', 'wp-hotel-booking' ),
			'MT' => __( 'Malta', 'wp-hotel-booking' ),
			'MH' => __( 'Marshall Islands', 'wp-hotel-booking' ),
			'MQ' => __( 'Martinique', 'wp-hotel-booking' ),
			'MR' => __( 'Mauritania', 'wp-hotel-booking' ),
			'MU' => __( 'Mauritius', 'wp-hotel-booking' ),
			'YT' => __( 'Mayotte', 'wp-hotel-booking' ),
			'MX' => __( 'Mexico', 'wp-hotel-booking' ),
			'FM' => __( 'Micronesia', 'wp-hotel-booking' ),
			'MD' => __( 'Moldova', 'wp-hotel-booking' ),
			'MC' => __( 'Monaco', 'wp-hotel-booking' ),
			'MN' => __( 'Mongolia', 'wp-hotel-booking' ),
			'ME' => __( 'Montenegro', 'wp-hotel-booking' ),
			'MS' => __( 'Montserrat', 'wp-hotel-booking' ),
			'MA' => __( 'Morocco', 'wp-hotel-booking' ),
			'MZ' => __( 'Mozambique', 'wp-hotel-booking' ),
			'MM' => __( 'Myanmar', 'wp-hotel-booking' ),
			'NA' => __( 'Namibia', 'wp-hotel-booking' ),
			'NR' => __( 'Nauru', 'wp-hotel-booking' ),
			'NP' => __( 'Nepal', 'wp-hotel-booking' ),
			'NL' => __( 'Netherlands', 'wp-hotel-booking' ),
			'AN' => __( 'Netherlands Antilles', 'wp-hotel-booking' ),
			'NC' => __( 'New Caledonia', 'wp-hotel-booking' ),
			'NZ' => __( 'New Zealand', 'wp-hotel-booking' ),
			'NI' => __( 'Nicaragua', 'wp-hotel-booking' ),
			'NE' => __( 'Niger', 'wp-hotel-booking' ),
			'NG' => __( 'Nigeria', 'wp-hotel-booking' ),
			'NU' => __( 'Niue', 'wp-hotel-booking' ),
			'NF' => __( 'Norfolk Island', 'wp-hotel-booking' ),
			'KP' => __( 'North Korea', 'wp-hotel-booking' ),
			'NO' => __( 'Norway', 'wp-hotel-booking' ),
			'OM' => __( 'Oman', 'wp-hotel-booking' ),
			'PK' => __( 'Pakistan', 'wp-hotel-booking' ),
			'PS' => __( 'Palestinian Territory', 'wp-hotel-booking' ),
			'PA' => __( 'Panama', 'wp-hotel-booking' ),
			'PG' => __( 'Papua New Guinea', 'wp-hotel-booking' ),
			'PY' => __( 'Paraguay', 'wp-hotel-booking' ),
			'PE' => __( 'Peru', 'wp-hotel-booking' ),
			'PH' => __( 'Philippines', 'wp-hotel-booking' ),
			'PN' => __( 'Pitcairn', 'wp-hotel-booking' ),
			'PL' => __( 'Poland', 'wp-hotel-booking' ),
			'PT' => __( 'Portugal', 'wp-hotel-booking' ),
			'QA' => __( 'Qatar', 'wp-hotel-booking' ),
			'RE' => __( 'Reunion', 'wp-hotel-booking' ),
			'RO' => __( 'Romania', 'wp-hotel-booking' ),
			'RU' => __( 'Russia', 'wp-hotel-booking' ),
			'RW' => __( 'Rwanda', 'wp-hotel-booking' ),
			'BL' => __( 'Saint Barth&eacute;lemy', 'wp-hotel-booking' ),
			'SH' => __( 'Saint Helena', 'wp-hotel-booking' ),
			'KN' => __( 'Saint Kitts and Nevis', 'wp-hotel-booking' ),
			'LC' => __( 'Saint Lucia', 'wp-hotel-booking' ),
			'MF' => __( 'Saint Martin (French part)', 'wp-hotel-booking' ),
			'SX' => __( 'Saint Martin (Dutch part)', 'wp-hotel-booking' ),
			'PM' => __( 'Saint Pierre and Miquelon', 'wp-hotel-booking' ),
			'VC' => __( 'Saint Vincent and the Grenadines', 'wp-hotel-booking' ),
			'SM' => __( 'San Marino', 'wp-hotel-booking' ),
			'ST' => __( 'S&atilde;o Tom&eacute; and Pr&iacute;ncipe', 'wp-hotel-booking' ),
			'SA' => __( 'Saudi Arabia', 'wp-hotel-booking' ),
			'SN' => __( 'Senegal', 'wp-hotel-booking' ),
			'RS' => __( 'Serbia', 'wp-hotel-booking' ),
			'SC' => __( 'Seychelles', 'wp-hotel-booking' ),
			'SL' => __( 'Sierra Leone', 'wp-hotel-booking' ),
			'SG' => __( 'Singapore', 'wp-hotel-booking' ),
			'SK' => __( 'Slovakia', 'wp-hotel-booking' ),
			'SI' => __( 'Slovenia', 'wp-hotel-booking' ),
			'SB' => __( 'Solomon Islands', 'wp-hotel-booking' ),
			'SO' => __( 'Somalia', 'wp-hotel-booking' ),
			'ZA' => __( 'South Africa', 'wp-hotel-booking' ),
			'GS' => __( 'South Georgia/Sandwich Islands', 'wp-hotel-booking' ),
			'KR' => __( 'South Korea', 'wp-hotel-booking' ),
			'SS' => __( 'South Sudan', 'wp-hotel-booking' ),
			'ES' => __( 'Spain', 'wp-hotel-booking' ),
			'LK' => __( 'Sri Lanka', 'wp-hotel-booking' ),
			'SD' => __( 'Sudan', 'wp-hotel-booking' ),
			'SR' => __( 'Suriname', 'wp-hotel-booking' ),
			'SJ' => __( 'Svalbard and Jan Mayen', 'wp-hotel-booking' ),
			'SZ' => __( 'Swaziland', 'wp-hotel-booking' ),
			'SE' => __( 'Sweden', 'wp-hotel-booking' ),
			'CH' => __( 'Switzerland', 'wp-hotel-booking' ),
			'SY' => __( 'Syria', 'wp-hotel-booking' ),
			'TW' => __( 'Taiwan', 'wp-hotel-booking' ),
			'TJ' => __( 'Tajikistan', 'wp-hotel-booking' ),
			'TZ' => __( 'Tanzania', 'wp-hotel-booking' ),
			'TH' => __( 'Thailand', 'wp-hotel-booking' ),
			'TL' => __( 'Timor-Leste', 'wp-hotel-booking' ),
			'TG' => __( 'Togo', 'wp-hotel-booking' ),
			'TK' => __( 'Tokelau', 'wp-hotel-booking' ),
			'TO' => __( 'Tonga', 'wp-hotel-booking' ),
			'TT' => __( 'Trinidad and Tobago', 'wp-hotel-booking' ),
			'TN' => __( 'Tunisia', 'wp-hotel-booking' ),
			'TR' => __( 'Turkey', 'wp-hotel-booking' ),
			'TM' => __( 'Turkmenistan', 'wp-hotel-booking' ),
			'TC' => __( 'Turks and Caicos Islands', 'wp-hotel-booking' ),
			'TV' => __( 'Tuvalu', 'wp-hotel-booking' ),
			'UG' => __( 'Uganda', 'wp-hotel-booking' ),
			'UA' => __( 'Ukraine', 'wp-hotel-booking' ),
			'AE' => __( 'United Arab Emirates', 'wp-hotel-booking' ),
			'GB' => __( 'United Kingdom (UK)', 'wp-hotel-booking' ),
			'US' => __( 'United States (US)', 'wp-hotel-booking' ),
			'UY' => __( 'Uruguay', 'wp-hotel-booking' ),
			'UZ' => __( 'Uzbekistan', 'wp-hotel-booking' ),
			'VU' => __( 'Vanuatu', 'wp-hotel-booking' ),
			'VA' => __( 'Vatican', 'wp-hotel-booking' ),
			'VE' => __( 'Venezuela', 'wp-hotel-booking' ),
			'VN' => __( 'Vietnam', 'wp-hotel-booking' ),
			'WF' => __( 'Wallis and Futuna', 'wp-hotel-booking' ),
			'EH' => __( 'Western Sahara', 'wp-hotel-booking' ),
			'WS' => __( 'Western Samoa', 'wp-hotel-booking' ),
			'YE' => __( 'Yemen', 'wp-hotel-booking' ),
			'ZM' => __( 'Zambia', 'wp-hotel-booking' ),
			'ZW' => __( 'Zimbabwe', 'wp-hotel-booking' ),
		);

		return $countries;
	}
}

if ( ! function_exists( 'hb_dropdown_countries' ) ) {
	function hb_dropdown_countries( $args = array() ) {
		$countries = hb_get_countries();
		$args      = wp_parse_args(
			$args,
			array(
				'name'              => 'countries',
				'selected'          => '',
				'show_option_none'  => false,
				'option_none_value' => '',
				'required'          => false,
			)
		);
		echo '<select name="' . $args['name'] . '"' . ( ( $args['required'] ) ? 'required' : '' ) . '>';
		if ( $args['show_option_none'] ) {
			echo '<option value="' . esc_attr( $args['option_none_value'] ) . '">' . esc_html( $args['show_option_none'] ) . '</option>';
		}
		foreach ( $countries as $code => $name ) {
			echo '<option value="' . esc_attr( $name ) . '" ' . selected( $name == $args['selected'] ) . '>' . esc_html( $name ) . '</option>';
		}
		echo '</select>';
	}
}

/**
 * Add a message to queue
 *
 * @param        $message
 * @param string $type
 */
if ( ! function_exists( 'hb_add_message' ) ) {
	function hb_add_message( $message, $type = 'message' ) {
		$messages = get_transient( 'hb_message_' . session_id() );
		if ( empty( $messages ) ) {
			$messages = array();
		}

		$messages[] = array(
			'type'    => $type,
			'message' => $message,
		);

		// hold in transient for 3 minutes
		set_transient( 'hb_message_' . session_id(), $messages, MINUTE_IN_SECONDS * 3 );
	}
}

if ( ! function_exists( 'hb_get_customer_fullname' ) ) {
	function hb_get_customer_fullname( $booking_id = null, $with_title = false ) {
		if ( $booking_id ) {
			$booking = WPHB_Booking::instance( $booking_id );

			$first_name = $last_name = '';
			if ( $booking->customer_first_name ) {
				$first_name = $booking->customer_first_name;
				$last_name  = $booking->customer_last_name;
			} elseif ( $booking->user_id ) {
				$user       = WPHB_User::get_user( $booking->user_id );
				$first_name = $user->first_name;
				$last_name  = $user->last_name;
			}

			if ( $with_title ) {
				$title = hb_get_title_by_slug( $booking->customer_title );
			} else {
				$title = '';
			}

			return sprintf( '%s%s %s', $title ? $title . ' ' : '', $first_name, $last_name );
		}
	}
}

if ( ! function_exists( 'is_room_category' ) ) {

	/**
	 * is_room_category - Returns true when viewing a room category.
	 *
	 * @param string $term (default: '') The term slug your checking for. Leave blank to return true on any.
	 *
	 * @return bool
	 */
	function is_room_category( $term = '' ) {
		return is_tax( 'hb_room', $term );
	}
}

if ( ! function_exists( 'is_room_taxonomy' ) ) {

	/**
	 * Returns true when viewing a room taxonomy archive.
	 *
	 * @return bool
	 */
	function is_room_taxonomy() {
		return is_tax( get_object_taxonomies( 'hb_room' ) );
	}
}

if ( ! function_exists( 'hb_render_label_shortcode' ) ) {

	/**
	 * Returns html label shortcode search.
	 *
	 * @return html
	 */
	function hb_render_label_shortcode( $atts = array(), $name = '', $text = '', $check = '' ) {
		$show = false;
		if ( ! isset( $atts[ $name ] ) || strtolower( $atts[ $name ] ) === $check ) {
			$show = true;
		}
		if ( $show === false ) {
			return;
		}

		echo '<label>' . sprintf( __( '%1$s', 'wp-hotel-booking' ), $text ) . '</label>';
	}
}

/**
 * Get date format
 *
 * @return string
 */
if ( ! function_exists( 'hb_date_format' ) ) {
	function hb_date_format() {
		return apply_filters( 'hb_date_format', 'd M Y' );
	}
}

if ( ! function_exists( 'is_room' ) ) {

	/**
	 * @return bool
	 */
	function is_room() {
		return is_singular( array( 'hb_room' ) );
	}
}

if ( ! function_exists( 'hb_get_url' ) ) {

	function hb_get_url( $params = array() ) {
		global $hb_settings;
		$query_str = '';
		if ( ! empty( $params ) ) {
			$query_str = '?hotel-booking-params=' . base64_encode( wp_json_encode( $params ) );
		}

		return apply_filters( 'hb_get_url', hb_get_page_permalink( 'search' ) . $query_str, hb_get_page_id( 'search' ), $params );
	}
}

if ( ! function_exists( 'hb_get_search_room_url' ) ) {
	/**
	 * @return mixed
	 */
	function hb_get_search_room_url() {
		$id = hb_get_page_id( 'search' );

		$url = home_url();
		if ( $id ) {
			$url = get_the_permalink( $id );
		}

		return apply_filters( 'hb_search_room_url', $url );
	}
}

if ( ! function_exists( 'hb_get_cart_url' ) ) {

	function hb_get_cart_url() {
		$id = hb_get_page_id( 'cart' );

		$url = home_url();
		if ( $id ) {
			$url = get_the_permalink( $id );
		}

		return apply_filters( 'hb_cart_url', $url );
	}
}

if ( ! function_exists( 'hb_get_thank_you_url' ) ) {

	function hb_get_thank_you_url( $booking_id = '', $booking_key = '' ) {

		if ( ! ( $booking_id && $booking_key ) ) {
			return false;
		}

		$id = hb_get_page_id( 'thankyou' );

		$url = home_url();
		if ( $id ) {
			$url = get_the_permalink( $id );
		}

		return apply_filters(
			'hb_thank_you_url',
			add_query_arg(
				array(
					'booking' => $booking_id,
					'key'     => $booking_key,
				),
				$url
			),
			$url,
			$id,
			$booking_id,
			$booking_key
		);
	}
}


if ( ! function_exists( 'hb_get_checkout_url' ) ) {

	function hb_get_checkout_url() {
		$id  = hb_get_page_id( 'checkout' );
		$url = '#';
		if ( $id ) {
			$url = get_the_permalink( $id );
		}
		return apply_filters( 'hb_checkout_url', $url );
	}
}

if ( ! function_exists( 'hb_get_account_url' ) ) {

	function hb_get_account_url() {
		$id = hb_get_page_id( 'account' );

		$url = home_url();
		if ( $id ) {
			$url = get_the_permalink( $id );
		}

		return apply_filters( 'hb_account_url', $url );
	}
}

if ( ! function_exists( 'hb_random_color_part' ) ) {

	function hb_random_color_part() {
		return str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT );
	}

	function hb_random_color() {
		return '#' . hb_random_color_part() . hb_random_color_part() . hb_random_color_part();
	}
}

if ( ! function_exists( 'hb_get_post_id_meta' ) ) {

	function hb_get_post_id_meta( $key, $value ) {
		global $wpdb;
		$meta = $wpdb->get_results( 'SELECT * FROM `' . $wpdb->postmeta . "` WHERE meta_key='" . esc_sql( $key ) . "' AND meta_value='" . esc_sql( $value ) . "'" );
		if ( is_array( $meta ) && ! empty( $meta ) && isset( $meta[0] ) ) {
			$meta = $meta[0];
		}
		if ( is_object( $meta ) ) {
			return $meta->post_id;
		} else {
			return false;
		}
	}
}

if ( ! function_exists( 'hb_get_date_format' ) ) {

	function hb_get_date_format() {
		$dateFormat = get_option( 'date_format' );

		$dateCustomFormat = get_option( 'date_format_custom' );
		if ( ! $dateFormat && $dateCustomFormat ) {
			$dateFormat = $dateCustomFormat;
		}

		return $dateFormat;
	}
}

if ( ! function_exists( 'hb_get_pages' ) ) {

	function hb_get_pages() {
		global $wpdb;
		$sql   = $wpdb->prepare(
			"
				SELECT ID, post_title FROM $wpdb->posts
				WHERE $wpdb->posts.post_type = %s AND $wpdb->posts.post_status = %s
				GROUP BY $wpdb->posts.post_name
			",
			'page',
			'publish'
		);
		$pages = $wpdb->get_results( $sql );

		return apply_filters( 'hb_get_pages', $pages );
	}
}

if ( ! function_exists( 'hb_dropdown_pages' ) ) {

	function hb_dropdown_pages( $args = array() ) {

		$args = wp_parse_args(
			$args,
			array(
				'show_option_none'  => __( '---Select page---', 'wp-hotel-booking' ),
				'option_none_value' => '',
				'add_new_title'     => __( '[ Add new page ]', 'wp-hotel-booking' ),
				'add_new_value'     => 'add_new_page',
				'name'              => '',
				'selected'          => '',
			)
		);

		$args  = apply_filters( 'hb_dropdown_pages_args', $args );
		$pages = hb_get_pages();

		$html   = array();
		$html[] = '<select id ="' . esc_attr( $args['name'] ) . '" name="' . esc_attr( $args['name'] ) . '" data-selected ="' . esc_attr( $args['selected'] ) . '" data-placeholder="' . __( 'Select a page&hellip;', 'wp-hotel-booking' ) . '">';
		$html[] = '<option value="' . $args['option_none_value'] . '">' . esc_html( $args['show_option_none'] ) . '</option>';
		$html[] = '<option value="' . $args['add_new_value'] . '">' . esc_html( $args['add_new_title'] ) . '</option>';
		foreach ( $pages as $page ) {
			$html[] = '<option value="' . esc_attr( $page->ID ) . '"' . selected( $args['selected'], $page->ID, false ) . '>' . esc_html( $page->post_title ) . '</option>';
		}
		$html[] = '</select>';
		echo implode( '', $html );
	}
}

/**
 *
 */
if ( ! function_exists( 'wp_hote_booking_blocked_days' ) ) {
	function wp_hotel_booking_blocked_days( $post_id = null, $strtotime = false ) {
		global $wpdb, $post;
		$unavailable_days = array();
		if ( ! $post || ! is_single( $post->ID ) || get_post_type( $post->ID ) !== 'hb_room' ) {
			return $unavailable_days;
		}
		if ( empty( $post_id ) ) {
			$post_id = $post->ID;
		}
		$title = $wpdb->prepare(
			"
				SELECT room.post_title
				FROM $wpdb->posts AS room
				WHERE
					room.post_status = %s
					AND room.post_type = %s
					AND room.ID = room_meta.post_id
				GROUP BY room.ID
			",
			'publish',
			'hb_room'
		);

		$query = $wpdb->prepare(
			"
				SELECT calendar.ID as calendarID, blocked.meta_value AS selected, room_meta.post_id AS ID, ( $title ) AS post_title
				FROM $wpdb->posts AS calendar
				INNER JOIN $wpdb->postmeta AS blocked ON calendar.ID = blocked.post_id
				INNER JOIN $wpdb->postmeta AS room_meta ON room_meta.meta_value = calendar.ID
				WHERE
					calendar.post_type = %s
					AND calendar.post_status = %s
					AND blocked.meta_key = %s
					AND room_meta.meta_key = %s
					AND room_meta.post_id = %s
				ORDER BY calendarID
			",
			'hb_blocked',
			'publish',
			'hb_blocked_time',
			'hb_blocked_id',
			$post_id
		);

		$results = $wpdb->get_results( $query, OBJECT );
		// compare the order booking date with the unavailable days
		$room_booked = WPHB_Room::instance( $post_id )->get_dates_available();

		if ( ! empty( $room_booked ) ) {
			$block_id = get_post_meta( $post_id, 'hb_blocked_id', true );
			if ( empty( $block_id ) ) {
				$block_id = wp_insert_post(
					array(
						'post_type'    => 'hb_blocked',
						'post_status'  => 'publish',
						'post_title'   => __( 'Block item', 'wp-hotel-booking' ),
						'post_content' => __( 'Block item', 'wp-hotel-booking' ),
					)
				);
				if ( ! is_wp_error( $block_id ) ) {
					add_post_meta( $post_id, 'hb_blocked_id', $block_id );
				}
			}
			foreach ( $room_booked as $date => $count ) {
				if ( $count <= 0 ) {
					$data             = new stdClass();
					$data->ID         = $post_id;
					$data->calendarID = $block_id;
					$data->selected   = $date;
					$data->post_title = get_the_title( $post_id );
					$results[]        = $data;
				}
			}
		}
		
		// end compare

		if ( is_array( $results ) && count( $results ) > 0 ) {
			foreach ( $results as $calendar ) {
				if ( ! $strtotime ) {
					if ( ! array_key_exists( $calendar->selected, $unavailable_days ) ) {
						$unavailable_days[] = date_i18n( 'Y-m-d', $calendar->selected );
					}
				} else {
					if ( ! array_key_exists( $calendar->selected, $unavailable_days ) ) {
						$unavailable_days[] = $calendar->selected;
					}
				}
			}
		}

		apply_filters( 'wp_hotel_booking_blocked_days', $unavailable_days, $post_id );

		return $unavailable_days;
	}
}

if ( ! function_exists( 'hotel_block_convert_current_time' ) ) {
	/**
	 * @param null $time
	 * @param int  $gmt
	 *
	 * @return float|int|null
	 */
	function hotel_block_convert_current_time( $time = null, $gmt = 0 ) {
		if ( ! $time ) {
			$time = time();
		}

		if ( ! $gmt ) {
			return $time + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );
		} else {
			return $time - ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) - 12 * HOUR_IN_SECONDS;
		}
	}
}


if ( ! function_exists( 'tp_hotel_booking_pages_required' ) ) {
	/**
	 * @return array
	 */
	function tp_hotel_booking_pages_required() {
		return apply_filters(
			'tp_hotel_booking_pages_required',
			array(
				'rooms_page_id'         => array(
					'name'    => __( 'Rooms Page', 'wp-hotel-booking' ),
					'slug'    => 'hotel-rooms',
					'id'      => 'tp_hotel_booking_rooms_page_id',
					'content' => '[' . apply_filters( 'hotel_booking_rooms_shortcode_tag', 'hotel_booking_rooms' ) . ']',
				),
				'search_page_id'        => array(
					'name'    => __( 'Search Page', 'wp-hotel-booking' ),
					'slug'    => 'hotel-search',
					'id'      => 'tp_hotel_booking_search_page_id',
					'content' => '[' . apply_filters( 'hotel_booking_search_shortcode_tag', 'hotel_booking' ) . ']',
				),
				'checkout_page_id'      => array(
					'name'    => __( 'Checkout Page', 'wp-hotel-booking' ),
					'slug'    => 'hotel-checkout',
					'id'      => 'tp_hotel_booking_checkout_page_id',
					'content' => '[' . apply_filters( 'hotel_booking_checkout_shortcode_tag', 'hotel_booking_checkout' ) . ']',
				),
				'cart_page_id'          => array(
					'name'    => __( 'Cart Page', 'wp-hotel-booking' ),
					'slug'    => 'hotel-cart',
					'id'      => 'tp_hotel_booking_cart_page_id',
					'content' => '[' . apply_filters( 'hotel_booking_cart_shortcode_tag', 'hotel_booking_cart' ) . ']',
				),
				'go_page_after_booking' => array(
					'name' => __( ' Redirect to page after book', 'wp-hotel-booking' ),
					'id'   => 'tp_hotel_booking_go_page_after_booking',
				),
				'account_page_id'       => array(
					'name'    => __( 'Account Page', 'wp-hotel-booking' ),
					'slug'    => 'hotel-account',
					'id'      => 'tp_hotel_booking_account_page_id',
					'content' => '[' . apply_filters( 'hotel_booking_account_shortcode_tag', 'hotel_booking_account' ) . ']',
				),
				'terms_page_id'         => array(
					'name'    => __( 'Terms Page', 'wp-hotel-booking' ),
					'slug'    => 'hotel-term-condition',
					'id'      => 'tp_hotel_booking_terms_page_id',
					'content' => apply_filters( 'hotel_booking_terms_content', 'Something notices' ),
				),
				'thankyou_page_id'      => array(
					'name'    => __( 'Thank You Page', 'wp-hotel-booking' ),
					'slug'    => 'hotel-thank-you',
					'id'      => 'tp_hotel_booking_thankyou_page_id',
					'content' => '[' . apply_filters( 'hotel_booking_thankyou_shortcode_tag', 'hotel_booking_thankyou' ) . ']',
				),
			)
		);
	}
}
