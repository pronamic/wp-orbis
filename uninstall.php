<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

$prefix = defined( 'ORBIS_TABLE_PREFIX' ) ? ORBIS_TABLE_PREFIX : $wpdb->prefix;

//////////////////////////////////////////////////
// Delete tables
//////////////////////////////////////////////////

$wpdb->query( "DROP TABLE IF EXISTS {$prefix}orbis_companies" );
$wpdb->query( "DROP TABLE IF EXISTS {$prefix}orbis_projects" );

//////////////////////////////////////////////////
// Delete posts
//////////////////////////////////////////////////

$wpdb->query( "DELETE FROM $wpdb->posts WHERE post_type = 'orbis_project';" );
$wpdb->query( "DELETE FROM $wpdb->posts WHERE post_type = 'orbis_company';" );

$wpdb->query( "DELETE FROM $wpdb->postmeta WHERE post_id NOT IN ( SELECT ID FROM $wpdb->posts );" );

//////////////////////////////////////////////////
// Delete options
//////////////////////////////////////////////////

// UPDATE wp_options SET option_value = 0 WHERE option_name = 'orbis_db_version';
delete_option( 'orbis_db_version' );
