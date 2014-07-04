<?php

function orbis_the_email_title() {
	global $orbis_email_title;

	echo esc_html( $orbis_email_title );
}

function orbis_email_header() {
	global $orbis_plugin;

	$orbis_plugin->get_template( 'emails/email-header.php' );
}

add_action( 'orbis_email_header', 'orbis_email_header' );


function orbis_email_footer() {
	global $orbis_plugin;

	$orbis_plugin->get_template( 'emails/email-footer.php' );
}

add_action( 'orbis_email_footer', 'orbis_email_footer' );
