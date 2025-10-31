<?php
/**
 * Admin View: External link tab
 *
 * @version     2.2.4
 * @package     WP_Hotel_Booking/Views
 * @category    Views
 * @author      Thimpress
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

$room_id = $post->ID;
if ( empty( $room_id ) ) {
	return;
}
$hb_extenal_link_settings = WPHB_Settings::instance()->get( 'external_link_settings' );

$setting_fields   = ! empty( $hb_extenal_link_settings ) ? json_decode( $hb_extenal_link_settings, true ) : array();
$default_icon_url = WPHB_PLUGIN_URL . '/assets/images/icon-128x128.png';

$room_external_link_settings     = get_post_meta( $room_id, '_hb_room_external_link', true );
$room_external_link_settings_arr = ! empty( $room_external_link_settings ) ? json_decode( $room_external_link_settings, true ) : array();

$counter = 0;
?>
<div><p class="description"><?php esc_html_e( 'Get more bookings from multiple sources. Connect your room with other OTA (Online Travel Agency) platforms. Guests will be redirected to these platforms instead of using the built-in booking system on website.', 'wp-hotel-booking' ) ?></p></div>
<div class="button-group">
	<a class="wphb-add-external-button button button-primary" href="<?php echo esc_url( admin_url(
		'admin.php?page=tp_hotel_booking_settings&tab=room#wphb-external-link-table'
		) ); ?>" target="_blank"><?php esc_html_e( 'Add New External OTA Platforms', 'wp-hotel-booking' ); ?><span class="dashicons dashicons-plus-alt2"></span>
	</a>
	<input type="hidden" name="_hb_room_external_link" id="_hb_room_external_link" value="<?php echo esc_attr( $room_external_link_settings ); ?>">
</div>
<table class="wphb-room-external-link-table wp-list-table widefat striped" >
	<thead>
		<th class="sort-column"></th>
		<th><?php esc_html_e( 'Icon', 'wp-hotel-booking' ); ?></th>
		<th><?php esc_html_e( 'Title', 'wp-hotel-booking' ); ?></th>
		<th><?php esc_html_e( 'Url', 'wp-hotel-booking' ); ?></th>
	</thead>
	<tbody>
		<?php if ( ! empty( $setting_fields ) ): ?>
			<?php foreach ( $setting_fields as $field_id => $field ): ?>
				<?php 
				$enabled       = false;
				$external_link = $field['external_link'];
				$order         = $counter + 1;
				if ( isset( $room_external_link_settings_arr[ $field_id ] ) && ! empty( $room_external_link_settings_arr[ $field_id ] ) ) {
					$single_link   = $room_external_link_settings_arr[ $field_id ];
					$enabled       = $single_link['enabled'];
					$external_link = $single_link['external_link'] ? $single_link['external_link'] : $field['external_link'];
					$order         = $single_link['order'] ? $single_link['order'] : $order;
				}
				 ?>
				<tr class="wphb-single-external-link" data-id="<?php echo esc_attr( $field_id ); ?>" data-order="<?php echo esc_attr( $order ); ?>">
					<td>
						<span class="dashicons dashicons-move"></span><input type="checkbox" name="enable-link" <?php checked( $enabled, true ); ?>><label><?php esc_html_e( 'Enable', 'wp-hotel-booking' ); ?></label>
					</td>
					<td>
						<img size="50" width="50" height="50" src="<?php echo esc_url( $field['icon_url'] ?: $default_icon_url ); ?>" class="wphb-select-link-icon" alt=""/>
						<input type="hidden" name="link-icon-id" value="<?php echo esc_attr( $field['icon_id'] ?? 0 ); ?>">
						<input type="hidden" name="link-icon-url" value="<?php echo esc_attr( $field['icon_url'] ?? '' ); ?>">
					</td>
		            <td><?php echo esc_html( $field['title'] ?? '' ); ?></td>
		            <td><input type="text" name="external_link" value="<?php echo esc_attr( $external_link ); ?>"></td>
				</tr>
			<?php endforeach ?>
		<?php endif; ?>
	</tbody>
</table>