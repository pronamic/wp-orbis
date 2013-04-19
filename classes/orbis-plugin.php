<?php

/**
 * Title: Orbis plugin
 * Description:
 * Copyright: Copyright (c) 2005 - 2011
 * Company: Pronamic
 * @author Remco Tolsma
 * @version 1.0
 */
class Orbis_Plugin {
	public $file;

	public $dirname;

	public $db_version;

	//////////////////////////////////////////////////

	/**
	 * Constructs and initialize an Orbis plugin
	 * 
	 * @param string $file
	 */
	public function __construct( $file ) {
		$this->file    = $file;
		$this->dirname = dirname( $file );

		add_action( 'admin_init',     array( $this, 'update' ) );
		add_action( 'plugins_loaded', array( $this, 'loaded' ) );
	}

	//////////////////////////////////////////////////

	/**
	 * Set the name of this plugin
	 * 
	 * @param string $name
	 */
	public function set_name( $name ) {
		$this->name = $name;
	}

	//////////////////////////////////////////////////

	/**
	 * Set database version
	 * 
	 * @param string $version
	 */
	public function set_db_version( $version ) {
		$this->db_version = $version;
	}

	//////////////////////////////////////////////////

	/**
	 * Update
	 */
	public function update() {
		if ( ! empty( $this->name ) ) {
			$option = $this->name . '_db_version';

			if ( get_option( $option ) != $this->db_version ) {
				$this->install();
			}
		}
	}

	//////////////////////////////////////////////////

	/**
	 * Install
	 */
	public function install() {
		if ( ! empty( $this->name ) ) {
			$option = $this->name . '_db_version';

			update_option( $option, $this->db_version );
		}
	}

	//////////////////////////////////////////////////

	/**
	 * Loaded
	 */
	public function loaded() {

	}

	//////////////////////////////////////////////////

	/**
	 * Plugin URL
	 * 
	 * @param string $path
	 */
	public function plugin_url( $path ) {
		return plugins_url( $path, $this->file );
	}

	//////////////////////////////////////////////////

	/**
	 * Plugin include
	 * 
	 * @param string $path
	 */
	public function plugin_include( $path ) {
		include $this->dirname . '/' . $path;
	}

	//////////////////////////////////////////////////

	/**
	 * Load text domain
	 * 
	 * @param string $domain
	 * @param string $path
	 */
	public function load_textdomain( $domain, $path = '' ) {
		$plugin_rel_path = dirname( plugin_basename( $this->file ) ) . $path;

		load_plugin_textdomain( $domain, false, $plugin_rel_path );
	}
}
