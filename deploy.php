<?php
/**
 * Deploy
 *
 * @package Pronamic\Orbis
 */

declare(strict_types=1);

namespace Deployer;

require 'recipe/common.php';

set( 'plugin_slug', 'orbis' );

set( 'build_path', './build/' );

host( 'orbis.pronamic.nl' )
	->set( 'hostname', 'esm7.siteground.biz' )
	->set( 'remote_user', 'u155-jlog1cramrrx' )
	->set( 'port', 18765 )
	->set( 'deploy_path', '~/projects/wp-orbis' )
	->set( 'plugins_dir', '~/www/orbis.pronamic.nl/public_html/wp-content/plugins' );

/**
 * Build.
 *
 * @link https://github.com/woocommerce/woocommerce/blob/48fdb94bf311c977d15cbaa3d8dab66bac01feb7/plugins/woocommerce/.distignore
 * @link https://github.com/woocommerce/woocommerce/blob/48fdb94bf311c977d15cbaa3d8dab66bac01feb7/plugins/woocommerce/bin/build-zip.sh
 */
task(
	'build',
	function () {
		runLocally( 'composer run-script build' );
	}
);

task(
	'deploy:update_code',
	function () {
		upload( '{{build_path}}/orbis/', '{{release_path}}' );
	}
);

task(
	'deploy:symlink_plugin',
	function () {
		run( 'ln -sfn {{deploy_path}}/current {{plugins_dir}}/{{plugin_slug}}' );
	}
);

after( 'deploy:symlink', 'deploy:symlink_plugin' );

task(
	'deploy',
	[
		'build',
		'deploy:prepare',
		'deploy:publish',
	]
);
