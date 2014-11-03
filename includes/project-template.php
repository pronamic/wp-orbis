<?php

function orbis_project_has_principal() {
	global $post;

	return isset( $post->principal_id );
}

function orbis_project_principel_get_the_name( ) {
	global $post;

	$principal = null;

	if ( isset( $post->principal_name ) ) {
		$principal = $post->principal_name;
	}

	return $principal;
}

function orbis_project_principel_the_name( ) {
	echo esc_html( orbis_project_principel_get_the_name() );
}

// @codingStandardsIgnoreStart
function orbis_project_principal_get_the_ID() {
// @codingStandardsIgnoreEnd
	global $post;

	$post_id = false;

	if ( isset( $post->principal_post_id ) ) {
		$post_id = $post->principal_post_id;
	}

	return $post_id;
}

function orbis_project_principal_get_permalink() {
	return get_permalink( orbis_project_principal_get_the_ID() );
}

function orbis_project_principal_the_permalink() {
	echo esc_url( orbis_project_principal_get_permalink() );
}

function orbis_project_get_the_time( $format = 'HH:MM' ) {
	global $post;

	$time = null;

	if ( isset( $post->project_number_seconds ) ) {
		$time = orbis_time( $post->project_number_seconds, $format );
	}

	return $time;
}

function orbis_project_the_time( $format = 'HH:MM' ) {
	echo esc_html( orbis_project_get_the_time( $format ) );
}
