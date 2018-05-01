<?php

function orbis_create_initial_post_types() {
	register_post_type(
		'orbis_person',
		array(
			'label'         => __( 'Contacts', 'orbis' ),
			'labels'        => array(
				'name'               => _x( 'Contacts', 'post type general name', 'orbis' ),
				'singular_name'      => _x( 'Contact', 'post type singular name', 'orbis' ),
				'menu_name'          => _x( 'Contacts', 'admin menu', 'orbis' ),
				'name_admin_bar'     => _x( 'Contact', 'add new on admin bar', 'orbis' ),
				'add_new'            => _x( 'Add New', 'contact', 'orbis' ),
				'add_new_item'       => __( 'Add New Contact', 'orbis' ),
				'new_item'           => __( 'New Contact', 'orbis' ),
				'edit_item'          => __( 'Edit Contact', 'orbis' ),
				'view_item'          => __( 'View Contact', 'orbis' ),
				'all_items'          => __( 'All Contacts', 'orbis' ),
				'search_items'       => __( 'Search Contacts', 'orbis' ),
				'parent_item_colon'  => __( 'Parent Contact:', 'orbis' ),
				'not_found'          => __( 'No contacts found.', 'orbis' ),
				'not_found_in_trash' => __( 'No contacts found in Trash.', 'orbis' ),
			),
			'public'        => true,
			'menu_position' => 30,
			'menu_icon'     => 'dashicons-businessman',
			'supports'      => array( 'title', 'editor', 'author', 'comments', 'thumbnail', 'custom-fields', 'revisions' ),
			'has_archive'   => true,
			'show_in_rest'  => true,
			'rest_base'     => 'orbis/persons',
			'rewrite'       => array(
				'slug' => _x( 'contacts', 'slug', 'orbis' ),
			),
		)
	);

	register_taxonomy(
		'orbis_gender',
		array( 'orbis_person' ),
		array(
			'hierarchical'       => true,
			'labels'             => array(
				'name'              => _x( 'Genders', 'taxonomy general name', 'orbis' ),
				'singular_name'     => _x( 'Gender', 'taxonomy singular name', 'orbis' ),
				'search_items'      => __( 'Search Genders', 'orbis' ),
				'all_items'         => __( 'All Genders', 'orbis' ),
				'parent_item'       => __( 'Parent Gender', 'orbis' ),
				'parent_item_colon' => __( 'Parent Gender:', 'orbis' ),
				'edit_item'         => __( 'Edit Gender', 'orbis' ),
				'update_item'       => __( 'Update Gender', 'orbis' ),
				'add_new_item'      => __( 'Add New Gender', 'orbis' ),
				'new_item_name'     => __( 'New Gender Name', 'orbis' ),
				'menu_name'         => __( 'Genders', 'orbis' ),
			),
			'public'             => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_nav_menus'  => true,
			'show_tagcloud'      => false,
			'show_in_quick_edit' => false,
			'meta_box_cb'        => false,
			'query_var'          => true,
			'rewrite'            => array(
				'slug' => _x( 'genders', 'slug', 'orbis' ),
			),
		)
	);

	register_taxonomy(
		'orbis_person_category',
		array( 'orbis_person' ),
		array(
			'hierarchical' => true,
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
				'slug' => _x( 'contact-category', 'slug', 'orbis' ),
			),
		)
	);
}

add_action( 'init', 'orbis_create_initial_post_types', 10 ); // highest priority

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
		'edit_published_posts'   => $grant,
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

	if ( isset( $wp_post_types[ $post_type ] ) ) {
		$cap = $wp_post_types[ $post_type ]->cap;

		foreach ( $capabilities as $capability => $grant ) {
			if ( isset( $cap->$capability ) ) {
				$result[ $cap->$capability ] = $grant;
			}
		}
	}

	return $result;
}
