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
require_once 'functions/projects-template.php';
require_once 'functions/log.php';
require_once 'functions/flot.php';
require_once 'includes/scheme.php';
require_once 'admin/includes/upgrade.php';

class Orbis_Database {
	private $wpdb;

	public $projects;
	public $companies;

	public function __construct() {
		global $wpdb;

		$this->wpdb = $wpdb;
		$this->projects = 'orbis_projects2';
		$this->companies = 'orbis_companies2';
	}
}

class Orbis_Plugin {
	public $file;

	public $dirname;

	public function __construct( $file ) {
		$this->file    = $file;
		$this->dirname = dirname( $file );

		add_action( 'init', array( $this, 'init' ) );
	}
	
	public function init() {
		
	}
	
	public function plugin_url( $path ) {
		return plugins_url( $path, $this->file );
	}
	
	public function plugin_include( $path ) {
		include $this->dirname . '/' . $path;
	}

	public function load_textdomain( $domain, $path = '' ) {
		$plugin_rel_path = dirname( plugin_basename( $this->file ) ) . $path;

		load_plugin_textdomain( $domain, false, $plugin_rel_path );
	}
}

class Orbis {
	public static $file;

	public static function bootstrap( $file ) {
		self::$file = $file;

		$GLOBALS['orbisdb'] = new Orbis_Database();

		add_action('init',       array(__CLASS__, 'init'));

		add_action('admin_init', array(__CLASS__, 'admin_init'));

		add_action('admin_menu', array(__CLASS__, 'admin_menu'));

		// High priority because the API is public avaiable (members plugin)
		add_action('template_redirect', array(__CLASS__, 'templateRedirect'), 0);

		add_filter('generate_rewrite_rules', array(__CLASS__, 'generateRewriteRules'));

		add_filter('query_vars', array(__CLASS__, 'queryVars'));

		add_filter('wp_loaded', array(__CLASS__, 'flushRules'));
	}

	public static function init() {
		// Load plugin text domain
		$relPath = dirname( plugin_basename( self::$file ) ) . '/languages/';

		load_plugin_textdomain( 'orbis', false, $relPath );
	
		$version = '0.1.1';
		if(get_option('orbis_version') != $version) {
			orbis_keychain_setup_roles();
			orbis_make_db();

			update_option('orbis_version', $version);
		}

		// Post types

		register_post_type(
			'orbis_project' , 
			array(
				'label'         => __( 'Projects', 'orbis' ) , 
				'labels'        => array(
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
				'public'        => true ,
				'menu_position' => 30 , 
				'menu_icon'     => plugins_url( 'images/project.png', __FILE__ ) , 
				'supports'      => array( 'title', 'editor', 'author', 'comments' ) ,
				'has_archive'   => true , 
				'rewrite'       => array( 'slug' => 'projecten' ) 
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
				'label'         => __( 'Companies', 'orbis' ), 
				'labels'        => array(
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
				'public'        => true ,
				'menu_position' => 30 , 
				'menu_icon'     => plugins_url('images/company.png', __FILE__) , 
				'supports'      => array('title', 'editor', 'author', 'comments', 'thumbnail') ,
				'has_archive'   => true , 
				'rewrite'       => array(
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

		$rules['project/([^/]+)$'] = 'index.php?project_id=' . $wpRewrite->preg_index(1);
		$rules['api/(.*)/(.*)$'] = 'index.php?api_call=true&api_object=' . $wpRewrite->preg_index(1) . '&api_method=' . $wpRewrite->preg_index(2);

		$wpRewrite->rules = $rules + $wpRewrite->rules;
	}

	public static function queryVars($queryVars) {
		$queryVars[] = 'project_id';
		$queryVars[] = 'api_call';
		$queryVars[] = 'api_object';
		$queryVars[] = 'api_method';

		return $queryVars;
	}

	public static function templateRedirect() {
		$id = get_query_var('project_id');

		if(!empty($id)) {
			var_dump($id);
			die();
		}
	}

	public static function admin_init() {
		// Scripts
		wp_enqueue_script(
			'select2',
			plugins_url( 'includes/select2/select2.js', __FILE__ ),
			array( 'jquery' ),
			'3.2'
		);
		

		// Styles
		wp_enqueue_style(
			'orbis-select2' , 
			plugins_url('includes/select2/select2.css', __FILE__)
		);

		wp_enqueue_style(
			'orbis-admin' , 
			plugins_url('css/admin.css', __FILE__)
		);
	}

	public static function admin_menu() {
		add_menu_page(
			$pageTitle = 'Orbis' , 
			$menuTitle = 'Orbis' , 
			$capability = 'orbis_view' , 
			$menuSlug = 'orbis' , 
			$function = array(__CLASS__, 'page') , 
			$iconUrl = plugins_url('images/icon-16x16.png', __FILE__)
		);

		// @see _add_post_type_submenus()
		// @see wp-admin/menu.php
		add_submenu_page(
			$parentSlug = 'orbis' , 
			$pageTitle = 'Settings' , 
			$menuTitle = 'Settings' , 
			$capability = 'orbis_view_settings' , 
			$menuSlug = 'orbis_settings' , 
			$function = array(__CLASS__, 'pageSettings')
		);

		// @see _add_post_type_submenus()
		// @see wp-admin/menu.php
		add_submenu_page(
			$parentSlug = 'orbis' , 
			$pageTitle = 'Stats' , 
			$menuTitle = 'Stats' , 
			$capability = 'orbis_view_stats' , 
			$menuSlug = 'orbis_stats' , 
			$function = array(__CLASS__, 'pageStats')
		);
		
		// Projects
		add_menu_page(
			$pageTitle = __('Projects', 'orbis') , 
			$menuTitle = __('Projects', 'orbis') , 
			$capability = 'orbis_view_projects' , 
			$menuSlug = 'orbis_projects' , 
			$function = array(__CLASS__, 'pageProjects') , 
			$iconUrl = plugins_url('images/icon-16x16.png', __FILE__)
		);
		
		// Domains
		add_menu_page(
			$pageTitle = __('Domains', 'orbis') , 
			$menuTitle = __('Domains', 'orbis') , 
			$capability = 'orbis_view_domains' , 
			$menuSlug = 'orbis_domains' , 
			$function = array(__CLASS__, 'pageDomains') , 
			$iconUrl = plugins_url('images/icon-16x16.png', __FILE__)
		);

		add_submenu_page(
			$parentSlug = 'orbis_domains' , 
			$pageTitle = __('Domains to invoice', 'orbis') , 
			$menuTitle = __('To Invoice', 'orbis') , 
			$capability = 'orbis_view_domains_to_invoice' , 
			$menuSlug = 'orbis_domains_to_invoice' , 
			$function = array(__CLASS__, 'pageDomainsToInvoice')
		);
		
		// Subscriptions
		add_menu_page(
			$pageTitle = __('Subscriptions', 'orbis') , 
			$menuTitle = __('Subscriptions', 'orbis') , 
			$capability = 'orbis_view_subscriptions' , 
			$menuSlug = 'orbis_subscriptions' , 
			$function = array(__CLASS__, 'pageSubscriptions') , 
			$iconUrl = plugins_url('images/icon-16x16.png', __FILE__)
		);
	}

	public static function page() {
		include 'views/orbis.php';
	}

	public static function pageSettings() {
		include 'views/settings.php';
	}

	public static function pageStats() {
		include 'views/stats.php';
	}

	public static function pageProjects() {
		include 'views/projects.php';
	}

	public static function pageDomains() {
		include 'views/domains.php';
	}

	public static function pageDomainsToInvoice() {
		include 'views/domains-to-invoice.php';
	}

	public static function pageSubscriptions() {
		include 'views/subscriptions.php';
	}
}

Orbis::bootstrap( __FILE__ );

function orbis_projects_posts_clauses( $pieces, $query ) {
	global $wpdb;

	$post_type = $query->get( 'post_type' );

	if ( $post_type == 'orbis_project' ) {
		$fields = ", 
			project.number_seconds AS project_number_seconds ,
			project.finished AS project_is_finished ,  
			project.invoiced AS project_is_invoiced ,
			principal.id AS principal_id ,
			principal.name AS principal_name , 
			principal.post_id AS principal_post_id
		";

		$join = " 
			LEFT JOIN 
				orbis_projects AS project 
					ON $wpdb->posts.ID = project.post_id 
			LEFT JOIN
				orbis_companies AS principal
					ON project.principal_id = principal.id
		";
		
		$pieces['join']   .= $join;
		$pieces['fields'] .= $fields;
	}

    return $pieces;
}

add_filter( 'posts_clauses', 'orbis_projects_posts_clauses', 10, 2 );


function orbis_format_seconds( $seconds, $format = 'H:m' ) {
	$hours = $seconds / 3600;
	$minutes = ( $seconds % 3600 ) / 60;

	$search = array( 'H', 'm' );
	$replace = array(
		sprintf( '%02d', $hours ),
		sprintf('%02d', $minutes )
	);

	return str_replace( $search, $replace, $format );
}

function orbis_project_get_the_time( $format = 'H:m' ) {
	global $post;

	$time = null;

	if ( isset( $post->project_number_seconds ) ) {
		$time = orbis_format_seconds( $post->project_number_seconds, $format );
	} 

	return $time;
}

function orbis_project_the_time( $format = 'H:m' ) {
	echo orbis_project_get_the_time( $format );
}


do_action( 'orbis_bootstrap' );
