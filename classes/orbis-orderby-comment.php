<?php

/**
 * Obis order by comments class.
 *
 * @see https://wordpress.org/support/topic/sort-posts-in-the-loop-by-recent-comments-or-activity/
 * @see https://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters
 */
class Orbis_OrderByComment {
	public function __construct() {
		// Filters
		add_filter( 'posts_fields', array( $this, 'posts_fields' ), 10, 2 );

		add_filter( 'posts_join', array( $this, 'posts_join' ), 10, 2 );

		add_filter( 'posts_orderby', array( $this, 'posts_orderby' ), 10, 2 );

		add_filter( 'date_query_valid_columns', array( $this, 'date_query_valid_columns' ) );
	}

	public function query_has_last_comment_date_query( $query ) {
		if ( 'last_comment_date' === $query->get( 'orderby' ) ) {
			return true;
		}

		if ( ! isset( $query->date_query, $query->date_query->queries ) ) {
			return false;
		}

		if ( $query->date_query ) {
			if ( ! is_array( $query->date_query->queries ) ) {
				return false;
			}

			foreach ( $query->date_query->queries as $query ) {
				if ( ! isset( $query['column'] ) ) {
					continue;
				}

				if ( 'last_comment_date' === $query['column'] ) {
					return true;
				}
			}
		}

		return false;
	}

	public function posts_fields( $fields, $query ) {
		if ( ! $this->query_has_last_comment_date_query( $query ) ) {
			return $fields;
		}

		$fields .= ', last_comment_date';

		return $fields;
	}

	/**
	 * Posts join.
	 *
	 * @see https://github.com/WordPress/WordPress/blob/4.6.1/wp-includes/query.php#L3203-L3211
	 * @param string $join
	 * @param WP_Query $query
	 * @return string
	 */
	public function posts_join( $join, $query ) {
		if ( ! $this->query_has_last_comment_date_query( $query ) ) {
			return $join;
		}

		global $wpdb;

		$join .= "LEFT JOIN ( 
			SELECT
				comment_post_ID,
				MAX( comment_date ) AS last_comment_date
			FROM
				$wpdb->comments
			WHERE
				comment_approved = '1'
			GROUP BY
				comment_post_ID
		) AS last_comment
			ON last_comment.comment_post_ID = $wpdb->posts.ID
		";

		return $join;
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
		if ( 'last_comment_date' === $query->get( 'orderby' ) ) {
			$orderby = 'last_comment_date ' . $query->get( 'order' );
		}

		return $orderby;
	}

	public function date_query_valid_columns( $valid_columns ) {
		$valid_columns[] = 'last_comment_date';

		return $valid_columns;
	}
}
