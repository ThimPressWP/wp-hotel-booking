<?php
defined( 'ABSPATH' ) || exit;

use Thim_EL_Kit\SingletonTrait;
use Thim_EL_Kit\Custom_Post_Type;


class Elementor {
	use SingletonTrait;

	const CATEGORY_ARCHIVE_ROOM = 'thim_ekit_archive_room';
	const CATEGORY_SINGLE_ROOM  = 'thim_ekit_single_room';

	const WIDGETS = array(
		'archive-room' => array(
            'archive-rooms',
        ),
		// 'single-room'  => array(
		// ),
		// 'loop-item'        => array(
		// ),
        // 'global' => array(
        //     'archive-rooms',
        // )
	);

	public function __construct() {
		$this->includes();

		add_filter( 'thim_ekit/elementor/widgets/list', array( $this, 'add_widgets' ), 20 );
		add_filter( 'thim_ekit/elementor/widget/file_path', array( $this, 'change_widget_path' ), 10, 2 );
		add_filter( 'thim_ekit_elementor_category', array( $this, 'add_categories' ) );
		add_filter( 'thim_ekit/elementor/documents/preview_item', array( $this, 'change_documents_preview_item' ), 10, 2 );
		
		add_filter(
			'thim_ekit/admin/enqueue/localize',
			function( $localize ) {
				$localize['loop_item']['post_type'][] = array(
					'label' => 'Room Types',
					'value' => 'hb_room',
				);

				return $localize;

			},10,1
		);
	}

	public function includes() {

        require_once WPHB_PLUGIN_PATH . '/includes/elementor/modules/archive-room/class-init.php';
        // require_once WPHB_PLUGIN_PATH . '/includes/elementor/modules/single-room/class-init.php';
	}

	public function add_widgets( $widget_default ) {
		$widgets = self::WIDGETS;

		global $post;

		// Only register archive-post, post-title in Elementor Editor only template.
		if ( $post && \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			$type = get_post_meta( $post->ID, Custom_Post_Type::TYPE, true );

			if ( $type !== 'archive-room' ) {
				//unset( $widgets['archive-room'] );
			}

			if ( $type !== 'single-room' ) {
				//unset( $widgets['single-room'] );
			}
		}

		$widgets = array_merge_recursive( $widget_default, $widgets );

        // var_dump($widgets);

		return $widgets;

	}

	public function change_documents_preview_item( $preview, $type ) {

		// if ( $type == SingleProperty::instance()->tab ) {
		// 	$preview = REALPRESS_PROPERTY_CPT;
		// }

		if ( get_the_ID() && $type === 'loop_item' ) {
			$preview = get_post_meta( get_the_ID(), 'thim_loop_item_post_type', true );
		}

		return $preview;
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
				'title' => esc_html__( 'Thim Archive Room', 'realpress' ),
				'icon'  => 'fa fa-plug',
			),
			self::CATEGORY_SINGLE_ROOM  => array(
				'title' => esc_html__( 'Thim Single Room', 'realpress' ),
				'icon'  => 'fa fa-plug',
			),
		) + $categories;

	}
}

Elementor::instance();