<?php

class Orbis_Core_Plugin extends Orbis_Plugin {
	public function __construct( $file ) {
		parent::__construct( $file );

		$this->set_name( 'orbis' );
		$this->set_db_version( '1.0' );

		$this->plugin_include( 'includes/template.php' );
		$this->plugin_include( 'includes/project-template.php' );

		orbis_register_table( 'orbis_projects', false, '' );
		
		if ( is_admin() ) {
			global $orbis_admin;
			
			$orbis_admin = new Orbis_Core_Admin( $this );
		}
	}

	public function loaded() {
		$this->load_textdomain( 'orbis', '/languages/' );
	}

	public function install() {
		orbis_install_table( 'orbis_projects', '
			id BIGINT(16) UNSIGNED NOT NULL AUTO_INCREMENT,
			name VARCHAR(128) NOT NULL,
			principal_id BIGINT(16) UNSIGNED DEFAULT NULL,
			contact_id_1 BIGINT(16) UNSIGNED DEFAULT NULL,
			contact_id_2 BIGINT(16) UNSIGNED DEFAULT NULL,
			description VARCHAR(128) NOT NULL,
			start_date DATE NOT NULL DEFAULT "0000-00-00",
			end_date DATE NOT NULL DEFAULT "0000-00-00",
			actual_end_date DATE NOT NULL DEFAULT "0000-00-00",
			percentage_completed INT(3) UNSIGNED NOT NULL DEFAULT 0,
			priority INT(3) UNSIGNED NOT NULL DEFAULT 0,
			comments TEXT,
			number_seconds INT(16) NOT NULL DEFAULT 0,
			invoicable BOOLEAN NOT NULL DEFAULT TRUE,
			invoiced BOOLEAN NOT NULL DEFAULT FALSE,
			invoice_number VARCHAR(128) DEFAULT NULL,
			invoice_paid BOOLEAN NOT NULL DEFAULT FALSE,
			finished BOOLEAN NOT NULL DEFAULT FALSE,
			PRIMARY KEY  (id),
			KEY principal_id (principal_id),
			KEY contact_id_1 (contact_id_1),
			KEY contact_id_2 (contact_id_2)
		' );
	}
}
