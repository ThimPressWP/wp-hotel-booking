<?php
/**
 * WP Hotel Booking override template helper class.
 *
 * @class       WPHB_Helper_Override_Template
 * @version     1.9.7.4
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'WPHB_Helper_Override_Template' ) ) {
	/**
	 * Class WPHB_Helper_Override_Template.
	 */
	class WPHB_Helper_Override_Template {

		/**
		 * @var array
		 */
		public static $counts = array(
			'all'        => 0,
			'outdated'   => 0,
			'up-to-date' => 0,
			'undefined'  => 0,
		);

		/**
		 * Get theme override templates.
		 *
		 * @param bool $check
		 *
		 * @return array|bool|mixed
		 */
		public static function get_theme_override_templates( $check = false ) {

			$plugins = apply_filters(
				'hb_plugins_templates_path',
				array(
					'wphb' => array(
						'folder' => hb_template_path(),
						'path'   => WPHB_TEMPLATES,
					),
				)
			);

			$template_dir   = get_template_directory();
			$stylesheet_dir = get_stylesheet_directory();
			$t_folder       = basename( $template_dir );
			$s_folder       = basename( $stylesheet_dir );

			$found_files        = array(
				$t_folder => array(),
				$s_folder => array(),
			);
			$outdated_templates = false;

			if ( ! is_array( $plugins ) || ! $plugins ) {
				return false;
			}

			foreach ( $plugins as $key => $template ) {
				$template_folder = $template['folder'];
				$template_path   = $template['path'];

				$scanned_files = self::_scan_template_files( $template_path );

				foreach ( $scanned_files as $file ) {
					$theme_folder = '';

					if ( file_exists( $stylesheet_dir . '/' . $file ) ) {
						$theme_file   = $stylesheet_dir . '/' . $file;
						$theme_folder = $s_folder;
					} elseif ( file_exists( $stylesheet_dir . '/' . $template_folder . '/' . $file ) ) {
						$theme_file   = $stylesheet_dir . '/' . $template_folder . '/' . $file;
						$theme_folder = $s_folder;
					} elseif ( file_exists( $template_dir . '/' . $file ) ) {
						$theme_file   = $template_dir . '/' . $file;
						$theme_folder = $t_folder;
					} elseif ( file_exists( $template_dir . '/' . $template_folder . '/' . $file ) ) {
						$theme_file   = $template_dir . '/' . $template_folder . '/' . $file;
						$theme_folder = $t_folder;
					} else {
						$theme_file = false;
					}

					if ( ! empty( $theme_file ) ) {
						self::$counts['all'] ++;
						$core_version  = self::_get_file_version( $template_path . $file );
						$theme_version = self::_get_file_version( $theme_file );
						// If core-template define version number then compare with it.
						if ( $core_version && ( empty( $theme_version ) || version_compare( $theme_version, $core_version, '<' ) ) ) {
							if ( ! $outdated_templates ) {
								$outdated_templates = true;
							}
							$found_files[ $theme_folder ][] = array(
								str_replace( WP_CONTENT_DIR . '/themes/', '', $theme_file ),
								$theme_version ? $theme_version : '-',
								$core_version,
								true,
							);
							if ( empty( $theme_version ) ) {
								self::$counts['undefined'] ++;
							}
							self::$counts['outdated'] ++;
						} else {
							$found_files[ $theme_folder ][] = array(
								str_replace( WP_CONTENT_DIR . '/themes/', '', $theme_file ),
								$theme_version ? $theme_version : '?',
								$core_version ? $core_version : '?',
								null,
							);
							self::$counts['up-to-date'] ++;
						}
					}
					if ( $check && $outdated_templates ) {
						return $outdated_templates;
					}
				}
			}

			if ( sizeof( $found_files ) > 1 ) {
				$found_files = array_merge( $found_files[ $t_folder ], $found_files[ $s_folder ] );
			} else {
				$found_files = reset( $found_files );
			}

			usort( $found_files, array( __CLASS__, '_sort_templates' ) );

			return $check ? $outdated_templates : $found_files;
		}

		/**
		 * @param $template_path
		 *
		 * @return array
		 */
		private static function _scan_template_files( $template_path ) {

			$files  = @scandir( $template_path );
			$result = array();

			if ( ! empty( $files ) ) {
				foreach ( $files as $key => $value ) {
					// Ignore special files
					if ( ! in_array( $value, array( '.', '..', 'index.php', 'index.html' ) ) ) {
						// If path is a folder, discover it.
						if ( is_dir( $template_path . '/' . $value ) ) {
							$sub_files = self::_scan_template_files( $template_path . '/' . $value );
							foreach ( $sub_files as $sub_file ) {
								$result[] = $value . '/' . $sub_file;
							}
						} else {
							$result[] = $value;
						}
					}
				}
			}

			return $result;
		}

		/**
		 * Get number version of a template file.
		 *
		 * @param string $file
		 *
		 * @return string
		 */
		private static function _get_file_version( $file ) {
			if ( ! file_exists( $file ) ) {
				return '';
			}
			$fp        = fopen( $file, 'r' );
			$file_data = fread( $fp, 8192 );
			fclose( $fp );
			$file_data = str_replace( "\r", "\n", $file_data );
			$version   = '';
			if ( preg_match( '/^[ \t\/*#@]*' . preg_quote( '@version', '/' ) . '(.*)$/mi', $file_data, $match ) && $match[1] ) {
				$version = _cleanup_header_comment( $match[1] );
			}

			return $version;
		}

		/**
		 * Sort overrides templates are outdated first
		 *
		 * @param array $a
		 * @param array $b
		 *
		 * @return int
		 */
		private static function _sort_templates( $a, $b ) {
			if ( $a[3] && $b[3] ) {
				return 0;
			}
			if ( $a[3] ) {
				return - 1;
			}
			if ( $b[3] ) {
				return 1;
			}

			return 0;
		}
	}
}

// Init
new WPHB_Helper_Override_Template();
