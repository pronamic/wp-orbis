<?php

function orbis_filter_time_input( $type, $variable_name ) {
	if ( isset( $_POST[ $variable_name ] ) ) {
		
	}

	$hours = filter_input( $type, $variable_name . '[hours]', FILTER_VALIDATE_INT );
	$minutes = filter_input( $type, $variable_name . '[minutes]', FILTER_VALIDATE_INT );

	$seconds = 0;
	
	$seconds += $hours * 3600;
	$seconds += $minutes * 3600;

	return $seconds;
}
