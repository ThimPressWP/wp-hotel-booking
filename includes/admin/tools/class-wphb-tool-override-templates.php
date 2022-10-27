<?php
/**
 * WP Hotel Booking admin override templates checker class.
 *
 * @class       WPHB_Admin_Tool_Override_Template
 * @version     1.9.4
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'WPHB_Admin_Tool_Override_Template' ) ) {

	/**
	 * Class WPHB_Admin_Tool_Override_Template.
	 */
	class WPHB_Admin_Tool_Override_Template extends WPHB_Abstract_Tool {

		/**
		 * @var string
		 */
		protected $title = '';

		/**
		 * @var string
		 */
		protected $id = 'override_template';

		/**
		 * WPHB_Admin_Tool_Override_Template constructor.
		 */
		public function __construct() {
			$this->title = __( 'Template', 'wp-hotel-booking' );
			parent::__construct();
		}

		/**
		 * Output.
		 */
		public function output() {
			$templates          = WPHB_Helper_Override_Template::get_theme_override_templates();
			$counts             = WPHB_Helper_Override_Template::$counts;
			$template_dir       = get_template_directory();
			$stylesheet_dir     = get_stylesheet_directory();
			$child_theme_folder = '';
			$theme_folder       = '';
			if ( $template_dir != $stylesheet_dir ) {
				$child_theme_folder = basename( $stylesheet_dir );
				$theme_folder       = basename( $template_dir );
			} ?>

			<table id="wphb-theme-override-templates" class="widefat" cellspacing="0">
				<thead>
				<tr>
					<th colspan="3">
						<h4><?php printf( __( 'Override Templates (%s)', 'wp-hotel-booking' ), esc_html( wp_get_theme()['Name'] ) ); ?></h4>
					</th>
				</tr>
				</thead>
				<tbody>
				<?php if ( $templates ) { ?>
					<tr>
						<th class="template-file">
							<?php _e( 'File', 'wp-hotel-booking' ); ?>
							<p>
								<a href="" class="template-filter current"
								   data-template=""><?php printf( __( 'All (%d)', 'wp-hotel-booking' ), $counts['all'] ); ?></a>
								<a href="" class="template-filter"
								   data-filter="up-to-date"><?php printf( __( 'Up to date (%d)', 'wp-hotel-booking' ), $counts['up-to-date'] ); ?></a>
								<a href="" class="template-filter"
								   data-filter="outdated"><?php printf( __( 'Outdated (%d)', 'wp-hotel-booking' ), $counts['outdated'] ); ?></a>
								<a href="" class="template-filter"
								   data-filter="undefined"><?php printf( __( 'Undefined (%d)', 'wp-hotel-booking' ), $counts['undefined'] ); ?></a>
							</p>
						</th>
						<th class="template-version">
							<?php _e( 'Template version', 'wp-hotel-booking' ); ?>
						</th>
						<th class="core-version"><?php _e( 'Plugin version', 'wp-hotel-booking' ); ?></th>
					</tr>
					<?php foreach ( $templates as $template ) { ?>
						<?php if ( $child_theme_folder && strpos( $template[0], $child_theme_folder ) !== false ) {
							$template_folder = $child_theme_folder;
						} else {
							$template_folder = $theme_folder;
						}
						$template_class = ( $template[1] == '-' ? 'undefined' : ( $template[3] ? 'outdated' : 'up-to-date' ) ); ?>

						<tr data-template="<?php echo esc_attr( $template_folder ); ?>"
						    class="template-row <?php echo $template_class; ?>"
						    data-filter-<?php echo esc_attr( $template_class ); ?>="yes">
							<td class="template-file"><code><?php echo $template[0]; ?></code></td>
							<td class="template-version"><span><?php echo $template[1]; ?></span></td>
							<td class="plugin-version"><span><?php echo $template[2]; ?></span></td>
						</tr>
					<?php } ?>
				<?php } ?>

				<tr class="no-templates <?php echo $templates ? 'hide-if-js' : ''; ?>">
					<td colspan="3">
						<p><?php _e( 'There is no template file has overwritten', 'wp-hotel-booking' ); ?></p>
					</td>
				</tr>
				</tbody>
			</table>
			<?php
		}
	}
}

return new WPHB_Admin_Tool_Override_Template();
