<?php

class HB_Extra_Field {

	protected $_extras_type = null;

	function __construct() {
		add_filter( 'hb_metabox_room_settings', array( $this, 'meta_fields' ) );
		add_action( 'hotel_booking_loop_after_item', array( $this, 'render_extra' ), 10, 1 );
		add_action( 'hotel_booking_after_add_room_to_cart_form', array( $this, 'render_extra' ), 10, 1 );

		// single room cart
		add_action( 'hotel_booking_room_before_quantity', array( $this, 'single_room_cart' ) );
		/**
		 * add package details booking
		 */
		add_action( 'hotel_booking_room_details_quantity', array( $this, 'admin_booking_room_details' ), 10, 3 );
	}

	/**
	 * meta field box render
	 *
	 * @param  [type] $fields [description]
	 *
	 * @return [type]         [description]
	 */
	function meta_fields( $fields ) {
		$fields[] = array(
			'name'       => 'room_extra',
			'label'      => __( 'Addition Package', 'wp-hotel-booking' ),
			'type'       => 'multiple',
			'options'    => $this->extra_fields(),
			'filter'     => array( $this, 'meta_value' ),
		);

		return $fields;
	}

	protected function extra_fields() {
		global $hb_extra_settings;
		$options = array();
		$extras  = $hb_extra_settings->get_extra();
		foreach ( $extras as $key => $ex ) {
			$opt        = new stdClass();
			$opt->text  = $ex->post_title;
			$opt->value = $ex->ID;
			$options[]  = $opt;
		}
		return $options;
	}

	/**
	 * return value meta box content
	 *
	 * @param   @string || @array
	 *
	 * @return  @string || @array
	 */
	function meta_value( $val ) {
		return $val;
	}

	function admin_booking_room_details( $booking_params, $search_key, $room_id ) {
		if ( !isset( $booking_params[$search_key] ) ||
			!isset( $booking_params[$search_key][$room_id] ) ||
			!isset( $booking_params[$search_key][$room_id]['extra_packages_details'] )
		) {
			return;
		}

		$packages = $booking_params[$search_key][$room_id]['extra_packages_details'];
		?>
		<ul>
			<?php foreach ( $packages as $id => $package ): ?>
				<li>
					<small><?php printf( '%s (x%s)', $package['package_title'], $package['package_quantity'] ) ?></small>
				</li>
			<?php endforeach ?>
		</ul>
		<?php
	}

	/**
	 * render html extra field search room
	 *
	 * @param  $post_id
	 *
	 * @return template
	 */
	function render_extra( $post_id ) {
		tp_hb_extra_get_template( 'loop/extra-search-room.php', array( 'post_id' => $post_id ) );
	}

	/**
	 * Extra single search room
	 *
	 * Add to cart
	 *
	 * @param $post object
	 *
	 * @return html
	 */
	function single_room_cart( $post ) {
		ob_start();
		tp_hb_extra_get_template( 'loop/extra-single-search-room.php', array( 'post' => $post ) );
		echo ob_get_clean();
	}

}

new HB_Extra_Field();
