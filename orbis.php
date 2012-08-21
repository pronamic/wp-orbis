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

require_once 'functions/keychains.php';
require_once 'functions/domain_names.php';
require_once 'functions/persons.php';

class Orbis {
	const TEXT_DOMAIN = 'orbis';

	public static $file;

	public static function bootstrap( $file ) {
		self::$file = $file;

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
	
		$version = '0.1';
		if(get_option('orbis_version') != $version) {
			orbis_keychain_setup_roles();
		
			update_option('orbis_version', $version);
		}

		// Post types

		register_post_type(
			'orbis_domain_name' , 
			array(
				'label' => __('Domain Names', 'orbis') , 
				'labels' => array(
					'name' => __('Domain Names', 'orbis') , 
					'singular_name' => __('Domain Name', 'orbis') , 
					'add_new' => _x('Add New', 'domain_name', 'orbis') , 
					'add_new_item' => __('Add New Domain Name', 'orbis')
				) ,
				'public' => true ,
				'menu_position' => 30 , 
				'menu_icon' => plugins_url('images/domain_name.png', __FILE__) ,
				'supports' => array('title', 'comments') ,
				'has_archive' => true , 
				'rewrite' => array('slug' => 'domeinnamen') 
			)
		);

		register_post_type(
			'orbis_project' , 
			array(
				'label' => __('Projects', 'orbis') , 
				'labels' => array(
					'name' => __('Projects', 'orbis') , 
					'singular_name' => __('Project', 'orbis'), 
					'add_new' => _x('Add New', 'project', 'orbis') , 
					'add_new_item' => __('Add New Project', 'orbis')
				) ,
				'public' => true ,
				'menu_position' => 30 , 
				'menu_icon' => plugins_url('images/project.png', __FILE__) , 
				'supports' => array('title', 'editor', 'author', 'comments') ,
				'has_archive' => true , 
				'rewrite' => array('slug' => 'projecten') 
			)
		);

		register_taxonomy(
			'orbis_project_category' , 
			array('orbis_project') , 
			array(
				'hierarchical' => true , 
				'labels' => array(
					'name' => _x( 'Categories', 'taxonomy general name', 'orbis') , 
					'singular_name' => _x( 'Category', 'taxonomy singular name', 'orbis') , 
					'search_items' =>  __( 'Search Categories', 'orbis') , 
					'all_items' => __( 'All Categories', 'orbis') , 
					'parent_item' => __( 'Parent Category', 'orbis') , 
					'parent_item_colon' => __( 'Parent Category:', 'orbis') , 
					'edit_item' => __( 'Edit Category', 'orbis') , 
					'update_item' => __( 'Update Category', 'orbis') , 
					'add_new_item' => __( 'Add New Category', 'orbis') , 
					'new_item_name' => __( 'New Category Name', 'orbis') , 
					'menu_name' => __( 'Categories', 'orbis') 
				) , 
				'show_ui' => true , 
				'query_var' => true , 
				'rewrite' => array('slug' => 'project-categorie')
			)
		);

		register_post_type(
			'orbis_company' , 
			array(
				'label' => __('Companies', 'orbis') , 
				'labels' => array(
					'name' => __('Companies', 'orbis') , 
					'singular_name' => __('Company', 'orbis')
				) ,
				'public' => true ,
				'menu_position' => 30 , 
				'menu_icon' => plugins_url('images/company.png', __FILE__) , 
				'supports' => array('title', 'editor', 'author', 'comments', 'thumbnail') ,
				'has_archive' => true , 
				'rewrite' => array('slug' => _x('companies', 'slug', 'orbis')) 
			)
		);

		register_post_type(
			'orbis_person' , 
			array(
				'label' => __('Persons', 'orbis') , 
				'labels' => array(
					'name' => __('Persons', 'orbis') , 
					'singular_name' => __('Person', 'orbis')
				) ,
				'public' => true ,
				'menu_position' => 30 , 
				'menu_icon' => plugins_url('images/person.png', __FILE__) , 
				'supports' => array('title', 'editor', 'author', 'comments', 'thumbnail') ,
				'has_archive' => true , 
				'rewrite' => array('slug' => _x('persons', 'slug', 'orbis')) 
			)
		);

		register_taxonomy(
			'orbis_gender' , 
			array('orbis_person') , 
			array(
				'hierarchical' => true , 
				'labels' => array(
					'name' => _x( 'Genders', 'taxonomy general name', 'orbis') , 
					'singular_name' => _x( 'Gender', 'taxonomy singular name', 'orbis') , 
					'search_items' =>  __( 'Search Genders', 'orbis') , 
					'all_items' => __( 'All Genders', 'orbis') , 
					'parent_item' => __( 'Parent Gender', 'orbis') , 
					'parent_item_colon' => __( 'Parent Gender:', 'orbis') , 
					'edit_item' => __( 'Edit Gender', 'orbis') , 
					'update_item' => __( 'Update Gender', 'orbis') , 
					'add_new_item' => __( 'Add New Gender', 'orbis') , 
					'new_item_name' => __( 'New Gender Name', 'orbis') , 
					'menu_name' => __( 'Genders', 'orbis') 
				) , 
				'show_ui' => true , 
				'query_var' => true , 
				'rewrite' => array('slug' => 'geslacht')
			)
		);

		register_taxonomy(
			'orbis_person_category' , 
			array('orbis_person') , 
			array(
				'hierarchical' => true , 
				'labels' => array(
					'name' => _x( 'Categories', 'taxonomy general name', 'orbis') , 
					'singular_name' => _x( 'Category', 'taxonomy singular name', 'orbis') , 
					'search_items' =>  __( 'Search Categories', 'orbis') , 
					'all_items' => __( 'All Categories', 'orbis') , 
					'parent_item' => __( 'Parent Category', 'orbis') , 
					'parent_item_colon' => __( 'Parent Category:', 'orbis') , 
					'edit_item' => __( 'Edit Category', 'orbis') , 
					'update_item' => __( 'Update Category', 'orbis') , 
					'add_new_item' => __( 'Add New Category', 'orbis') , 
					'new_item_name' => __( 'New Category Name', 'orbis') , 
					'menu_name' => __( 'Categories', 'orbis') 
				) , 
				'show_ui' => true , 
				'query_var' => true , 
				'rewrite' => array('slug' => 'persoon-categorie')
			)
		);

		register_post_type(
			'orbis_keychain' , 
			array(
				'label' => __('Keychains', 'orbis') , 
				'labels' => array(
					'name' => __('Keychains', 'orbis') , 
					'singular_name' => __('Keychain', 'orbis') ,
					'add_new' => _x('Add New', 'orbis_keychain', 'orbis') ,
					'add_new_item' => __('Add New Keychain', 'orbis') ,
					'edit_item' => __('Edit Keychain', 'orbis') ,
					'new_item' => __('New Keychain', 'orbis') ,
					'view_item' => __('View Keychain', 'orbis') ,
					'search_items' => __('Search Keychains', 'orbis') ,
					'not_found' => __('No keychains found', 'orbis') ,
					'not_found_in_trash' => __('No keychains found in Trash', 'orbis') 
				) ,
				'public' => true ,
				'menu_position' => 30 , 
				'menu_icon' => plugins_url('images/keychain.png', __FILE__) , 
				'capability_type' => array('keychain', 'keychains') , 
				'supports' => array('title', 'editor', 'author', 'comments') , 
				'has_archive' => true , 
				'rewrite' => array('slug' => _x('keychains', 'slug', 'orbis')) 
			)
		);

		register_taxonomy(
			'orbis_keychain_category' , 
			array('orbis_keychain') , 
			array(
				'hierarchical' => true , 
				'labels' => array(
					'name' => _x( 'Categories', 'orbis_keychain_category', 'orbis') , 
					'singular_name' => _x( 'Category', 'orbis_keychain_category', 'orbis') , 
					'search_items' =>  __( 'Search Categories', 'orbis') , 
					'all_items' => __( 'All Categories', 'orbis') , 
					'parent_item' => __( 'Parent Category', 'orbis') , 
					'parent_item_colon' => __( 'Parent Category:', 'orbis') , 
					'edit_item' => __( 'Edit Category', 'orbis') , 
					'update_item' => __( 'Update Category', 'orbis') , 
					'add_new_item' => __( 'Add New Category', 'orbis') , 
					'new_item_name' => __( 'New Category Name', 'orbis') , 
					'menu_name' => __( 'Categories', 'orbis') 
				) , 
				'show_ui' => true , 
				'query_var' => true , 
				'rewrite' => array('slug' => _x('keychain-categorie', 'slug', 'orbis'))
			)
		);

		register_taxonomy(
			'orbis_keychain_tag' , 
			array('orbis_keychain') , 
			array(
				'hierarchical' => false , 
				'labels' => array(
					'name' => _x( 'Tags', 'orbis_keychain_category', 'orbis') , 
					'singular_name' => _x( 'Tag', 'orbis_keychain_category', 'orbis') , 
					'search_items' =>  __( 'Search Tags', 'orbis') , 
					'all_items' => __( 'All Tags', 'orbis') , 
					'parent_item' => __( 'Parent Tag', 'orbis') , 
					'parent_item_colon' => __( 'Parent Tag:', 'orbis') , 
					'edit_item' => __( 'Edit Tag', 'orbis') , 
					'update_item' => __( 'Update Tag', 'orbis') , 
					'add_new_item' => __( 'Add New Tag', 'orbis') , 
					'new_item_name' => __( 'New Tag Name', 'orbis') , 
					'menu_name' => __( 'Tags', 'orbis') 
				) , 
				'show_ui' => true , 
				'query_var' => true , 
				'rewrite' => array('slug' => _x('keychain-tag', 'slug', 'orbis'))
			)
		);

		register_post_type(
			'orbis_subscription' , 
			array(
				'label' => __('Subscriptions', 'orbis') , 
				'labels' => array(
					'name' => __('Subscriptions', 'orbis') , 
					'singular_name' => __('Subscription', 'orbis')
				) ,
				'public' => true ,
				'menu_position' => 30 , 
				'menu_icon' => plugins_url('images/subscription.png', __FILE__) , 
				'supports' => array('title', 'editor', 'author', 'comments', 'thumbnail') ,
				'has_archive' => true , 
				'rewrite' => array('slug' => _x('subscriptions', 'slug', 'orbis')) 
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
		
		$apiCall = get_query_var('api_call');
		
		if(!empty($apiCall)) {
			$object = get_query_var('api_object');
			$method = get_query_var('api_method');
			
			if($object == 'licenses' && $method == 'show') {
				$type = INPUT_POST;

				$key = filter_input($type, 'key', FILTER_SANITIZE_STRING);
				$url = filter_input($type, 'url', FILTER_SANITIZE_STRING);

				$domain = parse_url($url, PHP_URL_HOST);
				if(substr($domain, 0, 4) == 'www.') {
					$domain = substr($domain, 4);
				}

				$query = '
					SELECT 
						subscription.id ,  
						subscription.name AS subscriptionName , 
						subscription.activation_date AS activationDate , 
						subscription.expiration_date AS expirationDate ,
						subscription.cancel_date AS cancelDate , 
						subscription.update_date AS updateDate ,
						subscription.license_key AS licenseKey , 
						subscription.expiration_date > NOW() AS isValid , 
						company.name AS companyName ,
						type.name AS typeName , 
						type.price AS price , 
						domain_name.domain_name AS domainName 
					FROM 
						orbis_subscriptions AS subscription
							LEFT JOIN
						orbis_companies AS company
								ON subscription.company_id = company.id
							LEFT JOIN
						orbis_subscription_types AS type
								ON subscription.type_id = type.id
							LEFT JOIN
						orbis_domain_names AS domain_name
								ON subscription.domain_name_id = domain_name.id
					WHERE
						subscription.license_key_md5 = %s
				';
				
				global $wpdb;

				$query = $wpdb->prepare($query, $key);

				$subscription = $wpdb->get_row($query);

				if($subscription != null) {
					if($subscription->subscriptionName != '*') {
						$isValidDomain = $subscription->subscriptionName == $domain;
						
						$subscription->isValid &= $isValidDomain;
					}
					
					$subscription->isValid = filter_var($subscription->isValid, FILTER_VALIDATE_BOOLEAN);
				}
				
				header('Content-Type: application/json');

				echo json_encode($subscription);
			}

			die();
		}
	}

	public static function admin_init() {
		

		// Styles
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
		
		// Companies
		add_menu_page(
			$pageTitle = __('Companies', 'orbis') , 
			$menuTitle = __('Companies', 'orbis') , 
			$capability = 'orbis_view_companies' , 
			$menuSlug = 'orbis_companies' , 
			$function = array(__CLASS__, 'pageCompanies') , 
			$iconUrl = plugins_url('images/icon-16x16.png', __FILE__)
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

	public static function pageCompanies() {
		include 'views/companies.php';
	}

	public static function pageSubscriptions() {
		include 'views/subscriptions.php';
	}
}

Orbis::bootstrap(__FILE__);
