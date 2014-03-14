<?php

/**
 * Add person meta boxes
 */
function orbis_company_add_meta_boxes() {
    add_meta_box(
        'orbis_company_details',
        __( 'Company Details', 'orbis' ),
        'orbis_company_details_meta_box',
        'orbis_company',
        'normal',
        'high'
    );
}

add_action( 'add_meta_boxes', 'orbis_company_add_meta_boxes' );

/**
 * Peron details meta box
 *
 * @param array $post
 */
function orbis_company_details_meta_box( $post ) {
	global $orbis_plugin;

	$orbis_plugin->plugin_include( 'admin/meta-box-company-details.php' );
}

/**
 * Save person details
 */
function orbis_save_company( $post_id, $post ) {
	// Doing autosave
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE) {
		return;
	}

	// Verify nonce
	$nonce = filter_input( INPUT_POST, 'orbis_company_details_meta_box_nonce', FILTER_SANITIZE_STRING );
	if( ! wp_verify_nonce( $nonce, 'orbis_save_company_details' ) ) {
		return;
	}

	// Check permissions
	if ( ! ( $post->post_type == 'orbis_company' && current_user_can( 'edit_post', $post_id ) ) ) {
		return;
	}

	// OK
	$definition = array(
		'_orbis_company_kvk_number' => FILTER_SANITIZE_STRING,
		'_orbis_company_email'      => FILTER_VALIDATE_EMAIL,
		'_orbis_company_website'    => FILTER_VALIDATE_URL,
		'_orbis_company_address'    => FILTER_SANITIZE_STRING,
		'_orbis_company_postcode'   => FILTER_SANITIZE_STRING,
		'_orbis_company_city'       => FILTER_SANITIZE_STRING,
		'_orbis_company_country'    => FILTER_SANITIZE_STRING,
		'_orbis_company_ebilling'   => FILTER_VALIDATE_BOOLEAN
	);

	$data = filter_input_array(INPUT_POST, $definition);

	foreach ( $data as $key => $value ) {
		if ( empty( $value ) ) {
			delete_post_meta( $post_id, $key);
		} else {
			update_post_meta( $post_id, $key, $value );
		}
	}
}

add_action( 'save_post', 'orbis_save_company', 10, 2 );

/**
 * Sync company with Orbis tables
 */
function orbis_save_company_sync( $post_id, $post ) {
	// Doing autosave
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE) {
		return;
	}

	// Check post type
	if ( ! ( $post->post_type == 'orbis_company' ) ) {
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

	// Orbis company ID
	$orbis_id = get_post_meta( $post_id, '_orbis_company_id', true );

	if ( empty( $orbis_id ) ) {
		$result = $wpdb->insert(
			$wpdb->orbis_companies,
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

			update_post_meta( $post_id, '_orbis_company_id', $orbis_id );
		}
	} else {
		$result = $wpdb->update(
			$wpdb->orbis_companies,
			array( 'name' => $post->post_title ) ,
			array( 'id' => $orbis_id ) ,
			array( '%s' ) ,
			array( '%d' )
		);
	}
}

add_action( 'save_post', 'orbis_save_company_sync', 10, 2 );

/**
 * Keychain edit columns
 */
function orbis_company_edit_columns($columns) {
	return array(
        'cb'                           => '<input type="checkbox" />',
        'title'                        => __( 'Title', 'orbis' ),
		// 'orbis_company_id'          => __( 'Orbis ID', 'orbis' ),
		'orbis_company_address'        => __( 'Address', 'orbis' ),
		'orbis_company_online'         => __( 'Online', 'orbis' ),
		'orbis_company_kvk_number'     => __( 'KvK Number', 'orbis' ),
		'author'                       => __( 'Author', 'orbis' ),
		'comments'                     => __( 'Comments', 'orbis' ),
        'date'                         => __( 'Date', 'orbis' ),
	);
}

add_filter( 'manage_edit-orbis_company_columns' , 'orbis_company_edit_columns' );

/**
 * Company column
 *
 * @param string $column
 */
function orbis_company_column( $column, $post_id ) {
	switch ( $column ) {
		case 'orbis_company_id':
			$id = get_post_meta( $post_id, '_orbis_company_id', true );

			if ( ! empty( $id ) ) {
				$url = sprintf( 'http://orbis.pronamic.nl/bedrijven/details/%s/', $id );

				printf( '<a href="%s" target="_blank">%s</a>', $url, $id );
			}

			break;
		case 'orbis_company_online':
			$br = '';

			$website = get_post_meta( $post_id, '_orbis_company_website', true );

			if ( ! empty( $website ) ) {
				printf( '<a href="%s" target="_blank">%s</a>', $website, $website );

				$br = '<br />';
			}

			$email = get_post_meta( $post_id, '_orbis_company_email', true );

			if ( ! empty( $email ) ) {
				echo $br;

				printf( '<a href="mailto:%s" target="_blank">%s</a>', $email, $email );
			}

			break;
		case 'orbis_company_address':
			$address  = get_post_meta( $post_id, '_orbis_company_address', true );
			$postcode = get_post_meta( $post_id, '_orbis_company_postcode', true );
			$city     = get_post_meta( $post_id, '_orbis_company_city', true );
			$country  = get_post_meta( $post_id, '_orbis_company_country', true );

			printf( '%s<br />%s %s', $address, $postcode, $city );

			break;
		case 'orbis_company_kvk_number':
			$kvk_number = get_post_meta( $post_id, '_orbis_company_kvk_number', true );

			if ( ! empty( $kvk_number ) ) {
				$url = sprintf( 'http://www.openkvk.nl/%s', $kvk_number );

				printf( '<a href="%s" target="_blank">%s</a>', $url, $kvk_number );
			}

			break;
	}
}

add_action( 'manage_posts_custom_column' , 'orbis_company_column', 10, 2 );

function orbis_companies_suggest_company_id() {
	global $wpdb;

	$term = filter_input( INPUT_GET, 'term', FILTER_SANITIZE_STRING );

	$query = $wpdb->prepare( "
		SELECT
			company.id AS id,
			company.name AS text
		FROM
			$wpdb->orbis_companies AS company
		WHERE
			company.name LIKE '%%%1\$s%%'
		;", $term
	);

	$data = $wpdb->get_results( $query );

	echo json_encode( $data );

	die();
}

add_action( 'wp_ajax_company_id_suggest', 'orbis_companies_suggest_company_id' );
