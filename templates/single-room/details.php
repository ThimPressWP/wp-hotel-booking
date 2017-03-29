<?php

if ( !defined( 'ABSPATH' ) ) {
	exit();
}

$room = WPHB_Room::instance( get_the_ID() );
ob_start();
the_content();
$content = ob_get_clean();

$tabsInfo   = array();
$tabsInfo[] = array(
	'id'      => 'hb_room_description',
	'title'   => __( 'Description', 'wp-hotel-booking' ),
	'content' => $content
);
$tabsInfo[] = array(
	'id'      => 'hb_room_additinal',
	'title'   => __( 'Additional Information', 'wp-hotel-booking' ),
	'content' => $room->addition_information
);
$tabs       = apply_filters( 'hotel_booking_single_room_infomation_tabs', $tabsInfo );
// prepend after li tabs single
do_action( 'hotel_booking_before_single_room_infomation' );
?>
<div class="hb_single_room_details">

    <ul class="hb_single_room_tabs">

		<?php foreach ( $tabs as $key => $tab ): ?>
            <li>
                <a href="#<?php echo esc_attr( $tab['id'] ) ?>">
					<?php do_action( 'hotel_booking_single_room_before_tabs_' . $tab['id'] ); ?>
					<?php printf( '%s', $tab['title'] ) ?>
					<?php do_action( 'hotel_booking_single_room_after_tabs_' . $tab['id'] ); ?>
                </a>
            </li>

		<?php endforeach; ?>
    </ul>

	<?php
	// append after li tabs single
	do_action( 'hotel_booking_after_single_room_infomation' ); ?>

    <div class="hb_single_room_tabs_content">

		<?php foreach ( $tabs as $key => $tab ): ?>

            <div id="<?php echo esc_attr( $tab['id'] ) ?>" class="hb_single_room_tab_details">
				<?php do_action( 'hotel_booking_single_room_before_tabs_content_' . $tab['id'] ); ?>

				<?php printf( '%s', $tab['content'] ); ?>

				<?php do_action( 'hotel_booking_single_room_after_tabs_content_' . $tab['id'] ); ?>
            </div>

		<?php endforeach; ?>
    </div>

</div>
