<?php

$attachment_id = filter_input( INPUT_GET, 'attachment_id', FILTER_SANITIZE_STRING );

$file = get_attached_file( $attachment_id );

$post_input = $this->get_import_post_input();
$meta_input = $this->get_import_meta_input();
$tax_input  = $this->get_import_tax_input();

// CSV
$csv_row_1 = array();
$csv_row_2 = array();

if ( is_readable( $file ) ) {
	$handle = fopen( $file, 'r' );

	$csv_row_1 = str_getcsv( fgets( $handle ) );
	$csv_row_2 = str_getcsv( fgets( $handle ) );
}

$input = $post_input + $meta_input + $tax_input;

$defaults = array_keys( $input );

$max_columns = max(
	count( $csv_row_1 ),
	count( $csv_row_2 )
);

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

	<form method="post" action="" enctype="multipart/form-data">
		<?php wp_nonce_field( 'orbis_contacts_import', 'orbis_contacts_import_nonce' ); ?>

		<?php if ( empty( $file ) ) : ?>

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

		<?php else : ?>

			<p>
				<code><?php echo esc_html( basename( $file ) ); ?></code>
			</p>

			<table class="widefat" style="width: auto;">
				<thead>
					<tr>
						<th scope="col"><?php esc_html_e( 'First Row', 'orbis' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Second Row', 'orbis' ); ?></th>
						<th scope="col"><?php esc_html_e( 'WordPress', 'orbis' ); ?></th>
					</tr>
				</thead>

				<tbody>

					<?php for ( $i = 0; $i < $max_columns; $i++ ) : ?>

						<tr>
							<td>
								<?php echo esc_html( isset( $csv_row_1[ $i ] ) ? $csv_row_1[ $i ] : '' ); ?>
							</td>
							<td>
								<?php echo esc_html( isset( $csv_row_2[ $i ] ) ? $csv_row_2[ $i ] : '' ); ?>
							</td>
							<td>
								<select style="width: 100%;" name="map[<?php echo esc_attr( $i ); ?>]">
									<option value=""><?php esc_html_e( '— Nothing (skip) —', 'orbis' ); ?></option>

									<optgroup label="<?php esc_attr_e( 'Post Fields', 'orbis' ); ?>">
										<?php

										foreach ( $post_input as $name => $label ) {
											printf(
												'<option value="%s" %s>%s</option>',
												esc_attr( $name ),
												selected( $name, $defaults[ $i ], false ),
												esc_html( $label )
											);
										}

										?>
									</optgroup>

									<optgroup label="<?php esc_attr_e( 'Meta Fields', 'orbis' ); ?>">
										<?php

										foreach ( $meta_input as $name => $label ) {
											printf(
												'<option value="%s" %s>%s</option>',
												esc_attr( $name ),
												selected( $name, $defaults[ $i ], false ),
												esc_html( $label )
											);
										}

										?>
									</optgroup>

									<optgroup label="<?php esc_attr_e( 'Taxonomies', 'orbis' ); ?>">
										<?php

										foreach ( $tax_input as $name => $label ) {
											printf(
												'<option value="%s" %s>%s</option>',
												esc_attr( $name ),
												selected( $name, $defaults[ $i ], false ),
												esc_html( $label )
											);
										}

										?>
									</optgroup>
								</select>
							</td>
						</tr>

					<?php endfor; ?>

				</tbody>
			</table>

			<input name="attachment_id" value="<?php echo esc_attr( $attachment_id ); ?>" type="hidden" />

			<?php

			submit_button(
				__( 'Import', 'orbis' ),
				'primary',
				'orbis_contacts_import'
			);

			?>

		<?php endif; ?>
	</form>
</div>
