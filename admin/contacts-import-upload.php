<h2><?php esc_html_e( 'Upload', 'orbis' ); ?></h2>

<p>
	<input type="file" name="orbis_contacts_import_file" />
</p>

<?php

submit_button(
	__( 'Upload', 'orbis' ),
	'primary',
	'orbis_contacts_import_upload'
);

?>
