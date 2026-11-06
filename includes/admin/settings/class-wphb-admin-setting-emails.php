<?php
/**
 * WP Hotel Booking admin setting emails.
 *
 * @version     2.3.5
 * @author      ThimPress
 * @package     WP_Hotel_Booking/Classes
 * @category    Classes
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPHB_Admin_Setting_Emails' ) ) {
	class WPHB_Admin_Setting_Emails extends WPHB_Admin_Setting_Page {

		public $id = 'emails';

		public $title = null;

		function __construct() {

			$this->title = __( 'Emails', 'wp-hotel-booking' );

			parent::__construct();

			add_action( 'hotel_booking_setting_field_wphb_email_live_preview_box', array( $this, 'render_email_live_preview_field' ) );
		}

		public function get_settings( $section = '' ) {
			$sections = $this->get_sections();
			if ( empty( $section ) ) {
				$section = 'email-options';
				if ( isset( $_REQUEST['section'] ) && array_key_exists( $_REQUEST['section'], $sections ) ) {
					$section = sanitize_text_field( $_REQUEST['section'] );
				}
			}

			$settings = array(
				array(
					'type'  => 'section_start',
					'id'    => 'email_options',
					'title' => __( 'Email Sender', 'wp-hotel-booking' ),
					'desc'  => __( 'The name and email address of the sender displays in email', 'wp-hotel-booking' ),
				),

				array(
					'type'        => 'text',
					'id'          => 'tp_hotel_booking_email_general_from_name',
					'title'       => __( 'From name', 'wp-hotel-booking' ),
					'default'     => get_option( 'blogname' ),
					'placeholder' => get_option( 'blogname' ),
				),

				array(
					'type'        => 'text',
					'id'          => 'tp_hotel_booking_email_general_from_email',
					'title'       => __( 'From Email', 'wp-hotel-booking' ),
					'default'     => get_option( 'admin_email' ),
					'placeholder' => get_option( 'admin_email' ),
				),

				array(
					'type'        => 'text',
					'id'          => 'tp_hotel_booking_email_general_subject',
					'title'       => __( 'Email subject', 'wp-hotel-booking' ),
					'desc'        => __( 'Subject for customer booking confirmation email. Available placeholders: {site_title}, {booking_number}, {booking_date}', 'wp-hotel-booking' ),
					'default'     => __( 'Reservation', 'wp-hotel-booking' ),
					'placeholder' => __( 'Reservation', 'wp-hotel-booking' ),
				),

				array(
					'type' => 'section_end',
					'id'   => 'email_options',
				),

				array(
					'type'  => 'section_start',
					'id'    => 'wphb_email_live_preview_section',
					'title' => __( 'Email Live Preview', 'wp-hotel-booking' ),
					'desc'  => __( 'Select an email template status and sample booking data to preview the full HTML email layout in real time.', 'wp-hotel-booking' ),
				),

				array(
					'type' => 'custom',
					'id'   => 'wphb_email_live_preview_box',
				),

				array(
					'type' => 'section_end',
					'id'   => 'wphb_email_live_preview_section',
				),
			);
			if ( $section === 'complete-booking' ) {
				$settings = array(
					array(
						'type'  => 'section_start',
						'id'    => 'complete_booking',
						'title' => __( 'Complete Booking', 'wp-hotel-booking' ),
						'desc'  => __( 'New booking emails are sent to chosen recipient(s) when a booking is completed.', 'wp-hotel-booking' ),
					),

					array(
						'type'    => 'checkbox',
						'id'      => 'tp_hotel_booking_email_new_booking_enable',
						'title'   => __( 'Enable', 'wp-hotel-booking' ),
						'default' => 1,
					),

					array(
						'type'        => 'text',
						'id'          => 'tp_hotel_booking_email_new_booking_recipients',
						'title'       => __( 'Recipient(s)', 'wp-hotel-booking' ),
						'desc'        => sprintf( __( 'Enter recipients (comma separated) for this email. Defaults to %s.', 'wp-hotel-booking' ), get_option( 'admin_email' ) ),
						'default'     => get_option( 'admin_email' ),
						'placeholder' => get_option( 'admin_email' ),
					),

					array(
						'type'        => 'text',
						'id'          => 'tp_hotel_booking_email_new_booking_subject',
						'title'       => __( 'Subject', 'wp-hotel-booking' ),
						'desc'        => __( 'Enter the subject of this email.', 'wp-hotel-booking' ),
						'default'     => '[{site_title}] Reservation completed ({booking_number}) - {booking_date}',
						'placeholder' => '[{site_title}] Reservation completed ({booking_number}) - {booking_date}',
					),

					array(
						'type'        => 'text',
						'id'          => 'tp_hotel_booking_email_new_booking_heading',
						'title'       => __( 'Email Heading', 'wp-hotel-booking' ),
						'desc'        => __( 'The main heading displays in the top of email. Default heading: Completed booking', 'wp-hotel-booking' ),
						'default'     => 'Completed Payment',
						'placeholder' => 'Completed Payment',
					),

					array(
						'type'        => 'text',
						'id'          => 'tp_hotel_booking_email_new_booking_heading_desc',
						'title'       => __( 'Email Heading Description', 'wp-hotel-booking' ),
						'default'     => __( 'The customer has completed the transaction', 'wp-hotel-booking' ),
						'placeholder' => __( 'The customer has completed the transaction', 'wp-hotel-booking' ),
					),

					array(
						'type'    => 'select',
						'id'      => 'tp_hotel_booking_email_new_booking_format',
						'title'   => __( 'Email Format', 'wp-hotel-booking' ),
						'default' => 'html',
						'options' => array(
							'plain' => __( 'Plain Text', 'wp-hotel-booking' ),
							'html'  => __( 'HTML', 'wp-hotel-booking' ),
						),
					),

					array(
						'type' => 'section_end',
						'id'   => 'complete_booking',
					),
				);
			}

			if ( $section === 'processing-booking' ) {
				$settings = array(
					array(
						'type'  => 'section_start',
						'id'    => 'processing_booking',
						'title' => __( 'Processing Booking', 'wp-hotel-booking' ),
						'desc'  => __( 'Confirmation email sent to the customer when a booking changes to Processing (e.g. a deposit/advance payment was paid online). Disabled by default.', 'wp-hotel-booking' ),
					),

					array(
						'type'    => 'checkbox',
						'id'      => 'tp_hotel_booking_email_processing_booking_enable',
						'title'   => __( 'Enable', 'wp-hotel-booking' ),
						'desc'    => __( 'Send the booking confirmation email to the customer when the booking status becomes Processing. The email is sent only once per booking.', 'wp-hotel-booking' ),
						'default' => 0,
					),

					array(
						'type'        => 'text',
						'id'          => 'tp_hotel_booking_email_processing_booking_subject',
						'title'       => __( 'Subject', 'wp-hotel-booking' ),
						'desc'        => __( 'Available placeholders: {site_title}, {booking_number}, {booking_date}', 'wp-hotel-booking' ),
						'default'     => '[{site_title}] Booking confirmed ({booking_number}) - {booking_date}',
						'placeholder' => '[{site_title}] Booking confirmed ({booking_number}) - {booking_date}',
					),

					array(
						'type'        => 'text',
						'id'          => 'tp_hotel_booking_email_processing_booking_heading',
						'title'       => __( 'Email Heading', 'wp-hotel-booking' ),
						'desc'        => __( 'The main heading displays in the top of email.', 'wp-hotel-booking' ),
						'default'     => __( 'Your booking is confirmed', 'wp-hotel-booking' ),
						'placeholder' => __( 'Your booking is confirmed', 'wp-hotel-booking' ),
					),

					array(
						'type'        => 'text',
						'id'          => 'tp_hotel_booking_email_processing_booking_heading_desc',
						'title'       => __( 'Email Heading Description', 'wp-hotel-booking' ),
						'default'     => __( 'We have received your deposit. The remaining balance is payable on arrival.', 'wp-hotel-booking' ),
						'placeholder' => __( 'We have received your deposit. The remaining balance is payable on arrival.', 'wp-hotel-booking' ),
					),

					array(
						'type' => 'section_end',
						'id'   => 'processing_booking',
					),
				);
			}

			if ( $section === 'cancel-booking' ) {
				$settings = array(
					array(
						'type'  => 'section_start',
						'id'    => 'cancel_booking',
						'title' => __( 'Cancel Booking', 'wp-hotel-booking' ),
						'desc'  => __( 'Cancel booking emails are sent to chosen recipient(s) when booking has been marked cancelled.', 'wp-hotel-booking' ),
					),

					array(
						'type'    => 'checkbox',
						'id'      => 'tp_hotel_booking_email_cancel_booking_enable',
						'title'   => __( 'Enable', 'wp-hotel-booking' ),
						'default' => 1,
					),

					array(
						'type'        => 'text',
						'id'          => 'tp_hotel_booking_email_cancel_booking_recipients',
						'title'       => __( 'Recipient(s)', 'wp-hotel-booking' ),
						'desc'        => sprintf( __( 'Enter recipients (comma separated) for this email. Defaults to %s.', 'wp-hotel-booking' ), get_option( 'admin_email' ) ),
						'default'     => get_option( 'admin_email' ),
						'placeholder' => get_option( 'admin_email' ),
					),

					array(
						'type'        => 'text',
						'id'          => 'tp_hotel_booking_email_cancel_booking_subject',
						'title'       => __( 'Subject', 'wp-hotel-booking' ),
						'desc'        => sprintf( __( 'Enter recipients (comma separated) for this email. Defaults to %s.', 'wp-hotel-booking' ), get_option( 'admin_email' ) ),
						'default'     => '[{site_title}] Cancelled Reservation ({booking_number}) - {booking_date}',
						'placeholder' => '[{site_title}] Cancelled Reservation  ({booking_number}) - {booking_date}',
					),

					array(
						'type'        => 'text',
						'id'          => 'tp_hotel_booking_email_cancel_booking_heading',
						'title'       => __( 'Email Heading', 'wp-hotel-booking' ),
						'desc'        => __( 'The main heading displays in the top of email. Default heading: Cancelled booking', 'wp-hotel-booking' ),
						'default'     => 'Cancelled booking',
						'placeholder' => 'Cancelled booking',
					),

					array(
						'type'        => 'text',
						'id'          => 'tp_hotel_booking_email_cancel_booking_heading_desc',
						'title'       => __( 'Email Heading Description', 'wp-hotel-booking' ),
						'default'     => __( 'Booking has been marked cancelled', 'wp-hotel-booking' ),
						'placeholder' => __( 'Booking has been marked cancelled', 'wp-hotel-booking' ),
					),

					array(
						'type'    => 'select',
						'id'      => 'tp_hotel_booking_email_cancel_booking_format',
						'title'   => __( 'Email Format', 'wp-hotel-booking' ),
						'default' => 'html',
						'options' => array(
							'plain' => __( 'Plain Text', 'wp-hotel-booking' ),
							'html'  => __( 'HTML', 'wp-hotel-booking' ),
						),
					),

					array(
						'type' => 'section_end',
						'id'   => 'cancel_booking',
					),
				);
			}

			return apply_filters( 'hotel_booking_admin_setting_fields_' . $this->id, $settings, $section );
		}

		public function get_sections() {
			$sections = array(
				'email-options'      => __( 'Email Options', 'wp-hotel-booking' ),
				'complete-booking'   => __( 'Complete Booking', 'wp-hotel-booking' ),
				'processing-booking' => __( 'Processing Booking', 'wp-hotel-booking' ),
				'cancel-booking'     => __( 'Cancel Booking', 'wp-hotel-booking' ),
			);

			return apply_filters( 'hotel_booking_admin_setting_sections_' . $this->id, $sections );
		}

		public function output() {
			$sections        = $this->get_sections();
			$current_section = null;
			if ( isset( $_REQUEST['section'] ) && isset( $_REQUEST['tab'] ) && $_REQUEST['tab'] === $this->id && array_key_exists( $_REQUEST['section'], $sections ) ) {
				$current_section = sanitize_text_field( $_REQUEST['section'] );
			}
			if ( ! $current_section ) {
				$section_keys    = array_keys( $sections );
				$current_section = reset( $section_keys );
			}

			foreach ( $sections as $sec_id => $sec_title ) {
				$is_active = ( $current_section === $sec_id );
				echo '<div class="wphb-settings-section-panel' . ( $is_active ? ' is-active' : '' ) . '" data-tab="' . esc_attr( $this->id ) . '" data-section="' . esc_attr( $sec_id ) . '">';
				$settings = $this->get_settings( $sec_id );
				WPHB_Admin_Settings::render_fields( $settings );
				echo '</div>';
			}
		}

		public function save( $tab = '' ) {
			$nonce = WPHB_Helpers::get_param( 'wphb_meta_box_settings_nonce', '' );
			if ( empty( $nonce ) || ! wp_verify_nonce( $nonce, 'wphb_update_meta_box_settings' ) ) {
				return;
			}

			if ( ! current_user_can( 'manage_hb_booking' ) && ! current_user_can( 'manage_options' ) ) {
				return;
			}

			if ( $this->id != $tab && 'emails' != $tab ) {
				return;
			}

			if ( ! empty( $_POST['wphb_save_all_tabs'] ) ) {
				$sections = $this->get_sections();
				foreach ( array_keys( $sections ) as $sec_id ) {
					$this->save_settings_fields( $this->get_settings( $sec_id ) );
				}
			} else {
				parent::save( $tab );
			}
		}

		/**
		 * Render Email Live Preview card inside Email Options sub-tab
		 *
		 * @param array $field
		 */
		public function render_email_live_preview_field( $field ) {
			$nonce            = wp_create_nonce( 'wphb_email_preview_nonce' );
			$settings         = function_exists( 'hb_settings' ) ? hb_settings() : null;
			$from_name        = ( $settings && $settings->get( 'email_general_from_name' ) ) ? $settings->get( 'email_general_from_name' ) : get_option( 'blogname' );
			$from_email       = ( $settings && $settings->get( 'email_general_from_email' ) ) ? $settings->get( 'email_general_from_email' ) : get_option( 'admin_email' );
			$initial_content  = function_exists( 'hb_get_sample_demo_email_content' ) ? hb_get_sample_demo_email_content( 'completed' ) : '';
			$preview_base_url = add_query_arg(
				array(
					'action' => 'wphb_render_email_preview',
					'status' => 'completed',
				),
				admin_url( 'admin-ajax.php' )
			);

			$proc_data = function_exists( 'hb_get_email_settings_by_status' ) ? hb_get_email_settings_by_status( 'processing' ) : array( 'subject' => '' );
			$comp_data = function_exists( 'hb_get_email_settings_by_status' ) ? hb_get_email_settings_by_status( 'completed' ) : array( 'subject' => '' );
			$canc_data = function_exists( 'hb_get_email_settings_by_status' ) ? hb_get_email_settings_by_status( 'cancelled' ) : array( 'subject' => '' );
			$admn_data = function_exists( 'hb_get_email_settings_by_status' ) ? hb_get_email_settings_by_status( 'admin_new' ) : array( 'subject' => '' );

			$proc_subj = $proc_data['subject'];
			$comp_subj = $comp_data['subject'];
			$canc_subj = $canc_data['subject'];
			$admn_subj = $admn_data['subject'];
			?>
			<tr valign="top" class="wphb-setting-row wphb-setting-row--email-live-preview">
				<th scope="row">
					<label><?php esc_html_e( 'Live Preview', 'wp-hotel-booking' ); ?></label>
				</th>
				<td class="hb-form-field">
					<div class="wphb-live-preview-wrapper" data-wphb-ignore-dirty="true" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.04); max-width: 900px; margin-top: 5px;">
						<style>
							#wphb_open_test_email_modal_btn:hover {
								background-color: #f0f7ff !important;
								border-color: #135e96 !important;
								color: #135e96 !important;
							}
							.wphb-modal-close:hover {
								color: #0f172a !important;
							}
						</style>
						<!-- Toolbar matching UI -->
						<div class="wphb-live-preview-toolbar" style="background: #f8fafc; padding: 12px 18px; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
							<div style="display: flex; align-items: center; gap: 10px;">
								<select id="wphb_preview_select_status" data-wphb-ignore-dirty="true" style="border: 1px solid #cbd5e1; border-radius: 6px; height: 34px; padding: 0 28px 0 10px; font-weight: 500; font-size: 13px; color: #1e293b; background-color: #ffffff; cursor: pointer;">
									<option value="completed" selected><?php esc_html_e( 'Customer - Completed booking', 'wp-hotel-booking' ); ?></option>
									<option value="processing"><?php esc_html_e( 'Customer - Processing booking', 'wp-hotel-booking' ); ?></option>
									<option value="cancelled"><?php esc_html_e( 'Customer - Cancelled booking', 'wp-hotel-booking' ); ?></option>
									<option value="admin_new"><?php esc_html_e( 'Admin - New booking completed', 'wp-hotel-booking' ); ?></option>
								</select>
							</div>

							<div style="display: flex; align-items: center; gap: 10px;">
								<!-- Device Toggle Buttons -->
								<div class="wphb-preview-device-group" style="display: inline-flex; align-items: center; gap: 4px; border: 1px solid #cbd5e1; border-radius: 6px; padding: 2px; background: #ffffff;">
									<button type="button" class="wphb-preview-device-btn is-active" data-width="100%" title="<?php esc_attr_e( 'Desktop view', 'wp-hotel-booking' ); ?>" style="border: none; background: #0f172a; color: #ffffff; border-radius: 4px; width: 30px; height: 28px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.15s ease;">
										<span class="dashicons dashicons-desktop" style="font-size: 17px; width: 17px; height: 17px;"></span>
									</button>
									<button type="button" class="wphb-preview-device-btn" data-width="380px" title="<?php esc_attr_e( 'Mobile view', 'wp-hotel-booking' ); ?>" style="border: none; background: transparent; color: #64748b; border-radius: 4px; width: 30px; height: 28px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.15s ease;">
										<span class="dashicons dashicons-smartphone" style="font-size: 17px; width: 17px; height: 17px;"></span>
									</button>
								</div>

								<!-- Send a test email button -->
								<button type="button" id="wphb_open_test_email_modal_btn" style="border: 1px solid #2271b1; background: #ffffff; color: #2271b1; font-weight: 600; font-size: 13px; height: 34px; padding: 0 16px; border-radius: 4px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 0.15s ease-in-out;">
									<?php esc_html_e( 'Send a test email', 'wp-hotel-booking' ); ?>
								</button>
							</div>
						</div>

						<!-- Simulated Email Header (matching screenshot) -->
						<div class="wphb-preview-email-header" style="padding: 20px 24px 16px; background: #ffffff; border-bottom: 1px solid #f1f5f9;">
							<h2 id="wphb_preview_header_subject" style="margin: 0 0 12px 0; font-size: 21px; font-weight: 700; color: #0f172a; line-height: 1.35; letter-spacing: -0.01em;">
								<?php echo esc_html( $comp_subj ); ?>
							</h2>
							<div style="display: flex; align-items: center; gap: 10px;">
								<div style="width: 34px; height: 34px; border-radius: 50%; background: #ede9fe; color: #7c3aed; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
									<span class="dashicons dashicons-admin-users" style="font-size: 18px; width: 18px; height: 18px; line-height: 18px;"></span>
								</div>
								<div style="font-size: 13px; color: #1e293b; line-height: 1.4;">
									<strong id="wphb_preview_header_from_name"><?php echo esc_html( $from_name ); ?></strong>
									<span id="wphb_preview_header_from_email" style="color: #64748b; margin-left: 4px;">&lt;<?php echo esc_html( $from_email ); ?>&gt;</span>
								</div>
							</div>
						</div>

						<!-- Frame Container -->
						<div style="background: #f8fafc; padding: 0; display: flex; justify-content: center; min-height: 560px; transition: all 0.3s ease;">
							<iframe id="wphb_email_live_preview_iframe" src="<?php echo esc_url( $preview_base_url ); ?>" srcdoc="<?php echo esc_attr( $initial_content ); ?>" style="width: 100%; min-height: 560px; border: none; background: #ffffff; transition: width 0.3s ease;"></iframe>
						</div>
					</div>

					<!-- Popup Modal for Send Test Email -->
					<div id="wphb-test-email-modal-overlay" data-wphb-ignore-dirty="true" style="display: none; position: fixed; z-index: 100000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.55); backdrop-filter: blur(2px); align-items: center; justify-content: center;">
						<div id="wphb-test-email-modal-card" style="background: #ffffff; border-radius: 12px; width: 100%; max-width: 480px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2), 0 8px 10px -6px rgba(0, 0, 0, 0.1); overflow: hidden; margin: 20px;">
							<div style="padding: 18px 24px; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; background: #f8fafc;">
								<h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 8px;">
									<span class="dashicons dashicons-email-alt" style="color: #2271b1; font-size: 18px; width: 18px; height: 18px;"></span>
									<?php esc_html_e( 'Send a test email', 'wp-hotel-booking' ); ?>
								</h3>
								<button type="button" class="wphb-modal-close" style="background: none; border: none; font-size: 22px; line-height: 1; cursor: pointer; color: #94a3b8; padding: 4px 6px;">&times;</button>
							</div>

							<div style="padding: 22px 24px;">
								<div style="margin-bottom: 16px; background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px 14px; font-size: 13px; color: #475569; display: flex; justify-content: space-between; align-items: center;">
									<span><?php esc_html_e( 'Email Template:', 'wp-hotel-booking' ); ?></span>
									<strong id="wphb-modal-status-label" style="color: #0f172a; font-weight: 600;"><?php esc_html_e( 'Customer - Completed booking', 'wp-hotel-booking' ); ?></strong>
								</div>

								<div style="margin-bottom: 14px;">
									<label for="wphb-modal-recipient" style="display: block; font-weight: 600; font-size: 13px; color: #334155; margin-bottom: 6px;">
										<?php esc_html_e( 'Send to', 'wp-hotel-booking' ); ?>
									</label>
									<input type="email" id="wphb-modal-recipient" value="<?php echo esc_attr( $admin_email ); ?>" style="width: 100%; box-sizing: border-box; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px;" placeholder="admin@example.com" />
								</div>

								<p style="margin: 0; font-size: 12px; color: #64748b; line-height: 1.45;">
									<?php esc_html_e( 'A sample email will be dispatched. Subject:', 'wp-hotel-booking' ); ?>
									<code id="wphb-modal-subject-preview" style="background: #f1f5f9; padding: 2px 6px; border-radius: 4px; font-size: 11px;">[Test] <?php echo esc_html( $comp_subj ); ?></code>
								</p>

								<div id="wphb-modal-alert" style="margin-top: 14px; font-size: 13px; padding: 10px 14px; border-radius: 6px; display: none;"></div>
							</div>

							<div style="padding: 14px 24px; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: flex-end; gap: 10px;">
								<button type="button" class="button wphb-modal-close" style="padding: 0 16px; height: 34px;">
									<?php esc_html_e( 'Cancel', 'wp-hotel-booking' ); ?>
								</button>
								<button type="button" id="wphb-modal-send-btn" class="button button-primary" style="padding: 0 18px; height: 34px; font-weight: 600;">
									<?php esc_html_e( 'Send Email', 'wp-hotel-booking' ); ?>
								</button>
								<span class="spinner" id="wphb-modal-spinner" style="float: none; margin: 0; display: none;"></span>
							</div>
						</div>
					</div>

					<script type="text/javascript">
					jQuery(document).ready(function($) {
						var previewBase = '<?php echo esc_js( admin_url( 'admin-ajax.php' ) ); ?>';
						var siteName = '<?php echo esc_js( $site_name ); ?>';

						var subjectsMap = {
							'processing': <?php echo json_encode( $proc_subj ); ?>,
							'completed': <?php echo json_encode( $comp_subj ); ?>,
							'cancelled': <?php echo json_encode( $canc_subj ); ?>,
							'admin_new': <?php echo json_encode( $admn_subj ); ?>
						};

						function updatePreview() {
							var status = $('#wphb_preview_select_status').val();

							if (subjectsMap[status]) {
								$('#wphb_preview_header_subject').text(subjectsMap[status]);
							}

							$.ajax({
								url: ajaxurl,
								type: 'GET',
								data: {
									action: 'wphb_render_email_preview',
									status: status
								},
								success: function(html) {
									if (html && html.trim() !== '0' && html.trim() !== '-1') {
										$('#wphb_email_live_preview_iframe').attr('srcdoc', html);
									} else {
										var url = previewBase + '?action=wphb_render_email_preview&status=' + encodeURIComponent(status);
										$('#wphb_email_live_preview_iframe').removeAttr('srcdoc').attr('src', url);
									}
								},
								error: function() {
									var url = previewBase + '?action=wphb_render_email_preview&status=' + encodeURIComponent(status);
									$('#wphb_email_live_preview_iframe').removeAttr('srcdoc').attr('src', url);
								}
							});
						}

						$('#wphb_preview_select_status').on('change', function(e) {
							e.stopPropagation();
							updatePreview();
						});

						// Isolate preview toolbar and modal from triggering admin settings dirty check
						var previewWrapEl = document.querySelector('.wphb-live-preview-wrapper');
						if (previewWrapEl) {
							previewWrapEl.addEventListener('change', function(e) { e.stopPropagation(); });
							previewWrapEl.addEventListener('input', function(e) { e.stopPropagation(); });
						}
						var modalOverlayEl = document.getElementById('wphb-test-email-modal-overlay');
						if (modalOverlayEl) {
							modalOverlayEl.addEventListener('change', function(e) { e.stopPropagation(); });
							modalOverlayEl.addEventListener('input', function(e) { e.stopPropagation(); });
						}

						// Device Switcher
						$('.wphb-preview-device-btn').on('click', function(e) {
							e.preventDefault();
							$('.wphb-preview-device-btn').removeClass('is-active').css({
								'background': 'transparent',
								'color': '#64748b'
							});
							$(this).addClass('is-active').css({
								'background': '#0f172a',
								'color': '#ffffff'
							});
							var width = $(this).data('width');
							$('#wphb_email_live_preview_iframe').css('width', width);
						});

						// Open Modal
						$('#wphb_open_test_email_modal_btn').on('click', function(e) {
							e.preventDefault();
							var currentStatus = $('#wphb_preview_select_status').val();
							var statusText = $('#wphb_preview_select_status option:selected').text();
							$('#wphb-modal-status-label').text(statusText);
							var currentSubj = subjectsMap[currentStatus] || '';
							$('#wphb-modal-subject-preview').text('[Test] ' + currentSubj);
							$('#wphb-modal-alert').hide().text('').removeClass('is-success is-error').attr('style', 'margin-top: 14px; font-size: 13px; padding: 10px 14px; border-radius: 6px; display: none;');
							$('#wphb-modal-send-btn').prop('disabled', false);
							$('#wphb-modal-spinner').hide();
							$('#wphb-test-email-modal-overlay').css('display', 'flex');
							$('#wphb-modal-recipient').focus();
						});

						// Close Modal
						$('.wphb-modal-close').on('click', function(e) {
							e.preventDefault();
							$('#wphb-test-email-modal-overlay').hide();
						});

						$('#wphb-test-email-modal-overlay').on('click', function(e) {
							if (e.target === this) {
								$(this).hide();
							}
						});

						$(document).on('keydown', function(e) {
							if (e.key === 'Escape' && $('#wphb-test-email-modal-overlay').is(':visible')) {
								$('#wphb-test-email-modal-overlay').hide();
							}
						});

						// Send Test Email AJAX
						$('#wphb-modal-send-btn').on('click', function(e) {
							e.preventDefault();
							var status = $('#wphb_preview_select_status').val();
							var recipient = $('#wphb-modal-recipient').val();
							var $alert = $('#wphb-modal-alert');
							var $btn = $(this);
							var $spinner = $('#wphb-modal-spinner');

							if (!recipient) {
								$alert.css({
									'background': '#fef2f2',
									'border': '1px solid #fecaca',
									'color': '#b91c1c',
									'display': 'block'
								}).text('<?php echo esc_js( __( 'Please enter a valid recipient email address.', 'wp-hotel-booking' ) ); ?>');
								return;
							}

							$btn.prop('disabled', true);
							$spinner.show().addClass('is-active');
							$alert.hide();

							$.ajax({
								url: ajaxurl,
								type: 'POST',
								dataType: 'json',
								data: {
									action: 'wphb_send_unified_test_email',
									nonce: '<?php echo esc_js( wp_create_nonce( 'wphb_unified_test_email_nonce' ) ); ?>',
									status: status,
									recipient: recipient
								},
								success: function(res) {
									$btn.prop('disabled', false);
									$spinner.hide().removeClass('is-active');
									if (res && res.success) {
										$alert.css({
											'background': '#f0fdf4',
											'border': '1px solid #bbf7d0',
											'color': '#15803d',
											'display': 'block'
										}).html('✓ ' + (res.data ? res.data.message : 'Sent successfully!'));
									} else {
										var msg = (res && res.data && res.data.message) ? res.data.message : 'Error sending email.';
										$alert.css({
											'background': '#fef2f2',
											'border': '1px solid #fecaca',
											'color': '#b91c1c',
											'display': 'block'
										}).html('✗ ' + msg);
									}
								},
								error: function(xhr, s, err) {
									$btn.prop('disabled', false);
									$spinner.hide().removeClass('is-active');
									$alert.css({
										'background': '#fef2f2',
										'border': '1px solid #fecaca',
										'color': '#b91c1c',
										'display': 'block'
									}).html('✗ ' + err);
								}
							});
						});
					});
					</script>
				</td>
			</tr>
			<?php
		}
	}

}
return new WPHB_Admin_Setting_Emails();
