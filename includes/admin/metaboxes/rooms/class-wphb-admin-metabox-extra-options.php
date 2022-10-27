<?php
if ( ! class_exists( 'WPHB_Meta_Box_Extra_Options' ) ) {
	class WPHB_Meta_Box_Extra_Options extends WPHB_Meta_Box {

		public $post_type = 'hb_extra_room';

		public $meta_key_prefix = 'tp_hb_extra_room_';
		/**
		 * Instance
		 *
		 * @var null|WPHB_Meta_Box_Extra_Options
		 */
		private static $instance = null;

		public function add_meta_box() {
			add_meta_box( 'extra_settings', esc_html__( 'Extra Settings', 'wp-hotel-booking' ), array( $this, 'render' ), $this->post_type, 'normal', 'high' );
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
					'price'     => array(
						'name'  => 'price',
						'label' => esc_html__( 'Price', 'wp-hotel-booking' ),
						'type'  => 'number',
						'std'   => '10',
						'desc'  => __( 'Price of extra room option', 'wp-hotel-booking' ),
						'min'   => 0,
						'step'  => 0.01,
					),
					'respondent_name' => array(
						'name'    => 'respondent_name',
						'label'   => __( 'Unit', 'wp-hotel-booking' ),
						'desc'    => __( 'Unit of extra room option', 'wp-hotel-booking' ),
						'type'    => 'text',
						'default' => __( 'Package', 'wp-hotel-booking' ),
					),
					'respondent'      => array(
						'name'    => 'respondent',
						'label'   => __( 'Type', 'wp-hotel-booking' ),
						'desc'    => __( 'Type of extra room option', 'wp-hotel-booking' ),
						'type'    => 'select',
						'options' => hb_extra_types(),
					),
					'required'        => array(
						'name'  => 'required',
						'label' => __( 'Required', 'wp-hotel-booking' ),
						'desc'  => __( 'Required include for all booking', 'wp-hotel-booking' ),
						'type'  => 'checkbox',
						'std'   => '',
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
					do_action( 'wphb/meta-box/extra_options/before' );

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

					do_action( 'wphb/meta-box/extra_options/after' );
					?>
				</div>
			</div>
			<?php
		}

		/**
		 * Get instance
		 *
		 * @return WPHB_Meta_Box_Extra_Options
		 */
		public static function instance(): WPHB_Meta_Box_Extra_Options {
			if ( ! self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}
	}
	WPHB_Meta_Box_Extra_Options::instance();
}


