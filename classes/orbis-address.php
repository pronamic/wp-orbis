<?php

/**
 * Title: Orbis address
 * Description:
 * Copyright: Copyright (c) 2005 - 2016
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.0
 */
class Orbis_Address {
	public function get_address() {
		return $this->address;
	}

	public function get_postcode() {
		return $this->postcode;
	}

	public function get_city() {
		return $this->city;
	}

	public function get_country() {
		return $this->country;
	}

	public function is_empty() {
		$data = arra_filter(
			array(
				$this->address,
				$this->postcode,
				$this->city,
				$this->country,
			)
		);

		return empty( $data );
	}

	public function __toString() {
		$data = array(
			$this->address,
			trim( $this->postcode . ' ' . $this->city ),
			$this->country,
		);

		$data = array_filter( $data );

		return implode( "\r\n", $data );
	}
}
