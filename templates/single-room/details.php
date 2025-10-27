<?php
/**
 * The template for displaying single room details.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/single-room/details.php.
 *
 * @author  ThimPress, leehld
 * @package WP-Hotel-Booking/Templates
 * @version 1.6
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();
$minium_booking_night = WPHB_Settings::instance()->get( 'minimum_booking_day', 0 );
$minium_checkout_date = 1 + $minium_booking_night;

$check_in_date  = hb_get_request( 'check_in_date', date( 'Y/m/d' ) );
$check_out_date = hb_get_request( 'check_out_date', date( 'Y/m/d', strtotime( "+{$minium_checkout_date} day" ) ) );
$adults         = hb_get_request( 'adults', 1 );
$children       = hb_get_request( 'max_child', 0 );
$room_qty       = hb_get_request( 'room_qty', 1 );

$room = WPHB_Room::instance(
	get_the_ID(),
	array(
		'check_in_date'  => $check_in_date,
		'check_out_date' => $check_out_date,
		'quantity'       => $room_qty,
	)
);

ob_start();
the_content();
$content = ob_get_clean();

$tabsInfo   = array();
$tabsInfo[] = array(
	'id'      => 'hb_room_description',
	'title'   => __( 'Description', 'wp-hotel-booking' ),
	'content' => $content,
);
$tabsInfo[] = array(
	'id'      => 'hb_room_additinal',
	'title'   => __( 'Additional Information', 'wp-hotel-booking' ),
	'content' => $room->addition_information,
);

$faqs = get_post_meta( $room->ID, '_wphb_room_faq', true );
if ( ! empty( $faqs ) ) {
	$tabsInfo[] = array(
		'id'      => 'hb_room_faq',
		'title'   => __( 'FAQS', 'wp-hotel-booking' ),
		'content' => $room->content_faqs,
	);
}

$rules = get_post_meta( $room->ID, '_hb_wphb_rule_room', true );
if ( ! empty( $rules ) ) {
	$tabsInfo[] = array(
		'id'      => 'hb_room_rule',
		'title'   => __( 'Room Rules', 'wp-hotel-booking' ),
		'content' => $room->content_rules,
	);
}

$facilities = get_post_meta( $room->ID, '_wphb_room_facilities', true );

if ( ! empty( $facilities ) ) {
	$tabsInfo[] = array(
		'id'      => 'hb_room_facilities',
		'title'   => __( 'Facilities', 'wp-hotel-booking' ),
		'content' => $room->get_facilities(),
	);
}

$tabs = apply_filters( 'hotel_booking_single_room_infomation_tabs', $tabsInfo );

// prepend after li tabs single
do_action( 'hotel_booking_before_single_room_infomation' );
?>

<div class="wphb-single-room-content">

	<div class="hb_single_room_details">

	    <ul class="hb_single_room_tabs">
			<?php foreach ( $tabs as $key => $tab ) { ?>
	            <li>
	                <a href="#<?php echo esc_attr( $tab['id'] ); ?>" class="<?php echo esc_attr( $active_class ?? '' ); ?>">
						<?php do_action( 'hotel_booking_single_room_before_tabs_' . $tab['id'] ); ?>
						<?php printf( '%s', $tab['title'] ); ?>
						<?php do_action( 'hotel_booking_single_room_after_tabs_' . $tab['id'] ); ?>
	                </a>
	            </li>
			<?php } ?>
	    </ul>

		<?php
		// append after li tabs single
		do_action( 'hotel_booking_after_single_room_infomation' );
		?>

	    <div class="hb_single_room_tabs_content">

			<?php foreach ( $tabs as $key => $tab ) { ?>
	            <div id="<?php echo esc_attr( $tab['id'] ); ?>" class="hb_single_room_tab_details">

					<?php do_action( 'hotel_booking_single_room_before_tabs_content_' . $tab['id'] ); ?>

					<?php printf( '%s', $tab['content'] ); ?>

					<?php do_action( 'hotel_booking_single_room_after_tabs_content_' . $tab['id'] ); ?>
	            </div>
			<?php } ?>
	    </div>

	</div>
	<div class="wphb-single-room-booking-container">
		<?php 
			wp_enqueue_script( 'wphb-single-room-js' );
			wphb_get_template_no_override( 'single-room/booking-form.php', compact( 'room' ) );
			do_action( 'hotel_booking_single_room_after_booking_form', $room ); 
		?>
	</div>
</div>