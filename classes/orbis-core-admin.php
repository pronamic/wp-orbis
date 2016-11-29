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

		// Users
		add_action( 'show_user_profile', array( $this, 'user_profile' ) );
		add_action( 'edit_user_profile', array( $this, 'user_profile' ) );

		add_action( 'personal_options_update', array( $this, 'user_update' ) );
		add_action( 'edit_user_profile_update', array( $this, 'user_update' ) );

		// Settings
		$this->settings = new Orbis_Core_Settings();
	}

	//////////////////////////////////////////////////

	/**
	 * Admin initialize
	 */
	public function admin_init() {
		$this->maybe_export_contacts();
		$this->maybe_import_contacts_upload();
		$this->maybe_import_contacts();
	}

	/**
	 * Maybe export contacts.
	 */
	private function get_export() {
		global $wpdb;

		$results = $wpdb->get_results( "
			SELECT
				post.ID,
				post.post_title,
				MAX( IF( meta.meta_key = '_orbis_title', meta.meta_value, NULL ) ) AS contact_title,
				MAX( IF( meta.meta_key = '_orbis_organization', meta.meta_value, NULL ) ) AS contact_organization,
				MAX( IF( meta.meta_key = '_orbis_person_email_address', meta.meta_value, NULL ) ) AS contact_email,
				MAX( IF( meta.meta_key = '_orbis_address', meta.meta_value, NULL ) ) AS contact_address,
				MAX( IF( meta.meta_key = '_orbis_postcode', meta.meta_value, NULL ) ) AS contact_postcode,
				MAX( IF( meta.meta_key = '_orbis_city', meta.meta_value, NULL ) ) AS contact_city,
				MAX( IF( meta.meta_key = '_orbis_country', meta.meta_value, NULL ) ) AS contact_country,
				MAX( IF( meta.meta_key = '_orbis_person_phone_number', meta.meta_value, NULL ) ) AS contact_phone_number,
				MAX( IF( meta.meta_key = '_orbis_person_mobile_number', meta.meta_value, NULL ) ) AS contact_mobile_number,
				MAX( IF( meta.meta_key = '_orbis_person_twitter', meta.meta_value, NULL ) ) AS contact_twitter,
				MAX( IF( meta.meta_key = '_orbis_person_facebook', meta.meta_value, NULL ) ) AS contact_facebook,
				MAX( IF( meta.meta_key = '_orbis_person_linkedin', meta.meta_value, NULL ) ) AS contact_linkedin
			FROM
				$wpdb->posts AS post
					LEFT JOIN
				$wpdb->postmeta AS meta
						ON post.ID = meta.post_id
			WHERE
				post_type = 'orbis_person'
					AND
				post_status IN ( 'publish', 'pending', 'draft', 'future' )
			GROUP BY
				post.ID
			;
		" );

		return $results;
	}

	public function maybe_import_contacts_upload() {
		if ( ! filter_has_var( INPUT_POST, 'orbis_contacts_import_upload' ) ) {
			return;
		}

		check_admin_referer( 'orbis_contacts_import', 'orbis_contacts_import_nonce' );

		$result = media_handle_upload( 'orbis_contacts_import_file', 0 );

		if ( is_int( $result ) ) {
			$url = add_query_arg( 'attachment_id', $result );

			wp_redirect( $url );

			exit;
		}
	}

	private function create_import_post( $skeleton, $data, $post = array() ) {
		foreach ( $skeleton as $key => $value ) {
			if ( is_array( $value ) ) {
				$post[ $key ] = $this->create_import_post( $value, $data );
			} else {
				if ( isset( $data[ $value ] ) ) {
					$post[ $key ] = $data[ $value ];
				}
			}
		}

		return $post;
	}

	public function maybe_import_contacts() {
		if ( ! filter_has_var( INPUT_POST, 'orbis_contacts_import' ) ) {
			return;
		}

		check_admin_referer( 'orbis_contacts_import', 'orbis_contacts_import_nonce' );

		$attachment_id = filter_input( INPUT_POST, 'attachment_id', FILTER_SANITIZE_STRING );

		$file = get_attached_file( $attachment_id );

		if ( ! is_readable( $file ) ) {
			return;
		}

		$data = array_map( 'str_getcsv', file( $file ) );

		$first = array_shift( $data );

		$skeleton = $_POST['post'];

		$updated = 0;

		foreach ( $data as $row ) {
			$import = $this->create_import_post( $skeleton, $row );

			$result = wp_update_post( $import );

			if ( 0 !== $result ) {
				$updated++;
			}
		}

		$url = add_query_arg( 'updated', $updated );

		wp_redirect( $url );

		exit;
	}

	public function maybe_export_contacts() {
		if ( ! filter_has_var( INPUT_GET, 'orbis_contacts_export' ) ) {
			return;
		}

		check_admin_referer( 'orbis_contacts_export', 'orbis_contacts_export_nonce' );

		// Set headers for download
		$filename = sprintf(
			__( 'orbis-contacts-export-%s.csv', 'orbis' ),
			date( 'Y-m-d_H-i' )
		);

		header( 'Content-Encoding: ' . get_bloginfo( 'charset' ) );
		header( 'Content-Type: text/csv; charset=' . get_bloginfo( 'charset' ) );
		header( 'Content-Disposition: attachment; filename=' . $filename );

		// Results
		$results = $this->get_export();

		$resource = fopen( 'php://output', 'w' );

		// Header
		$header = array(
			__( 'ID', 'orbis' ),
			__( 'Name', 'orbis' ),
			__( 'Title', 'orbis' ),
			__( 'Organization', 'orbis' ),
			__( 'Email', 'orbis' ),
			__( 'Address', 'orbis' ),
			__( 'Postcode', 'orbis' ),
			__( 'City', 'orbis' ),
			__( 'Country', 'orbis' ),
			__( 'Phone Number', 'orbis' ),
			__( 'Mobile Number', 'orbis' ),
			__( 'Twitter', 'orbis' ),
			__( 'Facebook', 'orbis' ),
			__( 'LinkedIn', 'orbis' ),
		);

		fputcsv( $resource, $header );

		foreach ( $results as $result ) {
			// Row
			$row = array(
				$result->ID,
				$result->post_title,
				$result->contact_title,
				$result->contact_organization,
				$result->contact_email,
				$result->contact_address,
				$result->contact_postcode,
				$result->contact_city,
				$result->contact_country,
				$result->contact_phone_number,
				$result->contact_mobile_number,
				$result->contact_twitter,
				$result->contact_facebook,
				$result->contact_linkedin,
			);

			fputcsv( $resource, $row );
		}

		exit;
	}

	//////////////////////////////////////////////////

	/**
	 * Admin enqueue scripts
	 */
	public function admin_enqueue_scripts() {
		$screen = get_current_screen();

		// Orbis screen
		if ( false !== strpos( $screen->id, 'orbis' ) ) {
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
		if ( 'orbis_page_orbis_plugins' === $screen->id ) {
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
			'data:image/svg+xml;base64,' . base64_encode( file_get_contents( plugin_dir_path( $this->plugin->file ) . 'images/orbis-icon-menu.svg' ) ), // icon_url
			'54.orbis.2'
		);

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

		add_submenu_page(
			'edit.php?post_type=orbis_person', // parent_slug
			__( 'Export Contacts', 'orbis' ), // page_title
			__( 'Export', 'orbis' ), // menu_title
			'manage_options', // capability
			'orbis-persons-export', // menu_slug
			array( $this, 'page_contacts_export' ) // function
		);

		add_submenu_page(
			'edit.php?post_type=orbis_person', // parent_slug
			__( 'Import Contacts', 'orbis' ), // page_title
			__( 'Import', 'orbis' ), // menu_title
			'manage_options', // capability
			'orbis-persons-import', // menu_slug
			array( $this, 'page_contacts_import' ) // function
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
			if ( false !== strpos( $item, 'orbis_' ) ) {
				$orbis_items[] = $item;
			} else {
				$other_items[] = $item;
			}
		}

		$orbis_index = array_search( 'orbis', $other_items, true ) + 1;

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

	public function page_contacts_export() {
		$this->plugin->plugin_include( 'admin/page-contacts-export.php' );
	}

	public function page_contacts_import() {
		$this->plugin->plugin_include( 'admin/page-contacts-import.php' );
	}

	//////////////////////////////////////////////////

	/**
	 * User update
	 */
	function user_update( $user_id ) {
		$orbis_user = filter_input( INPUT_POST, 'orbis_user', FILTER_VALIDATE_BOOLEAN );

		update_user_meta( $user_id, '_orbis_user', $orbis_user ? 'true' : 'false' );
	}

	//////////////////////////////////////////////////

	/**
	 * User profile
	 */
	public function user_profile( $user ) {
		$orbis_user = get_user_meta( $user->ID, '_orbis_user', true );

		?>
		<h3><?php _e( 'Orbis', 'orbis' ); ?></h3>

		<table class="form-table">
			<tr>
				<th>
					<label for="orbis_user">
						<?php _e( 'Orbis User', 'orbis' ); ?>
					</label>
				</th>
				<td>
					<fieldset>
						<legend class="screen-reader-text"><span><?php _e( 'Orbis User', 'orbis' ); ?></span></legend>

						<label for="orbis_user">
							<input name="orbis_user" type="checkbox" id="orbis_user" value="1" <?php checked( 'true' === $orbis_user ); ?> />

							<?php _e( 'Show user in Orbis.', 'orbis' ); ?>
						</label><br />
					</fieldset>
				</td>
			</tr>
		</table>
		<?php
	}

	//////////////////////////////////////////////////

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
