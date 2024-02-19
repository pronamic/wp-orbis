<?php

/**
 * Orbis access controller class
 */
class Orbis_AccessController {
	/**
	 * User team posts.
	 * 
	 * @var array<WP_Post>
	 */
	private $user_team_posts = [];

	/**
	 * Setup.
	 * 
	 * @return void
	 */
	public function setup() {
		\add_action( 'init', [ $this, 'init' ], 5000 );

		\add_action( 'p2p_init', [ $this, 'p2p_init' ], 200 );
		\add_action( 'p2p_created_connection', [ $this, 'p2p_created_connection' ] );
		\add_action( 'p2p_delete_connections', [ $this, 'p2p_delete_connections' ] );

		\add_filter(
			'the_posts',
			function ( $posts ) {
				$posts = \array_filter(
					$posts,
					function ( $post ) {
						return $this->can_user_view_post( $post );
					}
				);

				return $posts;
			},
			20
		);

		\add_action( 'parse_query', [ $this, 'parse_query' ], 0 );

		\add_action( 'save_post', [ $this, 'save_post' ] );
	}

	/**
	 * Initialize.
	 * 
	 * @return void
	 */
	public function init() {
		$post_types = [
			'page',
			'post',
			'orbis_project',
		];

		if ( current_user_can( 'freelancer' ) ) {
			$post_types = \get_post_types();
		}

		foreach ( $post_types as $post_type ) {
			\add_post_type_support( $post_type, 'orbis_teams' );
		}

		\remove_post_type_support( 'nav_menu_item', 'orbis_teams' );
	}

	/**
	 * Posts 2 Posts initialize.
	 * 
	 * @return void
	 */
	public function p2p_init() {
		$this->user_team_posts = \get_posts(
			[
				'post_type'         => 'orbis_team',
				'connected_type'    => 'orbis_teams_to_users',
				'connected_items'   => \wp_get_current_user(),
				'orbis_teams_query' => true,
				'nopaging'          => true,
			]
		);

		/**
		 * Each conencted team.
		 * 
		 * @link https://github.com/scribu/wp-posts-to-posts/wiki/each_connected#using-each_connected-multiple-times
		 */
		add_filter(
			'the_posts',
			function ( $posts, $query ) {
				if ( true === $query->get( 'orbis_teams_query' ) ) {
					return $posts;
				}

				\p2p_type( 'orbis_teams_to_posts' )->each_connected(
					$query,
					[
						'orbis_teams_query' => true,
					],
					'orbis_teams'
				);

				return $posts;
			},
			10,
			2
		);
	}

	/**
	 * Can user view post.
	 * 
	 * @param WP_Post $post Post.
	 * @return bool
	 */
	public function can_user_view_post( WP_Post $post ) {
		if ( 'orbis_team' === \get_post_type( $post ) ) {
			return true;
		}

		if ( \current_user_can( 'read_post', $post->ID ) ) {
			return true;
		}

		if ( ! \current_user_can( 'freelancer' ) ) {
			return true;
		}

		if ( \property_exists( $post, 'orbis_teams' ) ) {
			foreach ( $this->user_team_posts as $user_team_post ) {
				foreach ( $post->orbis_teams as $post_team_post ) {
					if ( $user_team_post->ID === $post_team_post->ID ) {
						return true;
					}
				}
			}
		}

		if ( \function_exists( '\members_get_post_roles' ) ) {
			$roles = \members_get_post_roles( $post->ID );

			if ( empty( $roles ) ) {
				return false;
			}
		}

		if ( \function_exists( '\members_can_current_user_view_post' ) ) {
			if ( \members_can_current_user_view_post( $post->ID ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Posts 2 Posts created connection.
	 * 
	 * @link https://github.com/scribu/wp-posts-to-posts/wiki/Actions-and-filters#p2p_created_connection
	 * @param int $p2p_id Posts 2 Posts connection ID.
	 * @return void
	 */
	public function p2p_created_connection( $p2p_id ) {
		$connection = \p2p_get_connection( $p2p_id );

		if ( false === $connection ) {
			return;
		}

		if ( 'orbis_teams_to_posts' !== $connection->p2p_type ) {
			return;
		}

		$team_id = $connection->p2p_from;
		$post_id = $connection->p2p_to;

		$team_post_ids = \get_post_meta( $post_id, '_orbis_team_post_id' );

		if ( \in_array( $team_id, $team_post_ids, true ) ) {
			return;
		}

		\add_post_meta( $post_id, '_orbis_team_post_id', $team_id );
	}

	/**
	 * Posts 2 Posts delete connection.
	 * 
	 * @link https://github.com/scribu/wp-posts-to-posts/wiki/Actions-and-filters#p2p_created_connection
	 * @param int $p2p_id Posts 2 Posts connection ID.
	 * @return void
	 */
	public function p2p_delete_connection( $p2p_id ) {
		$connection = \p2p_get_connection( $p2p_id );

		if ( false === $connection ) {
			return;
		}

		if ( 'orbis_teams_to_posts' !== $connection->p2p_type ) {
			return;
		}

		$team_id = $connection->p2p_from;
		$post_id = $connection->p2p_to;

		\delete_post_meta( $post_id, '_orbis_team_post_id', $team_id );
	}

	/**
	 * Posts 2 Posts delete connections.
	 * 
	 * @link https://github.com/scribu/wp-posts-to-posts/wiki/Actions-and-filters#p2p_created_connection
	 * @param array<int> $p2p_ids Posts 2 Posts connection IDs.
	 * @return void
	 */
	public function p2p_delete_connections( $p2p_ids ) {
		foreach ( $p2p_ids as $p2p_id ) {
			$this->p2p_delete_connection( $p2p_id );
		}
	}

	/**
	 * Save post.
	 * 
	 * @param int $post_id Post ID.
	 * @return void
	 */
	public function save_post( $post_id ) {
		$team_post_ids_now = \get_posts(
			[
				'fields'          => 'ids',
				'connected_type'  => 'orbis_teams_to_posts',
				'connected_items' => $post_id,
				'nopaging'        => true,
			]
		);

		$team_post_ids_old = \get_post_meta( $post_id, '_orbis_team_post_id' );

		$added   = \array_diff( $team_post_ids_now, $team_post_ids_old );
		$removed = \array_diff( $team_post_ids_old, $team_post_ids_now );

		foreach ( $added as $team_post_id ) {
			\add_post_meta( $post_id, '_orbis_team_post_id', $team_post_id );
		}

		foreach ( $removed as $team_post_id ) {
			\delete_post_meta( $post_id, '_orbis_team_post_id', $team_post_id );
		}
	}

	/**
	 * Parse query.
	 * 
	 * @param WP_Query $query WordPress posts query.
	 * @return void
	 */
	public function parse_query( $query ) {
		if ( \current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( true === $query->get( 'orbis_teams_query' ) ) {
			return;
		}

		$post_types = \array_filter(
			\wp_parse_list( $query->get( 'post_type' ) ),
			function ( $post_type ) {
				return \post_type_supports( $post_type, 'orbis_teams' );
			}
		);

		if ( 0 === count( $post_types ) ) {
			return;
		}

		$query->set(
			'meta_query',
			[
				'relation' => 'AND',
				[
					'key'     => '_orbis_team_post_id',
					'value'   => \wp_list_pluck( $this->user_team_posts, 'ID' ),
					'compare' => 'IN',
				],
				$query->get( 'meta_query' ),
			]
		);
	}
}
