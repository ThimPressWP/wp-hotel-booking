<?php
/**
 * @Author: ducnvtt
 * @Date:   2016-04-25 11:25:39
 * @Last Modified by:   ducnvtt
 * @Last Modified time: 2016-04-29 09:33:29
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class HBIP_Importer {

	/* id import */
	protected $id = null;

	/* base url */
	protected $base_url = null;
	protected $users;
	protected $attachments;
	protected $rooms;
	protected $bookings;
	protected $coupons;
	protected $extras;
	protected $blockeds;
	protected $orders;
	protected $pricings;

	protected $remaps = array();
	protected $url_remap = array();

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

		if ( is_wp_error( $records ) ) {
			$this->import_error( $records->get_error_message() );
		}

		/* users */
		$this->import_users();

		/* attachments */
		$this->import_attachments();

		/* terms */
		$this->import_terms();

		/* extras */
		$this->import_extras();

		/* rooms */
		$this->import_rooms();

		/* coupons */
		$this->import_coupons();

		/* bookings */
		$this->import_bookings();

		/* orders */
		$this->import_orders();

		/* pricings */
		$this->import_pricings();

	}

	/* parse xml return all data */
	public function parse( $file ) {

		/* global $wp_filesystem */
		WP_FileSystem();
		global $wp_filesystem;

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
		$this->base_url = $xml->xpath('/rss/channel/hb:siteurl');
		if ( $this->base_url && isset( $this->base_url[0] ) ) {
			$this->base_url = $this->base_url[0]->__toString();
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
				$this->users[] = array(
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
				$this->attachments[] = $b;
			}
		}

		/* terms */
		$term_path = $xml->xpath('/rss/channel/hb:term');
		if ( $term_path ) {
			foreach ( $term_path as $k => $term ) {
				$term = $term->children( $namespaces['hb'] );
				$termmetas = array();

				$t = array();
				foreach ( $term as $k => $v ) {
					if ( ! in_array( $k, array( 'meta', 'comment' ) ) ) {
						$t[ $k ]	=	(string) $v;
					}
				}

				/* meta */
				foreach ( $term->meta as $meta ) {
					$termmetas[ $meta->meta_key->__toString() ] = $meta->meta_value->__toString();
				}
				$t['meta'] = $termmetas;
				$this->terms[] = $t;
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

				/* room type */
				$term_meta = array();
				if ( $ro->term ) {
					foreach ( $ro->term as $tem ) {
						$term_meta[] = absint( $tem );
					}
				}
				$r['term'] = $term_meta;
				$this->rooms[] = $r;
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
				$this->bookings[] = $b;
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
				$this->coupons[] = $cou;
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
				$this->blockeds[] = $blo;
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
				$this->extras[] = $ex;
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
				$or['meta'] = $ord_metas;
				$this->orders[] = $or;
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

				$this->pricings[] = $single_pr;
			}
		}
	}

	/* import users */
	public function import_users( ) {
		$this->remaps['users'] = array();
		/* insert 20 records */
		foreach ( $this->users as $user ) {
			if ( isset( $user['user_email'] ) ) {
				$email = $user['user_email'];
				$ussss = get_user_by( 'email', $email );
				if ( ! $ussss ) {
					$ussss = get_user_by( 'login', $user['user_login'] );
				}
				if ( $ussss ) {
					$this->remaps['users'][ $user['user_id'] ] = $ussss->ID;
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
					$this->remaps['users'][ $user['user_id'] ] = $user_id;
				}
			}
		}
		unset( $this->users );
	}

	/* import attachments */
	public function import_attachments( ) {
		$this->remap[ 'attachments' ] = array();
		$chunk = array_chunk( $this->attachments, 20 );
		if ( ! $chunk ) return $this->remap[ 'attachments' ];
		global $wpdb;
		foreach ( $chunk as $attachments ) {
			foreach ( $attachments as $attach ) {
				$attach_file = isset( $attach['meta'], $attach['meta']['_wp_attached_file'] ) ? $attach['meta']['_wp_attached_file'] : '';
				$sql = $wpdb->prepare( "
						SELECT post_id FROM $wpdb->postmeta WHERE meta_key = %s AND meta_value = %s LIMIT 1
					", '_wp_attached_file', $attach_file );
				if ( $attach_id = $wpdb->get_var( $sql ) ) {
					$this->remap[ 'attachments' ][] = $attach_id;
				} else if ( isset( $attach['guid'] ) ) {
					/* process attachment */
					$post_id = $this->process_attachment( $attach, $attach['guid'] );
					if ( is_wp_error( $post_id ) ) {
						$this->import_error( $post_id->get_error_message() );
					}
				}
			}
		}
		unset( $this->attachments );
	}

	/**
	 * Decide what the maximum file size for downloaded attachments is.
	 * Default is 0 (unlimited), can be filtered via import_attachment_size_limit
	 *
	 * @return int Maximum attachment file size to import
	 */
	function max_attachment_size() {
		return apply_filters( 'import_attachment_size_limit', 0 );
	}

	/**
	 * If fetching attachments is enabled then attempt to create a new attachment
	 *
	 * @param array $post Attachment post details from WXR
	 * @param string $url URL to fetch attachment from
	 * @return int|WP_Error Post ID on success, WP_Error otherwise
	 */
	function process_attachment( $post, $url ) {

		// if the URL is absolute, but does not contain address, then upload it assuming base_site_url
		if ( preg_match( '|^/[\w\W]+$|', $url ) )
			$url = rtrim( $this->base_url, '/' ) . $url;

		$upload = $this->fetch_remote_file( $url, $post );
		if ( is_wp_error( $upload ) ) {
			return $upload;
		}

		if ( $info = wp_check_filetype( $upload['file'] ) ) {
			$post['post_mime_type'] = $info['type'];
		} else {
			return new WP_Error( 'attachment_processing_error', __('Invalid file type', 'tp-hotel-booking-importer') );
		}

		$post['guid'] = $upload['url'];

		// as per wp-admin/includes/upload.php
		$post_id = wp_insert_attachment( $post, $upload['file'] );
		wp_update_attachment_metadata( $post_id, wp_generate_attachment_metadata( $post_id, $upload['file'] ) );

		// remap resized image URLs, works by stripping the extension and remapping the URL stub.
		if ( preg_match( '!^image/!', $info['type'] ) ) {
			$parts = pathinfo( $url );
			$name = basename( $parts['basename'], ".{$parts['extension']}" ); // PATHINFO_FILENAME in PHP 5.2

			$parts_new = pathinfo( $upload['url'] );
			$name_new = basename( $parts_new['basename'], ".{$parts_new['extension']}" );

			$this->url_remap[$parts['dirname'] . '/' . $name] = $parts_new['dirname'] . '/' . $name_new;
		}

		return $post_id;
	}

	/**
	 * Attempt to download a remote file attachment
	 *
	 * @param string $url URL of item to fetch
	 * @param array $post Attachment details
	 * @return array|WP_Error Local file location details on success, WP_Error otherwise
	 */
	function fetch_remote_file( $url, $post ) {
		// extract the file name and extension from the url
		$file_name = basename( $url );

		// get placeholder file in the upload dir with a unique, sanitized filename
		$upload = wp_upload_bits( $file_name, 0, '', $post['post_date'] );
		if ( $upload['error'] ) {
			return new WP_Error( 'upload_dir_error', $upload['error'] );
		}

		// fetch the remote url and write it to the placeholder file
		// $http = new WP_Http(); echo $url; die();
		$headers = wp_get_http( $url, $upload['file'] );

		// request failed
		if ( ! $headers ) {
			@unlink( $upload['file'] );
			return new WP_Error( 'import_file_error', __('Remote server did not respond', 'tp-hotel-booking-importer') );
		}

		// make sure the fetch was successful
		if ( $headers['response'] != '200' ) {
			@unlink( $upload['file'] );
			return new WP_Error( 'import_file_error', sprintf( __('Remote server returned error response %1$d %2$s', 'tp-hotel-booking-importer'), esc_html($headers['response']), get_status_header_desc($headers['response']) ) );
		}

		$filesize = filesize( $upload['file'] );

		if ( isset( $headers['content-length'] ) && $filesize != $headers['content-length'] ) {
			@unlink( $upload['file'] );
			return new WP_Error( 'import_file_error', __('Remote file is incorrect size', 'tp-hotel-booking-importer') );
		}

		if ( 0 == $filesize ) {
			@unlink( $upload['file'] );
			return new WP_Error( 'import_file_error', __('Zero size file downloaded', 'tp-hotel-booking-importer') );
		}

		$max_size = (int) $this->max_attachment_size();
		if ( ! empty( $max_size ) && $filesize > $max_size ) {
			@unlink( $upload['file'] );
			return new WP_Error( 'import_file_error', sprintf(__('Remote file is too large, limit is %s', 'tp-hotel-booking-importer'), size_format($max_size) ) );
		}

		// keep track of the old and new urls so we can substitute them later
		$this->url_remap[$url] = $upload['url'];
		$this->url_remap[$post['guid']] = $upload['url']; // r13735, really needed?
		// keep track of the destination if the remote url is redirected somewhere else
		if ( isset($headers['x-final-location']) && $headers['x-final-location'] != $url )
			$this->url_remap[$headers['x-final-location']] = $upload['url'];

		return $upload;
	}

	/* import terms */
	public function import_terms() {
		$this->remaps['terms'] = array();
		if ( ! $this->terms ) {
			return;
		}

		foreach ( $this->terms as $term ) {
			if ( $term_id = term_exists( $term['name'], $term['taxonomy'] ) ) {
				if ( is_array( $term_id ) ){
					$term_id = $term_id['term_id'];
				}
				if ( isset( $term['term_id'] ) )
					$this->remaps['terms'][ $term['term_id'] ] = (int) $term_id;
				continue;
			}

			if ( empty( $term['parent'] ) ) {
				$parent = 0;
			} else {
				$parent = term_exists( $term['parent'], $term['taxonomy'] );
				if ( is_array( $parent ) ) $parent = $parent['term_id'];
			}
			$description = isset( $term['description'] ) ? $term['description'] : '';
			$termarr = array( 'slug' => $term['slug'], 'description' => $description, 'parent' => intval( $parent ) );

			$term_id = wp_insert_term( $term['name'], $term['taxonomy'], $termarr );
			if ( is_wp_error( $term_id ) ) {
				$this->import_error( $term_id->get_error_message() );
			} else {
				if ( $term['meta'] ) {
					foreach ( $term['meta'] as $meta_key => $meta_value ) {
						update_term_meta( $term_id, sanitize_key( $meta_key ), sanitize_text_field( $meta_value ) );
					}
				}
				$this->remaps['terms'][ $term['term_id'] ] = $term_id['term_id'];
			}
		}

		unset( $this->terms );
	}

	/* import extras */
	public function import_extras() {
		$this->remaps['extras'] = array();

		if ( ! $this->extras ) {
			return;
		}

		$chunk = array_chunk( $this->extras, 20 );
		foreach ( $chunk as $extras ) {
			foreach ( $extras as $extra ) {
				$post_id = get_posts( array (
				        'post_name'   => $extra['post_name'],
				        'post_type'   => $extra['post_type'],
				        'numberposts' => 1,
				        'fields' => 'ids'
				    ) );
				if ( $post_id ) {
					$post_id = array_shift( $post_id );
					$this->remaps[ 'extras' ][ $extra['ID'] ] = $post_id;
				} else {
					$author = isset( $this->remaps[ 'users' ][ $extra['post_author'] ] ) ? $this->remaps[ 'users' ][ $extra['post_author'] ] : get_current_user_id();
					$postdata = array(
						'post_author' => $author, 'post_date' => $extra['post_date'],
						'post_date_gmt' => $extra['post_date_gmt'], 'post_content' => $extra['post_content'],
						'post_excerpt' => $extra['post_excerpt'], 'post_title' => $extra['post_title'],
						'post_status' => $extra['post_status'], 'post_name' => $extra['post_name'],
						'comment_status' => $extra['comment_status'],
						'post_type' => $extra['post_type']
					);

					/* insert new record */
					$post_id = wp_insert_post( $postdata );
					if ( is_wp_error( $post_id ) ) {
						$this->import_error( $post_id->get_error_message() );
					} else {
						$this->remaps['extras'][ $extra['ID'] ] = $post_id;
					}

					/* extra meta */
					if ( isset( $extra['meta'] ) && $extra['meta'] ) {
						foreach ( $extra['meta'] as $meta_key => $value ) {
							update_post_meta( $post_id, sanitize_key( $meta_key ), $value );
						}
					}
				}
			}
		}

		unset( $this->extras );
	}

	/* import rooms */
	public function import_rooms() {
		$this->remaps['rooms'] = array();

		$chunk = array_chunk( $this->rooms, 20 );
		foreach ( $chunk as $rooms ) {
			foreach ( $rooms as $room ) {
				$post_id = get_posts( array (
				        'name'   => $room['post_name'],
				        'post_type'   => $room['post_type'],
				        'numberposts' => 1,
				        'fields' => 'ids'
				    ) );
				if ( $post_id ) {
					$post_id = array_shift( $post_id );
					$this->remaps[ 'rooms' ][ $room['ID'] ] = $post_id;
				} else {
					$author = isset( $this->remaps[ 'users' ][ $room['post_author'] ] ) ? $this->remaps[ 'users' ][ $room['post_author'] ] : get_current_user_id();
					$postdata = array(
						'post_author' => $author, 'post_date' => $room['post_date'],
						'post_date_gmt' => $room['post_date_gmt'], 'post_content' => $room['post_content'],
						'post_excerpt' => $room['post_excerpt'], 'post_title' => $room['post_title'],
						'post_status' => $room['post_status'], 'post_name' => $room['post_name'],
						'comment_status' => $room['comment_status'],
						'post_type' => $room['post_type']
					);

					/* insert new record */
					$post_id = wp_insert_post( $postdata );
					if ( is_wp_error( $post_id ) ) {
						$this->import_error( $post_id->get_error_message() );
					} else {
						$this->remaps['rooms'][ $room['ID'] ] = $post_id;
					}

					/* room meta */
					if ( isset( $room['meta'] ) && $room['meta'] ) {
						foreach ( $room['meta'] as $meta_key => $value ) {
							/* room type */
							if ( in_array( $meta_key, array( '_hb_room_type', '_hb_room_capacity' ) ) ) {
								$room_type_id = isset( $this->remaps['terms'], $this->remaps['terms'][ $value ] ) ? $this->remaps['terms'][ $value ] : $value;
								update_post_meta( $post_id, '_hb_room_type', $room_type_id );
							} else if ( $meta_key === '_hb_gallery' ) {
								$value = maybe_unserialize( $value );
								$new = array();
								foreach( $value as $value ) {
									if ( isset( $this->remaps['attachments'], $this->remaps['attachments'][ $value ] ) ) {
										$new[] = $this->remaps['attachments'][ $value ];
									}
								}
								update_post_meta( $post_id, '_hb_gallery', $new );
							} else if ( $meta_key === '_thumbnail_id' ) {
								$thumb_id = isset( $this->remaps['attachments'], $this->remaps['attachments'][ $value ] ) ? $this->remaps['attachments'][ $value ] : $value;
								update_post_meta( $post_id, '_thumbnail_id', $thumb_id );
							} else if ( $meta_key === '_hb_room_extra' ) {
								$value = maybe_unserialize( $value );
								$new = array();
								foreach( $value as $value ) {
									if ( isset( $this->remaps['extras'], $this->remaps['extras'][ $value ] ) ) {
										$new[] = $this->remaps['extras'][ $value ];
									}
								}
								update_post_meta( $post_id, '_hb_room_extra', $new );
							} else {
								update_post_meta( $post_id, sanitize_key( $meta_key ), sanitize_text_field( $value ) );
							}
						}
					}

					/* insert post term taxonomy room type */
					if ( isset( $room['term'] ) ) {
						$set_terms = array();
						foreach ( $room['term'] as $term ) {
							$set_terms[] = isset( $this->remaps['terms'][ $term ] ) ? $this->remaps['terms'][ $term ] : $term;
						}
						wp_set_post_terms( $post_id, $set_terms, 'hb_room_type' );
					}
				}
			}
		}

		unset( $this->rooms );
	}

	/* import coupons */
	public function import_coupons() {
		$this->remaps['coupons'] = array();

		if ( ! $this->coupons ) {
			return;
		}

		$chunk = array_chunk( $this->coupons, 20 );
		foreach ( $chunk as $coupons ) {
			foreach ( $coupons as $coupon ) {
				$post_id = get_posts( array (
				        'post_name'   => $coupon['post_name'],
				        'post_type'   => $coupon['post_type'],
				        'numberposts' => 1,
				        'fields' => 'ids'
				    ) );
				if ( $post_id ) {
					$post_id = array_shift( $post_id );
					$this->remaps[ 'coupons' ][ $coupon['ID'] ] = $post_id;
				} else {
					$author = isset( $this->remaps[ 'users' ][ $coupon['post_author'] ] ) ? $this->remaps[ 'users' ][ $coupon['post_author'] ] : get_current_user_id();
					$postdata = array(
						'post_author' => $author, 'post_date' => $coupon['post_date'],
						'post_date_gmt' => $coupon['post_date_gmt'], 'post_content' => $coupon['post_content'],
						'post_excerpt' => $coupon['post_excerpt'], 'post_title' => $coupon['post_title'],
						'post_status' => $coupon['post_status'], 'post_name' => $coupon['post_name'],
						'comment_status' => $coupon['comment_status'],
						'post_type' => $coupon['post_type']
					);

					/* insert new record */
					$post_id = wp_insert_post( $postdata );
					if ( is_wp_error( $post_id ) ) {
						$this->import_error( $post_id->get_error_message() );
					} else {
						$this->remaps['coupons'][ $coupon['ID'] ] = $post_id;
					}

					/* coupon meta */
					if ( isset( $coupon['meta'] ) && $coupon['meta'] ) {
						foreach ( $coupon['meta'] as $meta_key => $value ) {
							update_post_meta( $post_id, sanitize_key( $meta_key ), $value );
						}
					}
				}
			}
		}

		unset( $this->coupons );
	}

	/* import bookings */
	public function import_bookings() {
		$this->remaps['bookings'] = array();

		if ( ! $this->bookings ) {
			return;
		}

		$chunk = array_chunk( $this->bookings, 20 );
		foreach ( $chunk as $bookings ) {
			foreach ( $bookings as $booking ) {
				$post_id = get_posts( array (
				        'name'   		=> $booking['post_name'],
				        'post_type'   	=> $booking['post_type'],
				        'numberposts' 	=> 1,
				        'fields' 		=> 'ids'
				    ) );

				if ( $post_id ) {
					$post_id = array_shift( $post_id );
					$this->remaps[ 'bookings' ][ $booking['ID'] ] = $post_id;
				} else {
					$author = isset( $this->remaps[ 'users' ][ $booking['post_author'] ] ) ? $this->remaps[ 'users' ][ $booking['post_author'] ] : get_current_user_id();
					$postdata = array(
						'post_author' => $author, 'post_date' => $booking['post_date'],
						'post_date_gmt' => $booking['post_date_gmt'], 'post_content' => $booking['post_content'],
						'post_excerpt' => $booking['post_excerpt'], 'post_title' => $booking['post_title'],
						'post_status' => $booking['post_status'], 'post_name' => $booking['post_name'],
						'comment_status' => $booking['comment_status'],
						'post_type' => $booking['post_type']
					);

					/* insert new record */
					$post_id = wp_insert_post( $postdata );
					if ( is_wp_error( $post_id ) ) {
						$this->import_error( $post_id->get_error_message() );
					} else {
						$this->remaps['bookings'][ $booking['ID'] ] = $post_id;
					}

					/* booking meta */
					if ( isset( $booking['meta'] ) && $booking['meta'] ) {
						foreach ( $booking['meta'] as $meta_key => $value ) {
							if ( $meta_key === '_hb_user_id' ) {
								$_hb_user_id = isset( $this->remaps['users'], $this->remaps['users'][ $value ] ) ? $this->remaps['users'][ $value ] : $value;
								update_post_meta( $post_id, sanitize_key( $meta_key ), $_hb_user_id );
							} else {
								update_post_meta( $post_id, sanitize_key( $meta_key ), $value );
							}
						}
					}
				}
			}
		}

		unset( $this->bookings );
	}

	/* import orders */
	public function import_orders() {
		$this->remaps['orders'] = array();

		if ( ! $this->orders ) {
			return;
		}

		$chunk = array_chunk( $this->orders, 20 );
		foreach ( $chunk as $orders ) {
			foreach ( $orders as $order ) {
				if ( ! isset( $order['order_item_id'] ) ) continue;
				$parent_id = isset( $this->remaps['orders'], $this->remaps['orders'][ $order['order_item_id'] ] ) ? $this->remaps['orders'][ $order['order_item_id'] ] : $order['order_item_parent'];
				$booking_id = isset( $this->remaps['bookings'], $this->remaps['bookings'][ $order['order_id'] ] ) ? $this->remaps['bookings'][ $order['order_id'] ] : $order['order_id'];
				$order_param = array(
						'order_item_name'       => $order['order_item_name'],
		                'order_item_type'       => $order['order_item_type'],
		                'order_item_parent'     => $parent_id,
		                'order_id'              => $booking_id
					);

				$order_item_id = hb_add_order_item( $booking_id, $order_param );

				$this->remaps['orders'][ $order['order_item_id'] ] = $order_item_id;

				/* order meta */
				if ( isset( $order['meta'] ) && $order['meta'] ) {
					foreach ( $order['meta'] as $meta_key => $meta_value ) {
						hb_update_order_item_meta( $order_item_id, sanitize_key( $meta_key ), sanitize_text_field( $meta_value ) );
					}
				}
			}
		}

		unset( $this->orders );
	}

	/* import pricings */
	public function import_pricings() {
		$this->remap['pricing'] = array();
		$chunk = array_chunk( $this->pricings, 20 );
		foreach ( $chunk as $pricings ) {
			foreach ( $pricings as $pricing ) {
				if ( ! isset( $pricing['room_id'] ) ) continue;
				$room_id = isset( $this->remaps[ 'rooms' ], $this->remaps[ 'rooms' ][$pricing['room_id']] ) ? $this->remaps[ 'rooms' ][$pricing['room_id']] : $pricing['room_id'];
				$plan_id = hb_room_set_pricing_plan(array(
						'start_time'		=> $pricing['start_time'] ? $pricing['start_time'] : null,
						'end_time'			=> $pricing['end_time'] ? $pricing['end_time'] : null,
						'pricing'			=> maybe_unserialize( $pricing['pricing'] ),
						'room_id'			=> $room_id
					));
				$this->remap['pricing'][ $pricing['plan_id'] ] = $plan_id;
			}
		}
		unset( $this->pricings );
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
		printf( __( '<p>Import completed.</p>', 'tp-hotel-booking-importer' ) );
	}

}

new HBIP_Importer();

add_action( 'admin_init', 'hbip_importer' );
function hbip_importer() {
	$GLOBALS['hbip_importer'] = new HBIP_Importer();
	register_importer( 'hbip_importer', 'Hotel Booking', __( 'This will contain all of your <strong> rooms, bookings, coupons, users, pricing plan, block special date and additonal packages</strong>.', 'tp-hotel-booking-importer'), array( $GLOBALS['hbip_importer'], 'dispatch' ) );
}