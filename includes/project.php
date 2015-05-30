<?php

/**
 * Query vars
 *
 * @param array $query_vars
 * @return array
 */
function orbis_project_query_vars( $query_vars ) {
	$query_vars[] = 'orbis_project_principal';
	$query_vars[] = 'orbis_project_client_id';
	$query_vars[] = 'orbis_project_invoice_number';
	$query_vars[] = 'orbis_project_is_finished';

	return $query_vars;
}

add_filter( 'query_vars', 'orbis_project_query_vars' );

//////////////////////////////////////////////////

/**
 * Posts clauses
 *
 * http://codex.wordpress.org/WordPress_Query_Vars
 * http://codex.wordpress.org/Custom_Queries
 *
 * @param array $pieces
 * @param WP_Query $query
 * @return string
 */
function orbis_projects_posts_clauses( $pieces, $query ) {
	global $wpdb;

	$post_type = $query->get( 'post_type' );

	if ( 'orbis_project' === $post_type ) {
		// Fields
		$fields = ',
			project.number_seconds AS project_number_seconds,
			project.finished AS project_is_finished,
			project.invoiced AS project_is_invoiced,
			principal.id AS principal_id,
			principal.name AS principal_name,
			principal.post_id AS principal_post_id
		';

		// Join
		$join = "
			LEFT JOIN
				$wpdb->orbis_projects AS project
					ON $wpdb->posts.ID = project.post_id
			LEFT JOIN
				$wpdb->orbis_companies AS principal
					ON project.principal_id = principal.id
		";

		// Where
		$where = '';

		$principal = $query->get( 'orbis_project_principal' );

		if ( ! empty( $principal ) ) {
			$wildcard = '%';
			$term = esc_sql( like_escape( $principal ) );

			$where .= " AND principal.name LIKE '{$wildcard}{$term}{$wildcard}' ";
		}

		$client_id = $query->get( 'orbis_project_client_id' );

		if ( ! empty( $client_id ) ) {
			$where .= $wpdb->prepare( ' AND principal.post_id LIKE %d ', $client_id );
		}

		$invoice_number = $query->get( 'orbis_project_invoice_number' );

		if ( ! empty( $invoice_number ) ) {
			$wildcard = '%';
			$term = esc_sql( like_escape( $invoice_number ) );

			$where .= " AND project.invoice_number LIKE '{$wildcard}{$term}{$wildcard}' ";
		}

		$is_finished = $query->get( 'orbis_project_is_finished', null );

		if ( null !== $is_finished ) {
			$is_finished = filter_var( $is_finished, FILTER_VALIDATE_BOOLEAN );

			$where .= $wpdb->prepare( ' AND project.finished = %d', $is_finished );
		}

		// Pieces

		$pieces['join']   .= $join;
		$pieces['fields'] .= $fields;
		$pieces['where']  .= $where;
	}

	return $pieces;
}

add_filter( 'posts_clauses', 'orbis_projects_posts_clauses', 10, 2 );
