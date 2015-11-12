<?php

if ( ! defined( 'ABSPATH' ) )
	exit;

if( ! defined( 'HB_PLUGIN_PATH' ) )
	return;

if( ! defined( 'TP_HB_CURRENCY' ) )
	define( 'TP_HB_CURRENCY', __DIR__ );

if( ! defined( 'TP_HB_CURRENCY_URI' ) )
	define( 'TP_HB_CURRENCY_URI', HB_PLUGIN_URL . '/includes/tp-hb-currencies' );

if( ! defined( 'TP_HB_STORAGE_NAME' ) )
	define( 'TP_HB_STORAGE_NAME', 'tp_hb_sw_currency' );

class HB_SW_Factory
{
	public function __construct()
	{
		/**
		 * enqueue scripts
		 */
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
		$this->init();
	}

	function init()
	{
		require_once TP_HB_CURRENCY . '/class-hb-currencies.php';
	}

	/**
	 * enqueue script
	 * @return null
	 */
	public function enqueue()
	{
		wp_enqueue_script( 'tp-hb-currencies', TP_HB_CURRENCY_URI . '/assets/js/tp-hb-currencies.js', 'jquery', HB_VERSION, true );
		wp_enqueue_style( 'tp-hb-currencies', TP_HB_CURRENCY_URI . '/assets/css/tp-hb-currencies.css');
	}

}

new HB_SW_Factory();