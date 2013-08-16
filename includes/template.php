<?php

if ( ! function_exists( 'orbis_price' ) ) {
	/**
	 * Format price
	 * 
	 * @param float $price
	 * @return string
	 */
	function orbis_price( $price ) {
		$return = '';
	
		$return .= '&euro;';
		$return .= '&nbsp;';
	
		$return .= number_format( $price, 2, ',', '.' );
	
		return $return;
	}
}

if ( ! function_exists( 'orbis_time' ) ) {
	/**
	 * Format time
	 * 
	 * @param int $seconds
	 * @param string $format
	 * @return mixed
	 */
	function orbis_time( $seconds, $format = 'H:m' ) {
		$hours   = $seconds / 3600;
		$minutes = ( $seconds % 3600 ) / 60;
	
		$search  = array( 'H', 'm' );
		$replace = array(
			sprintf( '%02d', $hours ),
			sprintf( '%02d', $minutes )
		);
	
		return str_replace( $search, $replace, $format );
	}
}
