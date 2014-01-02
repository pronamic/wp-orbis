<?php
/*
Plugin Name: Orbis
Plugin URI: http://wp.pronamic.eu/plugins/orbis/
Description: Orbis is a powerful, extendable plugin to boost up your business. Project Management, Customer Relation Management & More...

Version: 1.0.1
Requires at least: 3.0

Author: Pronamic
Author URI: http://www.pronamic.eu/

Text Domain: orbis
Domain Path: /languages/

License: GPL

GitHub URI: https://github.com/pronamic/wp-orbis
*/

require_once 'includes/functions.php';
require_once 'includes/persons.php';
require_once 'includes/companies.php';
require_once 'includes/projects.php';
require_once 'includes/log.php';
require_once 'includes/flot.php';
require_once 'includes/scheme.php';
require_once 'includes/shortcodes.php';
require_once 'admin/includes/upgrade.php';

function orbis_bootstrap() {
	// Classes
	require_once 'classes/orbis-plugin.php';
	require_once 'classes/orbis-core-admin.php';
	require_once 'classes/orbis-core-plugin.php';
	require_once 'classes/orbis-api.php';
	require_once 'classes/orbis-plugin-manager.php';

	// Initialize
	global $orbis_plugin;

	$orbis_plugin = new Orbis_Core_Plugin( __FILE__ );
}

add_action( 'orbis_bootstrap', 'orbis_bootstrap', 1 );

// Bootstrap
do_action( 'orbis_bootstrap' );
