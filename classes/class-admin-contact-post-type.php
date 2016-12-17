<?php

class Orbis_Contacts_AdminContactPostType {
	/**
	 * Post type.
	 */
	const POST_TYPE = 'orbis_person';

	/**
	 * Construct.
	 */
	public function __construct( $plugin ) {		
		$this->plugin = $plugin;

		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );

		add_action( 'save_post_' . self::POST_TYPE, array( $this, 'save_post' ), 10, 2 );

		add_action( 'added_post_meta', array( $this, 'updated_post_meta' ), 10, 4 );
		add_action( 'updated_post_meta', array( $this, 'updated_post_meta' ), 10, 4 );
	}

	/**
	 * Add meta boxes.
	 */
	public function add_meta_boxes() {
		add_meta_box(
			'orbis_person',
			__( 'Contact information', 'orbis' ),
			array( $this, 'meta_box' ),
			'orbis_person',
			'normal',
			'high'
		);
	}

	/**
	 * Meta box.
	 *
	 * @param mixed $post
	 */
	public function meta_box( $post ) {
		$this->plugin->plugin_include( 'admin/meta-box-contact-details.php' );
	}

	/**
	 * Save post.
	 *
	 * @param int $post_id
	 * @param mixed $post
	 */
	public function save_post( $post_id, $post ) {
		// Doing autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Verify nonce
		$nonce = filter_input( INPUT_POST, 'orbis_person_details_meta_box_nonce', FILTER_SANITIZE_STRING );
		if ( ! wp_verify_nonce( $nonce, 'orbis_save_person_details' ) ) {
			return;
		}

		// Check permissions
		if ( ! ( 'orbis_person' === $post->post_type && current_user_can( 'edit_post', $post_id ) ) ) {
			return;
		}

		// OK
		$definition = array(
			'_orbis_title'                => FILTER_SANITIZE_STRING,
			'_orbis_organization'         => FILTER_SANITIZE_STRING,
			'_orbis_department'           => FILTER_SANITIZE_STRING,
			'_orbis_person_email_address' => FILTER_VALIDATE_EMAIL,
			'_orbis_person_phone_number'  => FILTER_SANITIZE_STRING,
			'_orbis_person_mobile_number' => FILTER_SANITIZE_STRING,
			'_orbis_address'              => FILTER_SANITIZE_STRING,
			'_orbis_postcode'             => FILTER_SANITIZE_STRING,
			'_orbis_city'                 => FILTER_SANITIZE_STRING,
			'_orbis_country'              => FILTER_SANITIZE_STRING,
			'_orbis_birth_date_string'    => FILTER_SANITIZE_STRING,
			'_orbis_person_twitter'       => FILTER_SANITIZE_STRING,
			'_orbis_person_facebook'      => FILTER_SANITIZE_STRING,
			'_orbis_person_linkedin'      => FILTER_SANITIZE_STRING,
		);

		$data = filter_input_array( INPUT_POST, $definition );

		foreach ( $data as $key => $value ) {
			if ( empty( $value ) ) {
				delete_post_meta( $post_id, $key );
			} else {
				update_post_meta( $post_id, $key, $value );
			}
		}
	}

	/**
	 * @see https://github.com/WordPress/WordPress/blob/4.4.1/wp-includes/meta.php#L215-L230
	 */
	public function updated_post_meta( $meta_id, $object_id, $meta_key, $meta_value ) {
		if ( '_orbis_birth_date_string' !== $meta_key ) {
			return;
		}

		if ( '' === $meta_value ) {
			return;
		}

		$date = DateTime::createFromFormat( 'd-m-Y', $meta_value );

		if ( false !== $date ) {
			update_post_meta( $object_id, '_orbis_birth_date', $date->format( 'Y-m-d' ) );
			update_post_meta( $object_id, '_orbis_birth_date_timestamp', $date->getTimestamp() );
		} else {
			delete_post_meta( $object_id, '_orbis_birth_date' );
			delete_post_meta( $object_id, '_orbis_birth_date_timestamp' );
		}
	}
}
