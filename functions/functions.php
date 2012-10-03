<?php

function orbis_filter_time_input( $type, $variable_name ) {
	$seconds = 0;

	if ( isset( $_POST[ $variable_name ] ) ) {
		$elements = array(
			'hours'   => 3600,
			'minutes' => 60
		);

		foreach ( $elements as $element => $seconds_in ) {
			if ( isset( $_POST[ $variable_name ][ $element ] ) ) {
				$var = $_POST[ $variable_name ][ $element ];
				$int = filter_var( $var, FILTER_VALIDATE_INT );
			
				if ( $int !== false ) {
					$seconds += $int * $seconds_in;
				}
			} 
		}
	}

	return $seconds;
}
