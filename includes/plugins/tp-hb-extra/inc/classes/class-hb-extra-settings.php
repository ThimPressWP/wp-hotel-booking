<?php

class HB_Extra_Settings
{

	/**
	 * options
	 * @var null
	 */
	protected $_options = null;

	/**
	 * type of package
	 * @var null
	 */
	protected $_type = null;

	/**
	 * self
	 * @var null
	 */
	static $_self = null;

	function __construct()
	{
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'admin_init', array( $this, 'adminInit' ) );
	}

	/**
	 * load extra room post type
	 * @return object
	 */
	function init()
	{
		if( ! $this->_options )
			$this->_options = $this->get_extra();
	}

	function adminInit()
	{
		if( ! isset( $_POST ) || empty( $_POST ) ) return;

		if( ! isset( $_POST[ TP_HB_OPTION_NAME ] ) || empty( $_POST[ TP_HB_OPTION_NAME ] ) ) return;

		$post_type = HB_Extra_Post_Type::instance();

		foreach ( (array)$_POST[ TP_HB_OPTION_NAME ] as $post_id => $post) {
			$post_type->add_extra( $post_id, $post );
		}

	}

	/**
	 * load options
	 * @return array extra post type
	 */
	function get_extra()
	{
		global $wpdb;
		$query = $wpdb->prepare( "
				SELECT * FROM $wpdb->posts WHERE `post_type` = %s
			", 'hb_extra_room' );
		return $wpdb->get_results( $query, OBJECT );
	}

	/**
	 * get instance instead of new ClassName();
	 * @return object class
	 */
	static function instance()
	{
		if( ! self::$_self )
			return new self();

		return self::$_self;
	}

}

// set global variable hb_extra_settings
$GLOBALS['hb_extra_settings'] = HB_Extra_Settings::instance();