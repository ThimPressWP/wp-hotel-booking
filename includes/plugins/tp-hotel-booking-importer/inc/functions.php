<?php
/* get rooms */
function hbip_get_rooms( $room_id = false ) {
    /* global wpdb */
    global $wpdb;
    if ( $room_id ) {
        $sql = $wpdb->prepare( "
				SELECT ID FROM {$wpdb->posts} WHERE post_type = %s AND post_status != %s
			", 'hb_room', 'auto-draft' );
        return $wpdb->get_col( $sql );
    } else {
        $sql = $wpdb->prepare( "
				SELECT * FROM {$wpdb->posts} WHERE post_type = %s AND post_status != %s
			", 'hb_room', 'auto-draft' );
        return $wpdb->get_results( $sql );
    }
}

/* get booking */

function hbip_get_books( $booking_ids = false ) {
    global $wpdb;
    if ( $booking_ids ) {
        $sql = $wpdb->prepare( "
				SELECT ID FROM {$wpdb->posts} WHERE post_type = %s AND post_status != %s
			", 'hb_booking', 'auto-draft' );
        return $wpdb->get_col( $sql );
    } else {
        $sql = $wpdb->prepare( "
				SELECT * FROM {$wpdb->posts} WHERE post_type = %s AND post_status != %s
			", 'hb_booking', 'auto-draft' );
        return $wpdb->get_results( $sql );
    }
}

/* get coupons */

function hbip_get_coupons( $booking_ids = false ) {
    global $wpdb;
    if ( $booking_ids ) {
        $sql = $wpdb->prepare( "
				SELECT ID FROM {$wpdb->posts} WHERE post_type = %s AND post_status != %s
			", 'hb_coupon', 'auto-draft' );
        return $wpdb->get_col( $sql );
    } else {
        $sql = $wpdb->prepare( "
				SELECT * FROM {$wpdb->posts} WHERE post_type = %s AND post_status != %s
			", 'hb_coupon', 'auto-draft' );
        return $wpdb->get_results( $sql );
    }
}

/* get postmeta */

function hbip_get_post_metas( $post_id = null ) {
    global $wpdb;

    $sql = $wpdb->prepare( "
			SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id = %d
		", absint( $post_id ) );

    return $wpdb->get_results( $sql );
}

/* get attachments */

function hbip_get_attachments() {
    global $wpdb;
    $sql = $wpdb->prepare( "
			SELECT attach.* FROM $wpdb->posts as attach
				INNER JOIN $wpdb->postmeta as meta ON meta.meta_value = attach.ID AND meta.meta_key = %s
				INNER JOIN $wpdb->posts as rooms ON rooms.ID = meta.post_id AND rooms.post_type = %s AND rooms.post_status = %s
			WHERE
				attach.post_type = %s
				AND attach.post_status = %s
				GROUP BY attach.ID
		", '_thumbnail_id', 'hb_room', 'publish', 'attachment', 'inherit' );

    return $wpdb->get_results( $sql );
}

/* get users */

function hbip_get_users( $room_ids = array(), $booking_ids = array() ) {
    /* global wpdb */
    global $wpdb;
    $users = array();
    if ( !empty( $room_ids ) ) {
        $sql = $wpdb->prepare( "
				SELECT DISTINCT post_author FROM $wpdb->posts WHERE post_status != %s AND post_type = %s
			", 'auto-draft', 'hb_room' );

        $results = $wpdb->get_results( $sql );
        foreach ( $results as $r ) {
            $users[] = $r->post_author;
        }
        unset( $sql, $results );
    }

    if ( !empty( $booking_ids ) ) {
        $sql = $wpdb->prepare( "
				SELECT DISTINCT bookmeta.meta_value FROM $wpdb->postmeta AS bookmeta
					INNER JOIN $wpdb->posts AS book ON book.ID = bookmeta.post_id AND bookmeta.meta_key = %s
				WHERE post_status != %s AND post_type = %s
			", '_hb_user_id', 'auto-draft', 'hb_room' );

        $results = $wpdb->get_results( $sql );
        foreach ( $results as $r ) {
            $users[] = $r->post_author;
        }
        unset( $sql, $results );

        $sql = $wpdb->prepare( "
				SELECT DISTINCT post_author FROM $wpdb->posts WHERE post_status != %s AND post_type = %s
			", 'auto-draft', 'hb_booking' );

        $results = $wpdb->get_results( $sql );
        foreach ( $results as $r ) {
            $users[] = $r->post_author;
        }
        unset( $sql, $results );
    }

    return array_unique( $users );
}

/* get user metas */

function hbip_get_user_metas( $user_id = null ) {
    global $wpdb;

    $sql = $wpdb->prepare( "
			SELECT meta_key, meta_value FROM $wpdb->usermeta WHERE user_id = %d
		", absint( $user_id ) );

    return $wpdb->get_results( $sql );
}

/* get room taxonomies */

function hbip_get_room_taxonomies() {
    $custom_terms = (array) get_terms( array( 'hb_room_type', 'hb_room_capacity' ), array( 'get' => 'all' ) );
    return $custom_terms;
}

/* get postmeta */

function hbip_get_term_metas( $term_id = null ) {
    global $wpdb;

    $sql = $wpdb->prepare( "
			SELECT meta_key, meta_value FROM $wpdb->termmeta WHERE term_id = %d
		", absint( $term_id ) );

    return $wpdb->get_results( $sql );
}

/* get order items */

function hbip_get_order_items( $order_item_id = false ) {
    global $wpdb;

    if ( $order_item_id ) {
        $sql = "SELECT DISTINCT ID FROM $wpdb->hotel_booking_order_items";
        return $wpdb->get_cols( $sql );
    } else {
        $sql = "SELECT * FROM $wpdb->hotel_booking_order_items";
        return $wpdb->get_results( $sql );
    }
}

/* get order item meta */

function hbip_get_order_itemmetas( $order_item_id = null ) {
    global $wpdb;

    $sql = $wpdb->prepare( "
			SELECT * FROM $wpdb->hotel_booking_order_itemmeta WHERE hotel_booking_order_item_id = %d
		", $order_item_id );

    return $wpdb->get_results( $sql );
}

/* get pricing plans */

function hbip_get_pricings() {
    global $wpdb;

    return $wpdb->get_results( "SELECT * FROM $wpdb->hotel_booking_plans" );
}

/* extra rooms */

function hbip_get_extra_rooms() {
    global $wpdb;
    $sql = $wpdb->prepare( "
			SELECT * FROM $wpdb->posts
			WHERE post_type = %s AND post_status = %s
		", 'hb_extra_room', 'publish' );
    return $wpdb->get_results( $sql );
}

/* extra meta */

function hbip_get_extra_meta( $extra_id = null ) {
    if ( !$extra_id )
        return;
    global $wpdb;

    $sql = $wpdb->prepare( "
			SELECT * FROM $wpdb->postmeta WHERE post_id = %d
		", absint( $extra_id ) );

    return $wpdb->get_results( $sql );
}

/* blocked rooms */

function hbip_get_blocked_rooms() {
    global $wpdb;
    $sql = $wpdb->prepare( "
			SELECT * FROM $wpdb->posts
			WHERE post_type = %s AND post_status = %s
		", 'hb_blocked', 'publish' );
    return $wpdb->get_results( $sql );
}

/**
 * Wrap given string in XML CDATA tag.
 *
 * @since 2.1.0
 *
 * @param string $str String to wrap in XML CDATA tag.
 * @return string
 */
function hbip_cdata( $str ) {
    if ( !seems_utf8( $str ) ) {
        $str = utf8_encode( $str );
    }
    // $str = ent2ncr(esc_html($str));
    $str = '<![CDATA[' . str_replace( ']]>', ']]]]><![CDATA[>', $str ) . ']]>';

    return $str;
}
