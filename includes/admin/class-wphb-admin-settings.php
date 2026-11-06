<?php
/**
 * WP Hotel Booking admin settings.
 *
 * @version       1.9.6
 * @author        ThimPress
 * @package       WP_Hotel_Booking/Classes
 * @category      Classes
 * @author        Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

class WPHB_Admin_Settings {

	/**
	 * Cached setting page instances to prevent duplicate hook registration.
	 *
	 * @var array|null
	 */
	protected static $setting_pages = null;

	public static function get_settings_pages() {
		if ( null !== self::$setting_pages ) {
			return self::$setting_pages;
		}

		WP_Hotel_Booking::instance()->_include( 'includes/admin/class-wphb-admin-setting-page.php' );
		$tabs = array();

		// use WP_Hotel_Booking::instance() return null active hook
		$tabs[] = include 'settings/class-wphb-admin-setting-general.php';
		$tabs[] = include 'settings/class-wphb-admin-setting-pages.php';
		$tabs[] = include 'settings/class-wphb-admin-setting-emails.php';
		$tabs[] = include 'settings/class-wphb-admin-setting-payments.php';
		$tabs[] = include 'settings/class-wphb-admin-setting-room.php';
		$tabs[] = include 'settings/class-wphb-admin-setting-advanced.php';

		self::$setting_pages = apply_filters( 'hotel_booking_admin_setting_pages', $tabs );

		return self::$setting_pages;
	}

	// save setting
	public static function save() {
	}

	/**
	 * AJAX handler to save all settings without page reload.
	 */
	public static function ajax_save_settings() {
		$nonce = WPHB_Helpers::get_param( 'wphb_meta_box_settings_nonce', '' );
		if ( empty( $nonce ) || ! wp_verify_nonce( $nonce, 'wphb_update_meta_box_settings' ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Security check failed. Please refresh the page and try again.', 'wp-hotel-booking' ),
				),
				403
			);
		}

		if ( ! current_user_can( 'manage_hb_booking' ) && ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'You do not have permission to save settings.', 'wp-hotel-booking' ),
				),
				403
			);
		}

		$already_loaded              = ( null !== self::$setting_pages );
		$_GET['page']                = 'tp_hotel_booking_settings';
		$_POST['wphb_save_all_tabs'] = '1';

		$pages = self::get_settings_pages();
		if ( $already_loaded && is_array( $pages ) ) {
			foreach ( $pages as $page_obj ) {
				if ( is_object( $page_obj ) && method_exists( $page_obj, 'save' ) && ! empty( $page_obj->id ) ) {
					$page_obj->save( (string) $page_obj->id );
				}
			}
		}

		do_action( 'wphb_admin_settings_saved', $_POST );

		wp_send_json_success(
			array(
				'message' => __( 'Settings saved successfully.', 'wp-hotel-booking' ),
			)
		);
	}

	/**
	 * Get Dashicon class for a settings tab slug.
	 *
	 * @param string $slug Tab slug.
	 * @return string
	 */
	public static function get_tab_icon( $slug ) {
		$icons = apply_filters(
			'wphb_admin_settings_tab_icons',
			array(
				'general'     => 'dashicons-admin-settings',
				'pages'       => 'dashicons-admin-page',
				'emails'      => 'dashicons-email-alt',
				'payments'    => 'dashicons-cart',
				'room'        => 'dashicons-building',
				'advanced'    => 'dashicons-admin-tools',
				'woocommerce' => 'dashicons-store',
				'lightboxs'   => 'dashicons-format-gallery',
			)
		);

		return $icons[ $slug ] ?? 'dashicons-admin-generic';
	}

	// render field
	public static function render_fields( $fields = array() ) {
		if ( empty( $fields ) ) {
			return;
		}
		foreach ( $fields as $k => $field ) {
			$field = wp_parse_args(
				$field,
				array(
					'id'          => '',
					'class'       => '',
					'title'       => '',
					'desc'        => '',
					'default'     => '',
					'type'        => '',
					'placeholder' => '',
					'options'     => '',
					'atts'        => array(),
				)
			);

			$field_type          = sanitize_key( $field['type'] ?? '' );
			$field_id            = sanitize_key( $field['id'] ?? '' );
			$field_default_value = $field['default'] ?? '';
			$field_value         = WPHB_Settings::instance()->get( $field_id, $field_default_value );
			if ( empty( $field_id ) || empty( $field_type ) ) {
				continue;
			}

			$custom_attr = '';
			if ( ! empty( $field['atts'] ) ) {
				foreach ( $field['atts'] as $k => $val ) {
					$custom_attr .= $k . '="' . $val . '"';
				}
			}

			$search_keywords = strtolower( trim( ( $field['title'] ?? '' ) . ' ' . ( $field['desc'] ?? '' ) . ' ' . $field_id ) );
			$tr_classes      = array( 'wphb-setting-row', 'wphb-setting-row--' . $field_type );
			if ( ! empty( $field['trclass'] ) && is_array( $field['trclass'] ) ) {
				$tr_classes = array_merge( $tr_classes, $field['trclass'] );
			}
			$tr_class_attr = implode( ' ', array_filter( array_map( 'sanitize_html_class', $tr_classes ) ) );

			switch ( $field['type'] ) {
				case 'section_start':
					?>
					<div class="wphb-settings-card" data-section-card="<?php echo esc_attr( $field_id ); ?>">
						<?php if ( ! empty( $field['title'] ) || ! empty( $field['desc'] ) ) : ?>
							<div class="wphb-settings-card__header">
								<div class="wphb-settings-card__header-main">
									<?php if ( ! empty( $field['title'] ) ) : ?>
										<h3 class="wphb-settings-card__title"><?php echo esc_html( $field['title'] ); ?></h3>
									<?php endif; ?>
									<?php if ( ! empty( $field['desc'] ) ) : ?>
										<p class="description wphb-settings-card__desc"><?php echo esc_html( $field['desc'] ); ?></p>
									<?php endif; ?>
								</div>
							</div>
						<?php endif; ?>
						<div class="wphb-settings-card__body">
							<table class="form-table wphb-settings-table">
					<?php
					break;

				case 'section_end':
					?>
							<?php do_action( 'hotel_booking_setting_field_' . $field_id . '_end' ); ?>
							</table>
							<?php do_action( 'hotel_booking_setting_field_' . $field_id . '_after' ); ?>
						</div>
					</div>
					<?php
					break;

				case 'select':
				case 'multiselect':
					$selected = WPHB_Settings::instance()->get( $field_id, $field['default'] ?? array() );
					?>
					<tr valign="top" class="<?php echo esc_attr( $tr_class_attr ); ?>" data-search-text="<?php echo esc_attr( $search_keywords ); ?>">
						<th scope="row">
							<?php if ( ! empty( $field['title'] ) ) : ?>
								<label for="<?php echo esc_attr( $field_id ); ?>">
									<?php echo esc_html( $field['title'] ); ?>
								</label>
							<?php endif; ?>
						</th>
						<td class="hb-form-field hb-form-field-<?php echo esc_attr( $field['type'] ); ?>">
							<?php if ( isset( $field['options'] ) && is_array( $field['options'] ) ) : ?>
								<select name="<?php echo esc_attr( $field_id ); ?><?php echo esc_attr( $field['type'] === 'multiselect' ? '[]' : '' ); ?>"
										id="<?php echo esc_attr( $field_id ); ?>"
									<?php echo ( $field['type'] === 'multiple' || $field['type'] === 'multiselect' ) ? 'multiple="multiple"' : ''; ?>
								>
									<?php foreach ( $field['options'] as $val => $text ) : ?>
										<?php
										$is_selected = false;
										if ( is_array( $selected ) ) {
											$is_selected = in_array(
												(string) $val,
												array_map( 'strval', $selected ),
												true
											);
										} else {
											$is_selected = (string) $selected === (string) $val;
										}
										?>
										<option value="<?php echo esc_attr( $val ); ?>"
											<?php echo selected( $is_selected, true, false ); ?>
										>
											<?php echo esc_html( $text ); ?>
										</option>
									<?php endforeach; ?>
								</select>
							<?php endif; ?>
							<?php if ( ! empty( $field['desc'] ) ) : ?>
								<p class="description"><?php echo esc_html( $field['desc'] ); ?></p>
							<?php endif; ?>
						</td>
					</tr>
					<?php
					break;

				case 'text':
				case 'number':
				case 'email':
				case 'password':
					if ( 'number' === $field_type && '' === $field_value && '' !== $field_default_value ) {
						$field_value = $field_default_value;
					}
					?>
					<tr valign="top" class="<?php echo esc_attr( $tr_class_attr ); ?>" data-search-text="<?php echo esc_attr( $search_keywords ); ?>">
						<th scope="row">
							<?php if ( ! empty( $field['title'] ) ) : ?>
								<label for="<?php echo esc_attr( $field_id ); ?>">
									<?php echo esc_html( $field['title'] ); ?>
								</label>
							<?php endif; ?>
						</th>
						<td class="hb-form-field hb-form-field-<?php echo esc_attr( $field['type'] ); ?>">
							<input
									type="<?php echo esc_attr( $field['type'] ); ?>"
									id="<?php echo esc_attr( $field_id ); ?>"
									name="<?php echo esc_attr( $field['id'] ); ?>"
									value="<?php echo esc_attr( $field_value ); ?>"
									class="regular-text"
									placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>"
								<?php if ( $field_type === 'number' ) : ?>

									<?php echo isset( $field['min'] ) && is_numeric( $field['min'] ) ? ' min="' . esc_attr( $field['min'] ) . '"' : ''; ?>
									<?php echo isset( $field['max'] ) && is_numeric( $field['max'] ) ? ' max="' . esc_attr( $field['max'] ) . '"' : ''; ?>
									<?php echo isset( $field['step'] ) ? ' step="' . esc_attr( $field['step'] ) . '"' : ''; ?>

								<?php endif; ?>
							/>
							<?php if ( ! empty( $field['desc'] ) ) : ?>
								<p class="description"><?php echo esc_html( $field['desc'] ); ?></p>
							<?php endif; ?>
						</td>
					</tr>
					<?php
					break;

				case 'checkbox':
					?>
					<tr valign="top" class="<?php echo esc_attr( $tr_class_attr ); ?>" data-search-text="<?php echo esc_attr( $search_keywords ); ?>">
						<th scope="row">
							<?php if ( ! empty( $field['title'] ) ) : ?>
								<label for="<?php echo esc_attr( $field_id ); ?>">
									<?php echo esc_html( $field['title'] ); ?>
								</label>
							<?php endif; ?>
						</th>
						<td class="hb-form-field hb-form-field-<?php echo esc_attr( $field_type ); ?>">
							<label class="wphb-toggle-switch" for="<?php echo esc_attr( $field_id ); ?>">
								<input type="checkbox" id="<?php echo esc_attr( $field_id ); ?>" name="<?php echo esc_attr( $field_id ); ?>"
									value="" <?php checked( $field_value == 1 ? 1 : 0, 1 ); ?>
									<?php WPHB_Helpers::print( $custom_attr ); ?>
								/>
								<span class="wphb-toggle-slider"></span>
							</label>
							<?php if ( ! empty( $field['desc'] ) ) : ?>
								<p class="description"><?php echo esc_html( $field['desc'] ); ?></p>
							<?php endif; ?>
						</td>
					</tr>
					<?php
					break;

				case 'radio':
					$selected = hb_settings()->get( $field['id'], isset( $field['default'] ) ? $field['default'] : '' );
					?>
					<tr valign="top" class="<?php echo esc_attr( $tr_class_attr ); ?>" data-search-text="<?php echo esc_attr( $search_keywords ); ?>">
						<th scope="row">
							<?php if ( ! empty( $field['title'] ) ) : ?>
								<label for="<?php echo esc_attr( $field_id ); ?>">
									<?php echo esc_html( $field['title'] ); ?>
								</label>
							<?php endif; ?>
						</th>
						<td class="hb-form-field hb-form-field-<?php echo esc_attr( $field['type'] ); ?>">
							<?php if ( isset( $field['options'] ) && is_array( $field['options'] ) ) : ?>
								<div class="wphb-radio-group">
									<?php foreach ( $field['options'] as $val => $text ) : ?>
										<label class="wphb-radio-item">
											<input type="radio" name="<?php echo esc_attr( $field_id ); ?>" value="<?php echo esc_attr( $val ); ?>" <?php checked( $selected, $val ); ?>/>
											<span><?php echo esc_html( $text ); ?></span>
										</label>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>
							<?php if ( ! empty( $field['desc'] ) ) : ?>
								<p class="description"><?php echo esc_html( $field['desc'] ); ?></p>
							<?php endif; ?>
						</td>
					</tr>
					<?php
					break;

				case 'image_size':
					$width  = hb_settings()->get( "{$field_id}_width", $field['default']['width'] ?? 270 );
					$height = hb_settings()->get( "{$field_id}_height", $field['default']['height'] ?? 270 );
					?>
					<tr valign="top" class="<?php echo esc_attr( $tr_class_attr ); ?>" data-search-text="<?php echo esc_attr( $search_keywords ); ?>">
						<th scope="row">
							<?php if ( ! empty( $field['title'] ) ) : ?>
								<label for="<?php echo esc_attr( $field_id ); ?>_width">
									<?php echo esc_html( $field['title'] ); ?>
								</label>
							<?php endif; ?>
						</th>
						<td class="hb-form-field hb-form-field-<?php echo esc_attr( $field_type ); ?>">
							<?php if ( isset( $field['options'] ) ) : ?>
								<div class="wphb-image-size-group">
									<?php if ( isset( $field['options']['width'] ) ) : ?>
										<label class="wphb-size-input-box">
											<span class="wphb-size-label"><?php esc_html_e( 'W', 'wp-hotel-booking' ); ?></span>
											<input
													type="number"
													id="<?php echo esc_attr( $field_id ); ?>_width"
													name="<?php echo esc_attr( $field_id ); ?>_width"
													value="<?php echo esc_attr( $width ); ?>"
													min="0"
											/>
										</label>
									<?php endif; ?>
									<?php if ( isset( $field['options']['width'] ) && isset( $field['options']['height'] ) ) : ?>
										<span class="wphb-size-separator">&times;</span>
									<?php endif; ?>
									<?php if ( isset( $field['options']['height'] ) ) : ?>
										<label class="wphb-size-input-box">
											<span class="wphb-size-label"><?php esc_html_e( 'H', 'wp-hotel-booking' ); ?></span>
											<input
													type="number"
													id="<?php echo esc_attr( $field_id ); ?>_height"
													name="<?php echo esc_attr( $field_id ); ?>_height"
													value="<?php echo esc_attr( $height ); ?>"
													min="0"
											/>
										</label>
										<span class="wphb-size-unit">px</span>
									<?php endif; ?>
								</div>
							<?php endif; ?>
							<?php if ( ! empty( $field['desc'] ) ) : ?>
								<p class="description"><?php echo esc_html( $field['desc'] ); ?></p>
							<?php endif; ?>
						</td>
					</tr>
					<?php
					break;

				case 'textarea':
					$content = hb_settings()->get( $field['id'] );
					?>
					<tr valign="top" class="<?php echo esc_attr( $tr_class_attr ); ?>" data-search-text="<?php echo esc_attr( $search_keywords ); ?>">
						<th scope="row">
							<?php if ( ! empty( $field['title'] ) ) : ?>
								<label for="<?php echo esc_attr( $field_id ); ?>">
									<?php echo esc_html( $field['title'] ); ?>
								</label>
							<?php endif; ?>
						</th>
						<td class="hb-form-field hb-form-field-<?php echo esc_attr( $field['type'] ); ?>">
							<?php if ( isset( $field['id'] ) ) : ?>
								<?php wp_editor( $content, $field['id'], isset( $field['options'] ) ? $field['options'] : array() ); ?>
							<?php endif; ?>
							<?php if ( ! empty( $field['desc'] ) ) : ?>
								<p class="description"><?php echo esc_html( $field['desc'] ); ?></p>
							<?php endif; ?>
						</td>
					</tr>
					<?php
					break;

				case 'select_page':
					$selected = hb_settings()->get( $field['id'], 0 );
					?>
					<tr valign="top" class="<?php echo esc_attr( $tr_class_attr ); ?>" data-search-text="<?php echo esc_attr( $search_keywords ); ?>">
						<th scope="row">
							<?php if ( ! empty( $field['title'] ) ) : ?>
								<label for="<?php echo esc_attr( $field_id ); ?>">
									<?php echo esc_html( $field['title'] ); ?>
								</label>
							<?php endif; ?>
						</th>
						<td class="hb-form-field hb-form-field-<?php echo esc_attr( $field['type'] ); ?>">
							<div class="wphb-select-page-control">
								<div class="list-pages-wrapper">
									<?php if ( isset( $field['id'] ) ) : ?>
										<?php
										hb_dropdown_pages(
											array(
												'show_option_none'  => __( '---Select page---', 'wp-hotel-booking' ),
												'option_none_value' => '',
												'add_new_title'     => __( '[ Add new page ]', 'wp-hotel-booking' ),
												'add_new_value'     => 'add_new_page',
												'name'              => $field['id'],
												'selected'          => $selected,
											)
										);
										?>
									<?php endif; ?>
									<span class="wphb-or-divider"><?php echo esc_html( _x( 'or', 'drop down pages', 'wp-hotel-booking' ) ); ?></span>
									<button class="button button-quick-add-page" data-id="<?php echo esc_attr( $field['id'] ?? '' ); ?>" type="button">
										<span class="dashicons dashicons-plus-alt2"></span>
										<?php esc_html_e( 'Create new', 'wp-hotel-booking' ); ?>
									</button>
								</div>
								<p class="quick-add-page-inline <?php echo esc_attr( $field['id'] ?? '' ); ?> hide-if-js">
									<input type="text" placeholder="<?php esc_attr_e( 'New page title', 'wp-hotel-booking' ); ?>"/>
									<button class="button button-primary" type="button">
										<?php esc_html_e( 'Ok [Enter]', 'wp-hotel-booking' ); ?>
									</button>
									<a href="" class="button-link-cancel"><?php esc_html_e( 'Cancel [ESC]', 'wp-hotel-booking' ); ?></a>
								</p>
								<p class="quick-add-page-actions <?php echo esc_attr( $field['id'] ); ?><?php echo $selected ? '' : ' hide-if-js'; ?>">
									<a class="edit-page wphb-page-action-link" href="<?php echo esc_url( (string) get_edit_post_link( $selected ) ); ?>"
									target="_blank">
										<span class="dashicons dashicons-edit"></span>
										<?php esc_html_e( 'Edit page', 'wp-hotel-booking' ); ?>
									</a>
									<span class="wphb-action-sep">&#124;</span>
									<a class="view-page wphb-page-action-link" href="<?php echo esc_url( (string) get_permalink( $selected ) ); ?>"
									target="_blank">
										<span class="dashicons dashicons-external"></span>
										<?php esc_html_e( 'View page', 'wp-hotel-booking' ); ?>
									</a>
								</p>
							</div>
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

		$selected_tab_title = $tabs[ $selected_tab ] ?? '';
		?>
		<div class="wrap wphb-settings-wrap">
			<h1 class="screen-reader-text"><?php esc_html_e( 'WP Hotel Booking Settings', 'wp-hotel-booking' ); ?></h1>

			<form method="post" action="" enctype="multipart/form-data" name="hb-admin-settings-form" id="wphb-admin-settings-form" class="wphb-settings-form">
				<input type="hidden" name="wphb_save_all_tabs" value="1" />
				<?php wp_nonce_field( 'wphb_update_meta_box_settings', 'wphb_meta_box_settings_nonce' ); ?>

				<!-- Sticky Top Header Bar -->
				<div class="wphb-settings-sticky-bar" id="wphb-settings-sticky-bar">
					<div class="wphb-settings-sticky-bar__left">
						<div class="wphb-settings-brand">
							<span class="dashicons dashicons-calendar-alt wphb-settings-brand__icon"></span>
							<div class="wphb-settings-brand__text">
								<span class="wphb-settings-brand__name"><?php esc_html_e( 'WP Hotel Booking', 'wp-hotel-booking' ); ?></span>
								<span class="wphb-settings-brand__sep">/</span>
								<span class="wphb-settings-brand__current" id="wphb-current-tab-title"><?php echo esc_html( $selected_tab_title ); ?></span>
							</div>
						</div>
					</div>

					<div class="wphb-settings-sticky-bar__right">
						<div class="wphb-settings-search" id="wphb-settings-search-wrap">
							<span class="dashicons dashicons-search wphb-settings-search__icon"></span>
							<input
								type="search"
								id="wphb-settings-search-input"
								class="wphb-settings-search__input"
								placeholder="<?php esc_attr_e( 'Search', 'wp-hotel-booking' ); ?>"
								autocomplete="off"
							/>
							<button type="button" class="wphb-settings-search__clear" id="wphb-settings-search-clear" hidden aria-label="<?php esc_attr_e( 'Clear search', 'wp-hotel-booking' ); ?>">
								<span class="dashicons dashicons-no-alt"></span>
							</button>
						</div>

						<span class="wphb-unsaved-badge" id="wphb-unsaved-badge" hidden>
							<span class="wphb-unsaved-dot"></span>
							<?php esc_html_e( 'Unsaved changes', 'wp-hotel-booking' ); ?>
						</span>

						<button type="submit" class="button button-primary wphb-btn-save" id="wphb-btn-save-top">
							<span class="wphb-btn-save__spinner" aria-hidden="true"></span>
							<span class="wphb-btn-save__label" data-default-label="<?php esc_attr_e( 'Save Settings', 'wp-hotel-booking' ); ?>" data-saving-label="<?php esc_attr_e( 'Saving...', 'wp-hotel-booking' ); ?>" data-saved-label="<?php esc_attr_e( 'Saved!', 'wp-hotel-booking' ); ?>">
								<?php esc_html_e( 'Save Settings', 'wp-hotel-booking' ); ?>
							</span>
						</button>
					</div>
				</div>

				<!-- Main 2-Column Workspace -->
				<div class="wphb-settings-workspace">
					<!-- Left Sidebar Navigation -->
					<aside class="wphb-settings-sidebar" aria-label="<?php esc_attr_e( 'Settings Sections', 'wp-hotel-booking' ); ?>">
						<h2 class="nav-tab-wrapper wphb-nav-tabs">
							<?php
							if ( $tabs ) :
								foreach ( $tabs as $slug => $title ) :
									$is_active = ( $selected_tab === $slug );
									$icon      = self::get_tab_icon( $slug );
									?>
									<a class="nav-tab wphb-nav-tab<?php echo esc_attr( $is_active ? ' nav-tab-active is-active' : '' ); ?>"
										href="?page=tp_hotel_booking_settings&tab=<?php echo esc_attr( $slug ); ?>"
										data-tab="<?php echo esc_attr( $slug ); ?>"
										data-tab-title="<?php echo esc_attr( $title ); ?>">
										<span class="dashicons <?php echo esc_attr( $icon ); ?> wphb-nav-tab__icon"></span>
										<span class="wphb-nav-tab__label"><?php echo esc_html( $title ); ?></span>
										<span class="wphb-nav-tab__badge" data-tab-match-count="<?php echo esc_attr( $slug ); ?>" hidden>0</span>
									</a>
									<?php
								endforeach;
							endif;
							?>
						</h2>
					</aside>

					<!-- Right Content Canvas (All Tabs Pre-rendered for 0ms Switching) -->
					<div class="wphb-settings-canvas">
						<div class="wphb-search-status-banner" id="wphb-search-status-banner" hidden>
							<span class="dashicons dashicons-filter"></span>
							<span id="wphb-search-status-text"></span>
							<button type="button" class="button-link" id="wphb-search-reset-btn"><?php esc_html_e( 'Reset filter', 'wp-hotel-booking' ); ?></button>
						</div>

						<?php
						if ( $tabs ) :
							foreach ( $tabs as $slug => $title ) :
								$is_active = ( $selected_tab === $slug );
								?>
								<div class="wphb-settings-tab-panel<?php echo esc_attr( $is_active ? ' is-active' : '' ); ?>"
									id="wphb-tab-panel-<?php echo esc_attr( $slug ); ?>"
									data-tab-panel="<?php echo esc_attr( $slug ); ?>"
									data-tab-title="<?php echo esc_attr( $title ); ?>">
									<?php do_action( 'hb_admin_settings_tab_before', $slug ); ?>
									<?php do_action( 'hb_admin_settings_sections_' . $slug ); ?>
									<?php do_action( 'hb_admin_settings_tab_' . $slug ); ?>
									<?php wp_nonce_field( 'hb_admin_settings_tab_' . $slug, 'hb_admin_settings_tab_' . $slug . '_field' ); ?>
									<?php do_action( 'hb_admin_settings_tab_after', $slug ); ?>
								</div>
								<?php
							endforeach;
						endif;
						?>

						<div class="wphb-settings-footer">
							<button type="submit" class="button button-primary wphb-btn-save" id="wphb-btn-save-bottom">
								<span class="wphb-btn-save__spinner" aria-hidden="true"></span>
								<span class="wphb-btn-save__label"><?php esc_html_e( 'Save Settings', 'wp-hotel-booking' ); ?></span>
							</button>
						</div>
					</div>
				</div>

				<!-- Floating Toast Notification -->
				<div class="wphb-settings-toast" id="wphb-settings-toast" role="status" aria-live="polite" hidden>
					<span class="dashicons dashicons-yes-alt wphb-settings-toast__icon"></span>
					<span class="wphb-settings-toast__message" id="wphb-settings-toast-message"></span>
				</div>
			</form>
		</div>
		<?php
	}
}

add_action( 'wp_ajax_wphb_save_admin_settings', array( 'WPHB_Admin_Settings', 'ajax_save_settings' ) );
