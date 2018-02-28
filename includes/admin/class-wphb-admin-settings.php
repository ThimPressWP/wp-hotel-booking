<?php
/**
 * @Author: ducnvtt
 * @Date:   2016-03-29 15:07:16
 * @Last Modified by:   someone
 * @Last Modified time: 2016-05-13 11:31:15
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class WPHB_Admin_Settings {

	public static function get_settings_pages() {

		WP_Hotel_Booking::instance()->_include( 'includes/admin/class-wphb-admin-setting-page.php' );
		$tabs = array();

		// use WP_Hotel_Booking::instance() return null active hook
		$tabs[] = include 'settings/class-wphb-admin-setting-general.php';
		$tabs[] = include 'settings/class-wphb-admin-setting-hotel-info.php';
		$tabs[] = include 'settings/class-wphb-admin-setting-emails.php';
		$tabs[] = include 'settings/class-wphb-admin-setting-payments.php';
		$tabs[] = include 'settings/class-wphb-admin-setting-room.php';

		return apply_filters( 'hotel_booking_admin_setting_pages', $tabs );
	}

	// save setting
	public static function save() {

	}

	// render field
	public static function render_fields( $fields = array() ) {
		if ( empty( $fields ) ) {
			return;
		}
		foreach ( $fields as $k => $field ) {
			$field = wp_parse_args( $field, array(
				'id'          => '',
				'class'       => '',
				'title'       => '',
				'desc'        => '',
				'default'     => '',
				'type'        => '',
				'placeholder' => '',
				'options'     => '',
				'atts'        => array()
			) );

			$custom_attr    = '';
			if ( ! empty( $field['atts'] ) ) {
				foreach ( $field['atts'] as $k => $val ) {
					$custom_attr .= $k . '="' . $val . '"';
				}
			}
			switch ( $field['type'] ) {
				case 'section_start':
					?>
					<?php if ( isset( $field['title'] ) ) : ?>
                    <h3><?php echo esc_html( $field['title'] ); ?></h3>
					<?php if ( isset( $field['desc'] ) ) : ?>
                        <p class="description"><?php echo esc_html( $field['desc'] ) ?></p>
					<?php endif; ?>
                    <table class="form-table">
				<?php endif; ?>
					<?php
					break;

				case 'section_end':
					?>
					<?php do_action( 'hotel_booking_setting_field_' . $field['id'] . '_end' ); ?>
                    </table>
					<?php do_action( 'hotel_booking_setting_field_' . $field['id'] . '_after' ); ?>
					<?php
					break;

				case 'select':
				case 'multiselect':
					$selected = hb_settings()->get( $field['id'], isset( $field['default'] ) ? $field['default'] : array() );
					?>
                    <tr valign="top">
                        <th scope="row">
							<?php if ( isset( $field['title'] ) ) : ?>
                                <label for="<?php echo isset( $field['id'] ) ? esc_attr( $field['id'] ) : '' ?>">
									<?php echo esc_html( $field['title'] ) ?>
                                </label>
							<?php endif; ?>
                        </th>
                        <td class="hb-form-field hb-form-field-<?php echo esc_attr( $field['type'] ) ?>">
							<?php if ( isset( $field['options'] ) ) : ?>
                                <select name="<?php echo isset( $field['id'] ) ? esc_attr( $field['id'] ) : '' ?><?php echo $field['type'] === 'multiselect' ? '[]' : '' ?>"
                                        id="<?php echo isset( $field['id'] ) ? esc_attr( $field['id'] ) : '' ?>"
									<?php echo ( $field['type'] === 'multiple' ) ? 'multiple="multiple"' : '' ?>
                                >
									<?php foreach ( $field['options'] as $val => $text ) : ?>
                                        <option value="<?php echo esc_attr( $val ) ?>"
											<?php echo ( is_array( $selected ) && in_array( $val, $selected ) ) || $selected === $val ? ' selected' : '' ?>
                                        >
											<?php echo esc_html( $text ) ?>
                                        </option>
									<?php endforeach; ?>
                                </select>
							<?php endif; ?>
							<?php if ( isset( $field['desc'] ) ) : ?>
                                <p class="description"><?php echo esc_html( $field['desc'] ) ?></p>
							<?php endif; ?>
                        </td>
                    </tr>
					<?php
					break;

				case 'text':
				case 'number':
				case 'email':
				case 'password':
					$value = hb_settings()->get( $field['id'] );
					?>
                    <tr valign="top">
                        <th scope="row">
							<?php if ( isset( $field['title'] ) ) : ?>
                                <label for="<?php echo isset( $field['id'] ) ? esc_attr( $field['id'] ) : '' ?>">
									<?php echo esc_html( $field['title'] ) ?>
                                </label>
							<?php endif; ?>
                        </th>
                        <td class="hb-form-field hb-form-field-<?php echo esc_attr( $field['type'] ) ?>">
                            <input
                                    type="<?php echo esc_attr( $field['type'] ) ?>"
                                    name="<?php echo esc_attr( $field['id'] ) ?>"
                                    value="<?php echo esc_attr( $value ) ?>"
                                    class="regular-text"
                                    placeholder="<?php echo esc_attr( $field['placeholder'] ) ?>"
								<?php if ( $field['type'] === 'number' ) : ?>

									<?php echo isset( $field['min'] ) && is_numeric( $field['min'] ) ? ' min="' . esc_attr( $field['min'] ) . '"' : '' ?>
									<?php echo isset( $field['max'] ) && is_numeric( $field['max'] ) ? ' max="' . esc_attr( $field['max'] ) . '"' : '' ?>
									<?php echo isset( $field['step'] ) ? ' step="' . esc_attr( $field['step'] ) . '"' : '' ?>

								<?php endif; ?>
                            />
							<?php if ( isset( $field['desc'] ) ) : ?>
                                <p class="description"><?php echo esc_html( $field['desc'] ) ?></p>
							<?php endif; ?>
                        </td>
                    </tr>
					<?php
					break;

				case 'checkbox':
					$val = hb_settings()->get( $field['id'] );
					?>
                    <tr valign="top"<?php echo isset( $field['trclass'] ) ? ' class="' . implode( '', $field['trclass'] ) . '"' : '' ?>>
                        <th scope="row">
							<?php if ( isset( $field['title'] ) ) : ?>
                                <label for="<?php echo isset( $field['id'] ) ? esc_attr( $field['id'] ) : '' ?>">
									<?php echo esc_html( $field['title'] ) ?>
                                </label>
							<?php endif; ?>
                        </th>
                        <td class="hb-form-field hb-form-field-<?php echo esc_attr( $field['type'] ) ?>">
                            <input type="hidden"
                                   name="<?php echo isset( $field['id'] ) ? esc_attr( $field['id'] ) : '' ?>"
                                   value="0"/>
                            <input type="checkbox"
                                   name="<?php echo isset( $field['id'] ) ? esc_attr( $field['id'] ) : '' ?>"
                                   value="1" <?php echo $custom_attr ?><?php checked( $val, $field['default'] ); ?>/>
							<?php if ( isset( $field['desc'] ) ) : ?>
                                <p class="description"><?php echo esc_html( $field['desc'] ) ?></p>
							<?php endif; ?>
                        </td>
                    </tr>
					<?php
					break;

				case 'radio':
					$selected = hb_settings()->get( $field['id'], isset( $field['default'] ) ? $field['default'] : '' );
					?>
                    <tr valign="top">
                        <th scope="row">
							<?php if ( isset( $field['title'] ) ) : ?>
                                <label for="<?php echo isset( $field['id'] ) ? esc_attr( $field['id'] ) : '' ?>">
									<?php echo esc_html( $field['title'] ) ?>
                                </label>
							<?php endif; ?>
                        </th>
                        <td class="hb-form-field hb-form-field-<?php echo esc_attr( $field['type'] ) ?>">
							<?php if ( isset( $field['options'] ) ) : ?>
								<?php foreach ( $field['options'] as $val => $text ) : ?>

                                    <label>
                                        <input type="radio"
                                               name="<?php echo isset( $field['id'] ) ? esc_attr( $field['id'] ) : '' ?>"<?php selected( $selected, $val ); ?>/>
										<?php echo esc_html( $text ) ?>
                                    </label>

								<?php endforeach; ?>
							<?php endif; ?>
							<?php if ( isset( $field['desc'] ) ) : ?>
                                <p class="description"><?php echo esc_html( $field['desc'] ) ?></p>
							<?php endif; ?>
                        </td>
                    </tr>
					<?php
					break;

				case 'image_size':
					$width = hb_settings()->get( $field['id'] . '_width', isset( $field['default']['width'] ) ? $field['default']['width'] : 270 );
					$height = hb_settings()->get( $field['id'] . '_height', isset( $field['default']['height'] ) ? $field['default']['height'] : 270 );
					?>
                    <tr valign="top">
                        <th scope="row">
							<?php if ( isset( $field['title'] ) ) : ?>
                                <label for="<?php echo isset( $field['id'] ) ? esc_attr( $field['id'] ) : '' ?>">
									<?php echo esc_html( $field['title'] ) ?>
                                </label>
							<?php endif; ?>
                        </th>
                        <td class="hb-form-field hb-form-field-<?php echo esc_attr( $field['type'] ) ?>">
							<?php if ( isset( $field['id'] ) && isset( $field['options'] ) ) : ?>

								<?php if ( isset( $field['options']['width'] ) ) : ?>
                                    <input
                                            type="number"
                                            name="<?php echo esc_attr( $field['id'] ) ?>_width"
                                            value="<?php echo esc_attr( $width ) ?>"
                                    /> x
								<?php endif; ?>
								<?php if ( isset( $field['options']['height'] ) ) : ?>
                                    <input
                                            type="number"
                                            name="<?php echo esc_attr( $field['id'] ) ?>_height"
                                            value="<?php echo esc_attr( $height ) ?>"
                                    /> px
								<?php endif; ?>
							<?php endif; ?>
							<?php if ( isset( $field['desc'] ) ) : ?>
                                <p class="description"><?php echo esc_html( $field['desc'] ) ?></p>
							<?php endif; ?>
                        </td>
                    </tr>
					<?php
					break;

				case 'textarea':
					$content = hb_settings()->get( $field['id'] );
					?>
                    <tr valign="top">
                        <th scope="row">
							<?php if ( isset( $field['title'] ) ) : ?>
                                <label for="<?php echo isset( $field['id'] ) ? esc_attr( $field['id'] ) : '' ?>">
									<?php echo esc_html( $field['title'] ) ?>
                                </label>
							<?php endif; ?>
                        </th>
                        <td class="hb-form-field hb-form-field-<?php echo esc_attr( $field['type'] ) ?>">
							<?php if ( isset( $field['id'] ) ) : ?>
								<?php wp_editor( $content, $field['id'], isset( $field['options'] ) ? $field['options'] : array() ); ?>
							<?php endif; ?>
							<?php if ( isset( $field['desc'] ) ) : ?>
                                <p class="description"><?php echo esc_html( $field['desc'] ) ?></p>
							<?php endif; ?>
                        </td>
                    </tr>
					<?php
					break;

				case 'select_page':
					$selected = hb_settings()->get( $field['id'], 0 );
					?>
                    <tr valign="top">
                        <th scope="row">
							<?php if ( isset( $field['title'] ) ) : ?>
                                <label for="<?php echo isset( $field['id'] ) ? esc_attr( $field['id'] ) : '' ?>">
									<?php echo esc_html( $field['title'] ) ?>
                                </label>
							<?php endif; ?>
                        </th>
                        <td class="hb-form-field hb-form-field-<?php echo esc_attr( $field['type'] ) ?>">
							<?php if ( isset( $field['id'] ) ) : ?>
								<?php
								hb_dropdown_pages(
									array(
										'show_option_none'  => __( '---Select page---', 'wp-hotel-booking' ),
										'option_none_value' => 0,
										'name'              => $field['id'],
										'selected'          => $selected
									)
								);
								?>
							<?php endif; ?>
							<?php if ( isset( $field['desc'] ) ) : ?>
                                <p class="description"><?php echo esc_html( $field['desc'] ) ?></p>
							<?php endif; ?>
                        </td>
                    </tr>
					<?php
					break;

				default:
					do_action( 'hotel_booking_setting_field_' . $field['id'], $field );
					break;
			}
		}
	}

	// save field settings
	public static function save_fields( $options = array() ) {

	}

	// output page settings
	public static function output() {
		self::get_settings_pages();
		$tabs         = hb_admin_settings_tabs();
		$selected_tab = ! empty( $_REQUEST['tab'] ) ? sanitize_text_field( $_REQUEST['tab'] ) : '';

		if ( ! array_key_exists( $selected_tab, $tabs ) ) {
			$tab_keys     = array_keys( $tabs );
			$selected_tab = reset( $tab_keys );
		}

		?>
        <div class="wrap">
            <h2 class="nav-tab-wrapper">
				<?php if ( $tabs ) :
					foreach ( $tabs as $slug => $title ) { ?>
                        <a class="nav-tab<?php echo sprintf( '%s', $selected_tab == $slug ? ' nav-tab-active' : '' ); ?>"
                           href="?page=tp_hotel_booking_settings&tab=<?php echo esc_attr( $slug ); ?>">
							<?php echo esc_html( $title ); ?>
                        </a>
					<?php } endif; ?>
            </h2>
            <form method="post" action="" enctype="multipart/form-data" name="hb-admin-settings-form">

				<?php do_action( 'hb_admin_settings_tab_before', $selected_tab ); ?>
				<?php do_action( 'hb_admin_settings_sections_' . $selected_tab ); ?>
				<?php do_action( 'hb_admin_settings_tab_' . $selected_tab ); ?>
				<?php wp_nonce_field( 'hb_admin_settings_tab_' . $selected_tab, 'hb_admin_settings_tab_' . $selected_tab . '_field' ); ?>
				<?php do_action( 'hb_admin_settings_tab_after', $selected_tab ); ?>
                <div class="clearfix"></div>
                <p class="clearfix">
                    <button class="button button-primary"><?php _e( 'Update', 'wp-hotel-booking' ); ?></button>
                </p>

            </form>
        </div>
		<?php
	}

}
