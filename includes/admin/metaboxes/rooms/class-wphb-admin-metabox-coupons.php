<?php
if ( ! class_exists( 'WPHB_Meta_Box_Coupons' ) ) {
	class WPHB_Meta_Box_Coupons extends WPHB_Meta_Box {

		public $post_type = 'hb_coupon';
		/**
		 * Instance
		 *
		 * @var null|WPHB_Meta_Box_Coupons
		 */
		private static $instance = null;

		public function add_meta_box() {
			add_meta_box( 'coupon_settings', esc_html__( 'Coupon Settings', 'wp-hotel-booking' ), array( $this, 'render' ), $this->post_type, 'normal', 'high' );
		}

		/**
		 * Add new field to meta box
		 *
		 * @param $post_id int
		 *
		 * @return WPHB_Meta_Box instance
		 */
		public function metabox( $post_id = 0 ) {

			return apply_filters(
				'wpbh_meta_box_room_extra_settings',
				array(
					'coupon_description'         => array(
						'name'  => 'coupon_description',
						'label' => __( 'Description', 'wp-hotel-booking' ),
						'type'  => 'textarea',
						'std'   => '',
					),
					'coupon_discount_type'       => array(
						'name'    => 'coupon_discount_type',
						'label'   => __( 'Discount type', 'wp-hotel-booking' ),
						'type'    => 'select',
						'std'     => '',
						'options' => array(
							'fixed_cart'   => __( 'Cart discount', 'wp-hotel-booking' ),
							'percent_cart' => __( 'Cart % discount', 'wp-hotel-booking' ),
						),
					),
					'coupon_discount_value'      => array(
						'name'  => 'coupon_discount_value',
						'label' => __( 'Discount value', 'wp-hotel-booking' ),
						'type'  => 'number',
						'std'   => '',
						'min'   => 0,
						'step'  => 0.1,
						'max'   => 100,
					),
					'coupon_date_from'           => array(
						'name'   => 'coupon_date_from',
						'label'  => __( 'Validate from', 'wp-hotel-booking' ),
						'type'   => 'datetime',
						'filter' => 'hb_meta_box_field_coupon_date',
					),
					'coupon_date_from_timestamp' => array(
						'name'  => 'coupon_date_from_timestamp',
						'label' => '',
						'type'  => 'hidden',
					),
					'coupon_date_to'             => array(
						'name'   => 'coupon_date_to',
						'label'  => __( 'Validate until', 'wp-hotel-booking' ),
						'type'   => 'datetime',
						'filter' => 'hb_meta_box_field_coupon_date',
					),
					'coupon_date_to_timestamp'   => array(
						'name'  => 'coupon_date_to_timestamp',
						'label' => '',
						'type'  => 'hidden',
					),
					'minimum_spend'              => array(
						'name'  => 'minimum_spend',
						'label' => __( 'Minimum spend', 'wp-hotel-booking' ),
						'type'  => 'number',
						'desc'  => __( 'This field allows you to set the minimum subtotal needed to use the coupon.', 'wp-hotel-booking' ),
						'min'   => 0,
						'step'  => 0.1,
						'max'   => 100,
					),
					'maximum_spend'              => array(
						'name'  => 'maximum_spend',
						'label' => __( 'Maximum spend', 'wp-hotel-booking' ),
						'type'  => 'number',
						'desc'  => __( 'This field allows you to set the maximum subtotal allowed when using the coupon.', 'wp-hotel-booking' ),
						'min'   => 0,
						'step'  => 0.1,
						'max'   => 100,
					),
					'limit_per_coupon'           => array(
						'name'  => 'limit_per_coupon',
						'label' => __( 'Usage limit per coupon', 'wp-hotel-booking' ),
						'type'  => 'number',
						'desc'  => __( 'How many times this coupon can be used before it is void.', 'wp-hotel-booking' ),
						'min'   => 0,
					),
					'used'                       => array(
						'name'   => 'used',
						'label'  => __( 'Used', 'wp-hotel-booking' ),
						'type'   => 'label',
						'filter' => 'hb_meta_box_field_coupon_used',
					),
				)
			);
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
			$post_id = $post->ID;
			?>
			<div class="wphb-meta-box lp-meta-box--extra_options">
				<div class="wphb-meta-box__inner">
					<?php
					do_action( 'wphb/meta-box/coupons/before' );

					foreach ( $this->metabox( $post->ID ) as $key => $field ) {
						echo '<div class="form-field ' . $field['name'] . '">';
						if ( isset( $field['label'] ) && $field['label'] != '' ) {
							echo '<label class="hb-form-field-label">' . $field['label'] . '</label>';
						}
						if ( $this->has_post_meta( $post->ID, $field['name'] ) ) {
							$field['std'] = get_post_meta( $post->ID, $this->meta_key_prefix . $field['name'], true );
						}
							$field['name'] = $this->meta_key_prefix . $field['name'];
						if ( empty( $field['id'] ) ) {
							$field['id'] = sanitize_title( $field['name'] );
						}
							echo '<div class="hb-form-field-input">';
								$tmpl = WP_Hotel_Booking::instance()->locate( "includes/admin/metaboxes/views/fields/{$field['type']}.php" );
								require $tmpl;
						if ( ! empty( $field['desc'] ) ) {
							printf( '<p class="description">%s</p>', $field['desc'] );
						}
						if ( ! empty( $field['edit_option'] ) ) {
							printf( '<a href="%s" class="edit_meta" target="_blank">Edit</a>', add_query_arg( $field['edit_option'], admin_url( $field['edit_option']['admin_url'] ) ) );
						}
						echo '</div>';
						echo '</div>';
					}

					do_action( 'wphb/meta-box/coupons/after' );
					?>
				</div>
			</div>
			<?php
		}

		/**
		 * Get instance
		 *
		 * @return WPHB_Meta_Box_Coupons
		 */
		public static function instance(): WPHB_Meta_Box_Coupons {
			if ( ! self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}
	}
	WPHB_Meta_Box_Coupons::instance();
}


