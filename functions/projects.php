<?php

/**
 * Add person meta boxes
 */
function orbis_project_add_meta_boxes() {
    add_meta_box( 
        'orbis_project',
        __('Project Information', 'orbis'),
        'orbis_project_meta_box',
        'orbis_project' ,
        'normal' ,
        'high'
    );
}

add_action( 'add_meta_boxes', 'orbis_project_add_meta_boxes' );

/**
 * Peron details meta box
 * 
 * @param array $post
 */
function orbis_project_meta_box( $post ) {
	include dirname(Orbis::$file) . '/admin/meta-box-project-details.php';
}

function orbis_enqueue_scripts() {
	wp_enqueue_script(
		'orbis-autocomplete',
		plugins_url( '/includes/js/autocomplete.js', Orbis::$file ),
		array( 'jquery', 'jquery-ui-autocomplete' )
	);
}

add_action( 'admin_enqueue_scripts', 'orbis_enqueue_scripts' );



function orbis_projects_suggest_project_id() {
	global $wpdb;

	$term = filter_input( INPUT_GET, 'term', FILTER_SANITIZE_STRING );

	$query = $wpdb->prepare( "
		SELECT 
			project.id AS value , 
			CONCAT(project.id, '. ', principal.name, ' - ', project.name) AS label
		FROM 
			orbis_projects AS project
				LEFT JOIN
			orbis_companies AS principal
					ON project.principal_id = principal.id
		WHERE
			project.finished = 0
				AND
			(
				project.name LIKE '%%%1\$s%%' 
					OR
				principal.name LIKE '%%%1\$s%%'
			)
		", $term 
	);
	
	$data = $wpdb->get_results( $query );

	echo json_encode( $data );

	die();
}

add_action( 'wp_ajax_project_id_suggest', 'orbis_projects_suggest_project_id' );