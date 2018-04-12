<?php

/**
 * Title: Orbis contacts importer
 * Description:
 * Copyright: Copyright (c) 2005 - 2017
 * Company: Pronamic
 * @author Remco Tolsma
 * @version 1.0
 */
class Orbis_Core_ContactsImporter {
	/**
	 * Plugin
	 *
	 * @var Orbis_Plugin
	 */
	private $plugin;

	//////////////////////////////////////////////////

	/**
	 * Constructs and initialize an Orbis core admin
	 *
	 * @param Orbis_Plugin $plugin
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;

		// Actions
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
	}

	//////////////////////////////////////////////////

	/**
	 * Admin initialize
	 */
	public function admin_init() {
		$this->maybe_export_contacts();
		$this->maybe_import_contacts_upload();
		$this->maybe_import_contacts_map();
		$this->maybe_import_contacts();
	}

	public function get_import_post_input() {
		return array(
			'ID'         => __( 'ID', 'orbis' ),
			'post_title' => __( 'Name', 'orbis' ),
		);
	}

	public function get_import_meta_input() {
		return array(
			'_orbis_title'         => __( 'Title', 'orbis' ),
			'_orbis_organization'  => __( 'Organization', 'orbis' ),
			'_orbis_department'    => __( 'Department', 'orbis' ),
			'_orbis_email'         => __( 'Email', 'orbis' ),
			'_orbis_address'       => __( 'Address', 'orbis' ),
			'_orbis_postcode'      => __( 'Postcode', 'orbis' ),
			'_orbis_city'          => __( 'City', 'orbis' ),
			'_orbis_country'       => __( 'Country', 'orbis' ),
			'_orbis_phone_number'  => __( 'Phone Number', 'orbis' ),
			'_orbis_mobile_number' => __( 'Mobile Number', 'orbis' ),
			'_orbis_twitter'       => __( 'Twitter', 'orbis' ),
			'_orbis_facebook'      => __( 'Facebook', 'orbis' ),
			'_orbis_linkedin'      => __( 'LinkedIn', 'orbis' ),
		);
	}

	public function get_import_tax_input() {
		return array(
			'orbis_person_category' => __( 'Category', 'orbis' ),
		);
	}

	/**
	 * Maybe export contacts.
	 */
	private function get_export() {
		global $wpdb;

		$results = $wpdb->get_results( "
			SELECT
				post.ID,
				post.post_title,
				MAX( IF( meta.meta_key = '_orbis_title', meta.meta_value, NULL ) ) AS contact_title,
				MAX( IF( meta.meta_key = '_orbis_organization', meta.meta_value, NULL ) ) AS contact_organization,
				MAX( IF( meta.meta_key = '_orbis_department', meta.meta_value, NULL ) ) AS contact_department,
				MAX( IF( meta.meta_key = '_orbis_email', meta.meta_value, NULL ) ) AS contact_email,
				MAX( IF( meta.meta_key = '_orbis_address', meta.meta_value, NULL ) ) AS contact_address,
				MAX( IF( meta.meta_key = '_orbis_postcode', meta.meta_value, NULL ) ) AS contact_postcode,
				MAX( IF( meta.meta_key = '_orbis_city', meta.meta_value, NULL ) ) AS contact_city,
				MAX( IF( meta.meta_key = '_orbis_country', meta.meta_value, NULL ) ) AS contact_country,
				MAX( IF( meta.meta_key = '_orbis_phone_number', meta.meta_value, NULL ) ) AS contact_phone_number,
				MAX( IF( meta.meta_key = '_orbis_mobile_number', meta.meta_value, NULL ) ) AS contact_mobile_number,
				MAX( IF( meta.meta_key = '_orbis_twitter', meta.meta_value, NULL ) ) AS contact_twitter,
				MAX( IF( meta.meta_key = '_orbis_facebook', meta.meta_value, NULL ) ) AS contact_facebook,
				MAX( IF( meta.meta_key = '_orbis_linkedin', meta.meta_value, NULL ) ) AS contact_linkedin
			FROM
				$wpdb->posts AS post
					LEFT JOIN
				$wpdb->postmeta AS meta
						ON post.ID = meta.post_id
			WHERE
				post_type = 'orbis_person'
					AND
				post_status IN ( 'publish', 'pending', 'draft', 'future' )
			GROUP BY
				post.ID
			;
		" );

		return $results;
	}

	public function maybe_import_contacts_upload() {
		if ( ! filter_has_var( INPUT_POST, 'orbis_contacts_import_upload' ) ) {
			return;
		}

		check_admin_referer( 'orbis_contacts_import', 'orbis_contacts_import_nonce' );

		$result = media_handle_upload( 'orbis_contacts_import_file', 0 );

		if ( is_int( $result ) ) {
			$url = add_query_arg( array(
				'attachment_id' => $result,
				'step'          => 'map',
			) );

			wp_safe_redirect( $url );

			exit;
		}

		if ( is_wp_error( $result ) ) {
			global $orbis_error;

			$orbis_error = $result;
		}
	}

	public function maybe_import_contacts_map() {
		if ( ! filter_has_var( INPUT_POST, 'orbis_contacts_import_map' ) ) {
			return;
		}

		check_admin_referer( 'orbis_contacts_import', 'orbis_contacts_import_nonce' );

		$attachment_id = filter_input( INPUT_POST, 'attachment_id', FILTER_SANITIZE_STRING );

		$map = filter_input( INPUT_POST, 'map', FILTER_SANITIZE_STRING, FILTER_FORCE_ARRAY );

		update_post_meta( $attachment_id, '_orbis_contacts_import_map', $map );

		$url = add_query_arg( array(
			'attachment_id' => $attachment_id,
			'step'          => 'confirm',
		) );

		wp_safe_redirect( $url );

		exit;
	}

	private function create_import_post( $map, $data ) {
		$post = array(
			'post_type'   => 'orbis_person',
			'post_status' => 'publish',
			'meta_input'  => array(),
			'tax_input'   => array(),
		);

		$post_input = $this->get_import_post_input();
		$meta_input = $this->get_import_meta_input();
		$tax_input  = $this->get_import_tax_input();

		foreach ( $map as $i => $key ) {
			if ( ! isset( $data[ $i ] ) ) {
				continue;
			}

			$value = $data[ $i ];
			$value = trim( $value );

			if ( empty( $value ) ) {
				continue;
			}

			if ( isset( $post_input[ $key ] ) ) {
				$post[ $key ] = $value;
			}

			if ( isset( $meta_input[ $key ] ) ) {
				$post['meta_input'][ $key ] = $value;
			}

			if ( isset( $tax_input[ $key ] ) ) {
				$result = wpcom_vip_term_exists( $value, $key );

				if ( ! is_array( $result ) ) {
					$result = wp_insert_term( $value, $key );
				}

				if ( ! isset( $post['tax_input'][ $key ] ) ) {
					$post['tax_input'][ $key ] = array();
				}

				if ( isset( $result['term_id'] ) ) {
					$post['tax_input'][ $key ][] = $result['term_id'];
				}
			}
		}

		$post = array_filter( $post );

		return $post;
	}

	public function get_import_contacts_url( $args ) {
		$args = wp_parse_args( $args, array(
			'post_type'     => 'orbis_person',
			'page'          => 'orbis-persons-import',
			'step'          => 'import',
			'attachment_id' => false,
			'offset'        => 0,
			'count'         => 10,
		) );

		$url = add_query_arg( $args, admin_url( 'edit.php' ) );

		$url = wp_nonce_url( $url, 'orbis_contacts_import', 'orbis_contacts_import_nonce' );

		return $url;
	}

	public function maybe_import_contacts() {
		if ( ! filter_has_var( INPUT_POST, 'orbis_contacts_import' ) ) {
			return;
		}

		check_admin_referer( 'orbis_contacts_import', 'orbis_contacts_import_nonce' );

		$url = $this->get_import_contacts_url( array(
			'attachment_id' => filter_input( INPUT_POST, 'attachment_id', FILTER_SANITIZE_STRING ),
		) );

		wp_safe_redirect( $url );

		exit;
	}

	public function maybe_export_contacts() {
		if ( ! filter_has_var( INPUT_GET, 'orbis_contacts_export' ) ) {
			return;
		}

		check_admin_referer( 'orbis_contacts_export', 'orbis_contacts_export_nonce' );

		// Set headers for download
		$filename = sprintf(
			__( 'orbis-contacts-export-%s.csv', 'orbis' ),
			date( 'Y-m-d_H-i' )
		);

		header( 'Content-Encoding: ' . get_bloginfo( 'charset' ) );
		header( 'Content-Type: text/csv; charset=' . get_bloginfo( 'charset' ) );
		header( 'Content-Disposition: attachment; filename=' . $filename );

		// Results
		$results = $this->get_export();

		$resource = fopen( 'php://output', 'w' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fopen

		// Header
		$header = array(
			__( 'ID', 'orbis' ),
			__( 'Name', 'orbis' ),
			__( 'Title', 'orbis' ),
			__( 'Organization', 'orbis' ),
			__( 'Department', 'orbis' ),
			__( 'Email', 'orbis' ),
			__( 'Address', 'orbis' ),
			__( 'Postcode', 'orbis' ),
			__( 'City', 'orbis' ),
			__( 'Country', 'orbis' ),
			__( 'Phone Number', 'orbis' ),
			__( 'Mobile Number', 'orbis' ),
			__( 'Twitter', 'orbis' ),
			__( 'Facebook', 'orbis' ),
			__( 'LinkedIn', 'orbis' ),
		);

		fputcsv( $resource, $header );

		foreach ( $results as $result ) {
			// Row
			$row = array(
				$result->ID,
				$result->post_title,
				$result->contact_title,
				$result->contact_organization,
				$result->contact_department,
				$result->contact_email,
				$result->contact_address,
				$result->contact_postcode,
				$result->contact_city,
				$result->contact_country,
				$result->contact_phone_number,
				$result->contact_mobile_number,
				$result->contact_twitter,
				$result->contact_facebook,
				$result->contact_linkedin,
			);

			fputcsv( $resource, $row );
		}

		exit;
	}

	//////////////////////////////////////////////////

	/**
	 * Admin menu
	 */
	public function admin_menu() {
		add_submenu_page(
			'edit.php?post_type=orbis_person', // parent_slug
			__( 'Export Contacts', 'orbis' ), // page_title
			__( 'Export', 'orbis' ), // menu_title
			'export', // capability
			'orbis-persons-export', // menu_slug
			array( $this, 'page_contacts_export' ) // function
		);

		add_submenu_page(
			'edit.php?post_type=orbis_person', // parent_slug
			__( 'Import Contacts', 'orbis' ), // page_title
			__( 'Import', 'orbis' ), // menu_title
			'import', // capability
			'orbis-persons-import', // menu_slug
			array( $this, 'page_contacts_import' ) // function
		);
	}

	public function page_contacts_export() {
		include plugin_dir_path( $this->plugin->file ) . '/admin/page-contacts-export.php';
	}

	public function page_contacts_import() {
		$attachment_id = filter_input( INPUT_GET, 'attachment_id', FILTER_SANITIZE_STRING );
		$step          = filter_input( INPUT_GET, 'step', FILTER_SANITIZE_STRING );

		include plugin_dir_path( $this->plugin->file ) . '/admin/page-contacts-import.php';
	}
}
