<?php

class Orbis_Core_Plugin extends Orbis_Plugin {
	public function __construct( $file ) {
		parent::__construct( $file );

		$this->set_name( 'orbis' );
		$this->set_db_version( '1.0' );

		$this->plugin_include( 'includes/deprecated.php' );
		$this->plugin_include( 'includes/administration.php' );
		$this->plugin_include( 'includes/post.php' );
		$this->plugin_include( 'includes/project.php' );
		$this->plugin_include( 'includes/template.php' );
		$this->plugin_include( 'includes/project-template.php' );

		orbis_register_table( 'orbis_companies', false, '' );
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
		// Tables
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
			finished BOOLEAN NOT NULL DEFAULT FALSE,
			PRIMARY KEY  (id),
			KEY principal_id (principal_id),
			KEY contact_id_1 (contact_id_1),
			KEY contact_id_2 (contact_id_2)
		' );
		
		// Roles
		$roles = $this->get_roles();

		$this->update_roles( $roles );
		
		// Parent
		parent::install();
	}

	//////////////////////////////////////////////////

	/**
	 * Get roles
	 * 
	 * @return array
	 */
	public function get_roles() {
		// @see http://codex.wordpress.org/Function_Reference/register_post_type
		global $wp_post_types;

		// Default roles
		$roles = array(
			'super_administrator' => array(
				'edit_orbis_project_administration' => true
			),
			'administrator' => array(
				
			),
			'editor' => array(
				
			),
			'employee' => array(
				
			)
		);

		// Roles post capabilities
		$roles_post_cap = array(
			'super_administrator' => array(
				'orbis_company' => orbis_post_type_capabilities( true, array(

				) ),
				'orbis_project' => orbis_post_type_capabilities( true, array(

				) )
			),
			'administrator' => array(
				'orbis_company' => orbis_post_type_capabilities( true, array(
					'delete_post' => false
				) ),
				'orbis_project' => orbis_post_type_capabilities( true, array(
					'delete_post' => false
				) )
			),
			'editor' => array(
				'orbis_company' => orbis_post_type_capabilities( false, array(
					'read_post' => true
				) ),
				'orbis_project' => orbis_post_type_capabilities( false, array(
					'read_post' => true
				) )
			),
			'employee' => array(
				'orbis_company' => orbis_post_type_capabilities( false, array(
					'read_post' => true
				) ),
				'orbis_project' => orbis_post_type_capabilities( false, array(
					'read_post' => true
				) )
			)
		);

		foreach ( $roles_post_cap as $role => $post_types ) {
			foreach ( $post_types as $post_type => $capabilities ) {
				orbis_translate_post_type_capabilities( $post_type, $capabilities, $roles[$role] );
			}
		}

		return $roles;
	}
}
