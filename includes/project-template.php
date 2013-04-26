<?php

function orbis_project_get_the_time( $format = 'H:m' ) {
	global $post;

	$time = null;

	if ( isset( $post->project_number_seconds ) ) {
		$time = orbis_format_seconds( $post->project_number_seconds, $format );
	}

	return $time;
}

function orbis_project_the_time( $format = 'H:m' ) {
	echo orbis_project_get_the_time( $format );
}
