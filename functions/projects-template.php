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
	echo orbis_project_principel_get_the_name();
}

function orbis_project_principal_get_the_ID() {
	global $post;

	$id = false;

	if ( isset( $post->principal_post_id ) ) {
		$id = $post->principal_post_id;
	}

	return $id;
}

function orbis_project_principal_get_permalink() {
	return get_permalink( orbis_project_principal_get_the_ID() );
}

function orbis_project_principal_the_permalink() {
	echo orbis_project_principal_get_permalink();
}
