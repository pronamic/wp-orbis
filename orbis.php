<?php
/*
Plugin Name: Orbis
Plugin URI: http://pronamic.eu/wordpress/orbis/
Description: This plugin creates an intranet environment in WordPress
Version: 0.1
Requires at least: 3.0
Author: Pronamic
Author URI: http://pronamic.eu/
*/

class Orbis {
	const SLUG = 'orbis';

	const TEXT_DOMAIN = 'orbis';

	public static $file;

	public static function bootstrap($file) {
		self::$file = $file;

		add_action('init', array(__CLASS__, 'initialize'));

		add_action('admin_init', array(__CLASS__, 'adminInitialize'));

		add_action('admin_menu', array(__CLASS__, 'adminMenu'));

		// High priority because the API is public avaiable (members plugin)
		add_action('template_redirect', array(__CLASS__, 'templateRedirect'), 0);

		add_filter('generate_rewrite_rules', array(__CLASS__, 'generateRewriteRules'));

		add_filter('query_vars', array(__CLASS__, 'queryVars'));

		add_filter('wp_loaded', array(__CLASS__, 'flushRules'));
	}

	public static function initialize() {
		// Load plugin text domain
		$relPath = dirname(plugin_basename(self::$file)) . '/languages/';

		load_plugin_textdomain(self::TEXT_DOMAIN, false, $relPath);

		register_post_type(
			'orbis_domain_name' , 
			array(
				'label' => __('Domain Names', self::TEXT_DOMAIN) , 
				'labels' => array(
					'name' => __('Domain Names', self::TEXT_DOMAIN) , 
					'singular_name' => __('Domain Name', self::TEXT_DOMAIN) , 
					'add_new' => _x('Add New', 'domain_name', self::TEXT_DOMAIN) , 
					'add_new_item' => __('Add New Domain Name', self::TEXT_DOMAIN)
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
				'label' => __('Projects', self::TEXT_DOMAIN) , 
				'labels' => array(
					'name' => __('Projects', self::TEXT_DOMAIN) , 
					'singular_name' => __('Project', self::TEXT_DOMAIN), 
					'add_new' => _x('Add New', 'project', self::TEXT_DOMAIN) , 
					'add_new_item' => __('Add New Project', self::TEXT_DOMAIN)
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
					'name' => _x( 'Categories', 'taxonomy general name', self::TEXT_DOMAIN) , 
					'singular_name' => _x( 'Category', 'taxonomy singular name', self::TEXT_DOMAIN) , 
					'search_items' =>  __( 'Search Categories', self::TEXT_DOMAIN) , 
					'all_items' => __( 'All Categories', self::TEXT_DOMAIN) , 
					'parent_item' => __( 'Parent Category', self::TEXT_DOMAIN) , 
					'parent_item_colon' => __( 'Parent Category:', self::TEXT_DOMAIN) , 
					'edit_item' => __( 'Edit Category', self::TEXT_DOMAIN) , 
					'update_item' => __( 'Update Category', self::TEXT_DOMAIN) , 
					'add_new_item' => __( 'Add New Category', self::TEXT_DOMAIN) , 
					'new_item_name' => __( 'New Category Name', self::TEXT_DOMAIN) , 
					'menu_name' => __( 'Categories', self::TEXT_DOMAIN) 
				) , 
				'show_ui' => true , 
				'query_var' => true , 
				'rewrite' => array('slug' => 'project-categorie')
			)
		);

		register_post_type(
			'orbis_person' , 
			array(
				'label' => __('Persons', self::TEXT_DOMAIN) , 
				'labels' => array(
					'name' => __('Persons', self::TEXT_DOMAIN) , 
					'singular_name' => __('Person', self::TEXT_DOMAIN)
				) ,
				'public' => true ,
				'menu_position' => 30 , 
				'menu_icon' => plugins_url('images/person.png', __FILE__) , 
				'rewrite' => array('slug' => 'personen') 
			)
		);

		register_taxonomy(
			'orbis_gender' , 
			array('orbis_person') , 
			array(
				'hierarchical' => true , 
				'labels' => array(
					'name' => _x( 'Genders', 'taxonomy general name', self::TEXT_DOMAIN) , 
					'singular_name' => _x( 'Gender', 'taxonomy singular name', self::TEXT_DOMAIN) , 
					'search_items' =>  __( 'Search Genders', self::TEXT_DOMAIN) , 
					'all_items' => __( 'All Genders', self::TEXT_DOMAIN) , 
					'parent_item' => __( 'Parent Gender', self::TEXT_DOMAIN) , 
					'parent_item_colon' => __( 'Parent Gender:', self::TEXT_DOMAIN) , 
					'edit_item' => __( 'Edit Gender', self::TEXT_DOMAIN) , 
					'update_item' => __( 'Update Gender', self::TEXT_DOMAIN) , 
					'add_new_item' => __( 'Add New Gender', self::TEXT_DOMAIN) , 
					'new_item_name' => __( 'New Gender Name', self::TEXT_DOMAIN) , 
					'menu_name' => __( 'Genders', self::TEXT_DOMAIN) 
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
					'name' => _x( 'Categories', 'taxonomy general name', self::TEXT_DOMAIN) , 
					'singular_name' => _x( 'Category', 'taxonomy singular name', self::TEXT_DOMAIN) , 
					'search_items' =>  __( 'Search Categories', self::TEXT_DOMAIN) , 
					'all_items' => __( 'All Categories', self::TEXT_DOMAIN) , 
					'parent_item' => __( 'Parent Category', self::TEXT_DOMAIN) , 
					'parent_item_colon' => __( 'Parent Category:', self::TEXT_DOMAIN) , 
					'edit_item' => __( 'Edit Category', self::TEXT_DOMAIN) , 
					'update_item' => __( 'Update Category', self::TEXT_DOMAIN) , 
					'add_new_item' => __( 'Add New Category', self::TEXT_DOMAIN) , 
					'new_item_name' => __( 'New Category Name', self::TEXT_DOMAIN) , 
					'menu_name' => __( 'Categories', self::TEXT_DOMAIN) 
				) , 
				'show_ui' => true , 
				'query_var' => true , 
				'rewrite' => array('slug' => 'persoon-categorie')
			)
		);

		/* register_post_type(
			'orbis_subscription' , 
			array(
				'label' => __('Subscriptions', self::TEXT_DOMAIN) , 
				'labels' => array(
					'name' => __('Subscriptions', self::TEXT_DOMAIN) , 
					'singular_name' => __('Subscription', self::TEXT_DOMAIN)
				) ,
				'public' => true ,
				'menu_position' => 200
			)
		); */
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
				$key = filter_input(INPUT_POST, 'key', FILTER_SANITIZE_STRING);
				$url = filter_input(INPUT_POST, 'url', FILTER_SANITIZE_STRING);
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
						subscription.name = %s
							AND
						subscription.license_key_md5 = %s
				';
				
				global $wpdb;

				$subscription = $wpdb->get_row($wpdb->prepare($query, $domain, $key));
				if($subscription != null) {
					$subscription->isValid = filter_var($subscription->isValid, FILTER_VALIDATE_BOOLEAN);
				}
				
				header('Content-Type: application/json');

				echo json_encode($subscription);
			}

			die();
		}
	}

	public static function adminInitialize() {
		

		// Styles
		wp_enqueue_style(
			'orbis-admin' , 
			plugins_url('css/admin.css', __FILE__)
		);
	}

	public static function adminMenu() {
		add_menu_page(
			$pageTitle = 'Orbis' , 
			$menuTitle = 'Orbis' , 
			$capability = 'manage_options' , 
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
			$capability = 'manage_options' , 
			$menuSlug = 'orbis_settings' , 
			$function = array(__CLASS__, 'pageSettings')
		);

		// @see _add_post_type_submenus()
		// @see wp-admin/menu.php
		add_submenu_page(
			$parentSlug = 'orbis' , 
			$pageTitle = 'Stats' , 
			$menuTitle = 'Stats' , 
			$capability = 'manage_options' , 
			$menuSlug = 'orbis_stats' , 
			$function = array(__CLASS__, 'pageStats')
		);
		
		// Projects
		add_menu_page(
			$pageTitle = __('Projects', self::TEXT_DOMAIN) , 
			$menuTitle = __('Projects', self::TEXT_DOMAIN) , 
			$capability = 'manage_options' , 
			$menuSlug = 'orbis_projects' , 
			$function = array(__CLASS__, 'pageProjects') , 
			$iconUrl = plugins_url('images/icon-16x16.png', __FILE__)
		);
		
		// Domains
		add_menu_page(
			$pageTitle = __('Domains', self::TEXT_DOMAIN) , 
			$menuTitle = __('Domains', self::TEXT_DOMAIN) , 
			$capability = 'manage_options' , 
			$menuSlug = 'orbis_domains' , 
			$function = array(__CLASS__, 'pageDomains') , 
			$iconUrl = plugins_url('images/icon-16x16.png', __FILE__)
		);

		add_submenu_page(
			$parentSlug = 'orbis_domains' , 
			$pageTitle = __('Domains to invoice', self::TEXT_DOMAIN) , 
			$menuTitle = __('To Invoice', self::TEXT_DOMAIN) , 
			$capability = 'manage_options' , 
			$menuSlug = 'orbis_domains_to_invoice' , 
			$function = array(__CLASS__, 'pageDomainsToInvoice')
		);
		
		// Subscriptions
		add_menu_page(
			$pageTitle = __('Subscriptions', self::TEXT_DOMAIN) , 
			$menuTitle = __('Subscriptions', self::TEXT_DOMAIN) , 
			$capability = 'manage_options' , 
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

Orbis::bootstrap(__FILE__);
