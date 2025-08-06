<?php
namespace WPHB;

use Thim_EL_Kit\SingletonTrait;
use Thim_EL_Kit\Custom_Post_Type;

class Elementor {
	use SingletonTrait;

    const CATEGORY_ARCHIVE_ROOM = 'thim_ekit_archive_room';
	const CATEGORY_SINGLE_ROOM  = 'thim_ekit_single_room';

    const WIDGETS = array(
		'global'     => array(
            'search-room',
			'filter-room',
			'filter-room-selected',
			'list-room'
        ),
		'loop-item'  => array(
			'loop-room-rating',
			'loop-room-price',
			'loop-room-info',
			'loop-room-add-to-cart'
		),
        'single-room' => array(
			'room-thumb',
			'room-content',
			'room-facilities',
			'room-infos',
			'room-rules',
			'room-faqs',
			'room-review',
			'room-booking',
			'room-preview',
			'room-related',
			'room-availability',
			'room-calendar-pricing',
		),
		'archive-room' => array(
			'archive-room',
			'list-results-room'
		)
	);

    public function __construct() {
		$this->includes();

		// Register Controls
		add_filter( 'thim_ekit/elementor/widgets/list', array( $this, 'add_widgets' ), 20 );
		add_filter( 'thim_ekit/elementor/widget/file_path', array( $this, 'change_widget_path' ), 10, 2 );
		add_filter( 'thim_ekit_elementor_category', array( $this, 'add_categories' ) );

		add_filter(
			'thim_ekit/admin/enqueue/localize',
			function( $localize ) {
				$localize['loop_item']['post_type'][] = array(
					'label' => 'Room',
					'value' => 'hb_room',
				);

				return $localize;

			},10,1
		);
		add_action( 'wp_enqueue_scripts', array( $this, 'add_elementor_widgets_dependencies') );
	}

    public function includes() {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		require_once WPHB_PLUGIN_PATH . '/includes/elementor/modules/group-control-global-el.php';
		require_once WPHB_PLUGIN_PATH . '/includes/elementor/modules/archive-room/class-init.php';
		require_once WPHB_PLUGIN_PATH . '/includes/elementor/modules/archive-room/class-rest-api.php';
		require_once WPHB_PLUGIN_PATH . '/includes/elementor/modules/single-room/class-init.php';
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

	public function add_elementor_widgets_dependencies() {
		//daterangepicker
		//wp_register_script( 'wphb-daterangepicker',  WPHB_PLUGIN_URL . '/includes/elementor/src/js/daterangepicker.min.js', array('jquery'), WPHB_VERSION );
		//magnific popup
		wp_register_script( 'wphb-light-gallery', WPHB_PLUGIN_URL . '/includes/elementor/src/js/lightgallery.min.js', array( 'jquery' ), WPHB_VERSION );
		//flexslide
		wp_register_script( 'wphb-flexslide', WPHB_PLUGIN_URL . '/includes/elementor/src/js/jquery.flexslider.min.js', array( 'jquery' ), WPHB_VERSION );
		//filter
		wp_register_script( 'wphb-filter-el', WPHB_PLUGIN_URL . '/includes/elementor/src/js/filter-by.js', array( 'jquery' ), WPHB_VERSION );

		wp_enqueue_script( 'wphb-widget-el',  WPHB_PLUGIN_URL . '/includes/elementor/src/js/widget.js', array('jquery'), WPHB_VERSION );

		wp_register_script( 'wphb-element-el',  WPHB_PLUGIN_URL . '/includes/elementor/src/js/hotel-booking-element.js', array('jquery'), WPHB_VERSION );

		//style
		wp_register_style( 'wphb-multidate-style', WPHB_PLUGIN_URL . '/includes/elementor/src/css/multidate.css', array(), WPHB_VERSION );
		wp_register_style( 'wphb-light-gallery', WPHB_PLUGIN_URL . '/includes/elementor/src/css/lightgallery.min.css', array(), WPHB_VERSION );
		wp_enqueue_style( 'wphb-frontend-style',  WPHB_PLUGIN_URL . '/includes/elementor/src/css/frontend/frontend-el-style.css', array(), WPHB_VERSION );
	}
}

Elementor::instance();
