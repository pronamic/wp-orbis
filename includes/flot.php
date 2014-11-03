<?php

/**
 * Orbis Flot
 *
 * @param string $id
 * @param array $data
 * @param array $options
 *
 * @see https://github.com/flot/flot/
 * @see http://www.yiiframework.com/extension/flot/
 */
function orbis_flot( $element_id, $data, $options ) {
	printf(
		'<script type="text/javascript">jQuery.plot("#%s", %s, %s);</script>',
		$element_id,
		json_encode( $data ),
		json_encode( $options )
	);
}

/**
 * Orbis enqueue Flot scripts
 *
 * @see https://github.com/woothemes/woocommerce/blob/v1.6.6/admin/woocommerce-admin-dashboard.php#L442
 */
function orbis_flot_enqueue_scripts() {
	global $orbis_plugin;
	global $wp_scripts;

	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

	// Register scripts
	wp_register_script(
		'excanvas',
		$orbis_plugin->plugin_url( 'includes/js/flot/excanvas' . $suffix. '.js' )
	);

	// @see http://wordpress.stackexchange.com/a/20877
	// @see https://github.com/flot/flot/blob/master/examples/basic.html
	$wp_scripts->add_data( 'excanvas', 'conditional', 'lte IE 8' );

	wp_register_script(
		'jquery-flot',
		$orbis_plugin->plugin_url( 'includes/js/flot/jquery.flot.js' ),
		array( 'jquery' )
	);

	wp_register_script(
		'jquery-flot-pie',
		$orbis_plugin->plugin_url( 'includes/js/flot/jquery.flot.pie.js' ),
		array( 'jquery-flot' )
	);

	wp_register_script(
		'jquery-flot-resize',
		$orbis_plugin->plugin_url( 'includes/js/flot/jquery.flot.resize.js' ),
		array( 'jquery-flot' )
	);

	// Enqueue scripts
	wp_enqueue_script( 'jquery-flot' );
	wp_enqueue_script( 'jquery-flot-pie' );
	wp_enqueue_script( 'jquery-flot-resize' );
}

add_action( 'wp_enqueue_scripts', 'orbis_flot_enqueue_scripts' );
