<?php

function orbis_language_attributes( $atts ) {
	$atts .= ' ' . 'ng-app="orbisApp"';

	return $atts;
}

add_filter( 'language_attributes', 'orbis_language_attributes' );
