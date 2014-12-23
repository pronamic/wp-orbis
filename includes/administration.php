<?php

function orbis_get_invoice_link( $invoice_number ) {
	$link = null;

	$link = apply_filters( 'orbis_invoice_link', $link, $invoice_number );

	return $link;
}
