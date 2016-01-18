<?php

if( ! function_exists( 'hotel_block_convert_current_time' ) )
{
	/**
	 * hotel_block_convert_current_time
	 * @param  $time is time()
	 * @return like current_time( 'mysql' )
	 */
	function hotel_block_convert_current_time( $time = null, $gmt = 0 )
	{
		if( ! $time ) {
			$time = time();
		}

		if( ! $gmt )
		{
			return $time + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );
		}
		else
		{
			return $time - ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) - 12 * HOUR_IN_SECONDS ;
		}
	}


}
