<?php
namespace WPHB\TemplateHooks\Admin;

use Exception;
use WPHB_Settings;
use WPHB\Helpers\Singleton;
use WPHB\Helpers\Template;
/**
 * AdminExterlinkIconSetting
 */
class AdminExternalLinkIconSetting {
	use Singleton;

	public function init() {
		add_action( 'hotel_booking_setting_field_tp_hotel_booking_external_link_settings', array( $this, 'layout' ) );
	}

	public function layout( $field ) {
		try {
			if ( ! did_action( 'wp_enqueue_media' ) ) {
				wp_enqueue_media();
			}
			wp_enqueue_script(
				'wphb-icon-external-link-upload',
				WPHB_PLUGIN_URL . '/assets/js/admin/icon-external-link.js',
				array(),
				uniqid(),
				array(
					'strategy'  => 'defer',
					'in_footer' => 1,
				)
			);
			$localize = array(
				'uploader_title'       => __( 'Select Images', 'wp-hotel-booking' ),
				'uploader_button_text' => __( 'Add to Gallery', 'wp-hotel-booking' ),
				'remove_button_title'  => __( 'Remove', 'wp-hotel-booking' ),
			);
			wp_localize_script( 'wphb-icon-external-link-upload', 'wphbIconExternalLinkSettings', $localize );
			$field_title   = $this->field_title( $field );
			$field_content = $this->field_content( $field );
			$sections      = array(
				'wrap'     => '<tr valign="top">',
				'title'    => $field_title,
				'content'  => $field_content,
				'wrap_end' => '</tr>',
			);

			echo Template::combine_components( $sections );
		} catch ( Exception $e ) {
			echo 'Error: ' . $e->getMessage();
		}
	}

	public function field_title( $field ) {
		return sprintf( '<th scope="row"><label>%s</label</th>', esc_html( $field['title'] ) );
	}

	public function field_content( $field ) {
		$setting      = WPHB_Settings::instance()->get( 'external_link_settings' );
		$link_setting = ! empty( $setting ) ? json_decode( $setting, true ) : [];
		$header_row   = sprintf( '<tr class="header-row"><td>%1$s</td><td>%2$s</td><td>%3$s</td><td></td></tr>',
			__( 'Icon', 'wp-hotel-booking' ),
			__( 'Title', 'wp-hotel-booking' ),
			__( 'Url', 'wp-hotel-booking' )
		);
		$fields_html = '';
		if ( ! empty( $link_setting ) ) {
			foreach ( $link_setting as $field_id => $link ) {
				$fields_html .= $this->render_setting_field( $link, $field_id );
			}
		}
		
		$section    = array(
			'wrap'        => '<td class="hb-form-field">',
			'button'      => sprintf( '<button class="button button-primary wphb-external-link-add-new" type="button">%s</button><p></p>', __( 'Add Link', 'wp-hotel-booking' ) ),
			'table'       => '<table class="wphb-external-link-table wp-list-table widefat striped" id="wphb-external-link-table">',
			'header_row'  => $header_row,
			'sample_row'  => $this->sample_row(),
			'fields'      => $fields_html,
			'table_end'   => '</table>',
			'input_field' => sprintf( '<input type="hidden" id="%1$s" name="%1$s" value="%2$s" />', $field['id'], $setting ),
			'wrap_end'    => '</td>',
		);
		return Template::combine_components( $section );
	}

	public function render_setting_field( $link, $field_id ) {
		$icon_url = ! empty( $link['icon_url'] ) ? $link['icon_url'] : WPHB_PLUGIN_URL . '/assets/images/plus-circle-50.png';
		return sprintf(
			'<tr class="wphb-single-external-link" data-id="%1$s">
				<td>
					<img src="%2$s" width="50" height="50" size="50" class="wphb-select-icon" alt="%3$s" title="%3$s"/>
					<input type="hidden" name="icon-url" value="%2$s">
					<input type="hidden" name="icon-id" value="%4$s">
				</td>
	            <td><input type="text" name="title" value="%5$s" /></td>
	            <td><input type="text" name="url" value="%6$s" /></td>
	            <td><button class="delete-external-link button" type="button">%7$s</button></td>
			</tr>',
			$field_id,
			esc_url( $icon_url ),
			__( 'Choose logo', 'wp-hotel-booking' ),
			$link['icon_id'],
			$link['title'],
			$link['external_link'],
			__( 'Delete', 'wp-hotel-booking' )
		);
	}

	public function sample_row() {
		ob_start();
		?>
		<tr class="wphb-sample-row" hidden>
			<td>
				<img src="<?php echo esc_url( WPHB_PLUGIN_URL . '/assets/images/plus-circle-50.png'); ?>" width="50" height="50" size="50" class="wphb-select-icon" alt="<?php esc_attr_e( 'Choose logo', 'wp-hotel-booking' ); ?>" title="<?php esc_attr_e( 'Choose logo', 'wp-hotel-booking' ); ?>"/>
				<input type="hidden" name="icon-id">
				<input type="hidden" name="icon-url">
			</td>
            <td><input type="text" name="title" value="" placeholder="<?php esc_html_e( 'Enter title', 'wp-hotel-booking' ) ?>" /></td>
            <td><input type="text" name="url" value="" placeholder="<?php esc_html_e( 'Enter Url', 'wp-hotel-booking' ) ?>" /></td>
            <td><button class="delete-external-link button" type="button"><?php esc_html_e( 'Delete', 'wp-hotel-booking' ); ?></button></td>
		</tr>
		<?php
		return ob_get_clean();
	}
}
