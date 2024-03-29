<?php

function orbis_filter_time_input( $type, $variable_name ) {
	$value = filter_input( $type, $variable_name, FILTER_SANITIZE_STRING );

	return orbis_parse_time( $value );
}

function orbis_parse_time( $value ) {
	$seconds = 0;

	$part_hours   = $value;
	$part_minutes = 0;

	$position_colon = strpos( $value, ':' );

	if ( false !== $position_colon ) {
		$part_hours   = substr( $value, 0, $position_colon );
		$part_minutes = substr( $value, $position_colon + 1 );
	}

	$var = filter_var( $part_hours, FILTER_VALIDATE_FLOAT );
	if ( false !== $var ) {
		$seconds += $var * 3600;
	}

	$var = filter_var( $part_minutes, FILTER_VALIDATE_FLOAT );
	if ( false !== $var ) {
		$seconds += $var * 60;
	}

	return $seconds;
}

/**
 * Register a table with $wpdb
 *
 * @param string $key The key to be used on the $wpdb object
 * @param string $name The actual name of the table, without $wpdb->prefix
 */
function orbis_register_table( $key, $name = false, $prefix = false ) {
	global $wpdb;

	if ( false === $name ) {
		$name = $key;
	}

	$prefix = defined( 'ORBIS_TABLE_PREFIX' ) ? ORBIS_TABLE_PREFIX : $wpdb->prefix;

	$wpdb->tables[] = $name;
	$wpdb->$key     = $prefix . $name;
}

/**
 * Orbis install table
*/
function orbis_install_table( $key, $columns ) {
	global $wpdb;

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	$full_table_name = $wpdb->$key;

	$charset_collate = '';

	if ( $wpdb->has_cap( 'collation' ) ) {
		if ( ! empty( $wpdb->charset ) ) {
			$charset_collate .= "DEFAULT CHARACTER SET $wpdb->charset";
		}

		if ( ! empty( $wpdb->collate ) ) {
			$charset_collate .= " COLLATE $wpdb->collate";
		}
	}

	$table_options = $charset_collate;

	dbDelta( "CREATE TABLE $full_table_name ( $columns ) $table_options" );
}

/**
 * Check for weekend
*/
function orbis_is_weekend() {
	$day = date( 'l' );

	return in_array( $day, [ 'Saturday', 'Sunday' ], true );
}
