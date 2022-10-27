<?php
if ( ! class_exists( 'WPHB_Meta_Box_Room' ) ) {
	class WPHB_Meta_Box_Room extends WPHB_Meta_Box {
		/**
		 * Instance
		 *
		 * @var null|WPHB_Meta_Box_Room
		 */
		private static $instance = null;

		public function add_meta_box() {
			add_meta_box( 'room_settings', esc_html__( 'Room Settings', 'wp-hotel-booking' ), array( $this, 'render' ), $this->post_type, 'normal', 'high' );
		}

		/**
		 * Add new field to meta box
		 *
		 * @param $post_id int
		 *
		 * @return WPHB_Meta_Box instance
		 */
		function metabox( $post_id = 0 ) {

			return apply_filters(
				'wpbh_meta_box_room_settings_tabs',
				array(
					'general_settings'    => array(
						'label'    => esc_html__( 'General', 'wp-hotel-booking' ),
						'target'   => 'general_room_data',
						'icon'     => 'dashicons-admin-tools',
						'priority' => 10,
						'content'  => $this->wphb_general( $post_id ),
					),
					'price_settings'      => array(
						'label'    => esc_html__( 'Pricing', 'wp-hotel-booking' ),
						'target'   => 'price_room_data',
						'icon'     => 'dashicons-cart',
						'priority' => 20,
						'content'  => $this->wphb_price(),
					),
					'block_room_settings' => array(
						'label'    => esc_html__( 'Block Special Date', 'wp-hotel-booking' ),
						'target'   => 'block_room_data',
						'icon'     => 'dashicons-calendar-alt',
						'priority' => 30,
						'content'  => $this->wphb_block_date(),
					),
					'gallery_settings'    => array(
						'label'    => esc_html__( 'Galary', 'wp-hotel-booking' ),
						'target'   => 'gallery_settings',
						'icon'     => 'dashicons-excerpt-view',
						'priority' => 40,
						'content'  => $this->wphb_gallery(),
					),
					'deposit_room'        => array(
						'label'         => esc_html__( 'Deposit', 'wp-hotel-booking' ),
						'target'        => 'deposit_room',
						'icon'          => 'dashicons-nametag',
						'priority'      => 50,
						'content'       => $this->wphb_deposit( $post_id ),
						'wrapper_class' => 'blocked',
					),
					'rule_room'           => array(
						'label'    => esc_html__( 'Regulations', 'wp-hotel-booking' ),
						'target'   => 'rule_room',
						'icon'     => 'dashicons-admin-settings',
						'priority' => 60,
						'content'  => $this->wphb_rule( $post_id ),
					),
					'room_faq'            => array(
						'label'    => esc_html__( 'FAQ', 'wp-hotel-booking' ),
						'target'   => 'room_faq',
						'icon'     => 'dashicons-welcome-learn-more',
						'priority' => 70,
						'content'  => $this->wphb_faq( $post_id ),
					),
				)
			);
		}

		public function wphb_faq( $post_id ) {
			$tab_faq = apply_filters(
				'wpbh_meta_box_room_faq_fields',
				array(
					'_wphb_room_faq' => new WPHB_Admin_Metabox_Room_FAQ(),
				)
			);

			return $tab_faq;
		}

		/**
		 * It creates a new tab rule in the room settings page.
		 *
		 * @return An array of arrays.
		 */
		public function wphb_rule( $post_id ) {
			$tab_rule = apply_filters(
				'wpbh_meta_box_rule_room_fields',
				array(
					'wphb_rule_room' => array(
						'name'            => 'wphb_rule_room',
						'label'           => __( 'Room rules', 'wp-hotel-booking' ),
						'type'            => 'textarea',
						'std'             => '',
						'editor'          => true,
						'editor_settings' => array(
							'editor_height' => 5,
							'editor_class'  => 'wphb_width_editor',
						),
						'wrapper_class'   => '_wphb_rule_room_ele',
					),
				),
			);

			return $tab_rule;
		}
		/**
		 * It returns an array of objects.
		 *
		 * @return an array of the class WPHB_Admin_Metabox_Room_Deposit.
		 */
		public function wphb_deposit( $post_id ) {
			$tab_deposit = apply_filters(
				'wpbh_meta_box_deposit_room_fields',
				array(
					'enable_deposit' => array(
						'name'  => 'enable_deposit',
						'label' => __( 'Deposit payment', 'wp-hotel-booking' ),
						'type'  => 'checkbox',
						'std'   => '',
						'desc'  => __( 'Enable deposit', 'wp-hotel-booking' ),
					),
					'deposit_type'   => array(
						'name'    => 'deposit_type',
						'label'   => __( 'Deposit type', 'wp-hotel-booking' ),
						'type'    => 'select',
						'options' => array(
							'fixed'   => __( 'Fixed value', 'wp-hotel-booking' ),
							'percent' => __( 'Percentage of price', 'wp-hotel-booking' ),
						),
					),
					'deposit_amount' => array(
						'name'  => 'deposit_amount',
						'label' => __( 'Deposit amount', 'wp-hotel-booking' ),
						'type'  => 'number',
						'std'   => 1,
						'step'  => 0.1,
						'min'   => 0,
						'max'   => 100,
						'desc'  => __( 'Enter deposit amount', 'wp-hotel-booking' ),
						// 'attr'  => 'required',
					),
				),
			);

			return $tab_deposit;
		}

		/**
		 * It returns an array of fields that are used to create the General tab in the Room post type.
		 *
		 * @return An array of arrays.
		 */
		public function wphb_general( $post_id ) {

			$preview      = get_post_meta( $post_id, '_hb_room_preview', true );
			$class_prview = $preview ? '' : 'hidden';

			$tab_general = apply_filters(
				'wpbh_meta_box_room_general_fields',
				array(
					// 'room_booking_only'         => array(
					// 'name'  => 'room_booking_only',
					// 'label' => __( 'Booking Only', 'wp-hotel-booking' ),
					// 'type'  => 'checkbox',
					// 'desc'  => __( 'Enable if you want this room to be booked only for 1 day ', 'wp-hotel-booking' ),
					// ),
					'num_of_rooms'              => array(
						'name'  => 'num_of_rooms',
						'label' => __( 'Quantity', 'wp-hotel-booking' ),
						'type'  => 'number',
						'std'   => '100',
						'desc'  => __( 'The number of rooms', 'wp-hotel-booking' ),
						'min'   => 1,
						'max'   => 100,
					),
					'room_origin_capacity'      => array(
						'name'        => 'room_origin_capacity',
						'label'       => __( 'Room Capacities', 'wp-hotel-booking' ),
						'type'        => 'select',
						'options'     => hb_get_room_capacities(
							array(
								'map_fields' => array(
									'term_id' => 'value',
									'name'    => 'text',
								),
							)
						),
						'edit_option' => array(
							'taxonomy'  => 'hb_room_capacity',
							'post_type' => 'hb_room',
							'admin_url' => 'edit-tags.php',
						),
					),
					'max_child_per_room'        => array(
						'name'  => 'max_child_per_room',
						'label' => __( 'Max children per room', 'wp-hotel-booking' ),
						'type'  => 'number',
						'std'   => 0,
						'min'   => 0,
						'max'   => 100,
					),
					'external_link'             => array(
						'name'  => 'external_link',
						'label' => __( 'External link', 'wp-hotel-booking' ),
						'type'  => 'text',
						'desc'  => __( 'Allows attaching a redirect link to another system when clicking the button booking room. Example: https://www.booking.com', 'wp-hotel-booking' ),
					),
					'room_addition_information' => array(
						'name'            => 'room_addition_information',
						'label'           => __( 'Additional Information', 'wp-hotel-booking' ),
						'type'            => 'textarea',
						'std'             => '',
						'editor'          => true,
						'editor_settings' => array(
							'editor_height' => 5,
							'editor_class'  => 'wphb_width_editor',
						),
					),
					'room_preview'              => array(
						'name'  => 'room_preview',
						'label' => __( 'Enable Video', 'wp-hotel-booking' ),
						'type'  => 'checkbox',
						'desc'  => __( 'Show video description Room', 'wp-hotel-booking' ),
					),
					'room_preview_url'          => array(
						'name'            => 'room_preview_url',
						'label'           => __( 'Source Video', 'wp-hotel-booking' ),
						'type'            => 'textarea',
						'desc'            => __( 'If enable Preview Room, Allow formats like: iframe, url...', 'wp-hotel-booking' ),
						'editor_settings' => array(
							'editor_height' => 10,
							'editor_class'  => 'wphb_width_editor',
						),
						'wrapper_class'   => $class_prview,
					),
				),
				$post_id
			);

			return $tab_general;
		}

		/**
		 * It returns the price of the room.
		 */
		public function wphb_price() {
			$tab_price = apply_filters(
				'wpbh_meta_box_room_price_fields',
				array(
					'_wphb_regular_price' => new WPHB_Admin_Metabox_Room_Price(),
				)
			);

			return $tab_price;
		}

		/**
		 * It returns an array of fields that are used to create a gallery metabox
		 *
		 * @return An array of arrays.
		 */
		public function wphb_gallery() {

			$tab_gallery = apply_filters(
				'wpbh_meta_box_room_gallery_fields',
				array(
					'gallery' => array(
						'name' => 'gallery',
						'type' => 'gallery',
					),
				)
			);

			return $tab_gallery;

		}

		/**
		 * It returns an array of objects that are used to create block date metaboxes
		 *
		 * @return the array of the class WPHB_Admin_Metabox_Room_Block_Date.
		 */
		public function wphb_block_date() {

			$tab_block_date = apply_filters(
				'wpbh_meta_box_room_block_date_fields',
				array(
					'block_room_settings' => new WPHB_Admin_Metabox_Room_Block_Date(),
				)
			);

			return $tab_block_date;
		}


		/**
		 * Output meta box content
		 *
		 * @param int
		 */
		public function render( $post ) {
			if ( empty( $post ) ) {
				return;
			}
			parent::render( $post );
			$data = array(
				'room_metabox' => $this,
				'post'         => $post,
			);
			//get template
			WP_Hotel_Booking::instance()->_include( 'includes/admin/views/admin-metabox-rooms.php', true, $data );
		}

		public function save( $post_id ) {
			$fieldTypeHtmlArr = array();

			foreach ( $this->metabox( $post_id ) as $key => $tab_content ) {
				if ( isset( $tab_content['content'] ) ) {
					foreach ( $tab_content['content'] as $field ) {
						if ( is_object( $field ) ) {
							$field->save( $post_id );
							continue;
						}
						if ( array_key_exists( $this->meta_key_prefix . $field['name'], (array) $_POST ) ) {
							$keyPost    = $this->meta_key_prefix . $field['name'];
							$meta_value = WPHB_Helpers::sanitize_params_submitted( $_POST[ $keyPost ] );

							if ( in_array( $keyPost, $fieldTypeHtmlArr ) ) {
								$meta_value = WPHB_Helpers::sanitize_params_submitted( $meta_value, 'html' );
							} else {
								$meta_value = WPHB_Helpers::sanitize_params_submitted( $meta_value );
							}
							if ( $keyPost == '_hb_wphb_rule_room' ) {
								$meta_value = sanitize_post_field( '_hb_wphb_rule_room', $_POST[ $keyPost ], $post_id );
							}
							if ( $keyPost == '_hb_room_addition_information' ) {
								$meta_value = $_POST[ $keyPost ];
							}
							if ( $keyPost == '_hb_room_preview_url' ) {
								$meta_value = $_POST[ $keyPost ];
							}

							$meta_value = apply_filters( 'hb_meta_box_update_meta_value', $meta_value, $field['name'], $post_id );
							update_post_meta( $post_id, $this->meta_key_prefix . $field['name'], $meta_value );
						} else {
							update_post_meta( $post_id, $this->meta_key_prefix . $field['name'], '' );
						}
					}
				}
			}
		}
		/**
		 * Get instance
		 *
		 * @return WPHB_Meta_Box_Room
		 */
		public static function instance(): WPHB_Meta_Box_Room {
			if ( ! self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}
	}
	WPHB_Meta_Box_Room::instance();
}


