<?php

class Orbis_Core_Email {
	public function __construct( $plugin ) {
		$this->plugin = $plugin;

		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_init', array( $this, 'maybe_email_manually' ) );

		add_action( 'orbis_email', array( $this, 'send_email' ) );
	}

	public function admin_init() {
		add_filter( sprintf( 'pre_update_option_%s', 'orbis_email_frequency' ), array( $this, 'update_option_frequency' ), 10, 2 );

		// E-mail
		add_settings_section(
			'orbis_email', // id
			__( 'E-mail', 'orbis' ), // title
			'__return_false', // callback
			'orbis' // page
		);

		$options = array( '' );
		foreach ( wp_get_schedules() as $name => $schedule ) {
			$options[ $name ] = $schedule['display'];
		}

		add_settings_field(
			'orbis_email_frequency', // id
			__( 'Frequency', 'orbis' ), // title
			array( $this, 'input_select' ), // callback
			'orbis', // page
			'orbis_email', // section
			array(
				'label_for' => 'orbis_email_frequency',
				'options'   => $options,
			) // args
		);

		add_settings_field(
			'orbis_email_time', // id
			__( 'Time', 'orbis' ), // title
			array( $this, 'input_text' ), // callback
			'orbis', // page
			'orbis_email', // section
			array(
				'label_for' => 'orbis_email_time',
				'classes'   => array(),
			) // args
		);

		add_settings_field(
			'orbis_email_next_schedule', // id
			__( 'Next Schedule', 'orbis' ), // title
			array( $this, 'next_schedule' ), // callback
			'orbis', // page
			'orbis_email' // section
		);

		add_settings_field(
			'orbis_email_subject', // id
			__( 'Subject', 'orbis' ), // title
			array( $this, 'input_text' ), // callback
			'orbis', // page
			'orbis_email', // section
			array(
				'label_for' => 'orbis_email_subject',
			) // args
		);

		add_settings_field(
			'orbis_email_subject_date_format', // id
			__( 'Subject Date Format', 'orbis' ), // title
			array( $this, 'input_text' ), // callback
			'orbis', // page
			'orbis_email', // section
			array(
				'label_for' => 'orbis_email_subject_date_format',
			) // args
		);

		add_settings_field(
			'orbis_email_manually', // id
			__( 'E-mail Manually', 'orbis' ), // title
			array( $this, 'button_email_manually' ), // callback
			'orbis', // page
			'orbis_email' // section
		);

		register_setting( 'orbis', 'orbis_email_frequency' );
		register_setting( 'orbis', 'orbis_email_time' );
		register_setting( 'orbis', 'orbis_email_subject' );
		register_setting( 'orbis', 'orbis_email_subject_date_format' );
	}

	/**
	 * Input text
	 *
	 * @param array $args
	 */
	public function input_text( $args ) {
		$name = $args['label_for'];

		$classes = array( 'regular-text' );
		if ( isset( $args['classes'] ) ) {
			$classes = $args['classes'];
		}

		printf(
			'<input name="%s" id="%s" type="text" class="%s" value="%s" />',
			esc_attr( $name ),
			esc_attr( $name ),
			esc_attr( implode( ' ', $classes ) ),
			esc_attr( get_option( $name, '' ) )
		);
	}

	/**
	 * Input checkbox
	 *
	 * @param array $args
	 */
	public function input_checkbox( $args ) {
		$name = $args['label_for'];

		$classes = array();
		if ( isset( $args['classes'] ) ) {
			$classes = $args['classes'];
		}

		printf(
			'<input name="%s" id="%s" type="checkbox" class="%s" %s />',
			esc_attr( $name ),
			esc_attr( $name ),
			esc_attr( implode( ' ', $classes ) ),
			checked( 'on', get_option( $name ), false )
		);
	}

	/**
	 * Input select
	 *
	 * @param array $args
	 */
	public function input_select( $args ) {
		$name = $args['label_for'];

		$classes = array();
		if ( isset( $args['classes'] ) ) {
			$classes = $args['classes'];
		}

		$options = array();
		if ( isset( $args['options'] ) ) {
			$options = $args['options'];
		}

		$multiple = false;
		if ( isset( $args['multiple'] ) && $args['multiple'] ) {
			$multiple = true;
		}

		printf(
			'<select name="%s" id="%s" class="%s" %s>',
			esc_attr( $name ) . ( $multiple ? '[]' : '' ),
			esc_attr( $name ),
			esc_attr( implode( ' ', $classes ) ),
			$multiple ? 'multiple="multiple" size="10"' : ''
		);

		$current_value = get_option( $name, '' );

		foreach ( $options as $option_key => $option ) {

			$selected = ( is_string( $current_value ) && $option_key === $current_value ) ||
						( is_array( $current_value ) && in_array( $option_key, $current_value ) );

			printf(
				'<option value="%s" %s>%s</option>',
				esc_attr( $option_key ),
				selected( $selected, true, false ),
				esc_attr( $option )
			);
		}

		echo '</select>';
	}

	public function button_email_manually() {
		submit_button(
			__( 'Send E-mail', 'orbis' ),
			'secondary',
			'orbis_email_manually',
			false
		);
	}

	public function next_schedule() {
		$timestamp = wp_next_scheduled( 'orbis_email' );

		if ( $timestamp ) {
			$timestamp = get_date_from_gmt( date( 'Y-m-d H:i:s', $timestamp ), 'U' );

			echo date_i18n( 'D j M Y H:i:s', $timestamp );
		} else {
			_e( 'Not scheduled', 'orbis' );
		}
	}

	public function update_option_frequency( $value ) {
		wp_clear_scheduled_hook( 'orbis_email' );

		if ( ! empty( $value ) ) {
			$timestamp = strtotime( get_option( 'orbis_email_time' ) );

			$time = get_gmt_from_date( date( 'Y-m-d H:i:s', $timestamp ), 'U' );

			wp_schedule_event( $time, $value, 'orbis_email' );
		}

		return $value;
	}

	public function maybe_email_manually() {
		if ( filter_has_var( INPUT_POST, 'orbis_email_manually' ) ) {
			$this->send_email();
		}
	}

	/**
	 * Sends an email containing this week's timesheets to all selected users.
	 */
	public function send_email() {
		$user_ids = get_users( array(
			'fields'     => 'ids',
			'meta_key'   => '_orbis_user',
			'meta_value' => 'true',
		) );

		global $orbis_email_title;

		$orbis_email_title = str_replace(
			array(
				'{date}',
			),
			array(
				date_i18n( get_option( 'orbis_email_subject_date_format' ) ),
			),
			get_option( 'orbis_email_subject', __( 'Orbis Update', 'orbis' ) )
		);

		$mail_to      = '';
		$mail_subject = $orbis_email_title;
		$mail_body    = $this->plugin->get_template( 'emails/update.php', false );
		$mail_headers = array(
			'From: ' . get_bloginfo( 'name' ) . ' <' . get_bloginfo( 'admin_email' ) . '>',
			'Content-Type: text/html',
		);

		foreach ( $user_ids as $user_id ) {
			$user_email = get_the_author_meta( 'user_email', $user_id );

			if ( is_email( $user_email ) ) {
				$mail_to .= ' ' . $user_email . ', ';
			}
		}

		wp_mail( $mail_to, $mail_subject, $mail_body, $mail_headers );
	}
}
