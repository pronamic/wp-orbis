<?php
/*
Plugin Name: Orbis
Plugin URI: http://pronamic.eu/wordpress/orbis/
Description: This plugin creates an intranet environment in WordPress

Version: 0.2.3
Requires at least: 3.0

Author: Pronamic
Author URI: http://pronamic.eu/

License: GPL
*/

require_once 'functions/functions.php';
require_once 'functions/persons.php';
require_once 'functions/companies.php';
require_once 'functions/projects.php';
require_once 'functions/log.php';
require_once 'functions/flot.php';
require_once 'includes/scheme.php';
require_once 'includes/shortcodes.php';
require_once 'admin/includes/upgrade.php';

function orbis_bootstrap() {
	// Classes
	require_once 'classes/orbis-plugin.php';
	require_once 'classes/orbis-core-admin.php';
	require_once 'classes/orbis-core-plugin.php';
	require_once 'classes/orbis-database.php';

	// Initialize
	global $orbis_plugin;

	$orbis_plugin = new Orbis_Core_Plugin( __FILE__ );
}

add_action( 'orbis_bootstrap', 'orbis_bootstrap', 1 );

// Bootstrap
do_action( 'orbis_bootstrap' );





class Orbis {
	public static $file;

	public static function bootstrap( $file ) {
		self::$file = $file;

		$GLOBALS['orbisdb'] = new Orbis_Database();

		add_action('init',       array(__CLASS__, 'init'));

		add_filter('generate_rewrite_rules', array(__CLASS__, 'generateRewriteRules'));

		add_filter('query_vars', array(__CLASS__, 'queryVars'));

		add_filter('wp_loaded', array(__CLASS__, 'flushRules'));
	}

	public static function init() {
		// Post types

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
					'menu_name'          => __( 'Projects', 'orbis' )
				) ,
				'public'          => true,
				'menu_position'   => 30,
				'menu_icon'       => plugins_url( 'images/project.png', __FILE__ ),
				'capability_type' => 'orbis_project',
				'supports'        => array( 'title', 'editor', 'author', 'comments' ),
				'has_archive'     => true, 
				'rewrite'         => array( 'slug' => 'projecten' ) 
			)
		);

		register_taxonomy(
			'orbis_project_category' , 
			array( 'orbis_project' ) , 
			array(
				'hierarchical' => true , 
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
					'menu_name'         => __( 'Categories', 'orbis') 
				) , 
				'show_ui'      => true , 
				'query_var'    => true , 
				'rewrite'      => array(
					'slug' => 'project-categorie'
				)
			)
		);

		register_post_type(
			'orbis_company' , 
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
					'menu_name'          => __( 'Companies', 'orbis' )
				) ,
				'public'          => true ,
				'menu_position'   => 30 , 
				'menu_icon'       => plugins_url('images/company.png', __FILE__) , 
				'capability_type' => array( 'orbis_company', 'orbis_companies' ),
				'supports'        => array('title', 'editor', 'author', 'comments', 'thumbnail') ,
				'has_archive'     => true , 
				'rewrite'         => array(
					'slug' => _x( 'companies', 'slug', 'orbis' )
				) 
			)
		);

		register_post_type(
			'orbis_person' , 
			array(
				'label'         => __('Persons', 'orbis') , 
				'labels'        => array(
					'name'          => __('Persons', 'orbis') , 
					'singular_name' => __('Person', 'orbis')
				) ,
				'public'        => true ,
				'menu_position' => 30 , 
				'menu_icon'     => plugins_url('images/person.png', __FILE__) , 
				'supports'      => array( 'title', 'editor', 'author', 'comments', 'thumbnail' ) ,
				'has_archive'   => true , 
				'rewrite'       => array( 
					'slug' => _x( 'persons', 'slug', 'orbis' )
				) 
			)
		);

		register_taxonomy(
			'orbis_gender' , 
			array('orbis_person') , 
			array(
				'hierarchical' => true , 
				'labels'       => array(
					'name'              => _x( 'Genders', 'taxonomy general name', 'orbis') , 
					'singular_name'     => _x( 'Gender', 'taxonomy singular name', 'orbis') , 
					'search_items'      => __( 'Search Genders', 'orbis') , 
					'all_items'         => __( 'All Genders', 'orbis') , 
					'parent_item'       => __( 'Parent Gender', 'orbis') , 
					'parent_item_colon' => __( 'Parent Gender:', 'orbis') , 
					'edit_item'         => __( 'Edit Gender', 'orbis') , 
					'update_item'       => __( 'Update Gender', 'orbis') , 
					'add_new_item'      => __( 'Add New Gender', 'orbis') , 
					'new_item_name'     => __( 'New Gender Name', 'orbis') , 
					'menu_name'         => __( 'Genders', 'orbis') 
				) , 
				'show_ui'      => true , 
				'query_var'    => true , 
				'rewrite'      => array(
					'slug' => 'geslacht'
				)
			)
		);

		register_taxonomy(
			'orbis_person_category' , 
			array('orbis_person') , 
			array(
				'hierarchical' => true , 
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
					'menu_name'         => __( 'Categories', 'orbis') 
				) , 
				'show_ui'      => true , 
				'query_var'    => true , 
				'rewrite'      => array(
					'slug' => 'persoon-categorie'
				)
			)
		);
	}

	public static function flushRules() {
		global $wp_rewrite;
	
		$wp_rewrite->flush_rules();
	}

	public static function generateRewriteRules($wpRewrite) {
		$rules = array();

		$rules['api/(.*)/(.*)$'] = 'index.php?api_call=true&api_object=' . $wpRewrite->preg_index(1) . '&api_method=' . $wpRewrite->preg_index(2);

		$wpRewrite->rules = $rules + $wpRewrite->rules;
	}

	public static function queryVars($queryVars) {
		$queryVars[] = 'api_call';
		$queryVars[] = 'api_object';
		$queryVars[] = 'api_method';

		return $queryVars;
	}
}

Orbis::bootstrap( __FILE__ );

function orbis_projects_posts_clauses( $pieces, $query ) {
	global $wpdb;

	$post_type = $query->get( 'post_type' );

	if ( $post_type == 'orbis_project' ) {
		$fields = ", 
			project.number_seconds AS project_number_seconds,
			project.finished AS project_is_finished,
			project.invoiced AS project_is_invoiced,
			principal.id AS principal_id,
			principal.name AS principal_name,
			principal.post_id AS principal_post_id
		";

		$join = " 
			LEFT JOIN 
				$wpdb->orbis_projects AS project 
					ON $wpdb->posts.ID = project.post_id 
			LEFT JOIN
				$wpdb->orbis_companies AS principal
					ON project.principal_id = principal.id
		";
		
		$pieces['join']   .= $join;
		$pieces['fields'] .= $fields;
	}

    return $pieces;
}

add_filter( 'posts_clauses', 'orbis_projects_posts_clauses', 10, 2 );
