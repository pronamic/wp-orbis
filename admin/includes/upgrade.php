<?php

function orbis_install() {
	
}

function orbis_make_db() {
	require_once ABSPATH . '/wp-admin/includes/upgrade.php';

	global $wpdb, $orbisdb;
	$charset_collate = '';

	if ( ! empty($wpdb->charset) )
		$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
	if ( ! empty($wpdb->collate) )
		$charset_collate .= " COLLATE $wpdb->collate";

	$queries  = '';

	$queries .= "CREATE TABLE $orbisdb->companies (
		`id` bigint(16) unsigned NOT NULL AUTO_INCREMENT,
		`post_id` bigint(20) unsigned DEFAULT NULL,
		`name` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
		PRIMARY KEY  (`id`),
		UNIQUE KEY `post_id` (`post_id`)
	) $charset_collate;";

	$queries .= "CREATE TABLE $orbisdb->projects (
		`id` BIGINT(16) UNSIGNED NOT NULL AUTO_INCREMENT,
		`name` VARCHAR(128) NOT NULL DEFAULT '',
		`post_id` BIGINT(20) UNSIGNED DEFAULT NULL,
		`principal_id` BIGINT(16) UNSIGNED DEFAULT NULL,
		`number_seconds` INT(16) NOT NULL DEFAULT '0',
		`invoicable` TINYINT(1) NOT NULL DEFAULT '1',
		`invoiced` TINYINT(1) NOT NULL DEFAULT '0',
		`invoice_number` VARCHAR(128) NOT NULL,
		`invoice_paid` TINYINT(1) NOT NULL DEFAULT '0',
		`finished` TINYINT(1) NOT NULL DEFAULT '0',
		PRIMARY KEY  (`id`),
		UNIQUE KEY `post_id` (`post_id`),
		KEY `principal_id` (`principal_id`)
	) $charset_collate;";

	dbDelta( $queries );
}
