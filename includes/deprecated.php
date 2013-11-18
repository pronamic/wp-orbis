<?php
/**
 * Deprecated functions from past Orbis versions. You shouldn't use these
 * functions and look for the alternatives instead. The functions will be
 * removed in a later version.
 *
 * @package Orbis
 * @subpackage Deprecated
 */

/*
 * Deprecated functions come here to die.
 */

/**
 * Format seconds
 *
 * @param int $seconds
 * @param string $format
 * @return mixed
 * @deprecated
 */
function orbis_format_seconds( $seconds, $format = 'H:m' ) {
	_deprecated_function( __FUNCTION__, '1.0', 'orbis_time()' );

	return orbis_time( $seconds, $format );
}

/**
 * WordPress user ID to Orbis person ID
 *
 * @param string $id
 */
function orbis_get_current_person_id() {
	$user_id   = get_current_user_id();
	$person_id = null;

	$persons = array(
		1 =>  null, // pronamic
		2 =>  6, // remco
		3 =>  5, // kj
		4 =>  1, // jelke
		5 =>  4, // jl
		6 =>  2, // martijn cordes
		7 =>  3, // leo
		8 => 24, // martijn duker
		9 => 25, // stefan
		10 => 26, // leon
	);

	if ( isset( $persons[$user_id] ) ) {
		$person_id = $persons[$user_id];
	}

	return $person_id;
}
