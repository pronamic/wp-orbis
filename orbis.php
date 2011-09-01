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

		add_action('template_redirect', array(__CLASS__, 'templateRedirect'));

		add_filter('generate_rewrite_rules', array(__CLASS__, 'generateRewriteRules'));

		add_filter('query_vars', array(__CLASS__, 'queryVars'));

		add_filter('wp_loaded', array(__CLASS__, 'flushRules'));
		
		add_action('wp_ajax_orbis_test', array(__CLASS__, 'apiTest'));
	}
	
	public static function apiTest() {
		echo 'ok';
		
		die();
	}

	public static function initialize() {
		// Load plugin text domain
		$relPath = dirname(plugin_basename(self::$file)) . '/languages/';

		load_plugin_textdomain(self::TEXT_DOMAIN, false, $relPath);

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
		$rules['api/([^/]+)$'] = 'wp-admin/admin-ajax.php?action=orbis_test&' . $wpRewrite->preg_index(1);

		$wpRewrite->rules = $rules + $wpRewrite->rules;
	}

	public static function queryVars($queryVars) {
		$queryVars[] = 'project_id';

		return $queryVars;
	}

	public static function templateRedirect() {
		$id = get_query_var('project_id');

		if(!empty($id)) {
			
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

	public static function pageDomainsToInvoice() {
		include 'views/domains-to-invoice.php';
	}

	public static function pageSubscriptions() {
		include 'views/subscriptions.php';
	}
}

Orbis::bootstrap(__FILE__);
