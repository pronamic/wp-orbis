<?php

global $wpdb;

$sql = "
	SELECT
		project.id ,
		project.name ,
		project.number_seconds AS available_seconds ,
		project.invoice_number AS invoice_number ,
		project.invoicable ,
		project.post_id AS project_post_id,
		manager.ID AS project_manager_id,
		manager.display_name AS project_manager_name,
		principal.id AS principal_id ,
		principal.name AS principal_name ,
		principal.post_id AS principal_post_id,
		SUM(registration.number_seconds) AS registered_seconds
	FROM
		orbis_projects AS project
			LEFT JOIN
		$wpdb->posts AS post
				ON project.post_id = post.ID
			LEFT JOIN
		$wpdb->users AS manager
				ON post.post_author = manager.ID
			LEFT JOIN
		orbis_companies AS principal
				ON project.principal_id = principal.id
			LEFT JOIN
		orbis_hours_registration AS registration
				ON project.id = registration.project_id
	WHERE
		(
			project.finished
				OR
			project.name LIKE '%strippenkaart%'
				OR
			project.name LIKE '%adwords%'
				OR
			project.name LIKE '%marketing%'
		)
			AND
		project.invoicable
			AND
		NOT project.invoiced
			AND
		project.start_date > '2011-01-01'
	GROUP BY
		project.id
	ORDER BY
		principal.name
	;
";

// Projects
$projects = $wpdb->get_results( $sql ); // unprepared SQL

// Managers
$managers = array();

// Projects and managers
foreach ( $projects as $project ) {
	// Find manager
	if ( ! isset( $managers[ $project->project_manager_id ] ) ) {
		$manager           = new stdClass();
		$manager->id       = $project->project_manager_id;
		$manager->name     = $project->project_manager_name;
		$manager->projects = array();

		$managers[ $manager->id ] = $manager;
	}

	$project->failed = $project->registered_seconds > $project->available_seconds;

	$manager = $managers[ $project->project_manager_id ];

	$manager->projects[] = $project;
}

ksort( $managers );

include 'projects-table-view.php';
