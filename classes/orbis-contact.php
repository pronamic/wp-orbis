<?php

/**
 * Title: Orbis contact
 * Description:
 * Copyright: Copyright (c) 2005 - 2016
 * Company: Pronamic
 * @author Remco Tolsma
 * @version 1.0
 */
class Orbis_Contact {
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

	public function get_name() {
		return get_the_title( $this->post );
	}

	public function get_title() {
		return get_post_meta( $this->post->ID, '_orbis_title', true );
	}

	public function get_organization() {
		return get_post_meta( $this->post->ID, '_orbis_organization', true );
	}

	public function get_department() {
		return get_post_meta( $this->post->ID, '_orbis_department', true );
	}

	public function get_email() {
		return get_post_meta( $this->post->ID, '_orbis_person_email_address', true );
	}

	public function get_gender() {
		$genders = get_the_terms( $this->post, 'orbis_gender' );

		$gender = is_array( $genders ) ? reset( $genders ) : null;

		return $gender;
	}

	public function get_address() {
		$address = new Orbis_Address();
		$address->address  = get_post_meta( $this->post->ID, '_orbis_address', true );
		$address->postcode = get_post_meta( $this->post->ID, '_orbis_postcode', true );
		$address->city     = get_post_meta( $this->post->ID, '_orbis_city', true );
		$address->country  = get_post_meta( $this->post->ID, '_orbis_country', true );

		return $address;
	}

	public function get_birth_date( $format = 'Y-m-d' ) {
		$value = get_post_meta( $this->post->ID, '_orbis_birth_date', true );

		if ( '' === $value ) {
			return;
		}

		$date = date_create( $value );

		return $date;
	}

	/**
	 * @see http://stackoverflow.com/questions/3776682/php-calculate-age
	 */
	public function get_age() {
		$birth_date = $this->get_birth_date();

		if ( empty( $birth_date ) ) {
			return null;
		}

 		$now  = new DateTime();
 
 		$interval = $now->diff( $birth_date );
 	
 		return $interval->y;
	}
}
