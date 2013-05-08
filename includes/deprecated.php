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
