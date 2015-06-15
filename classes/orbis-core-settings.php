<?php

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

		printf(
			'<span class="description"><br />%s</span>',
			sprintf(
				__( 'Example: %s', 'orbis' ),
				orbis_price( 12345678.90 )
			)
		);
	}
}
