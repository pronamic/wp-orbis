<?php

/**
 * Title: Orbis vCard
 * Description:
 * Copyright: Copyright (c) 2005 - 2011
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.0
 */
class Orbis_VCard {
	/**
	 * Constructs and initialize a Orbis vCard.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;

		add_action( 'init', array( $this, 'init' ) );

		add_action( 'template_redirect', array( $this, 'template_redirect' ) );
	}

	//////////////////////////////////////////////////

	/**
	 * Initialize.
	 *
	 * @see https://make.wordpress.org/plugins/2012/06/07/rewrite-endpoints-api/
	 */
	public function init() {
		add_rewrite_endpoint( 'vcard', EP_PERMALINK );
	}

	//////////////////////////////////////////////////

	/**
	 * Get company vcard.
	 *
	 * @see http://sabre.io/vobject/vcard/
	 * @param $post
	 * @return Sabre\VObject\Component\VCard
	 */
	private function get_company_vcard( $post ) {
		$orbis_company = new Orbis_Company( $post );

		$vcard = new Sabre\VObject\Component\VCard( array(
		    'FN'    => get_the_title( $post ),
		    'EMAIL' => $orbis_company->get_email(),
		) );

		return $vcard;
	}

	/**
	 * Get person vcard.
	 *
	 * @see http://sabre.io/vobject/vcard/
	 * @param $post
	 * @return Sabre\VObject\Component\VCard
	 */
	private function get_person_vcard( $post ) {
		$orbis_person = new Orbis_Person( $post );

		$vcard = new Sabre\VObject\Component\VCard( array(
		    'FN'    => get_the_title( $post ),
		    'EMAIL' => $orbis_person->get_email(),
		) );

		return $vcard;
	}

	/**
	 * Template redirect.
	 *
	 * @see https://make.wordpress.org/plugins/2012/06/07/rewrite-endpoints-api/
	 */
	public function template_redirect() {
		global $wp_query;

		// if this is not a request for json or a singular object then bail
		if ( null === $wp_query->get( 'vcard', null ) || ! is_singular() ) {
        	return;
		}

		$post = get_post();

		$vcard = new Sabre\VObject\Component\VCard();

		if ( 'orbis_company' === get_post_type( $post ) ) {
			$vcard = $this->get_company_vcard( $post );
		}

		if ( 'orbis_person' === get_post_type( $post ) ) {
			$vcard = $this->get_person_vcard( $post );
		}

		$filename = sprintf(
			'%s.vcf',
			get_post_field( 'post_name', $post )
		);

		header( 'Content-Type: text/x-vcard' );  
		header( sprintf( 'Content-Disposition: inline; filename="%s"', $filename ) );  

		echo $vcard->serialize();

		exit;
	}
}
