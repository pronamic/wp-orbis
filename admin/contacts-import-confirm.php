<h2><?php esc_html_e( 'Confirm', 'orbis' ); ?></h2>

<input name="attachment_id" value="<?php echo esc_attr( $attachment_id ); ?>" type="hidden" />

<?php

submit_button(
	__( 'Import', 'orbis' ),
	'primary',
	'orbis_contacts_import'
);

?>
