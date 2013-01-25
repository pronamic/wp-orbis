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
function orbis_flot( $id, $data, $options ) {
	printf(
		'<script type="text/javascript">jQuery.plot("#%s", %s, %s);</script>',
		$id,
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
	global $wp_scripts;

	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

	// Register scripts
	wp_register_script(
		'excanvas',
		plugins_url( '/includes/js/flot/excanvas' . $suffix. '.js', Orbis::$file )
	);
	
	// @see http://wordpress.stackexchange.com/a/20877
	// @see https://github.com/flot/flot/blob/master/examples/basic.html
	$wp_scripts->add_data( 'excanvas', 'conditional', 'lte IE 8' );

	wp_register_script(
		'jquery-flot',
		plugins_url( '/includes/js/flot/jquery.flot.js', Orbis::$file ),
		array( 'jquery' )
	);

	wp_register_script(
		'jquery-flot-pie',
		plugins_url( '/includes/js/flot/jquery.flot.pie.js', Orbis::$file ),
		array( 'jquery-flot' )
	);

	wp_register_script(
		'jquery-flot-resize',
		plugins_url( '/includes/js/flot/jquery.flot.resize.js', Orbis::$file ),
		array( 'jquery-flot' )
	);

	// Enqueue scripts
	wp_enqueue_script( 'jquery-flot' );
	wp_enqueue_script( 'jquery-flot-pie' );
	wp_enqueue_script( 'jquery-flot-resize' );
}

add_action( 'wp_enqueue_scripts', 'orbis_flot_enqueue_scripts' );
