<?php
/**
 * @Author: ducnvtt
 * @Date:   2016-04-25 11:25:39
 * @Last Modified by:   ducnvtt
 * @Last Modified time: 2016-04-27 17:22:38
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class HBIP_Importer {

	/* id import */
	protected $id = null;

	public function __construct() {
		/* import acction */
		// add_action( 'admin_init', array( $this, 'import' ) );
	}

	public function dispatch() {

		$step = 0;
		if ( isset( $_GET['step'] ) ) {
			$step = sanitize_text_field( $_GET['step'] );
		}

		switch ( $step ) {
			case 0:
					$this->load_form();
				break;

			case 1:
					check_admin_referer( 'import-upload' );
					if ( $this->handle_upload() ) {

						if ( $this->id ) {
							$file = get_attached_file( $this->id );
						}

						$this->import( $file );
					}
				break;
		}
	}

	/* form */
	public function load_form() {
		require_once HOTEL_BOOKING_IMPORTER_PATH . 'inc/views/import.php';
	}

	/* hanldle_upload */
	public function handle_upload() {
		$file = wp_import_handle_upload();
		if ( isset( $file['error'] ) ) {
			$this->import_error();
		} elseif ( ! file_exists( $file['file'] ) ) {
			$this->import_error( sprintf( __( 'Sorry, there has been an error, could not be found at <code>%s</code>. It is likely that this was caused by a permissions problem.', 'tp-hotel-booking-importer' ), $file['file'] ) );
		}

		if( isset( $file['id'] ) ) {
			$this->id = $file['id'];
		}

		return true;
	}

	/* import */
	public function import( $file = null ) {
		if ( ! file_exists( $file ) ) {
			$this->import_error( sprintf( __( 'Sorry, there has been an error. File is not exists.', 'tp-hotel-booking-importer' ), $file['error'] ) );
		}

		/* set limit time */
		$this->start();

		/* process import */
		$this->process( $file );

		/* import completed */
		$this->completed();
	}

	/* start import */
	public function start() {
		/* set request timeout */
		apply_filters( 'http_request_timeout', 60 );
	}

	/* handle */
	public function process( $file = null ) {
		/* parse file */
		$records = $this->parse( $file );
		$errors = array();

		if ( is_wp_error( $records ) ) {
			$this->import_error( $records->get_error_message() );
		}

		/* users */
		$users = array();
		if ( isset( $records['users'] ) ) {
			$users = $this->import_users( $records['users'] );
			if ( is_wp_error( $users ) ) {
				$this->import_error( $users->get_error_message() );
			}
			unset( $records['users'] );
		}

		/* attachments */
		$attachments = array();var_dump( $records['attachments']); die();
		if ( isset( $records['attachments'] ) ) {
			$attachments = $this->import_attachments( $records['attachments'] );
			if ( is_wp_error( $attachments ) ) {
				$this->import_error( $attachments->get_error_message() );
			}
			unset( $records['attachments'] );
		}

		/* terms */
		$terms = array();
		if ( isset( $records['terms'] ) ) {
			$terms = $this->import_terms( $records['terms'] );
			if ( is_wp_error( $terms ) ) {
				$this->import_error( $terms->get_error_message() );
			}
			unset( $records['terms'] );
		}

		/* extras */
		$extras = array();
		if ( isset( $records['extras'] ) ) {
			$extras = $this->import_extras( $records['extras'] );
			if ( is_wp_error( $extras ) ) {
				$this->import_error( $extras->get_error_message() );
			}
			unset( $records['extras'] );
		}

		/* rooms */
		$rooms = array();
		if ( isset( $records['rooms'] ) ) {
			$rooms = $this->import_rooms( $records['rooms'], $attachments, $terms, $extras );
			if ( is_wp_error( $rooms ) ) {
				$this->import_error( $rooms->get_error_message() );
			}
			unset( $records['rooms'], $terms );
		}

		/* coupons */
		$coupons = array();
		if ( isset( $records['coupons'] ) ) {
			$coupons = $this->import_coupons( $records['coupons'] );
			if ( is_wp_error( $coupons ) ) {
				$this->import_error( $coupons->get_error_message() );
			}
			unset( $records['coupons'] );
		}

		/* bookings */
		$bookings = array();
		if ( isset( $records['bookings'] ) ) {
			$bookings = $this->import_bookings( $records['bookings'], $rooms, $coupons );
			if ( is_wp_error( $bookings ) ) {
				$this->import_error( $bookings->get_error_message() );
			}
			unset( $records['bookings'] );
		}

		/* orders */
		$orders = array();
		if ( isset( $records['orders'] ) ) {
			$orders = $this->import_orders( $records['orders'], $rooms, $extras, $bookings );
			if ( is_wp_error( $orders ) ) {
				$this->import_error( $orders->get_error_message() );
			}
			unset( $records['orders'], $extras, $bookings );
		}

		/* pricings */
		$pricings = array();
		if ( isset( $records['pricings'] ) ) {
			$pricings = $this->import_pricings( $records['pricings'], $rooms );
			if ( is_wp_error( $pricings ) ) {
				$this->import_error( $pricings->get_error_message() );
			}
			unset( $records['pricings'], $rooms  );
		}

	}

	/* parse xml return all data */
	public function parse( $file ) {

		/* global $wp_filesystem */
		WP_FileSystem();
		global $wp_filesystem;

		/* set default array parse */
		$site_url = false;
		$users = $attachments = $rooms = $bookings = $coupons = $extras = $blockeds = $orders = $pricings = array();

		$xml = false;
		try{
			libxml_use_internal_errors(true);

			/* create Dom node */
			$doc = new DOMDocument;
			/* load xml content */
			$old_value = null;
			if ( function_exists( 'libxml_disable_entity_loader' ) ) {
				$old_value = libxml_disable_entity_loader( true );
			}
			$load = $doc->loadXML( trim( $wp_filesystem->get_contents( $file ) ) );
			if ( $load ) {
				$xml = simplexml_import_dom( $doc );
				unset( $doc );
			}
			if ( ! is_null( $old_value ) ) {
				libxml_disable_entity_loader( $old_value );
			}
		} catch( Exception $e ) {
			$this->import_error( $e->getMessage() );
		}

		if ( ! $xml ) {
			return new WP_Error( 'hpip_import_xml_error', __( 'Could not load content file.', 'tp-hotel-booking-importer' ) );
		}

		$namespaces = $xml->getDocNamespaces();
		if ( ! isset( $namespaces['hb'] ) ) {
			$namespaces['hb'] = 'http://wordpress.org/';
		}
		$site_url = $xml->xpath('/rss/channel/hb:siteurl');
		if ( $site_url && isset( $site_url[0] ) ) {
			$site_url = $site_url[0]->__toString();
		}

		/* users */
		$users_path = $xml->xpath('/rss/channel/hb:user');
		if ( $users_path ) {
			foreach ( $users_path as $k => $user ) {
				$user = $user->children( $namespaces['hb'] );
				$user_meta = array();
				foreach ( $user->meta as $meta ) {
					$user_meta[ $meta->meta_key->__toString() ] = $meta->meta_value->__toString();
				}
				$users[] = array(
						'user_id'		=> absint( $user->user_id ),
						'user_login'	=> $user->user_login->__toString(),
						'user_email'	=> $user->user_email->__toString(),
						'user_display_name'	=> $user->user_display_name->__toString(),
						'user_status'	=> $user->user_status->__toString(),
						'user_nicename'	=> $user->user_nicename->__toString(),
						'user_first_name'	=> $user->user_first_name->__toString(),
						'user_last_name'	=> $user->user_last_name->__toString(),
						'user_pass'			=> $user->user_pass->__toString(),
						'meta'				=> $user_meta
					);
			}
		}

		/* attachments */
		$attachment_path = $xml->xpath('/rss/channel/hb:attachment');
		if ( $attachment_path ) {
			foreach ( $attachment_path as $k => $atta ) {
				$atta = $atta->children( $namespaces['hb'] );
				$attametas = array();

				$b = array();
				foreach ( $atta as $k => $v ) {
					if ( ! in_array( $k, array( 'meta', 'comment' ) ) ) {
						$b[ $k ]	=	(string) $v;
					}
				}

				/* meta */
				foreach ( $atta->meta as $meta ) {
					$attametas[ $meta->meta_key->__toString() ] = $meta->meta_value->__toString();
				}
				$b['meta'] = $attametas;
				$attachments[] = $b;
			}
		}

		/* room */
		$room_path = $xml->xpath('/rss/channel/hb:room');
		if ( $room_path ) {
			foreach ( $room_path as $k => $ro ) {
				$ro = $ro->children( $namespaces['hb'] );
				$r_meta = array();

				$r = array();
				foreach ( $ro as $k => $v ) {
					if ( ! in_array( $k, array( 'meta', 'comment' ) ) ) {
						$r[ $k ]	=	(string) $v;
					}
				}

				/* meta */
				foreach ( $ro->meta as $meta ) {
					$r_meta[ $meta->meta_key->__toString() ] = $meta->meta_value->__toString();
				}
				$r['meta'] = $r_meta;
				$rooms[] = $r;
			}
		}

		/* books */
		$booking_path = $xml->xpath('/rss/channel/hb:booking');
		if ( $booking_path ) {
			foreach ( $booking_path as $k => $book ) {
				$book = $book->children( $namespaces['hb'] );
				$bookmetas = array();

				$b = array();
				foreach ( $book as $k => $v ) {
					if ( ! in_array( $k, array( 'meta', 'comment' ) ) ) {
						$b[ $k ]	=	(string) $v;
					}
				}

				/* meta */
				foreach ( $book->meta as $meta ) {
					$bookmetas[ $meta->meta_key->__toString() ] = $meta->meta_value->__toString();
				}
				$b['meta'] = $bookmetas;
				$bookings[] = $b;
			}
		}

		/* coupons */
		$coupon_path = $xml->xpath('/rss/channel/hb:coupon');
		if ( $coupon_path ) {
			foreach ( $coupon_path as $k => $extra ) {
				$extra = $extra->children( $namespaces['hb'] );

				$coupon_meta = array();
				foreach ( $extra->meta as $meta ) {
					$coupon_meta[ $meta->meta_key->__toString() ] = $meta->meta_value->__toString();
				}
				$cou = array();
				foreach ( $extra as $k => $v ) {
					if ( ! in_array( $k, array( 'meta', 'comment' ) ) ) {
						$cou[ $k ]	=	(string) $v;
					}
				}

				$cou['meta'] = $coupon_meta;
				$coupons[] = $cou;
			}
		}

		/* blocked */
		$blocked_path = $xml->xpath('/rss/channel/hb:blocked');
		if ( $blocked_path ) {
			foreach ( $blocked_path as $k => $blk ) {
				$blk = $blk->children( $namespaces['hb'] );
				$blkmeta = array();

				$blo = array();
				foreach ( $blk as $k => $v ) {
					if ( ! in_array( $k, array( 'meta', 'comment' ) ) ) {
						$blo[ $k ]	=	(string) $v;
					}
				}

				/* meta */
				foreach ( $blk->meta as $meta ) {
					$blkmeta[ $meta->meta_key->__toString() ] = $meta->meta_value->__toString();
				}
				$blo['meta'] = $blkmeta;
				$blockeds[] = $blo;
			}
		}

		/* extras */
		$extra_path = $xml->xpath('/rss/channel/hb:extra');
		if ( $extra_path ) {
			foreach ( $extra_path as $k => $extra ) {
				$extra = $extra->children( $namespaces['hb'] );
				$extrametas = array();

				$ex = array();
				foreach ( $extra as $k => $v ) {
					if ( ! in_array( $k, array( 'meta', 'comment' ) ) ) {
						$ex[ $k ]	=	(string) $v;
					}
				}

				/* meta */
				foreach ( $extra->meta as $meta ) {
					$extrametas[ $meta->meta_key->__toString() ] = $meta->meta_value->__toString();
				}
				$ex['meta'] = $extrametas;
				$extras[] = $ex;
			}
		}

		$order_path = $xml->xpath('/rss/channel/hb:order');
		if ( $order_path ) {
			foreach ( $order_path as $k => $ord ) {
				$ord = $ord->children( $namespaces['hb'] );
				$ord_metas = array();

				$or = array();
				foreach ( $ord as $k => $v ) {
					if ( ! in_array( $k, array( 'meta', 'comment' ) ) ) {
						$or[ $k ]	=	(string) $v;
					}
				}

				/* meta */
				foreach ( $ord->meta as $meta ) {
					$ord_metas[ $meta->meta_key->__toString() ] = $meta->meta_value->__toString();
				}
				$or['meta'] = $extrametas;
				$orders[] = $or;
			}
		}

		$pricing_path = $xml->xpath('/rss/channel/hb:pricing');
		if ( $pricing_path ) {
			foreach ( $pricing_path as $k => $pr ) {
				$pr = $pr->children( $namespaces['hb'] );

				$single_pr = array();
				foreach ( $pr as $k => $v ) {
					if ( ! in_array( $k, array( 'meta', 'comment' ) ) ) {
						$single_pr[ $k ]	=	(string) $v;
					}
				}

				$pricings[] = $single_pr;
			}
		}

		return apply_filters( 'hotel_booking_import_params', array(
				'users'		=> $users,
				'attachments'	=> $attachments,
				'rooms'			=> $rooms,
				'bookings'		=> $bookings,
				'extras'		=> $extras,
				'blockeds'		=> $blockeds,
				'orders'		=> $orders,
				'pricings'		=> $pricings
			) );
	}

	/* import users */
	public function import_users( $users ) {
		$user_ids = array();
		/* insert 20 records */
		foreach ( $users as $user ) {
			if ( isset( $user['user_email'] ) ) {
				$email = $user['user_email'];
				if ( $ussss = get_user_by( 'email', $email ) ) {
					$user_ids[ $ussss->ID ] = $ussss->ID;
				} else {
					$user_id = wp_insert_user( array(
								'user_login'	=> $user['user_login'],
								'user_email'	=> $user['user_email'],
								'user_pass'		=> $user['user_pass'],
								'user_nicename'	=> $user['user_nicename'],
								'user_display_name'	=> $user['user_display_name'],
								'user_status'		=> $user['user_status']
						) );
					if ( is_wp_error( $user_id ) ) {
						$this->import_error( sprintf( '%s', $user_id->get_error_message() ) );
					}

					/* insert user meta */
					foreach ( $user['meta'] as $meta_key => $meta_value ) {
						update_user_meta( $user_id, $meta_key, $meta_value );
					}
					$user_ids[ $user['ID'] ] = $user_id;
				}
			}
		}

		return $user_ids;
	}

	/* import attachments */
	public function import_attachments( $attachments ) {
		var_dump($attachments); die();
	}

	/* import terms */
	public function import_terms( $terms ) {

	}

	/* import extras */
	public function import_extras( $extras ) {

	}

	/* import rooms */
	public function import_rooms( $rooms ) {

	}

	/* import coupons */
	public function import_coupons( $coupons ) {

	}

	/* import bookings */
	public function import_bookings( $bookings ) {

	}

	/* import orders */
	public function import_orders( $orders ) {

	}

	/* import pricings */
	public function import_pricings( $pricings ) {

	}

	/**
	 * Show import error and quit.
	 * @param  string $message
	 */
	private function import_error( $message = '' ) {
		echo '<p><strong>' . __( 'Sorry, there has been an error.', 'tp-hotel-booking-importer' ) . '</strong><br />';
		if ( $message ) {
			echo esc_html( $message );
		}
		echo '</p>'; die();
	}

	/* completed message */
	private function completed() {
		printf( __( '<p>Import complted.</p>', 'tp-hotel-booking-importer' ) );
	}

}

new HBIP_Importer();

add_action( 'admin_init', 'hbip_importer' );
function hbip_importer() {
	$GLOBALS['hbip_importer'] = new HBIP_Importer();
	register_importer( 'hbip_importer', 'Hotel Booking', __( 'This will contain all of your <strong> rooms, bookings, coupons, users, pricing plan, block special date and additonal packages</strong>.', 'tp-hotel-booking-importer'), array( $GLOBALS['hbip_importer'], 'dispatch' ) );
}