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

/**
 * Orbis projects post class
 * 
 * @param array $classes
 */
function orbis_projects_post_class( $classes ) {
	global $post;

	if ( isset( $post->project_is_finished ) ) {
		$classes[] = $post->project_is_finished ? 'orbis-status-finished' : 'orbis-status-open';
	}

	return $classes;
}

add_filter( 'post_class', 'orbis_projects_post_class' );



/**
 * Sync project with Orbis tables
 */
function orbis_save_project_sync( $post_id, $post ) {
	// Doing autosave
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE) { 
		return;
	}

	// Check post type
	if ( ! ( $post->post_type == 'orbis_project' ) ) {
		return;
	}

	// Revision
	if ( wp_is_post_revision( $post_id ) ) {
		return;
	}

	// Publish
	if ( $post->post_status != 'publish' ) {
		return;
	}

	// OK
	global $wpdb;

	// Orbis project ID
	$orbis_id = get_post_meta( $post_id, '_orbis_project_id', true );

	if ( empty( $orbis_id ) ) {
		$result = $wpdb->insert( 
			'orbis_projects' , 
			array(
				'post_id' => $post_id ,
				'name' => $post->post_title  
			) , 
			array(
				'%d' , 
				'%s' 
			)
		);

		if ( $result !== false ) {
			$orbis_id = $wpdb->insert_id;

			update_post_meta( $post_id, '_orbis_project_id', $orbis_id );
		}
	} else {
		$result = $wpdb->update(
			'orbis_projects' , 
			array( 'name' => $post->post_title ) , 
			array( 'id' => $orbis_id ) , 
			array( '%s' ) , 
			array( '%d' ) 
		);
	}
}

add_action( 'save_post', 'orbis_save_project_sync', 10, 2 );

/**
 * Keychain edit columns
 */
function orbis_project_edit_columns($columns) {
	return array(
        'cb'                       => '<input type="checkbox" />' , 
		'orbis_project_id'         => __( 'Orbis ID', 'orbis' ) , 
        'orbis_project_principal'  => __( 'Principal', 'orbis' ) , 
        'title'                    => __( 'Title', 'orbis' ) , 
		'orbis_project_time'       => __( 'Time', 'orbis' ) , 
		'author'                   => __( 'Author', 'orbis' ) , 
		'comments'                 => __( 'Comments', 'orbis' ) ,  
        'date'                     => __( 'Date', 'orbis' ) , 
	);
}

add_filter( 'manage_edit-orbis_project_columns' , 'orbis_project_edit_columns' );

/**
 * Project column
 * 
 * @param string $column
 */
function orbis_project_column( $column, $post_id ) {
	switch ( $column ) {
		case 'orbis_project_id':
			$id = get_post_meta( $post_id, '_orbis_project_id', true );

			if ( ! empty( $id ) ) {
				$url = sprintf( 'http://orbis.pronamic.nl/projecten/details/%s/', $id );

				printf( '<a href="%s" target="_blank">%s</a>', $url, $id );
			}

			break;
		case 'orbis_project_principal':
			orbis_project_the_principal();

			break;
		case 'orbis_project_time':
			orbis_project_the_time();

			break;
	}
}

add_action( 'manage_posts_custom_column' , 'orbis_project_column', 10, 2 );

