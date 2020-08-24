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

	public $dir_path;

	public $db_version;

	//////////////////////////////////////////////////

	/**
	 * Constructs and initialize an Orbis plugin
	 *
	 * @param string $file
	 */
	public function __construct( $file ) {
		$this->file     = $file;
		$this->dirname  = dirname( $file );
		$this->dir_path = plugin_dir_path( $file );

		add_action( 'plugins_loaded', array( $this, 'loaded' ) );

		add_action( 'admin_init', array( $this, 'update' ), 5 );
		add_action( 'admin_init', array( $this, 'install_redirect' ) );

		add_action( 'rest_api_init', array( $this, 'rest_api_init' ) );
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
		if ( is_admin() && ! empty( $this->name ) ) {
			$option = $this->name . '_db_version';

			if ( get_option( $option ) !== $this->db_version ) {
				$this->install();

				update_option( $option, $this->db_version );
			}
		}
	}

	//////////////////////////////////////////////////

	/**
	 * Install
	 */
	public function install() {
		// Flush rewrite rules
		flush_rewrite_rules();

		// Redirect to welcome screen
		set_transient( '_orbis_activation_redirect', 1, 60 * 60 );
	}

	/**
	 * Install redirect
	 */
	public function install_redirect() {
		if ( ! get_transient( '_orbis_activation_redirect' ) ) {
			return;
		}

		// Delete the redirect transient
		delete_transient( '_orbis_activation_redirect' );

		if ( is_network_admin() || isset( $_GET['activate-multi'] ) || defined( 'IFRAME_REQUEST' ) ) { // WPCS: CSRF ok.
			return;
		}

		if ( ( isset( $_GET['action'] ) && 'upgrade-plugin' === $_GET['action'] ) && ( isset( $_GET['plugin'] ) && strstr( $_GET['plugin'], 'orbis' ) ) ) { // WPCS: CSRF ok.
			return;
		}

		wp_safe_redirect( admin_url( '/' ) );

		exit;
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
	 * @param array  $args (optional, defaults to an empty array)
	 */
	public function plugin_include( $path, $args = array() ) {
		extract( $args ); // phpcs:ignore WordPress.Functions.DontExtract.extract_extract

		include $this->dir_path . '/' . $path;
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

	//////////////////////////////////////////////////

	/**
	 * Update the specified roles
	 *
	 * @param array $roles
	 */
	public function update_roles( $roles ) {
		global $wp_roles;

		if ( isset( $wp_roles ) ) {
			foreach ( $roles as $role => $capabilities ) {
				foreach ( $capabilities as $cap => $grant ) {
					$wp_roles->add_cap( $role, $cap, $grant );
				}
			}
		}
	}

	//////////////////////////////////////////////////

	public function locate_template( $template_name ) {
		$template = locate_template(
			array(
				$template_name,
			)
		);

		if ( ! $template ) {
			$template = $this->dir_path . 'templates/' . $template_name;
		}

		return $template;
	}

	public function get_template( $template_name, $echo = true, $args = array() ) {
		if ( ! $echo ) {
			ob_start();
		}

		$located = $this->locate_template( $template_name );

		extract( $args ); // phpcs:ignore WordPress.Functions.DontExtract.extract_extract

		include $located;

		if ( ! $echo ) {
			return ob_get_clean();
		}
	}

	/**
	 * REST API initialize.
	 *
	 * @link https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/
	 * @link https://github.com/wp-orbis/wp-orbis-accounts/blob/master/classes/orbis-accounts-plugin.php
	 */
	public function rest_api_init() {
		\register_rest_route( 'orbis/v1', '/users/by', array(
			'methods'  => 'GET',
			'callback' => array( $this, 'rest_api_user_by' )
		) );

		\register_rest_route( 'orbis/v1', '/users/(?P<id>\d+)', array(
			'methods'  => 'GET',
			'callback' => array( $this, 'rest_api_user' )
		) );
	}

	/**
	 * REST API user by email.
	 *
	 * @link https://developer.wordpress.org/reference/functions/get_user_by/
	 * @param \WP_REST_Request $request WordPress REST API request object.
	 * @return object
	 */ 
	public function rest_api_user_by( \WP_REST_Request $request ) {
		$email = $request->get_param( 'email' );

		$user = \get_user_by( 'email', $email );

		if ( false === $user ) {
			return new WP_Error( 'rest_user_invalid_email', 'Invalid user email.', array( 'status' => 404 ) );
		}

		$response = (object) array(
			'id'    => $user->ID,
			'name'  => $user->display_name,
			'email' => $user->user_email,
		);

		return $response;
	}

	/**
	 * REST API user.
	 *
	 * @param \WP_REST_Request $request WordPress REST API request object.
	 * @return object
	 */
	public function rest_api_user( \WP_REST_Request $request ) {
		global $wpdb;

		$user_id = $request->get_param( 'id' );

		$user = \get_user_by( 'id', $user_id );

		if ( false === $user ) {
			return new WP_Error( 'rest_user_invalid_id', 'Invalid user ID.', array( 'status' => 404 ) );
		}

		$response = (object) array(
			'id'            => $user->ID,
			'name'          => $user->display_name,
			'email'         => $user->user_email,
			'companies'     => array(),
			'subscriptions' => array(),
		);

		/**
		 * Companies.
		 */
		$query = new \WP_Query( array(
			'connected_type'  => 'orbis_users_to_companies',
			'connected_items' => $user->ID,
			'nopaging'        => true,
		) );

		$companies = array();

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				$company = new stdClass();

				$company->id    = get_the_ID();
				$company->title = get_the_title();

				$company->post_id = \get_the_ID();

				// Address.
				$address  = get_post_meta( $company->post_id, '_orbis_address', true );
				$postcode = get_post_meta( $company->post_id, '_orbis_postcode', true );
				$city     = get_post_meta( $company->post_id, '_orbis_city', true );
				$country  = get_post_meta( $company->post_id, '_orbis_country', true );

				$company->address = (object) array(
					'line_1'       => empty( $address ) ? null : $address,
					'postal_code'  => empty( $postcode ) ? null : $postcode,
					'city'         => empty( $city ) ? null : $city,
					'country_code' => empty( $country ) ? null : $country,
				);

				// Email.
				$email = get_post_meta( $company->post_id, '_orbis_email', true );

				$company->email = empty( $email ) ? null : $email;

				$accounting_email = get_post_meta( $company->post_id, '_orbis_accounting_email', true );

				$company->accounting_email = empty( $accounting_email ) ? null : $accounting_email;

				$invoice_email = get_post_meta( $company->post_id, '_orbis_invoice_email', true );

				$company->invoice_email = empty( $invoice_email ) ? null : $invoice_email;


				$response->companies[] = $company;
			}
		}

		/**
		 * Subscriptions.
		 */
		if ( \count( $response->companies ) > 0 ) {
			$ids = wp_list_pluck( $response->companies, 'id' );

			$list = implode( ',', $ids );

			$query = "
				SELECT
					subscription.id, 
					subscription.type_id,
					company.name AS company_name,
					product.name AS product_name,
					product.interval AS product_interval,
					product.price,
					subscription.name,
					subscription.activation_date,
					subscription.cancel_date IS NOT NULL AS canceled,
					subscription.post_id
				FROM
					$wpdb->orbis_subscriptions AS subscription
						LEFT JOIN
					$wpdb->orbis_subscription_products AS product
							ON subscription.type_id = product.id
						LEFT JOIN
					$wpdb->orbis_companies as company
							ON subscription.company_id = company.id
				WHERE
					company.post_id IN ( $list )
				ORDER BY
					activation_date ASC
				;
			";

			$data = $wpdb->get_results( $query );

			foreach ( $data as $item ) {
				$subscription = (object) array(
					'id'                      => \intval( $item->id ),
					'type_id'                 => \intval( $item->type_id ),
					'company_name'            => $item->company_name,
					'product_name'            => $item->product_name,
					'price'                   => $item->price,
					'name'                    => $item->name,
					'activation_date'         => $item->activation_date,
					'canceled'                => \boolval( $item->canceled ),
					'post_id'                 => \intval( $item->post_id ),
					'current_period_end_date' => \Orbis_Subscription::get_current_period_end_date( $item->activation_date, $item->product_interval )->format( \DATE_ATOM ),
				);				

				$response->subscriptions[] = $subscription;	
			}
		}

		return $response;
	}
}
