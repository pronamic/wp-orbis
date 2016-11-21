<?php
/*
Plugin Name: Orbis
Plugin URI: https://www.pronamic.eu/plugins/orbis/
Description: Orbis is a powerful, extendable plugin to boost up your business. Project Management, Customer Relation Management & More...

Version: 1.3.3
Requires at least: 3.0

Author: Pronamic
Author URI: https://www.pronamic.eu/

Text Domain: orbis
Domain Path: /languages/

License: GPL

GitHub URI: https://github.com/wp-orbis/wp-orbis
*/

/**
 * Autoload
 */
require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

/**
 * Includes
 */
require_once 'includes/functions.php';
require_once 'includes/persons.php';
require_once 'includes/companies.php';
require_once 'includes/log.php';
require_once 'includes/flot.php';
require_once 'admin/includes/upgrade.php';

/**
 * Bootstrap
 */
function orbis_bootstrap() {
	// Initialize
	global $orbis_plugin;

	$orbis_plugin = new Orbis_Core_Plugin( __FILE__ );
}

add_action( 'orbis_bootstrap', 'orbis_bootstrap', 1 );

// Bootstrap
do_action( 'orbis_bootstrap' );
