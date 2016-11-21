<?php

/**
 * Title: Orbis extended search support
 * Description:
 * Copyright: Copyright (c) 2005 - 2016
 * Company: Pronamic
 * @author Remco Tolsma
 * @version 1.0.0
 */
class Orbis_ExtendedSearchSupport {
	/**
	 * Constructs and initialize Orbis extend search support.
	 */
	public function __construct() {
		add_filter( 'wpes_meta_keys', array( $this, 'wpes_meta_keys' ) );
	}

	/**
	 * WP Extened Search meta keys.
	 *
	 * @see https://github.com/wp-plugins/wp-extended-search/blob/1.1/admin/WP_ES_admin.php#L92-L122
	 * @param array $meta_keys
	 * @return array
	 */
	public function wpes_meta_keys( $meta_keys ) {
		$meta_keys[] = '_orbis_organization';
		$meta_keys[] = '_orbis_address';
		$meta_keys[] = '_orbis_city';

		return $meta_keys;
	}
}
