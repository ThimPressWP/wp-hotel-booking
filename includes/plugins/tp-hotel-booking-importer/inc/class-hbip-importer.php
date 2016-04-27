<?php
/**
 * @Author: ducnvtt
 * @Date:   2016-04-25 11:25:39
 * @Last Modified by:   ducnvtt
 * @Last Modified time: 2016-04-27 14:13:28
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class HBIP_Importer {

	public function __construct() {
		/* import acction */
		add_action( 'admin_init', array( $this, 'import' ) );
	}

	/* import */
	public function import() {
		if ( ! isset( $_POST['hbip-import-upload'] ) || ! wp_verify_nonce( $_POST['hbip-import-upload'], 'hbip-import-upload' ) ) {
			return;
		}

		$messages = array(
			'error'		=> array(),
			'updated'	=> array()
		);
		if ( isset( $_POST['import'] ) && empty( $_FILES['import'] )  ) {
			$messages['error'][] = sprintf( __( 'There was an error importing the logs. File type detected: \'%s\'. \'text/xml\' expected', 'tp-hotel-booking-importer' ), $file['type'] );
		}

		$file = $_FILES['import'];

		// Is it of the expected type?
	    if ( isset( $file[ 'type' ] ) && $file[ 'type' ] != 'text/xml' ) {
	        $messages['error'][] = sprintf( __( 'There was an error importing the logs. File type detected: \'%s\'. \'text/xml\' expected', 'tp-hotel-booking-importer' ), $file['type'] );
	    }

	    // Impose a limit on the size of the uploaded file. Max 1048576 bytes = 1048576 * 8MB
	    $max_size = apply_filters( 'hotel_booking_import_max_size', wp_max_upload_size() );
	    if ( isset( $file[ 'size' ] ) && $file[ 'size' ] > $max_size ) {
	        $size = size_format( $file['size'], 2 );
	        $messages['error'][] = sprintf( __( 'File size too large (%s). Maximum 2MB', 'tp-hotel-booking-importer' ), $size );
	    }

	    /* error upload */
	    if( isset( $file[ 'error' ] ) && $file[ 'error' ] > 0 ) {
        	$messages['error'][ 'error' ][] = sprintf( __( 'Error encountered: %d', 'tp-hotel-booking-importer' ), $file["error"] );
	    }

	    /* has errors */
		if ( ! empty( $messages['error'] ) ) {
			$messages['error'][] = $errors;
		} else {
			// process import
			$upload = $this->handle_upload( $file['tmp_name'] );
			if ( is_wp_error( $upload ) ) {
				foreach ( $upload->get_error_messages() as $msg ) {
					$messages['error'][]	= $msg;
				}
			} else {
				$messages['updated'][]	= __( 'Import Completed.', 'tp-hotel-booking-importer' );
			}
		}

		$_SESSION[ 'hbip_import_flash_messages' ] = $messages;
		wp_redirect( admin_url( 'admin.php?page=tp-hotel-tools&tab=import' ) ); exit();
	}

	/* handle */
	public function handle_upload( $file ) {
		/* parse file */
		$records = $this->parse( $file );
		$errors = array();
		if ( is_wp_error( $records ) ) {
			return $records;
		}

		/* users */
		$users = array();
		if ( isset( $records['users'] ) ) {
			$users = $this->import_users( $records['users'] );
			if ( is_wp_error( $users ) ) {
				return $users;
			}
			unset( $records['users'] );
		}

		/* attachments */
		$attachments = array();
		if ( isset( $records['attachments'] ) ) {
			$attachments = $this->import_attachments( $records['attachments'] );
			if ( is_wp_error( $attachments ) ) {
				return $attachments;
			}
			unset( $records['attachments'] );
		}

		/* terms */
		$terms = array();
		if ( isset( $records['terms'] ) ) {
			$terms = $this->import_terms( $records['terms'] );
			if ( is_wp_error( $terms ) ) {
				return $terms;
			}
			unset( $records['terms'] );
		}

		/* extras */
		$extras = array();
		if ( isset( $records['extras'] ) ) {
			$extras = $this->import_extras( $records['extras'] );
			if ( is_wp_error( $extras ) ) {
				return $extras;
			}
			unset( $records['extras'] );
		}

		/* rooms */
		$rooms = array();
		if ( isset( $records['rooms'] ) ) {
			$rooms = $this->import_rooms( $records['rooms'], $attachments, $terms, $extras );
			if ( is_wp_error( $rooms ) ) {
				return $rooms;
			}
			unset( $records['rooms'], $terms );
		}

		/* coupons */
		$coupons = array();
		if ( isset( $records['coupons'] ) ) {
			$coupons = $this->import_coupons( $records['coupons'] );
			if ( is_wp_error( $coupons ) ) {
				return $coupons;
			}
			unset( $records['coupons'] );
		}

		/* bookings */
		$bookings = array();
		if ( isset( $records['bookings'] ) ) {
			$bookings = $this->import_bookings( $records['bookings'], $rooms, $coupons );
			if ( is_wp_error( $bookings ) ) {
				return $bookings;
			}
			unset( $records['bookings'] );
		}

		/* orders */
		$orders = array();
		if ( isset( $records['orders'] ) ) {
			$orders = $this->import_orders( $records['orders'], $rooms, $extras, $bookings );
			if ( is_wp_error( $orders ) ) {
				return $orders;
			}
			unset( $records['orders'], $extras, $bookings );
		}

		/* pricings */
		$pricings = array();
		if ( isset( $records['pricings'] ) ) {
			$pricings = $this->import_pricings( $records['pricings'], $rooms );
			if ( is_wp_error( $pricings ) ) {
				return $pricings;
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
		/* create Dom node */
		$doc = new DOMDocument;
		/* load xml content */
		$doc->loadXML( trim( $wp_filesystem->get_contents( $file ) ) );
		$xml = simplexml_import_dom( $doc );

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
						'user_first_name'	=> $user->user_first_name->__toString(),
						'user_last_name'	=> $user->user_last_name->__toString(),
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

		/* attachments */
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

	}

	/* import attachments */
	public function import_attachments( $attachments ) {

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

}

new HBIP_Importer();
