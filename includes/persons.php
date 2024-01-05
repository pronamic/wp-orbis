<?php

function orbis_persons_suggest_person_id() {
	global $wpdb;

	$term = filter_input( INPUT_GET, 'term', FILTER_SANITIZE_STRING );

	$query = "
		SELECT
			post.ID AS id,
			post.post_title AS text
		FROM
			$wpdb->posts AS post
		WHERE
			post.post_type = 'orbis_person'
				AND
			post.post_title LIKE %s
		;
	";

	$like = '%' . $wpdb->esc_like( $term ) . '%';

	$query = $wpdb->prepare( $query, $like ); // unprepared SQL

	$data = $wpdb->get_results( $query ); // unprepared SQL

	echo wp_json_encode( $data );

	die();
}

add_action( 'wp_ajax_person_id_suggest', 'orbis_persons_suggest_person_id' );
