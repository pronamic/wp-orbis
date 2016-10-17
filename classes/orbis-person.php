<?php

/**
 * Title: Orbis person
 * Description:
 * Copyright: Copyright (c) 2005 - 2016
 * Company: Pronamic
 * @author Remco Tolsma
 * @version 1.0
 */
class Orbis_Person {
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
		return get_post_meta( $this->post->ID, '_orbis_person_email_address', true );
	}

	public function get_gender() {
		$genders = get_the_terms( $this->post, 'orbis_gender' );
		
		$gender = is_array( $genders ) ? reset( $genders ) : null;

		return $gender;
	}
}
