<?php

/**
 * Title: Orbis CSV
 * Description:
 * Copyright: Copyright (c) 2005 - 2011
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.0
 */
class Orbis_Csv {
	/**
	 * Constructs and initialize a Orbis CSV.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;

		add_action( 'init', array( $this, 'init' ), 0 );

		add_filter( 'post_limits', array( $this, 'post_limits' ), 10, 2 );

		add_filter( 'feed_content_type', array( $this, 'feed_content_type' ), 10, 2 );
	}

	//////////////////////////////////////////////////

	/**
	 * Initialize.
	 *
	 * @see https://make.wordpress.org/plugins/2012/06/07/rewrite-endpoints-api/
	 */
	public function init() {
		add_feed( 'csv', array( $this, 'feed_csv' ) );
	}

	/**
	 * Post limits
	 *
	 * @param string $limits
	 * @param WP_Query $query
	 * @return string
	 */
	public function post_limits( $limits, $query ) {
		if ( 'csv' === $query->get( 'feed' ) ) {
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
		$resource = fopen( 'php://output', 'w' );

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

		if ( have_posts() ) {
			while ( have_posts() ) {
				the_post();

				$post = get_post();

				$contact = new Orbis_Contact( $post );

				$address = $contact->get_address();

				// Row
				$row = array(
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
					get_post_meta( $post->ID, '_orbis_person_phone_number', true ),
					get_post_meta( $post->ID, '_orbis_person_mobile_number', true ),
					get_post_meta( $post->ID, '_orbis_person_twitter', true ),
					get_post_meta( $post->ID, '_orbis_person_facebook', true ),
					get_post_meta( $post->ID, '_orbis_person_linkedin', true ),
				);

				fputcsv( $resource, $row );
			}
		}

		exit;
	}
}
