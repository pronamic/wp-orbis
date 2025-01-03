<?php

class Orbis_Core_Plugin extends Orbis_Plugin {
	public function __construct( $file ) {
		parent::__construct( $file );

		$this->set_name( 'orbis' );
		$this->set_db_version( '1.3.7' );

		// Actions
		add_action( 'init', [ $this, 'init' ] );

		add_action( 'init', [ $this, 'register_scripts' ] );

		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

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
		( new Orbis_AccessController() )->setup();
		( new Orbis_TeamsController() )->setup();

		// Shortcodes
		add_shortcode( 'orbis_list_pages', [ $this, 'shortcode_list_pages' ] );

		// Admin
		if ( is_admin() ) {
			global $orbis_admin;

			$orbis_admin = new Orbis_Core_Admin( $this );
		}

		$this->vcard = new Orbis_VCard( $this );

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
		$select2_version = '4.1.0-rc.0';

		wp_register_script(
			'select2',
			$this->plugin_url( 'assets/select2/js/select2.full.js' ),
			[
				'jquery',
			],
			$select2_version,
			true
		);

		$names = [
			// 'nl-NL'
			str_replace( '_', '-', get_locale() ),
			// 'nl'
			substr( get_locale(), 0, 2 ),
		];

		foreach ( $names as $name ) {
			$path = '/assets/select2/js/i18n/' . $name . '.js';

			if ( is_readable( $dir . $path ) ) {
				wp_register_script(
					'select2-i18n',
					$uri . $path,
					[
						'select2',
					],
					$select2_version,
					true
				);

				break;
			}
		}

		wp_register_style(
			'select2',
			$this->plugin_url( 'assets/select2/css/select2.min.css' ),
			[],
			$select2_version
		);

		/**
		 * Select2
		 */
		wp_register_style(
			'select2-bootstrap4-theme',
			$this->plugin_url( 'assets/select2-bootstrap4-theme/select2-bootstrap4.min.css' ),
			[
				'bootstrap',
				'select2',
			],
			'1.5.2'
		);

		// jQuery UI datepicker
		wp_register_style( 'jquery-ui-datepicker', $this->plugin_url( '/jquery-ui/themes/base/jquery.ui.all.css' ) );

		// Orbis
		wp_register_script(
			'orbis',
			$this->plugin_url( 'includes/js/orbis.js' ),
			[ 'jquery', 'jquery-ui-datepicker' ],
			'1.0.0',
			true
		);

		$orbis_vars = [
			'ajaxUrl'         => admin_url( 'admin-ajax.php' ),
			'restUrlProjects' => \rest_url( 'wp/v2/orbis/projects' )
		];

		wp_localize_script( 'orbis', 'orbis', $orbis_vars );

		// Orbis - Autocomplete
		wp_register_script(
			'orbis-autocomplete',
			$this->plugin_url( 'includes/js/autocomplete.js' ),
			[ 'jquery', 'jquery-ui-autocomplete', 'select2', 'orbis' ],
			'1.0.6',
			true
		);

		$translation_array = [
			'theme'                 => \is_admin() ? null : 'bootstrap4',
			'noMatches'             => __( 'No matches found', 'orbis' ),
			'inputTooShort'         => sprintf( __( 'Please enter %s more characters', 'orbis' ), '{todo}' ),
			'selectionTooBigSingle' => sprintf( __( 'You can only select %s item', 'orbis' ), '{limit}' ),
			'selectionTooBigPlural' => sprintf( __( 'You can only select %s items', 'orbis' ), '{limit}' ),
			'loadMore'              => __( 'Loading more results...', 'orbis' ),
			'searching'             => __( 'Searching...', 'orbis' ),
		];

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

		// Select2 Boostrap 4 theme
		wp_enqueue_style( 'select2-bootstrap4-theme' );

		// Orbis
		wp_enqueue_script( 'orbis' );

		// Orbis autocomplete
		wp_enqueue_script( 'orbis-autocomplete' );
	}

	//////////////////////////////////////////////////

	/**
	 * Enqueue jQuery datepicker
	 */
	public function enqueue_jquery_datepicker() {
		// jQuery UI datepicker
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_style( 'jquery-ui-datepicker' );
	}

	//////////////////////////////////////////////////

	public function loaded() {
		$this->load_textdomain( 'orbis', '/languages/' );
		$this->load_textdomain( 'pronamic-money', '/vendor/pronamic/wp-money/languages/' );
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
		orbis_install_table(
			'orbis_log',
			'
			id BIGINT(16) UNSIGNED NOT NULL AUTO_INCREMENT,
			created DATETIME NOT NULL,
			wp_user_id BIGINT(20) UNSIGNED DEFAULT NULL,
			message VARCHAR(512) NOT NULL,
			PRIMARY KEY  (id)
		'
		);

		// Roles
		$roles = $this->get_roles();

		$this->update_roles( $roles );

		// Replace old meta values
		global $wpdb;

		$replace_values = [
			'_orbis_person_twitter'       => '_orbis_twitter',
			'_orbis_person_facebook'      => '_orbis_facebook',
			'_orbis_person_linkedin'      => '_orbis_linkedin',
			'_orbis_person_email_address' => '_orbis_email',
			'_orbis_person_phone_number'  => '_orbis_phone_number',
			'_orbis_person_mobile_number' => '_orbis_mobile_number',
			'_orbis_company_twitter'      => '_orbis_twitter',
			'_orbis_company_facebook'     => '_orbis_facebook',
			'_orbis_company_linkedin'     => '_orbis_linkedin',
			'_orbis_company_email'        => '_orbis_email',
			'_orbis_company_address'      => '_orbis_address',
			'_orbis_company_postcode'     => '_orbis_postcode',
			'_orbis_company_city'         => '_orbis_city',
			'_orbis_company_country'      => '_orbis_country',
			'_orbis_company_kvk_number'   => '_orbis_kvk_number',
			'_orbis_company_vat_number'   => '_orbis_vat_number',
			'_orbis_company_website'      => '_orbis_website',
		];

		foreach ( $replace_values as $old_key => $new_key ) {
			$wpdb->query(
				$wpdb->prepare(
					"
					UPDATE $wpdb->postmeta
						SET meta_key = %s
						WHERE meta_key = %s
					",
					$new_key,
					$old_key
				)
			);
		}

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
		$roles = [
			'super_administrator' => [
				'manage_orbis' => true,
			],
			'administrator'       => [
				'manage_orbis' => true,
			],
			'editor'              => [],
			'employee'            => [],
		];

		// Roles post capabilities
		$roles_post_cap = [
			'super_administrator' => [
				'orbis_company' => orbis_post_type_capabilities( true, [] ),
				'orbis_project' => orbis_post_type_capabilities( true, [] ),
			],
			'administrator'       => [
				'orbis_company' => orbis_post_type_capabilities(
					true,
					[
						'delete_post' => false,
					]
				),
				'orbis_project' => orbis_post_type_capabilities(
					true,
					[
						'delete_post' => false,
					]
				),
			],
			'editor'              => [
				'orbis_company' => orbis_post_type_capabilities(
					false,
					[
						'read_post' => true,
					]
				),
				'orbis_project' => orbis_post_type_capabilities(
					false,
					[
						'read_post' => true,
					]
				),
			],
			'employee'            => [
				'orbis_company' => orbis_post_type_capabilities(
					false,
					[
						'read_post' => true,
					]
				),
				'orbis_project' => orbis_post_type_capabilities(
					false,
					[
						'read_post' => true,
					]
				),
			],
		];

		foreach ( $roles_post_cap as $role => $post_types ) {
			foreach ( $post_types as $post_type => $capabilities ) {
				orbis_translate_post_type_capabilities( $post_type, $capabilities, $roles[ $role ] );
			}
		}

		return $roles;
	}

	public function shortcode_list_pages( $atts, $content, $tag ) {
		$atts = shortcode_atts(
			[
				'child_of'  => get_the_ID(),
				'depth'     => 0,
				'post_type' => 'page',
				'title_li'  => null,
			],
			$atts,
			$tag
		);

		$atts['echo'] = false;

		$result = wp_list_pages( $atts );

		if ( empty( $atts['title_li'] ) ) {
			$result = '<ul>' . $result . '</ul>';
		}

		return $result;
	}
}
