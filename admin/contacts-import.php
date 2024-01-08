<?php

check_admin_referer( 'orbis_contacts_import', 'orbis_contacts_import_nonce' );

$attachment_id = filter_input( INPUT_GET, 'attachment_id', FILTER_SANITIZE_STRING );
$offset        = filter_input( INPUT_GET, 'offset', FILTER_VALIDATE_INT );
$count         = filter_input( INPUT_GET, 'count', FILTER_VALIDATE_INT );

$file = get_attached_file( $attachment_id );

if ( ! is_readable( $file ) ) {
	return;
}

$map = get_post_meta( $attachment_id, '_orbis_contacts_import_map', true );

if ( empty( $map ) ) {
	return;
}

$data = array_map( 'str_getcsv', file( $file ) );

$first = array_shift( $data );

$updated = 0;

echo '<h2>';
printf(
	__( 'Importing %1$s/%2$s contacts', 'orbis' ),
	esc_html( $offset ),
	esc_html( count( $data ) )
);
echo '</h2>';

$data = array_slice( $data, $offset, $count );

if ( empty( $data ) ) {
	return;
}

echo '<ul>';

foreach ( $data as $row ) {
	$post = $this->create_import_post( $map, $row );

	$result = wp_insert_post( $post, true );

	if ( ! is_wp_error( $result ) ) {
		++$updated;
	}

	echo '<li>';

	if ( is_wp_error( $result ) ) {
		echo $result->get_error_message();
	} else {
		echo $result, ' - ', get_the_title( $result );
	}

	echo '</li>';
}

echo '</ul>';

$url = $this->get_import_contacts_url(
	[
		'attachment_id' => $attachment_id,
		'offset'        => $offset + $count,
	]
);

printf(
	'<a id="orbis-import-next-link" href="%s" >%s</a>',
	esc_url( $url ),
	esc_html__( 'Next', 'orbis' )
);

?>
<script type="text/javascript">
	setTimeout( function() {
		document.getElementById( "orbis-import-next-link" ).click();
	}, 1250 );
</script>
