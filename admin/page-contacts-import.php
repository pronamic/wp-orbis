<?php

$attachment_id = filter_input( INPUT_GET, 'attachment_id', FILTER_SANITIZE_STRING );

$file = get_attached_file( $attachment_id );

$wp_columns = array(
	'post[ID]'           => __( 'ID', 'orbis' ),
	'post[post_title]'   => __( 'Name', 'orbis' ),
);

$meta = array(
	'_orbis_title'                => __( 'Title', 'orbis' ),
	'_orbis_organization'         => __( 'Organization', 'orbis' ),
	'_orbis_department'           => __( 'Department', 'orbis' ),
	'_orbis_person_email_address' => __( 'Email', 'orbis' ),
	'_orbis_address'              => __( 'Address', 'orbis' ),
	'_orbis_postcode'             => __( 'Postcode', 'orbis' ),
	'_orbis_city'                 => __( 'City', 'orbis' ),
	'_orbis_country'              => __( 'Country', 'orbis' ),
	'_orbis_person_phone_number'  => __( 'Phone Number', 'orbis' ),
	'_orbis_person_mobile_number' => __( 'Mobile Number', 'orbis' ),
	'_orbis_person_twitter'       => __( 'Twitter', 'orbis' ),
	'_orbis_person_facebook'      => __( 'Facebook', 'orbis' ),
	'_orbis_person_linkedin'      => __( 'LinkedIn', 'orbis' ),
);

foreach ( $meta as $key => $label ) {
	$name = sprintf(
		'post[meta_input][%s]',
		$key
	);

	$wp_columns[ $name ] = $label;
}

$csv_row_1 = array();
$csv_row_2 = array();

if ( is_readable( $file ) ) {
	$handle = fopen( $file, 'r' );

	$csv_row_1 = str_getcsv( fgets( $handle ) );
	$csv_row_2 = str_getcsv( fgets( $handle ) );
}

$defaults = array_keys( $wp_columns );

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
						<th scope="col"><?php esc_html_e( 'WordPress', 'orbis' ); ?></th>
						<th scope="col"><?php esc_html_e( 'First Row', 'orbis' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Second Row', 'orbis' ); ?></th>
						<th scope="col"><?php esc_html_e( 'CSV', 'orbis' ); ?></th>
					</tr>
				</thead>

				<tbody>

					<?php $i = 0; ?>

					<?php foreach ( $wp_columns as $key => $label ) : ?>

						<tr>
							<td>
								<?php echo esc_html( $label ); ?>
							</td>
							<td>
								<?php echo esc_html( $csv_row_1[ $i ] ); ?>
							</td>
							<td>
								<?php echo esc_html( $csv_row_2[ $i ] ); ?>
							</td>
							<td>
								<select style="width: 100%;" name="<?php echo esc_attr( $key ); ?>">
									<option value=""><?php esc_html_e( '— Nothing (skip) —', 'orbis' ); ?></option>

									<?php

									foreach ( $csv_row_1 as $csv_index => $csv_label ) {
										printf(
											'<option value="%s" %s>%s</option>',
											esc_attr( $csv_index ),
											selected( $key, $defaults[ $csv_index ], false ),
											esc_html( $csv_label )
										);
									}

									?>
								</select>
							</td>
						</tr>

						<?php $i++; ?>

					<?php endforeach; ?>

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
