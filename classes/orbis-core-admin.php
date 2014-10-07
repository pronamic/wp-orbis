<?php

/**
 * Title: Orbis core admin
 * Description:
 * Copyright: Copyright (c) 2005 - 2011
 * Company: Pronamic
 * @author Remco Tolsma
 * @version 1.0
 */
class Orbis_Core_Admin {
	/**
	 * Plugin
	 *
	 * @var Orbis_Plugin
	 */
	private $plugin;

	/**
	 * Orbis plugins page slug
	 */
	public static $ORBIS_PLUGINS_SLUG = 'orbis_plugins';

	//////////////////////////////////////////////////

	/**
	 * Constructs and initialize an Orbis core admin
	 *
	 * @param Orbis_Plugin $plugin
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;

		// Actions
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

		add_filter( 'menu_order', array( $this, 'menu_order' ) );
		add_filter( 'custom_menu_order', array( $this, 'custom_menu_order' ) );

		add_action( 'wp_ajax_orbis_install_plugin' , array( $this, 'orbis_install_plugin' ) );
		add_action( 'wp_ajax_orbis_activate_plugin', array( $this, 'orbis_activate_plugin' ) );

		// Settings
		$this->settings = new Orbis_Core_Settings();
	}

	//////////////////////////////////////////////////

	/**
	 * Admin initialize
	 */
	public function admin_init() {

	}

	//////////////////////////////////////////////////

	/**
	 * Admin enqueue scripts
	 */
	public function admin_enqueue_scripts() {
		$screen = get_current_screen();

		// Orbis screen
		if ( strpos( $screen->id, 'orbis' ) !== false ) {
			// Select2
			wp_enqueue_script( 'select2' );

			wp_enqueue_style( 'orbis-select2' );

			// jQuery datepicker
			$this->plugin->enqueue_jquery_datepicker();

			// General
			wp_enqueue_style(
				'wp-orbis-admin',
				$this->plugin->plugin_url( 'admin/css/orbis.css' )
			);
		}

		// Orbis plugins screen
		if ( 'orbis_page_orbis_plugins' == $screen->id ) {
			wp_enqueue_script(
				'orbis-plugins-script',
				$this->plugin->plugin_url( 'includes/js/orbis-plugins.js' ),
				array( 'jquery', 'thickbox' )
			);

			wp_localize_script( 'orbis-plugins-script', 'orbis_plugins_script_strings', array(
				'install_button_text'      => __( 'Install', 'orbis' ),
				'activate_button_text'     => __( 'Activate', 'orbis' ),
				'active_button_text'       => __( 'Active', 'orbis' ),
				'error_message_unknown'    => __( 'An unknown error occurred', 'orbis' ),
				'error_message_connection' => __( 'Could not connect to the server', 'orbis' ),
			) );

			wp_enqueue_style( 'thickbox' );
		}
	}

	//////////////////////////////////////////////////

	/**
	 * Admin menu
	 */
	public function admin_menu() {
		/*
		 * @see https://github.com/woothemes/woocommerce/blob/v2.0.13/admin/woocommerce-admin-init.php#L62
		 * @see http://codex.wordpress.org/Function_Reference/add_menu_page
		 */
		global $menu;

		$menu['54.orbis.1'] = array( '', 'read', 'separator-orbis', '', 'wp-menu-separator orbis' );

		add_menu_page(
			__( 'Orbis', 'orbis' ), // page_title
			__( 'Orbis', 'orbis' ), // menu_title
			'manage_orbis', // capability
			'orbis', // menu_slug
			array( $this, 'page' ) , // function
			$this->plugin->plugin_url( 'images/icon-16x16.png' ), // icon_url
			'54.orbis.2'
		);

		// @see _add_post_type_submenus()
		// @see wp-admin/menu.php
		add_submenu_page(
			'orbis', // parent_slug
			__( 'Orbis Settings', 'orbis' ), // page_title
			__( 'Settings', 'orbis' ), // menu_title
			'manage_options', // capability
			'orbis_settings', // menu_slug
			array( $this, 'page_settings' ) // function
		);

		add_submenu_page(
			'orbis', // parent_slug
			__( 'Orbis Stats', 'orbis' ), // page_title
			__( 'Stats', 'orbis' ), // menu_title
			'manage_options', // capability
			'orbis_stats', // menu_slug
			array( $this, 'page_stats' ) // function
		);

		add_submenu_page(
			'orbis', // parent_slug
			__( 'Orbis Plugins', 'orbis' ), // page_title
			__( 'Plugins', 'orbis' ), // menu_title
			'manage_options', // capability
			'orbis_plugins', // menu_slug
			array( $this, 'page_plugins' ) // function
		);
	}

	/**
	 * Reorder the WC menu items in admin.
	 *
	 * @param mixed $menu_order
	 * @return array
	 */
	public function menu_order( $menu_order ) {
		// Initialize our custom order array
		$orbis_menu_order = array();

		$orbis_items = array();
		$other_items = array();

		foreach ( $menu_order as $item ) {
			if ( strpos( $item, 'orbis_' ) !== false ) {
				$orbis_items[] = $item;
			} else {
				$other_items[] = $item;
			}
		}

		$orbis_index = array_search( 'orbis', $other_items ) + 1;

		$before = array_slice( $other_items, 0, $orbis_index );
		$after  = array_slice( $other_items, $orbis_index );

		$orbis_menu_order = array_merge( $before, $orbis_items, $after );

		// Return order
		return $orbis_menu_order;
	}

	/**
	 * Custom menu order
	 *
	 * @return boolean
	 */
	public function custom_menu_order() {
		return true;
	}

	//////////////////////////////////////////////////

	public function page() {
		$this->plugin->plugin_include( 'admin/page-dashboard.php' );
	}

	public function page_settings() {
		$this->plugin->plugin_include( 'admin/page-settings.php' );
	}

	public function page_stats() {
		$this->plugin->plugin_include( 'admin/page-stats.php' );
	}

	public function page_plugins() {
		$this->plugin->plugin_include( 'admin/page-plugins.php' );
	}

	/**
	 * Called through the WordPress AJAX hook. Installs a plugin that matches the slug passed through the $_POST variable.
	 */
	public function orbis_install_plugin() {
		$plugin_slug = filter_input( INPUT_POST, 'plugin_slug', FILTER_SANITIZE_STRING );

		if ( ! $plugin_slug ) {
			die( json_encode( array( 'success' => false, 'message' => __( 'The plugin could not be found', 'orbis' ) ) ) );
		}

		check_ajax_referer( 'manage-plugin-' . $plugin_slug, 'nonce' );

		$plugin_installer = new Orbis_Plugin_Manager( $this->plugin );

		$result = $plugin_installer->install_plugin( $plugin_slug );

		if ( is_wp_error( $result ) ) {
			die( json_encode( array( 'success' => false, 'error_code' => $result->get_error_code(), 'message' => $result->get_error_message() ) ) );
		}

		die( json_encode( array( 'success' => true, 'message' => __( 'The plugin was installed and activated successfully', 'orbis' ) ) ) );
	}

	/**
     * Called through the WordPress AJAX hook. Activates a plugin that matches the slug passed through the $_POST variable.
	 */
	public function orbis_activate_plugin() {
		$plugin_slug = filter_input( INPUT_POST, 'plugin_slug', FILTER_SANITIZE_STRING );

		if ( ! $plugin_slug ) {
			die( json_encode( array( 'success' => false, 'message' => __( 'The plugin could not be found', 'orbis' ) ) ) );
		}

		check_ajax_referer( 'manage-plugin-' . $plugin_slug, 'nonce' );

		$plugin_installer = new Orbis_Plugin_Manager( $this->plugin );

		$result = $plugin_installer->activate_plugin( $plugin_slug );

		if ( is_wp_error( $result ) ) {
			die( json_encode( array( 'success' => false, 'error_code' => $result->get_error_code(), 'message' => $result->get_error_message() ) ) );
		}

		die( json_encode( array( 'success' => true, 'message' => __( 'The plugin was activated successfully', 'orbis' ) ) ) );
	}
}
