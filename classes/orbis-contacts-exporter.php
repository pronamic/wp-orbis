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
	 * Constructs and initialize a Orbis CSV.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;

		add_action( 'init', [ $this, 'init' ], 0 );

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
		$php_excel = new PHPExcel();

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

		$writer = PHPExcel_IOFactory::createWriter( $php_excel, 'Excel5' );
		$writer->save( 'php://output' );

		exit;
	}
}
