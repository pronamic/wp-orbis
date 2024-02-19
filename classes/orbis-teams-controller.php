<?php

/**
 * Orbis teams controller class
 */
class Orbis_TeamsController {
	/**
	 * Setup.
	 * 
	 * @return void
	 */
	public function setup() {
		\add_action( 'init', [ $this, 'init' ], 300 );

		\add_action( 'p2p_init', [ $this, 'p2p_init' ] );
	}

	/**
	 * Initialize.
	 * 
	 * @return void
	 */
	public function init() {
		\register_post_type(
			'orbis_team',
			[
				'label'     => \__( 'Teams', 'orbis' ),
				'labels'    => [
					'name'                     => \__( 'Teams', 'orbis' ),
					'singular_name'            => \__( 'Team', 'orbis' ),
					'add_new'                  => \__( 'Add New Team', 'orbis' ),
					'add_new_item'             => \__( 'Add New Team', 'orbis' ),
					'edit_item'                => \__( 'Edit Team', 'orbis' ),
					'new_item'                 => \__( 'New Team', 'orbis' ),
					'view_item'                => \__( 'View Team', 'orbis' ),
					'view_items'               => \__( 'View Teams', 'orbis' ),
					'search_items'             => \__( 'Search Teams', 'orbis' ),
					'not_found'                => \__( 'No teams found.', 'orbis' ),
					'not_found_in_trash'       => \__( 'No teams found in Trash.', 'orbis' ),
					'parent_item_colon'        => \__( 'Parent Team:', 'orbis' ),
					'all_items'                => \__( 'All Teams', 'orbis' ),
					'archives'                 => \__( 'Team Archives', 'orbis' ),
					'attributes'               => \__( 'Team Attributes', 'orbis' ),
					'insert_into_item'         => \__( 'Insert into team', 'orbis' ),
					'uploaded_to_this_item'    => \__( 'Uploaded to this team', 'orbis' ),
					'featured_image'           => \__( 'Featured image', 'orbis' ),
					'set_featured_image'       => \__( 'Set featured image', 'orbis' ),
					'remove_featured_image'    => \__( 'Remove featured image', 'orbis' ),
					'use_featured_image'       => \__( 'Use as featured image', 'orbis' ),
					'filter_items_list'        => \__( 'Filter teams list', 'orbis' ),
					'filter_by_date'           => \__( 'Filter by date', 'orbis' ),
					'items_list_navigation'    => \__( 'Teams list navigation', 'orbis' ),
					'items_list'               => \__( 'Teams list', 'orbis' ),
					'item_published'           => \__( 'Team published.', 'orbis' ),
					'item_published_privately' => \__( 'Team published privately.', 'orbis' ),
					'item_reverted_to_draft'   => \__( 'Team reverted to draft.', 'orbis' ),
					'item_trashed'             => \__( 'Team trashed.', 'orbis' ),
					'item_scheduled'           => \__( 'Team scheduled.', 'orbis' ),
					'item_updated'             => \__( 'Team updated.', 'orbis' ),
					'item_link'                => \__( 'Team Link.', 'orbis' ),
					'item_link_description'    => \__( 'A link to a team.', 'orbis' ),
					'menu_name'                => \__( 'Teams', 'orbis' ),
				],
				'public'    => true,
				'menu_icon' => 'dashicons-groups',
				'supports'  => [
					'title',
					'editor',
					'comments',
					'revisions',
					'author',
				],
			]
		);
	}

	/**
	 * Posts 2 Posts init.
	 * 
	 * @link https://github.com/scribu/wp-posts-to-posts/wiki/Basic-usage
	 * @return void
	 */
	public function p2p_init() {
		\p2p_register_connection_type(
			[
				'name'        => 'orbis_teams_to_users',
				'from'        => 'orbis_team',
				'to'          => 'user',
				'title'       => [
					'from' => \__( 'Users', 'orbis' ),
					'to'   => \__( 'Teams', 'orbis' ),
				],
				'from_labels' => [
					'singular_name' => \__( 'Team', 'orbis' ),
					'search_items'  => \__( 'Search teams', 'orbis' ),
					'not_found'     => \__( 'No teams found.', 'orbis' ),
					'create'        => \__( 'Assign team', 'orbis' ),
				],
				'to_labels'   => [
					'singular_name' => \__( 'User', 'orbis' ),
					'search_items'  => \__( 'Search users', 'orbis' ),
					'not_found'     => \__( 'No users found.', 'orbis' ),
					'create'        => \__( 'Assign user', 'orbis' ),
				],
			]
		);

		$post_types = \array_filter(
			\get_post_types(),
			function ( $post_type ) {
				return \post_type_supports( $post_type, 'orbis_teams' );
			}
		);

		if ( count( $post_types ) > 0 ) {
			\p2p_register_connection_type(
				[
					'name'         => 'orbis_teams_to_posts',
					'from'         => 'orbis_team',
					'to'           => $post_types,
					'admin_column' => 'to',
					'title'        => [
						'from' => \__( 'Content', 'orbis' ),
						'to'   => \__( 'Teams', 'orbis' ),
					],
					'from_labels'  => [
						'singular_name' => \__( 'Team', 'orbis' ),
						'search_items'  => \__( 'Search teams', 'orbis' ),
						'not_found'     => \__( 'No teams found.', 'orbis' ),
						'create'        => \__( 'Connect team', 'orbis' ),
					],
					'to_labels'    => [
						'singular_name' => \__( 'Content', 'orbis' ),
						'search_items'  => \__( 'Search content', 'orbis' ),
						'not_found'     => \__( 'No content found.', 'orbis' ),
						'create'        => \__( 'Connect content', 'orbis' ),
					],
				]
			);
		}
	}
}
