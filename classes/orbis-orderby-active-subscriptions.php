<?php

/**
 * Obis order by comments class.
 *
 * @see https://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters
 */
class Orbis_OrderByActiveSubscriptions {
	public function __construct() {
		// Filters
		add_filter( 'posts_fields', [ $this, 'posts_fields' ], 10, 2 );

		add_filter( 'posts_orderby', [ $this, 'posts_orderby' ], 10, 2 );
	}

	public function query_has_active_subscriptions_query( $query ) {
		if ( 'active_subscriptions' === $query->get( 'orderby' ) ) {
			return true;
		}

		return false;
	}

	public function posts_fields( $fields, $query ) {
		if ( ! $this->query_has_active_subscriptions_query( $query ) ) {
			return $fields;
		}

		$fields .= ', cancel_date';

		return $fields;
	}

	/**
	 * Posts orderby.
	 *
	 * @see https://github.com/WordPress/WordPress/blob/4.6.1/wp-includes/query.php#L3355-L3363
	 * @see https://github.com/WordPress/WordPress/blob/4.6.1/wp-includes/query.php#L2310-L2403
	 * @param string $orderby
	 * @param WP_Query $query
	 * @return string
	 */
	public function posts_orderby( $orderby, $query ) {
		if ( 'active_subscriptions' === $query->get( 'orderby' ) ) {
			$orderby = 'cancel_date ' . $query->get( 'order' );
		}

		return $orderby;
	}
}
