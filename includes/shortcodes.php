<?php

/**
 * Subscriptions to invoice shortcode
 * 
 * @param unknown $atts
 * @return string
 */
function orbis_shortcode_projects_active( $atts ) {
	global $wpdb;

	$return  = '';

	ob_start();
	
	include dirname( __FILE__ ) . '/../templates/projects.php';
	
	$return = ob_get_contents();
	
	ob_end_clean();
	
	return $return;
}

add_shortcode( 'orbis_projects_active', 'orbis_shortcode_projects_active' );
