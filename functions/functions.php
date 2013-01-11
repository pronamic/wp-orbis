<?php

function orbis_filter_time_input( $type, $variable_name ) {
	$seconds = 0;

	$value = filter_input( $type, $variable_name, FILTER_SANITIZE_STRING );

	$part_hours = $value;
	$part_minutes = 0;

	$position_colon = strpos( $value, ':' );

	if ( $position_colon !== false ) {
		$part_hours   = substr( $value, 0, $position_colon );
		$part_minutes = substr( $value, $position_colon + 1 );
	}

	$var = filter_var( $part_hours, FILTER_VALIDATE_FLOAT );
	if ( $var !== false ) {
		$seconds += $var * 3600;
	}
	
	$var = filter_var( $part_minutes, FILTER_VALIDATE_FLOAT );
	if ( $var !== false ) {
		$seconds += $var * 60;
	}

	return $seconds;
}
