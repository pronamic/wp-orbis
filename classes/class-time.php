<?php

/**
 * Title: Orbis time
 * Description:
 * Copyright: Copyright (c) 2005 - 2016
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.0
 */
class Orbis_Time {
	private $seconds;

	/**
	 * Construct.
	 */
	public function __construct( $seconds = null ) {
		$this->seconds = $seconds;
	}

	/**
	 * Format.
	 *
	 * @param int $seconds
	 * @param string $format
	 * @return mixed
	 */
	public function format( $format = 'HH:MM' ) {
		if ( null === $this->seconds ) {
			return;
		}

		// @see http://stackoverflow.com/a/3856312
		$hours   = floor( $this->seconds / 3600 );
		$minutes = floor( ( $this->seconds - ( $hours * 3600 ) ) / 60 );
		$seconds = floor( $this->seconds % 60 );

		$replacements = array(
			'HH' => sprintf( '%02d', $hours ),
			'H'  => $hours,
			'MM' => sprintf( '%02d', $minutes ),
			'M'  => $minutes,
			'SS' => sprintf( '%02d', $seconds ),
			'S'  => $seconds,
		);

		$string = str_replace(
			array_keys( $replacements ),
			array_values( $replacements ),
			$format
		);

		return $string;
	}

	public function __toString() {
		return $this->format();
	}
}
