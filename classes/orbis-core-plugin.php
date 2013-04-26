<?php

class Orbis_Core_Plugin extends Orbis_Plugin {
	public function __construct( $file ) {
		parent::__construct( $file );

		$this->set_name( 'orbis' );
		$this->set_db_version( '1.0' );

		$this->plugin_include( 'includes/template.php' );
		$this->plugin_include( 'includes/project-template.php' );
		
		if ( is_admin() ) {
			global $orbis_admin;
			
			$orbis_admin = new Orbis_Core_Admin( $this );
		}
	}

	public function loaded() {
		$this->load_textdomain( 'orbis', '/languages/' );
	}
}
