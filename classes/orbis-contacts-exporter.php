<?php

/**
 * Title: Orbis contacts exporter
 * Description:
 * Copyright: Copyright (c) 2005 - 2017
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.0
 */
class Orbis_ContactsExporter {
	/**
	 * Plugin.
	 *
	 * @var Orbis_Plugin
	 */
	private $plugin;

	/**
	 * Constructs and initialize a Orbis CSV.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;

		add_action( 'init', [ $this, 'init' ], 0 );
		add_action( 'admin_init', [ $this, 'maybe_export_contacts' ] );
		add_action( 'admin_menu', [ $this, 'admin_menu' ] );

		add_filter( 'post_limits', [ $this, 'post_limits' ], 10, 2 );

		add_filter( 'feed_content_type', [ $this, 'feed_content_type' ], 10, 2 );
	}

	//////////////////////////////////////////////////

	/**
	 * Initialize.
	 *
	 * @see https://make.wordpress.org/plugins/2012/06/07/rewrite-endpoints-api/
	 */
	public function init() {
		add_feed( 'csv', [ $this, 'feed_csv' ] );
		add_feed( 'xls', [ $this, 'feed_xls' ] );
	}

	/**
	 * Post limits
	 *
	 * @param string $limits
	 * @param WP_Query $query
	 * @return string
	 */
	public function post_limits( $limits, $query ) {
		if ( in_array( $query->get( 'feed' ), [ 'csv', 'xls' ], true ) ) {
			$limits = '';
		}

		return $limits;
	}

	/**
	 * Feed content type.
	 *
	 * @param string $content_type
	 * @param string $type
	 * @return string
	 */
	public function feed_content_type( $content_type, $type ) {
		if ( 'csv' === $type ) {
			$content_type = 'text/csv';
		}

		if ( 'xls' === $type ) {
			$content_type = 'application/vnd.ms-excel';
		}

		return $content_type;
	}

	/**
	 * Admin menu.
	 */
	public function admin_menu() {
		add_submenu_page(
			'edit.php?post_type=orbis_person', // parent_slug
			__( 'Export Contacts', 'orbis' ), // page_title
			__( 'Export', 'orbis' ), // menu_title
			'export', // capability
			'orbis-persons-export', // menu_slug
			[ $this, 'page_contacts_export' ] // function
		);
	}

	/**
	 * Render the contacts export page.
	 */
	public function page_contacts_export() {
		include plugin_dir_path( $this->plugin->file ) . '/admin/page-contacts-export.php';
	}

	/**
	 * Retrieve contacts for the admin CSV export.
	 *
	 * @return array
	 */
	private function get_export() {
		global $wpdb;

		$results = $wpdb->get_results(
			"
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
		"
		);

		return $results;
	}

	/**
	 * Export contacts from the admin screen.
	 */
	public function maybe_export_contacts() {
		if ( ! filter_has_var( INPUT_GET, 'orbis_contacts_export' ) ) {
			return;
		}

		check_admin_referer( 'orbis_contacts_export', 'orbis_contacts_export_nonce' );

		$filename = sprintf(
			/* translators: %s: Export date and time. */
			__( 'orbis-contacts-export-%s.csv', 'orbis' ),
			date( 'Y-m-d_H-i' ) // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date -- Preserve the existing local-time filename.
		);

		header( 'Content-Encoding: ' . get_bloginfo( 'charset' ) );
		header( 'Content-Type: text/csv; charset=' . get_bloginfo( 'charset' ) );
		header( 'Content-Disposition: attachment; filename=' . $filename );

		$results = $this->get_export();

		$resource = fopen( 'php://output', 'w' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fopen

		$header = [
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
		];

		fputcsv( $resource, $header ); // phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.file_ops_fputcsv

		foreach ( $results as $result ) {
			$row = [
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
			];

			fputcsv( $resource, $row ); // phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.file_ops_fputcsv
		}

		exit;
	}

	/**
	 * Feed CSV.
	 */
	public function feed_csv() {
		// Set headers for download
		$filename = sprintf(
			__( 'csv-export-%s.csv', 'orbis' ),
			date( 'Y-m-d_H-i' )
		);

		header( 'Content-Encoding: ' . get_bloginfo( 'charset' ) );
		header( 'Content-Type: text/csv; charset=' . get_bloginfo( 'charset' ) );
		header( 'Content-Disposition: attachment; filename=' . $filename );

		// Results
		$resource = fopen( 'php://output', 'w' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fopen

		// Header
		$header = [
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
		];

		fputcsv( $resource, $header );

		if ( have_posts() ) {
			while ( have_posts() ) {
				the_post();

				$post = get_post();

				$contact = new Orbis_Contact( $post );

				$address = $contact->get_address();

				// Row
				$row = [
					$post->ID,
					$post->post_title,
					$contact->get_title(),
					$contact->get_organization(),
					$contact->get_department(),
					$contact->get_email(),
					$address->get_address(),
					$address->get_postcode(),
					$address->get_city(),
					$address->get_country(),
					get_post_meta( $post->ID, '_orbis_phone_number', true ),
					get_post_meta( $post->ID, '_orbis_mobile_number', true ),
					get_post_meta( $post->ID, '_orbis_twitter', true ),
					get_post_meta( $post->ID, '_orbis_facebook', true ),
					get_post_meta( $post->ID, '_orbis_linkedin', true ),
				];

				fputcsv( $resource, $row );
			}
		}

		exit;
	}

	private function get_excel() {

		// PHP Excel
		$php_excel = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

		// Set document properties
		$php_excel->getProperties()
			->setCreator( 'Orbis' )
			->setLastModifiedBy( 'Orbis' )
			->setTitle( 'Orbis' )
			->setSubject( 'Orbis' );

		// Data
		$data = [];

		// Header
		$header = [
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
		];

		$data[] = $header;

		if ( have_posts() ) {
			while ( have_posts() ) {
				the_post();

				$post = get_post();

				$contact = new Orbis_Contact( $post );

				$address = $contact->get_address();

				// Row
				$row = [
					$post->ID,
					$post->post_title,
					$contact->get_title(),
					$contact->get_organization(),
					$contact->get_department(),
					$contact->get_email(),
					$address->get_address(),
					$address->get_postcode(),
					$address->get_city(),
					$address->get_country(),
					get_post_meta( $post->ID, '_orbis_phone_number', true ),
					get_post_meta( $post->ID, '_orbis_mobile_number', true ),
					get_post_meta( $post->ID, '_orbis_twitter', true ),
					get_post_meta( $post->ID, '_orbis_facebook', true ),
					get_post_meta( $post->ID, '_orbis_linkedin', true ),
				];

				$data[] = $row;
			}
		}

		$php_excel->getActiveSheet()->fromArray( $data );

		return $php_excel;
	}

	/**
	 * Feed Excel.
	 *
	 * @see https://github.com/PHPOffice/PHPExcel/blob/1.8.1/Examples/01simple-download-xls.php
	 */
	public function feed_xls() {
		// Set headers for download
		$filename = sprintf(
			__( 'xls-export-%s.xls', 'orbis' ),
			date( 'Y-m-d_H-i' )
		);

		header( 'Content-Encoding: ' . get_bloginfo( 'charset' ) );
		header( 'Content-Type: application/vnd.ms-excel; charset=' . get_bloginfo( 'charset' ) );
		header( 'Content-Disposition: attachment; filename=' . $filename );

		$php_excel = $this->get_excel();

		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter( $php_excel, 'Xls' );
		$writer->save( 'php://output' );

		exit;
	}
}
