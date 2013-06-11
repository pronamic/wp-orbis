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

	//////////////////////////////////////////////////

	/**
	 * Constructs and initialize an Orbis core admin
	 * 
	 * @param string $file
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;

		// Actions
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
	}

	//////////////////////////////////////////////////

	/**
	 * Admin initialize
	 */
	public function admin_init() {
		// Scripts
		wp_enqueue_script(
			'select2',
			$this->plugin->plugin_url( 'includes/select2/select2.js' ),
			array( 'jquery' ),
			'3.2'
		);

		// Styles
		wp_enqueue_style(
			'orbis-select2',
			$this->plugin->plugin_url( 'includes/select2/select2.css' )
		);

		wp_enqueue_style(
			'orbis-admin',
			$this->plugin->plugin_url( 'css/admin.css' )
		);
	}

	//////////////////////////////////////////////////

	/**
	 * Admin menu
	 */
	public function admin_menu() {
		add_menu_page(
			__( 'Orbis', 'orbis' ), // page_title
			__( 'Orbis', 'orbis' ), // menu_title
			'orbis_view', // capability
			'orbis', // menu_slug
			array( $this, 'page') , // function
			$this->plugin->plugin_url( 'images/icon-16x16.png' ) // icon_url
		);
		
		// @see _add_post_type_submenus()
		// @see wp-admin/menu.php
		add_submenu_page(
			'orbis', // parent_slug 
			__( 'Orbis Settings', 'orbis' ), // page_title
			__( 'Settings', 'orbis' ), // menu_title
			'orbis_view_settings', // capability
			'orbis_settings', // menu_slug
			array( $this, 'pageSettings' ) // function
		);
		
		add_submenu_page(
			'orbis', // parent_slug 
			__( 'Orbis Stats', 'orbis' ), // page_title
			__( 'Stats', 'orbis' ), // menu_title
			'orbis_view_stats', // capability
			'orbis_stats', // menu_slug
			array( $this, 'pageStats' ) // function
		);
	}

	//////////////////////////////////////////////////
	
	public function page() {
		$this->plugin->plugin_include( 'views/orbis.php' );
	}
	
	public function pageSettings() {
		$this->plugin->plugin_include( 'views/settings.php' );
	}
	
	public function pageStats() {
		$this->plugin->plugin_include( 'views/stats.php' );
	}
}
