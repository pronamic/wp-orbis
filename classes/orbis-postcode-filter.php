<?php

/**
 * Title: Orbis postcode filter
 * Description:
 * Copyright: Copyright (c) 2005 - 2016
 * Company: Pronamic
 * @author Remco Tolsma
 * @version 1.0.0
 */
class Orbis_PostcodeFilter {
	/**
	 * Constructs and initialize an Orbis postcode filter.
	 */
	public function __construct() {
		add_filter( 'query_vars', array( $this, 'query_vars' ) );
		add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
	}

	/**
	 * Query vars.
	 *
	 * @see https://codex.wordpress.org/Plugin_API/Filter_Reference/query_vars
	 * @param array $query_vars
	 * @return array
	 */
	public function query_vars( $query_vars ) {
		$query_vars[] = 'min_postcode';
		$query_vars[] = 'max_postcode';

		return $query_vars;
	}

	/**
	 * Pre get posts.
	 *
	 * @see https://codex.wordpress.org/Plugin_API/Action_Reference/pre_get_posts
	 * @see https://codex.wordpress.org/Class_Reference/WP_Query
	 * @param WP_Query $query
	 */
	public function pre_get_posts( $query ) {
		$min_postcode = $query->get( 'min_postcode', null );
		$max_postcode = $query->get( 'max_postcode', null );

		if ( null === $min_postcode && null === $max_postcode ) {
			return;
		}

		$meta_query = $query->get( 'meta_query' );
		$meta_query = is_array( $meta_query ) ? $meta_query : array();

		if ( null !== $min_postcode && null !== $max_postcode ) {
			$meta_query[] = array(
				'key'     => '_orbis_postcode',
				'value'   => array( $min_postcode, $max_postcode ),
				'type'    => 'numeric',
				'compare' => 'BETWEEN',
			);
		} elseif ( null !== $min_postcode ) {
			$meta_query[] = array(
				'key'     => '_orbis_postcode',
				'value'   => $min_postcode,
				'type'    => 'numeric',
				'compare' => '>=',
			);
		} elseif ( null !== $max_postcode ) {
			$meta_query[] = array(
				'key'     => '_orbis_postcode',
				'value'   => $max_postcode,
				'type'    => 'numeric',
				'compare' => '<=',
			);
		}

		$query->set( 'meta_query', $meta_query );
	}
}
