<?php
/**
 * Admin View: Other settings view.
 *
 * @version     1.9.7
 * @package     WP_Hotel_Booking/Views
 * @category    Views
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

$settings = apply_filters( 'hotel_booking_addon_menus', array() );
?>

<div id="tp_hotel_booking_other_settings">
	<ul class="tp_hotel_booking_tabs_settings">
		<?php foreach( $settings as $k => $v ): ?>

			<li>
				<a href="#<?php echo esc_attr( $k ); ?>"><?php printf( '%s', $v ) ?></a>
			</li>

		<?php endforeach; ?>
	</ul>
	<div class="tp_hotel_booking_settings_content">
		<?php foreach( $settings as $k => $v ): ?>

			<div class="tp_hotel_booking_setting_fields" id="<?php echo esc_attr( $k ); ?>">
				<?php do_action( $k ); ?>
			</div>

		<?php endforeach; ?>
	</div>
</div>