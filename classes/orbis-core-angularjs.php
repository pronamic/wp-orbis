<?php

/**
 * Title: Orbis core AngularJS
 * Description:
 * Copyright: Copyright (c) 2005 - 2014
 * Company: Pronamic
 * @author Remco Tolsma
 * @version 1.0.0
 */
class Orbis_Core_AngularJS {
	/**
	 * Plugin
	 *
	 * @var Orbis_Plugin
	 */
	private $plugin;

	//////////////////////////////////////////////////

	/**
	 * Constructs and initialize an Orbis AngularJS object
	 *
	 * @param Orbis_Plugin $plugin
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;

		// Actions
		add_action( 'init', array( $this, 'init' ) );

		// Filters
		if ( ! is_admin() ) {
			add_filter( 'language_attributes', array( $this, 'ng_app_attributes' ) );
		}
	}

	//////////////////////////////////////////////////

	public function init() {
		// AngularJS
		wp_register_script(
			'angular',
			$this->plugin->plugin_url( 'assets/angular/angular.js' ),
			array(),
			'1.2.23',
			true
		);

		wp_register_style(
			'angular-csp',
			$this->plugin->plugin_url( 'assets/angular/angular-csp.css' ),
			array(),
			'1.2.23',
			true
		);

		// AngularJS ui-date directive
		wp_register_script(
			'angular-ui-date',
			$this->plugin->plugin_url( 'assets/angular-ui-date/date.js' ),
			array( 'angular', 'jquery' ),
			false,
			true
		);

		// AngularJS ui-select
		wp_register_script(
			'angular-ui-select',
			$this->plugin->plugin_url( 'assets/angular-ui-select/select.js' ),
			array(),
			'0.8.3',
			true
		);

		wp_register_style(
			'angular-ui-select',
			$this->plugin->plugin_url( 'assets/angular-ui-select/select.css' ),
			array(),
			'0.8.3',
			true
		);

		// Orbis
		wp_register_script(
			'orbis-angular-app',
			$this->plugin->plugin_url( 'src/orbis-angular/orbis-angular.js' ),
			array( 'angular' ),
			'1.0.0',
			true
		);
	}

	//////////////////////////////////////////////////

	public function ng_app_attributes( $atts ) {
		$atts .= ' ' . 'ng-app="orbisApp"';

		return $atts;
	}
}
