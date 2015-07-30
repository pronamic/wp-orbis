<?php

/**
 * Add person meta boxes
 */
function orbis_project_add_meta_boxes() {
	add_meta_box(
		'orbis_project',
		__( 'Project Information', 'orbis' ),
		'orbis_project_meta_box',
		'orbis_project',
		'normal',
		'high'
	);
}

add_action( 'add_meta_boxes', 'orbis_project_add_meta_boxes' );

/**
 * Peron details meta box
 *
 * @param array $post
 */
function orbis_project_meta_box() {
	global $orbis_plugin;

	$orbis_plugin->plugin_include( 'admin/meta-box-project-details.php' );
}

function orbis_enqueue_scripts() {
	wp_enqueue_script( 'orbis-autocomplete' );
	wp_enqueue_style( 'select2' );
}

add_action( 'admin_enqueue_scripts', 'orbis_enqueue_scripts' );



function orbis_projects_suggest_project_id() {
	global $wpdb;

	$term = filter_input( INPUT_GET, 'term', FILTER_SANITIZE_STRING );

	$extra_select = '';
	$extra_join   = '';

	if ( isset( $wpdb->orbis_timesheets ) ) {
		$extra_select .= ',
			SUM( entry.number_seconds ) AS project_logged_time
		';

		$extra_join = "
			LEFT JOIN
				$wpdb->orbis_timesheets AS entry
					ON entry.project_id = project.id
		";
	}

	$query = $wpdb->prepare( "
		SELECT
			project.id AS project_id,
			principal.name AS principal_name,
			project.name AS project_name,
			project.number_seconds AS project_time
			$extra_select
		FROM
			$wpdb->orbis_projects AS project
				LEFT JOIN
			$wpdb->orbis_companies AS principal
					ON project.principal_id = principal.id
			$extra_join
		WHERE
			project.finished = 0
				AND
			(
				project.name LIKE '%%%1\$s%%'
					OR
				principal.name LIKE '%%%1\$s%%'
			)
		GROUP BY
			project.id
		ORDER BY
			project.id
		", $term
	);

	$projects = $wpdb->get_results( $query );

	$data = array();

	foreach ( $projects as $project ) {
		$result = new stdClass();
		$result->id   = $project->project_id;

		$text = sprintf(
			'%s. %s - %s ( %s )',
			$project->project_id,
			$project->principal_name,
			$project->project_name,
			orbis_time( $project->project_time )
		);

		if ( isset( $project->project_logged_time ) ) {
			$text = sprintf(
				'%s. %s - %s ( %s / %s )',
				$project->project_id,
				$project->principal_name,
				$project->project_name,
				orbis_time( $project->project_logged_time ),
				orbis_time( $project->project_time )
			);
		}

		$result->text = $text;

		$data[] = $result;
	}

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
 * Helper functions
 */
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

function orbis_project_is_invoicable() {
	global $post;

	$is_invoicable = false;

	if ( get_post_meta( get_the_ID(), '_orbis_project_is_invoicable', true ) ) {
		$is_invoicable = true;
	}

	return $is_invoicable;
}

/**
 * Save project details
 */
function orbis_save_project( $post_id, $post ) {
	// Doing autosave
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Verify nonce
	$nonce = filter_input( INPUT_POST, 'orbis_project_details_meta_box_nonce', FILTER_SANITIZE_STRING );
	if ( ! wp_verify_nonce( $nonce, 'orbis_save_project_details' ) ) {
		return;
	}

	// Check permissions
	if ( ! ( 'orbis_project' === $post->post_type && current_user_can( 'edit_post', $post_id ) ) ) {
		return;
	}

	// OK
	$definition = array(
		'_orbis_project_principal_id' => FILTER_VALIDATE_INT,
		'_orbis_project_agreement_id' => FILTER_VALIDATE_INT,
		'_orbis_project_is_finished'  => FILTER_VALIDATE_BOOLEAN,
	);

	$data = filter_input_array( INPUT_POST, $definition );

	$data['_orbis_project_seconds_available'] = orbis_filter_time_input( INPUT_POST, '_orbis_project_seconds_available' );

	// Finished
	$is_finished_old = filter_var( get_post_meta( $post_id, '_orbis_project_is_finished', true ), FILTER_VALIDATE_BOOLEAN );
	$is_finished_new = $data['_orbis_project_is_finished'] ;

	foreach ( $data as $key => $value ) {
		if ( empty( $value ) ) {
			delete_post_meta( $post_id, $key );
		} else {
			update_post_meta( $post_id, $key, $value );
		}
	}

	// Action
	if ( 'publish' === $post->post_status && $is_finished_old !== $is_finished_new ) {
		// @see https://github.com/woothemes/woocommerce/blob/v2.1.4/includes/class-wc-order.php#L1274
		do_action( 'orbis_project_finished_update', $post_id, $is_finished_new );
	}
}

add_action( 'save_post', 'orbis_save_project', 10, 2 );

/**
 * Project finished update
 *
 * @param int $post_id
 */
function orbis_project_finished_update( $post_id, $is_finished ) {
	// Date
	update_post_meta( $post_id, '_orbis_project_finished_modified', time() );

	// Comment
	$user = wp_get_current_user();

	$comment_content = sprintf(
		__( "This '%s' project is just '%s' by %s.", 'orbis' ),
		$is_finished ? __( 'opened', 'orbis' ) : __( 'completed', 'orbis' ),
		$is_finished ? __( 'completed', 'orbis' ) : __( 'opened', 'orbis' ),
		$user->display_name
	);

	$data = array(
		'comment_post_ID' => $post_id,
		'comment_content' => $comment_content,
		'comment_author'  => 'Orbis',
		'comment_type'    => 'orbis_comment',
	);

	wp_insert_comment( $data );
}

add_action( 'orbis_project_finished_update', 'orbis_project_finished_update', 10, 2 );

/**
 * Sync project with Orbis tables
 */
function orbis_save_project_sync( $post_id, $post ) {
	// Doing autosave
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Check post type
	if ( ! ( 'orbis_project' === $post->post_type ) ) {
		return;
	}

	// Revision
	if ( wp_is_post_revision( $post_id ) ) {
		return;
	}

	// Publish
	if ( 'publish' !== $post->post_status ) {
		return;
	}

	// OK
	global $wpdb;

	// Orbis project ID
	$orbis_id       = get_post_meta( $post_id, '_orbis_project_id', true );
	$orbis_id       = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM $wpdb->orbis_projects WHERE post_id = %d;", $post_id ) );

	$principal_id   = get_post_meta( $post_id, '_orbis_project_principal_id', true );
	$is_invoicable  = get_post_meta( $post_id, '_orbis_project_is_invoicable', true );
	$is_invoiced    = get_post_meta( $post_id, '_orbis_project_is_invoiced', true );
	$invoice_number = get_post_meta( $post_id, '_orbis_project_invoice_number', true );
	$is_finished    = get_post_meta( $post_id, '_orbis_project_is_finished', true );
	$seconds        = get_post_meta( $post_id, '_orbis_project_seconds_available', true );

	$data = array();
	$form = array();

	$data['name'] = $post->post_title;
	$form['name'] = '%s';

	if ( ! empty( $principal_id ) ) {
		$data['principal_id'] = $principal_id;
		$form['principal_id'] = '%d';
	}

	$data['start_date'] = get_the_time( 'Y-m-d', $post );
	$form['start_date'] = '%s';

	$data['number_seconds'] = $seconds;
	$form['number_seconds'] = '%d';

	$data['invoicable'] = $is_invoicable;
	$form['invoicable'] = '%d';

	$data['invoiced'] = $is_invoiced;
	$form['invoiced'] = '%d';

	if ( ! empty( $invoice_number ) ) {
		$data['invoice_number'] = $invoice_number;
		$form['invoice_number'] = '%s';
	}

	$data['finished'] = $is_finished;
	$form['finished'] = '%d';

	if ( empty( $orbis_id ) ) {
		$data['post_id'] = $post_id;
		$form['post_id'] = '%d';

		$result = $wpdb->insert( $wpdb->orbis_projects, $data, $form );

		if ( false !== $result ) {
			$orbis_id = $wpdb->insert_id;
		}
	} else {
		$result = $wpdb->update(
			$wpdb->orbis_projects,
			$data,
			array( 'id' => $orbis_id ),
			$form,
			array( '%d' )
		);
	}

	update_post_meta( $post_id, '_orbis_project_id', $orbis_id );
}

add_action( 'save_post', 'orbis_save_project_sync', 500, 2 );

/**
 * Keychain edit columns
 */
function orbis_project_edit_columns( $columns ) {
	$columns = array(
		'cb'                      => '<input type="checkbox" />',
		'title'                   => __( 'Title', 'orbis' ),
		'orbis_project_principal' => __( 'Principal', 'orbis' ),
		'orbis_project_time'      => __( 'Time', 'orbis' ),
		'orbis_project_id'        => __( 'Orbis ID', 'orbis' ),
		'author'                  => __( 'Author', 'orbis' ),
		'comments'                => __( 'Comments', 'orbis' ),
		'date'                    => __( 'Date', 'orbis' ),
	);

	return $columns;
}

add_filter( 'manage_edit-orbis_project_columns' , 'orbis_project_edit_columns' );

/**
 * Project column
 *
 * @param string $column
 */
function orbis_project_column( $column, $post_id ) {
	switch ( $column ) {
		case 'orbis_project_id' :
			$orbis_id = get_post_meta( $post_id, '_orbis_project_id', true );

			if ( ! empty( $orbis_id ) ) {
				$url = sprintf( 'http://orbis.pronamic.nl/projecten/details/%s/', $orbis_id );

				printf( '<a href="%s" target="_blank">%s</a>', $url, $orbis_id );
			}

			break;
		case 'orbis_project_principal' :
			if ( orbis_project_has_principal() ) {
				printf(
					'<a href="%s">%s</a>',
					esc_attr( orbis_project_principal_get_permalink() ),
					orbis_project_principel_get_the_name()
				);
			}

			break;
		case 'orbis_project_time' :
			orbis_project_the_time();

			break;
	}
}

add_action( 'manage_posts_custom_column' , 'orbis_project_column', 10, 2 );

/**
 * Pre get posts
 * @param WP_Query $query
 */
function orbis_projects_pre_get_posts( $query ) {
	$orderby = $query->get( 'orderby' );

	if ( 'project_finished_modified' === $orderby ) {
		$query->set( 'orderby', 'meta_value_num' );
		$query->set( 'meta_key', '_orbis_project_finished_modified' );
	}
}

add_action( 'pre_get_posts', 'orbis_projects_pre_get_posts' );
