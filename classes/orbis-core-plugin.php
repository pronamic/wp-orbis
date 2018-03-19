<?php

class Orbis_Core_Plugin extends Orbis_Plugin {
	public function __construct( $file ) {
		parent::__construct( $file );

		$this->set_name( 'orbis' );
		$this->set_db_version( '1.3.0' );

		// Actions
		add_action( 'init', array( $this, 'init' ) );

		add_action( 'init', array( $this, 'register_scripts' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Includes
		$this->plugin_include( 'includes/deprecated.php' );
		$this->plugin_include( 'includes/administration.php' );
		$this->plugin_include( 'includes/email.php' );
		$this->plugin_include( 'includes/post.php' );
		$this->plugin_include( 'includes/template.php' );

		// Tables
		orbis_register_table( 'orbis_log' );

		// API
		$this->api = new Orbis_API();

		// Email
		$this->email = new Orbis_Core_Email( $this );

		// Other
		new Orbis_OrderByComment();
		new Orbis_PostcodeFilter();

		// Shortcodes
		add_shortcode( 'orbis_list_pages', array( $this, 'shortcode_list_pages' ) );

		// Admin
		if ( is_admin() ) {
			global $orbis_admin;

			$orbis_admin = new Orbis_Core_Admin( $this );
		}

		$this->angularjs = new Orbis_Core_AngularJS( $this );
		$this->vcard     = new Orbis_VCard( $this );

		$this->contacts_exporter = new Orbis_ContactsExporter( $this );
	}

	//////////////////////////////////////////////////

	/**
	 * Initialize
	 */
	public function init() {

	}

	//////////////////////////////////////////////////

	/**
	 * Register scripts
	 */
	public function register_scripts() {
		$dir = plugin_dir_path( $this->file );
		$uri = plugin_dir_url( $this->file );

		// Select2
		$select2_version = '4.0.6-rc.1';

		wp_register_script(
			'select2',
			$this->plugin_url( 'assets/select2/js/select2.full.js' ),
			array(
				'jquery',
			),
			$select2_version,
			true
		);

		$names = array(
			// 'nl-NL'
			str_replace( '_', '-', get_locale() ),
			// 'nl'
			substr( get_locale(), 0, 2 ),
		);

		foreach ( $names as $name ) {
			$path = '/assets/select2/js/i18n/' . $name . '.js';

			if ( is_readable( $dir . $path ) ) {
				wp_register_script(
					'select2-i18n',
					$uri . $path,
					array(
						'select2',
					),
					$select2_version,
					true
				);

				break;
			}
		}

		wp_register_style(
			'select2',
			$this->plugin_url( 'assets/select2/css/select.css' ),
			array(),
			$select2_version
		);

		// jQuery UI datepicker
		wp_register_style( 'jquery-ui-datepicker', $this->plugin_url( '/jquery-ui/themes/base/jquery.ui.all.css' ) );

		// Orbis
		wp_register_script(
			'orbis',
			$this->plugin_url( 'includes/js/orbis.js' ),
			array( 'jquery', 'jquery-ui-datepicker' ),
			'1.0.0',
			true
		);

		$orbis_vars = array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		);

		wp_localize_script( 'orbis', 'orbis', $orbis_vars );

		// Orbis - Autocomplete
		wp_register_script(
			'orbis-autocomplete',
			$this->plugin_url( 'includes/js/autocomplete.js' ),
			array( 'jquery', 'jquery-ui-autocomplete', 'select2', 'orbis' ),
			'1.0.6',
			true
		);

		$translation_array = array(
			'noMatches'             => __( 'No matches found', 'orbis' ),
			'inputTooShort'         => sprintf( __( 'Please enter %s more characters', 'orbis' ), '{todo}' ),
			'selectionTooBigSingle' => sprintf( __( 'You can only select %s item', 'orbis' ), '{limit}' ),
			'selectionTooBigPlural' => sprintf( __( 'You can only select %s items', 'orbis' ), '{limit}' ),
			'loadMore'              => __( 'Loading more results...', 'orbis' ),
			'searching'             => __( 'Searching...', 'orbis' ),
		);

		wp_localize_script( 'orbis-autocomplete', 'orbisl10n', $translation_array );
	}

	/**
	 * Enqueue scripts
	 */
	public function enqueue_scripts() {
		$this->enqueue_jquery_datepicker();

		// Select2
		wp_enqueue_style( 'select2' );

		wp_enqueue_script( 'select2' );
		wp_enqueue_script( 'select2-i18n' );

		// Orbis
		wp_enqueue_script( 'orbis' );
	}

	//////////////////////////////////////////////////

	/**
	 * Enqueue jQuery datepicker
	 */
	public function enqueue_jquery_datepicker() {
		// jQuery UI datepicker
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_style( 'jquery-ui-datepicker' );

		$this->enqueue_jquery_ui_i18n_path( 'datepicker' );
	}

	//////////////////////////////////////////////////

	/**
	 * Get jQuery UI i18n file
	 * https://github.com/jquery/jquery-ui/tree/master/ui/i18n
	 *
	 * @param string $module
	 */
	private function enqueue_jquery_ui_i18n_path( $module ) {
		$result = false;

		// Retrive the WordPress locale, for example 'en_GB'
		$locale = get_locale();

		// jQuery UI uses 'en-GB' notation, replace underscore with hyphen
		$locale = str_replace( '_', '-', $locale );

		// Create an search array with two variants 'en-GB' and 'en'
		$search = array(
			// en-GB
			$locale,
			// en
			substr( $locale, 0, 2 ),
		);

		foreach ( $search as $name ) {
			$path = sprintf( '/jquery-ui/languages/jquery.ui.%s-%s.js', $module, $name );

			$file = $this->dir_path . '/' . $path;

			if ( is_readable( $file ) ) {
				wp_enqueue_script(
					'jquery-ui-' . $module . '-' . $name,
					$this->plugin_url( $path )
				);

				break;
			}
		}

		return $result;
	}

	//////////////////////////////////////////////////

	public function loaded() {
		$this->load_textdomain( 'orbis', '/languages/' );
	}

	/**
	 * Install
	 *
	 * @mysql UPDATE wp_options SET option_value = 0 WHERE option_name = 'orbis_db_version';
	 *
	 * @see Orbis_Plugin::install()
	 */
	public function install() {
		// Tables
		orbis_install_table( 'orbis_log', '
			id BIGINT(16) UNSIGNED NOT NULL AUTO_INCREMENT,
			created DATETIME NOT NULL,
			wp_user_id BIGINT(20) UNSIGNED DEFAULT NULL,
			message VARCHAR(512) NOT NULL,
			PRIMARY KEY  (id)
		' );

		// Roles
		$roles = $this->get_roles();

		$this->update_roles( $roles );

		// Install
		parent::install();
	}

	//////////////////////////////////////////////////

	/**
	 * Get roles
	 *
	 * @return array
	 */
	public function get_roles() {
		// Default roles
		$roles = array(
			'super_administrator' => array(
				'manage_orbis' => true,
			),
			'administrator'       => array(
				'manage_orbis' => true,
			),
			'editor'              => array(),
			'employee'            => array(),
		);

		// Roles post capabilities
		$roles_post_cap = array(
			'super_administrator' => array(
				'orbis_company' => orbis_post_type_capabilities( true, array() ),
				'orbis_project' => orbis_post_type_capabilities( true, array() ),
			),
			'administrator'       => array(
				'orbis_company' => orbis_post_type_capabilities( true, array(
					'delete_post' => false,
				) ),
				'orbis_project' => orbis_post_type_capabilities( true, array(
					'delete_post' => false,
				) ),
			),
			'editor'              => array(
				'orbis_company' => orbis_post_type_capabilities( false, array(
					'read_post' => true,
				) ),
				'orbis_project' => orbis_post_type_capabilities( false, array(
					'read_post' => true,
				) ),
			),
			'employee'            => array(
				'orbis_company' => orbis_post_type_capabilities( false, array(
					'read_post' => true,
				) ),
				'orbis_project' => orbis_post_type_capabilities( false, array(
					'read_post' => true,
				) ),
			),
		);

		foreach ( $roles_post_cap as $role => $post_types ) {
			foreach ( $post_types as $post_type => $capabilities ) {
				orbis_translate_post_type_capabilities( $post_type, $capabilities, $roles[ $role ] );
			}
		}

		return $roles;
	}

	public function shortcode_list_pages( $atts, $content, $tag ) {
		$atts = shortcode_atts( array(
			'child_of'  => get_the_ID(),
			'depth'     => 0,
			'post_type' => 'page',
			'title_li'  => null,
		), $atts, $tag );

		$atts['echo'] = false;

		$result = wp_list_pages( $atts );

		if ( empty( $atts['title_li'] ) ) {
			$result = '<ul>' . $result . '</ul>';
		}

		return $result;
	}
}
