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

		add_filter('wp_loaded',array(__CLASS__, 'flushRules'));
	}

	public static function initialize() {
		// Load plugin text domain
		$relPath = dirname(plugin_basename(self::$file)) . '/languages/';

		load_plugin_textdomain(self::TEXT_DOMAIN, false, $relPath);
	}

	public static function flushRules() {
		global $wp_rewrite;
	
		$wp_rewrite->flush_rules();
	}

	public static function generateRewriteRules($wpRewrite) {
		$rules = array();

		$rules['project/([^/]+)$'] = 'index.php?project_id=' . $wpRewrite->preg_index(1);

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
			$menuSlug = 'orbis-settings' , 
			$function = array(__CLASS__, 'pageSettings')
		);

		// @see _add_post_type_submenus()
		// @see wp-admin/menu.php
		add_submenu_page(
			$parentSlug = 'orbis' , 
			$pageTitle = 'Stats' , 
			$menuTitle = 'Stats' , 
			$capability = 'manage_options' , 
			$menuSlug = 'orbis-stats' , 
			$function = array(__CLASS__, 'pageStats')
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
}

Orbis::bootstrap(__FILE__);
