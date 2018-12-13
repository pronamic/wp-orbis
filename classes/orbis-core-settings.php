<?php

use Pronamic\WordPress\Money\Money;

class Orbis_Core_Settings {
	public function __construct() {
		// Actions
		add_action( 'admin_init', array( $this, 'admin_init' ) );
	}

	public function admin_init() {
		add_settings_section(
			'orbis_currency',
			__( 'Currency', 'orbis' ),
			'__return_false',
			'orbis'
		);

		add_settings_field(
			'orbis_currency',
			__( 'Currency', 'orbis' ),
			array( $this, 'dropdown_currencies' ),
			'orbis',
			'orbis_currency'
		);

		register_setting( 'orbis', 'orbis_currency' );

		add_settings_section(
			'orbis_billing',
			__( 'Billing', 'orbis' ),
			'__return_false',
			'orbis'
		);

		add_settings_field(
			'orbis_invoice_header_text',
			__( 'Invoice Header Text', 'orbis' ),
			array( $this, 'input_text' ),
			'orbis',
			'orbis_billing'
		);

		register_setting( 'orbis', 'orbis_invoice_header_text' );

		add_settings_field(
			'orbis_invoice_footer_text',
			__( 'Invoice Footer Text', 'orbis' ),
			array( $this, 'input_text' ),
			'orbis',
			'orbis_billing'
		);

		register_setting( 'orbis', 'orbis_invoice_footer_text' );
	}

	public function dropdown_currencies() {
		$currencies = array(
			'GBP' => __( 'Pound sterling', 'orbis' ),
			'EUR' => __( 'Euros', 'orbis' ),
			'USD' => __( 'US Dollars', 'orbis' ),
		);

		$current = get_option( 'orbis_currency' );

		echo '<select name="orbis_currency" id="orbis_currency" class="code">';
		foreach ( $currencies as $code => $currency ) {
			printf(
				'<option value="%s" %s>%s</option>',
				esc_attr( $code ),
				selected( $code, $current, false ),
				$currency
			);
		}
		echo '</select>';

		$example_price = new Money( 12345678.90, 'EUR' );

		printf(
			'<span class="description"><br />%s</span>',
			sprintf(
				__( 'Example: %s', 'orbis' ),
				$example_price->format_i18n()
			)
		);
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
}
