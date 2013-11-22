<?php

/**
 * Title: Orbis API
 * Description:
 * Copyright: Copyright (c) 2005 - 2011
 * Company: Pronamic
 * @author Remco Tolsma
 * @version 1.0
 */
class Orbis_API {
	/**
	 * Constructs and initialize an Orbis API
	 */
	public function __construct() {
		add_filter( 'generate_rewrite_rules', array( $this, 'generateRewriteRules' ) );

		add_filter( 'query_vars', array( $this, 'queryVars' ) );

		add_filter( 'wp_loaded', array( $this, 'flushRules' ) );
	}

	public function flushRules() {
		global $wp_rewrite;

		$wp_rewrite->flush_rules();
	}

	public function generateRewriteRules($wpRewrite) {
		$rules = array();

		$rules['api/(.*)/(.*)$'] = 'index.php?api_call=true&api_object=' . $wpRewrite->preg_index(1) . '&api_method=' . $wpRewrite->preg_index(2);

		$wpRewrite->rules = $rules + $wpRewrite->rules;
	}

	public function queryVars($queryVars) {
		$queryVars[] = 'api_call';
		$queryVars[] = 'api_object';
		$queryVars[] = 'api_method';

		return $queryVars;
	}
}
