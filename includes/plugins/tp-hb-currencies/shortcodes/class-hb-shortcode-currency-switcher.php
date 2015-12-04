<?php

class HB_SW_Curreny_Switcher extends HB_SW_Curreny_Shortcode
{

	/**
	 * shortcode name
	 * @var string
	 */
	protected $_shortcode_name;

	/**
	 * template file name
	 * @var string
	 */
	protected $_template;

	public function __construct()
	{
		$this->_shortcode_name = 'hotel_booking_curreny_switcher';

		$this->_template = 'switcher.php';
		parent::__construct();
	}

	/**
	 * parse attr
	 * @param  array $atts array
	 * @return array       array
	 */
	public function parse_attr( $atts = array() )
	{
		if( isset( $atts['currencies'] ) )
		{
			$atts['currencies'] = explode( ',', $atts['currencies'] );
		}

		return $atts;
	}

}

new HB_SW_Curreny_Switcher();