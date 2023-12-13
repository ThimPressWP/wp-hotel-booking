<?php
namespace WPHB;

use Thim_EL_Kit\SingletonTrait;
use Thim_EL_Kit\Custom_Post_Type;

class Elementor {
	use SingletonTrait;

    const CATEGORY              = 'thim-hotel-booking';
    const CATEGORY_ARCHIVE_ROOM = 'thim_ekit_archive_room';
	const CATEGORY_SINGLE_ROOM  = 'thim_ekit_single_room';

    const WIDGETS = array(
		'global'             => array(
            'search-room',
			'filter-room'
        ),
	);

    public function __construct() {
		$this->includes();

		// Register Controls
		add_filter( 'thim_ekit/elementor/widgets/list', array( $this, 'add_widgets' ), 20 );
		add_filter( 'thim_ekit/elementor/widget/file_path', array( $this, 'change_widget_path' ), 10, 2 );
		add_filter( 'thim_ekit_elementor_category', array( $this, 'add_categories' ) );
	}

    public function includes() {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}


    }

    public function add_widgets( $widget_default ) {
		$widgets = self::WIDGETS;

		global $post;

		// Only register archive-post, post-title in Elementor Editor only template.
		if ( $post && \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			$type = get_post_meta( $post->ID, Custom_Post_Type::TYPE, true );

			if ( $type !== 'archive-room' ) {
				unset( $widgets['archive-room'] );
			}

			if ( $type !== 'single-room' ) {
				unset( $widgets['single-room'] );
			}
		}

		$widgets = array_merge_recursive( $widget_default, $widgets );

		return $widgets;
	}

    public function change_widget_path( $path, $widget ) {
		foreach ( self::WIDGETS as $key => $widgets ) {
			if ( in_array( $widget, $widgets ) ) {
				$path = WPHB_PLUGIN_PATH . '/includes/elementor/widgets/' . $key . '/' . $widget . '.php';
			}
		}

		return $path;
	}

    public function add_categories( $categories ) {
		return array(
            self::CATEGORY => array(
				'title' => esc_html__( 'Thim Hotel Booking', 'wp-hotel-booking' ),
				'icon'  => 'fa fa-plug',
			),
			self::CATEGORY_ARCHIVE_ROOM => array(
				'title' => esc_html__( 'Thim Archive Room', 'wp-hotel-booking' ),
				'icon'  => 'fa fa-plug',
			),
			self::CATEGORY_SINGLE_ROOM  => array(
				'title' => esc_html__( 'Thim Single Room', 'wp-hotel-booking' ),
				'icon'  => 'fa fa-plug',
			),
		) + $categories;
	}
}

Elementor::instance();