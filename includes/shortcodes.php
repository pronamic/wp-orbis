<?php

/**
 * Projects active shortcode
 * 
 * @param array $atts
 * @return string
 */
function orbis_shortcode_projects_active( $atts ) {
	global $wpdb;
	global $orbis_plugin;

	$return  = '';

	ob_start();
	
	$orbis_plugin->plugin_include( 'templates/projects.php' );
	
	$return = ob_get_contents();
	
	ob_end_clean();
	
	return $return;
}

add_shortcode( 'orbis_projects_active', 'orbis_shortcode_projects_active' );
