<?php

/**
 * Title: Orbis API
 * Description:
 * Copyright: Copyright (c) 2005 - 2011
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.0
 */
class Orbis_API {
	/**
	 * Constructs and initialize an Orbis API
	 */
	public function __construct() {
		add_filter( 'generate_rewrite_rules', [ $this, 'generate_rewrite_rules' ] );

		add_filter( 'query_vars', [ $this, 'query_vars' ] );
	}

	public function generate_rewrite_rules( $wp_rewrite ) {
		$rules = [];

		$rules['api/(.*)/(.*)$'] = 'index.php?api_call=true&api_object=' . $wp_rewrite->preg_index( 1 ) . '&api_method=' . $wp_rewrite->preg_index( 2 );

		$wp_rewrite->rules = $rules + $wp_rewrite->rules;
	}

	public function query_vars( $query_vars ) {
		$query_vars[] = 'api_call';
		$query_vars[] = 'api_object';
		$query_vars[] = 'api_method';

		return $query_vars;
	}
}
