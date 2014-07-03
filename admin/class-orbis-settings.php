<?php
/**
 * Orbis_Admin class. This class should ideally be used to work with the
 * administrative side of the WordPress site.
 *
 * If you're interested in introducing public-facing
 * functionality, then refer to `class-plugin-name.php`
 *
 * @package Orbis_Admin
 */
class Orbis_Settings {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . 'orbis.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );
		
		// Populate options page
		add_action( 'admin_init', array( $this, 'orbis_settings_init' ) );

		if(get_option('orbis_currency')) {
		
			function orbis_currency() {
			   echo '<style type="text/css">
			           body.toplevel_page_wpseo_dashboard #sidebar-container { display: none; }
			           body.seo_page_wpseo_titles #sidebar-container { display: none; }
			           body.seo_page_wpseo_social #sidebar-container { display: none; }
			           body.seo_page_wpseo_xml #sidebar-container { display: none; }
			           body.seo_page_wpseo_permalinks #sidebar-container { display: none; }
			           body.seo_page_wpseo_internal-links #sidebar-container { display: none; }
			           body.seo_page_wpseo_rss #sidebar-container { display: none; }
			           body.seo_page_wpseo_import #sidebar-container { display: none; }
			           body.seo_page_wpseo_files #sidebar-container { display: none; }
			         </style>';
			}
			add_action('admin_head', 'orbis_currency');
		
		}
		
		function remove_page_analysis_from_publish_box() { return false; }
		add_filter('wpseo_use_page_analysis', 'remove_page_analysis_from_publish_box');
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		/*
		 * @TODO :
		 *
		 * - Uncomment following lines if the admin class should only be available for super admins
		 */
		/* if( ! is_super_admin() ) {
			return;
		} */

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_style( 'orbis-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), orbis::VERSION );
		}

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_script( 'orbis-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery' ), orbis::VERSION );
		}
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {

		/*
		 * Add a settings page for this plugin to the Settings menu.
		 *
		 * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
		 *
		 *        Administration Menus: http://codex.wordpress.org/Administration_Menus
		 *
		 * @TODO:
		 *
		 * - Change 'manage_options' to the capability you see fit
		 *   For reference: http://codex.wordpress.org/Roles_and_Capabilities
		 */
		$this->plugin_screen_hook_suffix = add_options_page(
			__( 'Whitelabel Settings', 'orbis' ),
			__( 'Whitelabeling', 'orbis' ),
			'manage_options',
			'orbis',
			array( $this, 'display_plugin_admin_page' )
		);
	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */

	public function orbis_settings_init() {
		add_settings_section( 'orbis_general', 'Currency Settings', array( $this, 'orbis_general_callback'), 'orbis' );
		add_settings_field( 'orbis_currency', 'Default Currency', array( $this, 'orbis_currency_callback'), 'orbis', 'orbis_general' );
		register_setting( 'orbis', 'orbis_currency' );
	}

	/*	 Uncomment to add help text
	public function orbis_general_callback() {
	    echo 'Some help text goes here.';
	}
	*/

	public function orbis_currency_callback() {
	    echo '<select name="orbis_currency" id="orbis_currency" class="code">';
	    	echo '<option value="eur"'. selected( 'eur', get_option( 'orbis_currency' ), false ) . '>Euro</option>';
	    	echo '<option value="usd"'. selected( 'usd', get_option( 'orbis_currency' ), false ) . '>US Dollar</option>';
	    echo '</select>';
	}

	public function display_plugin_admin_page() {
		include_once( 'views/settings.php' );
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=orbis' ) . '">' . __( 'Settings', 'orbis' ) . '</a>'
			),
			$links
		);

	}

	/**
	 * NOTE:     Actions are points in the execution of a page or process
	 *           lifecycle that WordPress fires.
	 *
	 *           Actions:    http://codex.wordpress.org/Plugin_API#Actions
	 *           Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
	 *
	 * @since    1.0.0
	 */
	public function action_method_name() {
		// @TODO: Define your action hook callback here
	}

	/**
	 * NOTE:     Filters are points of execution in which WordPress modifies data
	 *           before saving it or sending it to the browser.
	 *
	 *           Filters: http://codex.wordpress.org/Plugin_API#Filters
	 *           Reference:  http://codex.wordpress.org/Plugin_API/Filter_Reference
	 *
	 * @since    1.0.0
	 */
	public function filter_method_name() {
		// @TODO: Define your filter hook callback here
	}

}
