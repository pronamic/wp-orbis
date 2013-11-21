<?php

function orbis_create_initial_post_types() {
	global $orbis_plugin;

	register_post_type(
		'orbis_project',
		array(
			'label'           => __( 'Projects', 'orbis' ),
			'labels'          => array(
				'name'               => __( 'Projects', 'orbis' ),
				'singular_name'      => __( 'Project', 'orbis' ),
				'add_new'            => _x( 'Add New', 'orbis_project', 'orbis' ),
				'add_new_item'       => __( 'Add New Project', 'orbis' ),
				'edit_item'          => __( 'Edit Project', 'orbis' ),
				'new_item'           => __( 'New Project', 'orbis' ),
				'all_items'          => __( 'All Projects', 'orbis' ),
				'view_item'          => __( 'View Project', 'orbis' ),
				'search_items'       => __( 'Search Projects', 'orbis' ),
				'not_found'          => __( 'No projects found.', 'orbis' ),
				'not_found_in_trash' => __( 'No projects found in Trash.', 'orbis' ),
				'parent_item_colon'  => __( 'Parent Project:', 'orbis' ),
				'menu_name'          => __( 'Projects', 'orbis' ),
			),
			'public'          => true,
			'menu_position'   => 30,
			'menu_icon'       => $orbis_plugin->plugin_url( 'images/project.png' ),
			'capability_type' => 'orbis_project',
			'supports'        => array( 'title', 'editor', 'author', 'comments' ),
			'has_archive'     => true,
			'rewrite'         => array(
				'slug' => _x( 'projects', 'slug', 'orbis' ),
			),
		)
	);

	register_taxonomy(
		'orbis_project_category',
		array( 'orbis_project' ),
		array(
			'hierarchical' => true ,
			'labels'       => array(
				'name'              => _x( 'Categories', 'taxonomy general name', 'orbis' ),
				'singular_name'     => _x( 'Category', 'taxonomy singular name', 'orbis' ),
				'search_items'      => __( 'Search Categories', 'orbis' ),
				'all_items'         => __( 'All Categories', 'orbis' ),
				'parent_item'       => __( 'Parent Category', 'orbis' ),
				'parent_item_colon' => __( 'Parent Category:', 'orbis' ),
				'edit_item'         => __( 'Edit Category', 'orbis' ),
				'update_item'       => __( 'Update Category', 'orbis' ),
				'add_new_item'      => __( 'Add New Category', 'orbis' ),
				'new_item_name'     => __( 'New Category Name', 'orbis' ),
				'menu_name'         => __( 'Categories', 'orbis' ),
			),
			'show_ui'      => true,
			'query_var'    => true,
			'rewrite'      => array(
				'slug' => _x( 'project-category', 'slug', 'orbis' ),
			),
		)
	);

	register_post_type(
		'orbis_company',
		array(
			'label'           => __( 'Companies', 'orbis' ),
			'labels'          => array(
				'name'               => __( 'Companies', 'orbis' ),
				'singular_name'      => __( 'Company', 'orbis' ),
				'add_new'            => _x( 'Add New', 'orbis_company', 'orbis' ),
				'add_new_item'       => __( 'Add New Company', 'orbis' ),
				'edit_item'          => __( 'Edit Company', 'orbis' ),
				'new_item'           => __( 'New Company', 'orbis' ),
				'all_items'          => __( 'All Companies', 'orbis' ),
				'view_item'          => __( 'View Company', 'orbis' ),
				'search_items'       => __( 'Search Companies', 'orbis' ),
				'not_found'          => __( 'No companies found.', 'orbis' ),
				'not_found_in_trash' => __( 'No companies found in Trash.', 'orbis' ),
				'parent_item_colon'  => __( 'Parent Company:', 'orbis' ),
				'menu_name'          => __( 'Companies', 'orbis' ),
			),
			'public'          => true,
			'menu_position'   => 30,
			'menu_icon'       => $orbis_plugin->plugin_url( 'images/company.png' ),
			'capability_type' => array( 'orbis_company', 'orbis_companies' ),
			'supports'        => array('title', 'editor', 'author', 'comments', 'thumbnail') ,
			'has_archive'     => true,
			'rewrite'         => array(
				'slug' => _x( 'companies', 'slug', 'orbis' ),
			),
		)
	);

	register_post_type(
		'orbis_person' ,
		array(
			'label'         => __( 'Persons', 'orbis' ),
			'labels'        => array(
				'name'          => __( 'Persons', 'orbis' ),
				'singular_name' => __( 'Person', 'orbis' ),
			),
			'public'        => true,
			'menu_position' => 30,
			'menu_icon'     => $orbis_plugin->plugin_url( 'images/person.png' ),
			'supports'      => array( 'title', 'editor', 'author', 'comments', 'thumbnail' ),
			'has_archive'   => true,
			'rewrite'       => array(
				'slug' => _x( 'persons', 'slug', 'orbis' ),
			),
		)
	);

	register_taxonomy(
		'orbis_person_category',
		array( 'orbis_person' ),
		array(
			'hierarchical' => true,
			'labels'       => array(
				'name'              => _x( 'Categories', 'taxonomy general name', 'orbis') ,
				'singular_name'     => _x( 'Category', 'taxonomy singular name', 'orbis') ,
				'search_items'      => __( 'Search Categories', 'orbis') ,
				'all_items'         => __( 'All Categories', 'orbis') ,
				'parent_item'       => __( 'Parent Category', 'orbis') ,
				'parent_item_colon' => __( 'Parent Category:', 'orbis') ,
				'edit_item'         => __( 'Edit Category', 'orbis') ,
				'update_item'       => __( 'Update Category', 'orbis') ,
				'add_new_item'      => __( 'Add New Category', 'orbis') ,
				'new_item_name'     => __( 'New Category Name', 'orbis') ,
				'menu_name'         => __( 'Categories', 'orbis'),
			),
			'show_ui'      => true,
			'query_var'    => true,
			'rewrite'      => array(
				'slug' => _x( 'person-category', 'slug', 'orbis' ),
			),
		)
	);
}

add_action( 'init', 'orbis_create_initial_post_types', 0 ); // highest priority

/**
 * Get the post type capabilties merged with the capabilities passed in
 *
 * @param array $capabilities
 * @return array
 */
function orbis_post_type_capabilities( $grant, array $capabilities ) {
	$default_capabilties = array(
		'edit_post'              => $grant,
		'read_post'              => $grant,
		'delete_post'            => $grant,
		'edit_posts'             => $grant,
		'edit_others_posts'      => $grant,
		'publish_posts'          => $grant,
		'read_private_posts'     => $grant,
		// map_meta_cap
		'delete_posts'           => $grant,
		'delete_private_posts'   => $grant,
		'delete_published_posts' => $grant,
		'delete_others_posts'    => $grant,
		'edit_private_posts'     => $grant,
		'edit_published_posts'   => $grant
	);

	return array_merge( $default_capabilties, $capabilities );
}

/**
 * Translate post type capabilites
 *
 * @param string $post_type
 * @param array $capabilities
 * @return array
 */
function orbis_translate_post_type_capabilities( $post_type, $capabilities, &$result = array() ) {
	global $wp_post_types;

	if ( isset( $wp_post_types[$post_type] ) ) {
		$cap = $wp_post_types[$post_type]->cap;

		foreach ( $capabilities as $capability => $grant ) {
			if ( isset( $cap->$capability ) ) {
				$result[$cap->$capability] = $grant;
			}
		}
	}

	return $result;
}
