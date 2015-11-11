<?php

/**
 * Abstract class shortcode
 */
abstract class HB_SW_Curreny_Shortcode
{

	/**
	 * abstract protected shortcode name
	 * @var [type]
	 */
	protected $_shortcode_name;

	/**
	 * abstract protected template file
	 * @var [type]
	 */
	protected $_template;

	public function __construct()
	{
		add_shortcode( $this->_shortcode_name, array( $this, 'render' ) );
	}

	/**
	 * before shortcode render
	 * @return html
	 */
	public function before()
	{
		return '<div class="hb_currency_switcher_wrap '.$this->_shortcode_name.'">';
	}

	/**
	 * render shortcode
	 * @param  [type] $att     [description]
	 * @param  [type] $content [description]
	 * @return [type]          [description]
	 */
	public function render( $att, $content = null )
	{
		$html = array();

		$html[] = $this->before();

		if( $this->_template )
		{
			ob_start();
			hb_sw_get_template( $this->_template, $this->parse_attr( $att ) );
			$html[] = ob_get_clean();
		}

		$html[]	= $this->after();

		return implode('', $html);

	}

	/**
	 * after shortcode render
	 * @return html
	 */
	public function after()
	{
		return '</div>';
	}

	public function parse_attr( $atts )
	{
		return $atts;
	}

}