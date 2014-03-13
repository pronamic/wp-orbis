<?php

/**
 * Add person meta boxes
 */
function orbis_person_add_meta_boxes() {
    add_meta_box(
        'orbis_person',
        __( 'Contact information', 'orbis' ),
        'orbis_person_meta_box',
        'orbis_person',
        'normal',
        'high'
    );
}

add_action( 'add_meta_boxes', 'orbis_person_add_meta_boxes' );

/**
 * Peron details meta box
 *
 * @param array $post
 */
function orbis_person_meta_box( $post ) {
	global $orbis_plugin;

	$orbis_plugin->plugin_include( 'admin/meta-box-person-details.php' );
}

/**
 * Save person details
 */
function orbis_save_person( $post_id, $post ) {
	// Doing autosave
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE) {
		return;
	}

	// Verify nonce
	$nonce = filter_input( INPUT_POST, 'orbis_person_details_meta_box_nonce', FILTER_SANITIZE_STRING );
	if( ! wp_verify_nonce( $nonce, 'orbis_save_person_details' ) ) {
		return;
	}

	// Check permissions
	if ( ! ( $post->post_type == 'orbis_person' && current_user_can( 'edit_post', $post_id ) ) ) {
		return;
	}

	// OK
	$definition = array(
		'_orbis_person_email_address' => FILTER_VALIDATE_EMAIL,
		'_orbis_person_phone_number'  => FILTER_SANITIZE_STRING,
		'_orbis_person_mobile_number' => FILTER_SANITIZE_STRING,
		'_orbis_person_twitter'       => FILTER_SANITIZE_STRING,
		'_orbis_person_facebook'      => FILTER_SANITIZE_STRING,
		'_orbis_person_linkedin'      => FILTER_SANITIZE_STRING
	);

	$data = filter_input_array( INPUT_POST, $definition );

	foreach ( $data as $key => $value ) {
		if ( empty( $value ) ) {
			delete_post_meta( $post_id, $key);
		} else {
			update_post_meta( $post_id, $key, $value );
		}
	}
}

add_action( 'save_post', 'orbis_save_person', 10, 2 );

function orbis_persons_suggest_person_id() {
	global $wpdb;

	$term = filter_input( INPUT_GET, 'term', FILTER_SANITIZE_STRING );

	$query = $wpdb->prepare( "
		SELECT
			post.ID AS id,
			post.post_title AS text
		FROM
			$wpdb->posts AS post
		WHERE
			post.post_type = 'orbis_person'
				AND
			post.post_title LIKE '%%%1\$s%%'
		;", $term
	);

	$data = $wpdb->get_results( $query );

	echo json_encode( $data );

	die();
}

add_action( 'wp_ajax_person_id_suggest', 'orbis_persons_suggest_person_id' );
