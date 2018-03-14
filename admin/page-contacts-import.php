<?php

global $orbis_error;

$file = get_attached_file( $attachment_id );

$post_input = $this->get_import_post_input();
$meta_input = $this->get_import_meta_input();
$tax_input  = $this->get_import_tax_input();

// CSV
$csv_row_1 = array();
$csv_row_2 = array();

if ( is_readable( $file ) ) {
	$handle = fopen( $file, 'r' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fopen

	$csv_row_1 = str_getcsv( fgets( $handle ) );
	$csv_row_2 = str_getcsv( fgets( $handle ) );
}

$input = $post_input + $meta_input + $tax_input;

$defaults = array_keys( $input );

$max_columns = max(
	count( $csv_row_1 ),
	count( $csv_row_2 )
);

$map = get_post_meta( $attachment_id, '_orbis_contacts_import_map', true );

if ( empty( $map ) ) {
	$map = $defaults;
}

?>

<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<?php if ( filter_has_var( INPUT_GET, 'updated' ) ) : ?>

		<div id="message" class="updated notice notice-success is-dismissible">
			<p>
				<?php esc_html_e( 'Imported contacts.', 'orbis' ); ?>
			</p>
		</div>

	<?php endif; ?>

	<?php if ( is_wp_error( $orbis_error ) ) : ?>

		<div id="notice" class="notice notice-warning is-dismissible">
			<p>
				<?php echo esc_html( $orbis_error->get_error_message() ); ?>
			</p>
		</div>

	<?php endif; ?>

	<form method="post" action="" enctype="multipart/form-data">
		<?php wp_nonce_field( 'orbis_contacts_import', 'orbis_contacts_import_nonce' ); ?>

		<?php

		switch ( $step ) {
			case 'import':
				include 'contacts-import.php';

				break;
			case 'confirm':
				include 'contacts-import-confirm.php';

				break;
			case 'map':
				include 'contacts-import-map.php';

				break;
			case 'upload':
			default:
				include 'contacts-import-upload.php';

				break;
		}

		?>
	</form>
</div>
