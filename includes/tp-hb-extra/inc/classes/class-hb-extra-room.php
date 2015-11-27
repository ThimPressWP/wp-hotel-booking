<?php

class HB_Room_Extra extends HB_Room
{
	/**
     * @var array
     */
    protected static $_instance = array();

    /**
     * meta key extra packages
     * @var string
     */
    protected $_meta_key = '_hb_room_extra';

	function __construct( $post, $options = null )
	{
		parent::__construct( $post, $options = null );
	}

	function get_extra()
	{
		$extras = get_post_meta( $this->ID, $this->_meta_key, true );

		if( ! $extras ) return;

		$results = array();
		foreach( $extras as $k => $post_id )
		{
			$ext = new stdClass();
			$ext->ID 					= (int)$post_id;
			$ext->title 				= get_the_title( $post_id );
			$ext->description 			= get_post_field( 'post_content', $post_id );
			$ext->price   				= (float)get_post_meta( $post_id, 'tp_hb_extra_room_price', true );
			$ext->respondent   			= get_post_meta( $post_id, 'tp_hb_extra_room_respondent', true );
			$ext->respondent_name   	= get_post_meta( $post_id, 'tp_hb_extra_room_respondent_name', true );
            $ext->selected              = get_post_meta( $post_id, 'tp_hb_extra_room_selected', true );
			$results[ $post_id ] = $ext;
		}

        $default = get_posts( array(
                'posts_per_page'        => 9999,
                'post_type'             => 'hb_extra_room',
                'post_status'           => 'publish',
                'meta_key'              => 'tp_hb_extra_room_selected',
                'meta_value'            => 1
            ) );

        foreach ( $default as $key => $post ) {
           if( ! array_key_exists( $post->ID, $results ) )
           {
                $ext = new stdClass();
                $ext->ID                    = (int)$post->ID;
                $ext->title                 = get_the_title( $post->ID );
                $ext->description           = get_post_field( 'post_content', $post->ID );
                $ext->price                 = (float)get_post_meta( $post->ID, 'tp_hb_extra_room_price', true );
                $ext->respondent            = get_post_meta( $post->ID, 'tp_hb_extra_room_respondent', true );
                $ext->respondent_name       = get_post_meta( $post->ID, 'tp_hb_extra_room_respondent_name', true );
                $ext->selected              = get_post_meta( $post->ID, 'tp_hb_extra_room_selected', true );
                $results[ $post->ID ] = $ext;
           }
        }

		return $results;
	}

	static function instance( $room, $options = null )
	{
		$post = $room;
        if( $room instanceof WP_Post ){
            $id = $room->ID;
        }elseif( is_object( $room ) && isset( $room->ID ) ){
            $id = $room->ID;
        }else{
            $id = $room;
        }

        if( empty( self::$_instance[ $id ] ) ){
            return self::$_instance[ $id ] = new self( $post, $options );
        }
        else
        {
            $room = self::$_instance[ $id ];

            if( isset($options['check_in_date'], $options['check_out_date'])
                && ( ($options['check_in_date'] !== $room->check_in_date) || ($options['check_out_date'] !== $room->check_out_date) )
                || $room->quantity === false || $room->quantity != $options['quantity']
            )
            {
                return new self( $post, $options );
            }
        }
        return self::$_instance[ $id ];

	}

}