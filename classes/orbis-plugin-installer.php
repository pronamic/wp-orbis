<?php

/**
 * Title: Orbis plugin installer
 * Description: 
 * Copyright: Copyright (c) 2005 - 2013
 * Company: Pronamic
 * @author Stefan Boonstra
 * @version 1.0
 */
class Orbis_Plugin_Installer {
	/**
	 * Plugin
	 *
	 * @var Orbis_Plugin
	 */
	private $plugin;

	//////////////////////////////////////////////////

	/**
	 * Constructs and initialize an Orbis plugin installer
	 *
	 * @param Orbis_Plugin $plugin
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
	}
	
	//////////////////////////////////////////////////
	
	/**
	 * Installs and activates the plugin that matches the passed $plugin_slug.
	 * 
	 * Returns true on success and WP_Error on failure.
	 * 
	 * @param string $plugin_slug
	 * 
	 * @return bool | WP_Error
	 */
	public function install_plugin( $plugin_slug ) {
		if ( ! current_user_can( 'install_plugins' ) ) {
			return new WP_Error(  __( 'Error 2', 'orbis' ) );
		}
		
		include_once ( ABSPATH . 'wp-admin/includes/plugin-install.php' );
		
		$plugins_api = plugins_api( 'plugin_information', array( 'slug' => $plugin_slug, 'fields' => array( 'sections' => false ) ) );
		
		if ( is_wp_error( $plugins_api ) ) {
			return $plugins_api; //sprintf( __( 'ERROR: Error fetching plugin information: %s', 'a8c-developer' ), $plugins_api->get_error_message() ) );
		}

        $orbis_empty_upgrader_skin = new Orbis_Empty_Upgrader_Skin( array(
            'nonce'  => 'install-plugin_' . $plugin_slug,
            'plugin' => $plugin_slug,
            'api'    => $plugins_api,
        ) );

		$plugin_upgrader = new Plugin_Upgrader( $orbis_empty_upgrader_skin );
		
		$install_result = $plugin_upgrader->install( $plugins_api->download_link );

        // Check if the skin has stored any errors
        if ( is_wp_error( $orbis_empty_upgrader_skin->error ) ) {
            return $orbis_empty_upgrader_skin->error;
        }

        // Check if the installation resulted in any errors. $install_result is false when the file directory isn't writeable
		if ( ! $install_result || is_wp_error( $install_result ) ) {
			if ( is_wp_error( $install_result ) ) {
				return $install_result;
			} else {
				return new WP_Error( __( 'Please ensure the file system is writeable', 'orbis' ) );
			}
		}
		
		return $this->activate_plugin( $plugin_slug );
	}
	
	//////////////////////////////////////////////////
	
	/**
	 * Activates the plugin that matches the passed $plugin_slug.
	 * 
	 * Returns true on success and WP_Error on failure.
	 * 
	 * @param string $plugin_slug
	 * 
	 * @return bool | WP_Error
	 */
	public function activate_plugin( $plugin_slug ) {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return new WP_Error( __( 'Error 4', 'orbis' ) );
		}

		$activate_result = activate_plugin( $plugin_slug . '/' . $plugin_slug . '.php' );
		
		if ( is_wp_error( $activate_result ) ) {
			return new WP_Error( sprintf( __( 'ERROR: Failed to activate plugin: %s', 'a8c-developer' ), $activate_result->get_error_message() ) );
		}
		
		return true;
	}
}

include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );

/**
 * Title: Orbis empty upgrader skin
 * Description: 
 * Copyright: Copyright (c) 2005 - 2013
 * Company: Pronamic
 * @author Stefan Boonstra
 * @version 1.0
 */
class Orbis_Empty_Upgrader_Skin extends WP_Upgrader_Skin {
    /**
     * @var WP_Error
     */
    public $error;

    /**
	 * Constructs and initialize an Orbis empty upgrader skin
	 * 
	 * @param mixed $args 
	 */
	public function __construct( $args = array() ) {
		$defaults = array( 'type' => 'web', 'url' => '', 'plugin' => '', 'nonce' => '', 'title' => '' );
		
		$args = wp_parse_args( $args, $defaults );

		$this->type = $args[ 'type' ];
		$this->api  = isset( $args[ 'api' ] ) ? $args[ 'api' ] : array();

		parent::__construct( $args );
	}
	
	//////////////////////////////////////////////////
	
	/**
	 * TODO Implement this method, or find an alternative to it, to be able to install plugins where FTP credentials are required.
	 *
     * @param bool $error
     *
     * @return bool
	 */
	public function request_filesystem_credentials( $error = false ) {
		return true;
	}
	
	//////////////////////////////////////////////////
	
	/**
	 * @param WP_Error $error
	 */
	public function error( $error ) {
        if ( ! is_wp_error( $this->error ) ) {
            $this->error = new WP_Error();
        }

		$this->error->add( $error->get_error_code(), $error->get_error_message(), $error->get_error_data() );
	}
	
	//////////////////////////////////////////////////

	public function header() { }
	
	public function footer() { }
	
	public function feedback( $string ) { }
}