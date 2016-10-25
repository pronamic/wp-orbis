<?php

/**
 * Title: Orbis company
 * Description:
 * Copyright: Copyright (c) 2005 - 2016
 * Company: Pronamic
 * @author Remco Tolsma
 * @version 1.0
 */
class Orbis_Company {
	private $post;

	//////////////////////////////////////////////////

	/**
	 * Constructs and initialize an Orbis plugin
	 *
	 * @param string $file
	 */
	public function __construct( $post = null ) {
		$this->post = get_post( $post );
	}

	public function get_email() {
		return get_post_meta( $this->post->ID, '_orbis_company_email', true );
	}

	public function get_address() {
		$address = new Orbis_Address();
		$address->address  = get_post_meta( $this->post->ID, '_orbis_company_address', true );
		$address->postcode = get_post_meta( $this->post->ID, '_orbis_company_postcode', true );
		$address->city     = get_post_meta( $this->post->ID, '_orbis_company_city', true );
		$address->country  = get_post_meta( $this->post->ID, '_orbis_company_country', true );

		return $address;
	}
}
