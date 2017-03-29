<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Class WPHB_Payment_Gateway_Base
 */
class WPHB_Payment_Gateway_Base{
    /**
     * @var string
     */
    protected $_title = '';

    /**
     * @var string
     */
    protected $_description = '';

    /**
     * @var string
     */
    protected $_slug = '';

    /**
     * Construction
     */
    function __construct(){

    }

    function __get( $key ){
        $return = false;
        switch( $key ){
            case 'title':
                $return = $this->_title;
                break;
            case 'description':
                $return = $this->_description;
                break;
            case 'slug':
                if( empty( $this->_slug ) ){
                    $return = sanitize_title( $this->_title );
                }else{
                    $return = $this->_slug;
                }
        }
        return $return;
    }

    function is_enable(){
        return false;
    }

    function process_checkout( $customer_id = null ){
        return array(
            'result' => ''
        );
    }

}