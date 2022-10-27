<?php
/**
 * WP Hotel Booking Updates.
 *
 * @version       1.0.0
 * @author        ThimPress
 * @package       WP_Hotel_Booking/Classes
 * @category      Classes
 * @author        Thimpress
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

/**
 * Class WPHB_Updates
 */
if ( ! class_exists( 'WPHB_Updates' ) ) {
	class WPHB_Tool_Updates extends WPHB_Abstract_Tool {

		/**
		 * @var string
		 */
		protected $title = '';

		/**
		 * @var string
		 */
		protected $id = 'wphb_update';
		/**
		 * WPHB_Updates constructor.
		 */
		public function __construct() {
			$this->title = __( 'Updates', 'wp-hotel-booking' );
			parent::__construct();
		}

		/**
		 * Output.
		 */
		public function output() {
			$flag = version_compare( get_option( 'hotel_booking_version' ), WPHB_VERSION, '<' );
			?>
			<table id="wphb-update-db" class="widefat" cellspacing="0">
				<thead>
				<tr>
					<th colspan="3">
						<h4><?php echo __( 'Info Update', 'wp-hotel-booking' ); ?></h4>
					</th>
				</tr>
				</thead>
				<tbody>
				<?php if ( $flag ) { ?>
					<tr>
						<td>
							<p>
							<?php _e( 'Template version', 'wp-hotel-booking' ); ?>
							</p>
						</td>
					</tr>
					<tr class="template-row">	
						<td class="template-file">
							<button type="submit" class="button button-primary button-large _wphb_update_field">
								<?php _e( 'Update', 'wp-hotel-booking' ); ?>
							</button>
						</td>
					</tr>
				<?php } else { ?>
					<tr>
						<td colspan="3">
							<p><?php _e( 'Database new version has been successfully updated ! ', 'wp-hotel-booking' ); ?></p>
						</td>
					</tr>
				<?php } ?>
				</tbody>
			</table>
			
			<?php
		}
	}
	return new WPHB_Tool_Updates();
}
