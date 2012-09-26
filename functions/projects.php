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


function orbis_project_is_finished() {
	global $post;

	$is_finished = false;

	if ( isset( $post->project_is_finished ) ) {
		$is_finished = (boolean) $post->project_is_finished;
	}
	
	return $is_finished;
}

function orbis_project_is_invoiced() {
	global $post;

	$is_invoiced = false;

	if ( isset( $post->project_is_invoiced ) ) {
		$is_invoiced = (boolean) $post->project_is_invoiced;
	}
	
	return $is_invoiced;
}

/**
 * Save project details
 */
function orbis_save_project( $post_id, $post ) {
	// Doing autosave
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE) { 
		return;
	}

	// Verify nonce
	$nonce = filter_input( INPUT_POST, 'orbis_project_details_meta_box_nonce', FILTER_SANITIZE_STRING );
	if( ! wp_verify_nonce( $nonce, 'orbis_save_project_details' ) ) {
		return;
	}

	// Check permissions
	if ( ! ( $post->post_type == 'orbis_project' && current_user_can( 'edit_post', $post_id ) ) ) {
		return;
	}
	
	// OK
	$definition = array(
		'_orbis_project_principal_id' => FILTER_VALIDATE_INT,
		'_orbis_project_is_invoicable' => FILTER_VALIDATE_BOOLEAN,
		'_orbis_project_is_invoiced' => FILTER_VALIDATE_BOOLEAN,
		'_orbis_project_invoice_number' => FILTER_SANITIZE_STRING,
		'_orbis_project_is_invoice_paid' => FILTER_VALIDATE_BOOLEAN,
		'_orbis_project_is_finished' => FILTER_VALIDATE_BOOLEAN
	);

	$data = filter_input_array(INPUT_POST, $definition);
	
	$data['_orbis_project_available_seconds'] = orbis_filter_time_input( INPUT_POST, '_orbis_project_available_seconds' );
var_dump($data);
exit;
	foreach ( $data as $key => $value ) {		
		if ( empty( $value ) ) {
			delete_post_meta( $post_id, $key);
		} else {
			update_post_meta( $post_id, $key, $value );
		}
	}
}

add_action( 'save_post', 'orbis_save_project', 10, 2 );

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
	$principal_id = get_post_meta( $post_id, '_orbis_project_principal_id', true );
	$is_invoicable = get_post_meta( $post_id, '_orbis_project_is_invoicable', true );
	$is_invoiced = get_post_meta( $post_id, '_orbis_project_is_invoiced', true );
	$is_finished = get_post_meta( $post_id, '_orbis_project_is_finished', true );

	$data = array();
	$format = array();

	$data['name']   = $post->post_title;
	$format['name'] = '%s';

	if ( ! empty( $principal_id ) ) {
		$data['principal_id']   = $principal_id;
		$format['principal_id'] = '%d';
	}

	$data['invoicable']   = $is_invoicable;
	$format['invoicable'] = '%d';

	$data['invoiced']   = $is_invoiced;
	$format['invoiced'] = '%d';

	$data['finished']   = $is_finished;
	$format['finished'] = '%d';

	if ( empty( $orbis_id ) ) {
		$data['post_id'] = $post_id;
		$format['post_id'] = '%d';

		$result = $wpdb->insert( 'orbis_projects', $data, $format );

		if ( $result !== false ) {
			$orbis_id = $wpdb->insert_id;

			update_post_meta( $post_id, '_orbis_project_id', $orbis_id );
		}
	} else {
		$result = $wpdb->update(
			'orbis_projects', 
			$data, 
			array( 'id' => $orbis_id ), 
			$format, 
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
			if ( orbis_project_has_principal() ) {
				printf( 
					'<a href="%s">%s</a>',
					esc_attr( orbis_project_principal_get_permalink() ),
					orbis_project_principel_get_the_name()
				);
			}

			break;
		case 'orbis_project_time':
			orbis_project_the_time();

			break;
	}
}

add_action( 'manage_posts_custom_column' , 'orbis_project_column', 10, 2 );
