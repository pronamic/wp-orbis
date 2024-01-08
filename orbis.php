<?php
/**
 * Orbis
 *
 * @package   Pronamic\Orbis
 * @author    Pronamic
 * @copyright 2024 Pronamic
 * @license   GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Orbis
 * Plugin URI:        https://wp.pronamic.directory/plugins/orbis/
 * Description:       Orbis is a powerful, extendable plugin to boost up your business. Project Management, Customer Relation Management & More…
 * Version:           1.3.3
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Pronamic
 * Author URI:        https://www.pronamic.eu/
 * Text Domain:       orbis
 * Domain Path:       /languages/
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Update URI:        https://wp.pronamic.directory/plugins/orbis/
 * GitHub URI:        https://github.com/pronamic/wp-orbis
 */

/**
 * Autoload
 */
require_once __DIR__ . '/vendor/autoload_packages.php';

/**
 * Includes
 */
require_once 'includes/functions.php';
require_once 'includes/persons.php';
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
