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
$external_links_raw = get_post_meta( $room_id, '_hb_room_external_link', true );
$external_links = ! empty( $external_links_raw ) ? json_decode( $external_links_raw, true ) : '';
?>
<div class="button-group">
	<button class="wphb-add-external-button button button-primary" type="button"><?php esc_html_e( 'Add external link', 'wp-hotel-booking' ); ?><span class="dashicons dashicons-plus-alt2"></span></button>
	<button class="wphb-save-link-button button-secondary" data-id="<?php echo esc_attr( $room_id ); ?>" type="button"><?php esc_html_e( 'Save', 'wp-hotel-booking' ); ?><span class="dashicons dashicons-saved"></span></button>
	<input type="hidden" id="wphb_room_external_link" name="_hb_room_external_link" value="<?php echo esc_attr( $external_links_raw ); ?>"/>
</div>
<table class="wphb-room-external-link-table wp-list-table widefat striped fixed" >
	<thead>
		<th class="sort-column"></th>
		<th><?php esc_html_e( 'Icon', 'wp-hotel-booking' ); ?></th>
		<th><?php esc_html_e( 'Url', 'wp-hotel-booking' ); ?></th>
		<th><?php esc_html_e( 'Action', 'wp-hotel-booking' ); ?></th>
	</thead>
	<tbody>
		<tr class="wphb-sample-row" hidden>
			<td>
				<span class="dashicons dashicons-move"></span><input type="checkbox" name="enable-link"><label><?php esc_html_e( 'Enable', 'wp-hotel-booking' ) ?></label>
			</td>
			<td>
				<img src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4KPHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB3aWR0aD0iNTBweCIgaGVpZ2h0PSI1MHB4IiB2aWV3Qm94PSIwIDAgNTAgNTAiIHZlcnNpb249IjEuMSI+CiAgICA8ZyBpZD0ic3VyZmFjZTEiPgogICAgICAgIDxwYXRoIHN0eWxlPSIgc3Ryb2tlOm5vbmU7ZmlsbC1ydWxlOm5vbnplcm87ZmlsbDpyZ2IoMCUsMCUsMCUpO2ZpbGwtb3BhY2l0eToxOyIgZD0iTSAzNy41IDI2Ljc4NTE1NiBMIDM3LjUgMjMuMjE0ODQ0IEMgMzcuNSAyMi43MzA0NjkgMzcuMzI0MjE5IDIyLjMxMjUgMzYuOTY4NzUgMjEuOTU3MDMxIEMgMzYuNjE3MTg4IDIxLjYwNTQ2OSAzNi4xOTkyMTkgMjEuNDI5Njg4IDM1LjcxNDg0NCAyMS40Mjk2ODggTCAyOC41NzAzMTIgMjEuNDI5Njg4IEwgMjguNTcwMzEyIDE0LjI4NTE1NiBDIDI4LjU3MDMxMiAxMy44MDA3ODEgMjguMzk0NTMxIDEzLjM4MjgxMiAyOC4wNDI5NjkgMTMuMDMxMjUgQyAyNy42ODc1IDEyLjY3NTc4MSAyNy4yNjk1MzEgMTIuNSAyNi43ODUxNTYgMTIuNSBMIDIzLjIxNDg0NCAxMi41IEMgMjIuNzMwNDY5IDEyLjUgMjIuMzEyNSAxMi42NzU3ODEgMjEuOTU3MDMxIDEzLjAzMTI1IEMgMjEuNjA1NDY5IDEzLjM4MjgxMiAyMS40Mjk2ODggMTMuODAwNzgxIDIxLjQyOTY4OCAxNC4yODUxNTYgTCAyMS40Mjk2ODggMjEuNDI5Njg4IEwgMTQuMjg1MTU2IDIxLjQyOTY4OCBDIDEzLjgwMDc4MSAyMS40Mjk2ODggMTMuMzgyODEyIDIxLjYwNTQ2OSAxMy4wMzEyNSAyMS45NTcwMzEgQyAxMi42NzU3ODEgMjIuMzEyNSAxMi41IDIyLjczMDQ2OSAxMi41IDIzLjIxNDg0NCBMIDEyLjUgMjYuNzg1MTU2IEMgMTIuNSAyNy4yNjk1MzEgMTIuNjc1NzgxIDI3LjY4NzUgMTMuMDMxMjUgMjguMDQyOTY5IEMgMTMuMzgyODEyIDI4LjM5NDUzMSAxMy44MDA3ODEgMjguNTcwMzEyIDE0LjI4NTE1NiAyOC41NzAzMTIgTCAyMS40Mjk2ODggMjguNTcwMzEyIEwgMjEuNDI5Njg4IDM1LjcxNDg0NCBDIDIxLjQyOTY4OCAzNi4xOTkyMTkgMjEuNjA1NDY5IDM2LjYxNzE4OCAyMS45NTcwMzEgMzYuOTY4NzUgQyAyMi4zMTI1IDM3LjMyNDIxOSAyMi43MzA0NjkgMzcuNSAyMy4yMTQ4NDQgMzcuNSBMIDI2Ljc4NTE1NiAzNy41IEMgMjcuMjY5NTMxIDM3LjUgMjcuNjg3NSAzNy4zMjQyMTkgMjguMDQyOTY5IDM2Ljk2ODc1IEMgMjguMzk0NTMxIDM2LjYxNzE4OCAyOC41NzAzMTIgMzYuMTk5MjE5IDI4LjU3MDMxMiAzNS43MTQ4NDQgTCAyOC41NzAzMTIgMjguNTcwMzEyIEwgMzUuNzE0ODQ0IDI4LjU3MDMxMiBDIDM2LjE5OTIxOSAyOC41NzAzMTIgMzYuNjE3MTg4IDI4LjM5NDUzMSAzNi45Njg3NSAyOC4wNDI5NjkgQyAzNy4zMjQyMTkgMjcuNjg3NSAzNy41IDI3LjI2OTUzMSAzNy41IDI2Ljc4NTE1NiBaIE0gNDYuNDI5Njg4IDI1IEMgNDYuNDI5Njg4IDI4Ljg4NjcxOSA0NS40Njg3NSAzMi40NzI2NTYgNDMuNTU0Njg4IDM1Ljc1NzgxMiBDIDQxLjY0MDYyNSAzOS4wMzkwNjIgMzkuMDM5MDYyIDQxLjY0MDYyNSAzNS43NTc4MTIgNDMuNTU0Njg4IEMgMzIuNDcyNjU2IDQ1LjQ2ODc1IDI4Ljg4NjcxOSA0Ni40Mjk2ODggMjUgNDYuNDI5Njg4IEMgMjEuMTEzMjgxIDQ2LjQyOTY4OCAxNy41MjczNDQgNDUuNDY4NzUgMTQuMjQyMTg4IDQzLjU1NDY4OCBDIDEwLjk2MDkzOCA0MS42NDA2MjUgOC4zNTkzNzUgMzkuMDM5MDYyIDYuNDQ1MzEyIDM1Ljc1NzgxMiBDIDQuNTMxMjUgMzIuNDcyNjU2IDMuNTcwMzEyIDI4Ljg4NjcxOSAzLjU3MDMxMiAyNSBDIDMuNTcwMzEyIDIxLjExMzI4MSA0LjUzMTI1IDE3LjUyNzM0NCA2LjQ0NTMxMiAxNC4yNDIxODggQyA4LjM1OTM3NSAxMC45NjA5MzggMTAuOTYwOTM4IDguMzU5Mzc1IDE0LjI0MjE4OCA2LjQ0NTMxMiBDIDE3LjUyNzM0NCA0LjUzMTI1IDIxLjExMzI4MSAzLjU3MDMxMiAyNSAzLjU3MDMxMiBDIDI4Ljg4NjcxOSAzLjU3MDMxMiAzMi40NzI2NTYgNC41MzEyNSAzNS43NTc4MTIgNi40NDUzMTIgQyAzOS4wMzkwNjIgOC4zNTkzNzUgNDEuNjQwNjI1IDEwLjk2MDkzOCA0My41NTQ2ODggMTQuMjQyMTg4IEMgNDUuNDY4NzUgMTcuNTI3MzQ0IDQ2LjQyOTY4OCAyMS4xMTMyODEgNDYuNDI5Njg4IDI1IFogTSA0Ni40Mjk2ODggMjUgIiAvPgogICAgPC9nPgo8L3N2Zz4=" width="50" height="50" size="50" class="wphb-select-link-icon" alt="<?php esc_attr_e( 'Choose logo', 'wp-hotel-booking' ); ?>" title="<?php esc_attr_e( 'Choose logo', 'wp-hotel-booking' ); ?>"/>
				<input type="hidden" name="link-icon-id">
				<input type="hidden" name="link-icon-url">
			</td>
            <td><input type="text" name="link-value" value="" placeholder="<?php esc_html_e( 'Enter Url', 'wp-hotel-booking' ) ?>" /></td>
            <td><button class="delete-external-link button" type="button"><?php esc_html_e( 'Delete', 'wp-hotel-booking' ); ?></button></td>
		</tr>
		<?php if ( ! empty( $external_links ) ): ?>
			<?php foreach ( $external_links as $link ): ?>
				<tr class="wphb-single-external-link">
					<td>
						<span class="dashicons dashicons-move"></span><input type="checkbox" name="enable-link" <?php checked( $link['enable'], true ); ?>><label><?php esc_html_e( 'Enable', 'wp-hotel-booking' ) ?></label>
					</td>
					<td>
						<img size="50" width="50" height="50" src="<?php echo esc_url( $link['icon_url'] ); ?>" class="wphb-select-link-icon" alt="<?php esc_attr_e( 'Choose logo', 'wp-hotel-booking' ); ?>" title="<?php esc_attr_e( 'Choose logo', 'wp-hotel-booking' ); ?>"/>
						<input type="hidden" name="link-icon-id" value="<?php echo esc_attr( $link['icon_id'] ) ?>">
						<input type="hidden" name="link-icon-url" value="<?php echo esc_attr( $link['icon_url'] ) ?>">
					</td>
		            <td><input type="text" name="link-value" value="<?php echo esc_attr( $link['external_link'] ) ?>" placeholder="<?php esc_html_e( 'Enter Url', 'wp-hotel-booking' ) ?>" /></td>
		            <td><button class="delete-external-link button" type="button"><?php esc_html_e( 'Delete', 'wp-hotel-booking' ); ?></button></td>
				</tr>
			<?php endforeach ?>
		<?php endif; ?>
	</tbody>
</table>