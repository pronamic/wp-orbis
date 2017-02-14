<h2><?php esc_html_e( 'Map', 'orbis' ); ?></h2>

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
									selected( $name, $map[ $i ], false ),
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
									selected( $name, $map[ $i ], false ),
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
									selected( $name, $map[ $i ], false ),
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
	'orbis_contacts_import_map'
);

?>
