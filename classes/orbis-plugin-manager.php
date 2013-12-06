<?php

/**
 * Title: Orbis plugin manager
 * Description: 
 * Copyright: Copyright (c) 2005 - 2013
 * Company: Pronamic
 * @author Stefan Boonstra
 * @version 1.0
 */
class Orbis_Plugin_Manager {
	/**
	 * Plugin
	 *
	 * @var Orbis_Plugin
	 */
	private $plugin;

    /**
     * List of recommended plugins
     */
    public static $recommended_plugins = array(
        'members'        => array( 'title' => 'Members'      , 'plugin_file_name' => null ),
        'posts-to-posts' => array( 'title' => 'Posts 2 Posts', 'plugin_file_name' => null )
    );

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
     * Checks if the plugin with the passed slug (and file name) is installed.
     *
     * @param string $plugin_slug
     * @param string $plugin_file_name (Optional, defaults to null)
     *
     * @return bool $is_plugin_installed
     */
    public static function is_plugin_installed( $plugin_slug, $plugin_file_name = null ) {
        global $orbis_plugin;

        if ( strlen( $plugin_file_name ) > 0 ) {
            $file = dirname( $orbis_plugin->dirname ) . DIRECTORY_SEPARATOR . $plugin_slug . DIRECTORY_SEPARATOR . $plugin_file_name;
        } else {
            $file = dirname( $orbis_plugin->dirname ) . DIRECTORY_SEPARATOR . $plugin_slug . DIRECTORY_SEPARATOR . $plugin_slug . '.php';
        }

        return file_exists( $file );
    }

    //////////////////////////////////////////////////

    /**
     * Checks if the plugin with the passed slug (and file name) is active.
     *
     * @param string $plugin_slug
     * @param string $plugin_file_name (Optional, defaults to null)
     *
     * @return bool $is_plugin_active
     */
    public static function is_plugin_active( $plugin_slug, $plugin_file_name = null ) {
        if ( strlen( $plugin_file_name ) > 0 ) {
            return is_plugin_active( $plugin_slug . '/' . $plugin_file_name );
        }

        return is_plugin_active( $plugin_slug . '/' . $plugin_slug . '.php' );
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