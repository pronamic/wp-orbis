<?php

/**
 * Projects active shortcode
 *
 * @param array $atts
 * @return string
 */
function orbis_shortcode_projects_active() {
	global $orbis_plugin;

	$return  = '';

	ob_start();

	$orbis_plugin->plugin_include( 'templates/projects.php' );

	$return = ob_get_contents();

	ob_end_clean();

	return $return;
}

add_shortcode( 'orbis_projects_active', 'orbis_shortcode_projects_active' );

/**
 * Projects without agreement
*
* @param array $atts
* @return string
*/
function orbis_shortcode_projects_without_agreement() {
	global $orbis_plugin;

	$return  = '';

	ob_start();

	$orbis_plugin->plugin_include( 'templates/projects-without-agreement.php' );

	$return = ob_get_contents();

	ob_end_clean();

	return $return;
}

add_shortcode( 'orbis_projects_without_agreement', 'orbis_shortcode_projects_without_agreement' );

/**
 * Projects to invoice
 *
 * @param array $atts
 * @return string
 */
function orbis_shortcode_projects_to_invoice() {
	global $orbis_plugin;

	$return  = '';

	ob_start();

	$orbis_plugin->plugin_include( 'templates/projects-to-invoice.php' );

	$return = ob_get_contents();

	ob_end_clean();

	return $return;
}

add_shortcode( 'orbis_projects_to_invoice', 'orbis_shortcode_projects_to_invoice' );
