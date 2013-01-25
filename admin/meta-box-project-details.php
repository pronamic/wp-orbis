<?php 

global $post;

wp_nonce_field( 'orbis_save_project_details', 'orbis_project_details_meta_box_nonce' );

$principal_id  = get_post_meta( $post->ID, '_orbis_project_principal_id', true );
$is_invoicable = filter_var( get_post_meta( $post->ID, '_orbis_project_is_invoicable', true ), FILTER_VALIDATE_BOOLEAN );
$is_invoiced   = filter_var( get_post_meta( $post->ID, '_orbis_project_is_invoiced'  , true ), FILTER_VALIDATE_BOOLEAN );
$invoice_number = get_post_meta( $post->ID, '_orbis_project_invoice_number', true );
$is_finished   = filter_var( get_post_meta( $post->ID, '_orbis_project_is_finished'  , true ), FILTER_VALIDATE_BOOLEAN );
$seconds = get_post_meta( $post->ID, '_orbis_project_seconds_available', true );

?>
<table class="form-table">
	<tbody>
		<tr valign="top">
			<th scope="row">
				<label for="_orbis_project_principal_id"><?php _e( 'Principal ID', 'orbis' ); ?></label>
			</th>
			<td>
				<input type="text" id="_orbis_project_principal_id" name="_orbis_project_principal_id" value="<?php echo esc_attr( $principal_id ); ?>" class="orbis_company_id_field regular-text" data-text="<?php echo esc_attr( $principal_id ); ?>" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="_orbis_project_seconds_available"><?php _e( 'Time', 'orbis' ); ?></label>
			</th>
			<td>
				<input size="5" id="_orbis_project_seconds_available" name="_orbis_project_seconds_available" value="<?php echo esc_attr( orbis_format_seconds( $seconds ) ); ?>" />

				<p class="description">
					<?php _e( 'You can enter time as 1.5 or 1:30 (they both mean 1 hour and 30 minutes).', 'orbis' ); ?>
				</p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="_orbis_project_is_invoicable">
					<?php _e( 'Invoicable', 'orbis' ); ?>
				</label>
			</th>
			<td>
				<label for="_orbis_project_is_invoicable">
					<input type="checkbox" value="yes" id="_orbis_project_is_invoicable" name="_orbis_project_is_invoicable" <?php checked( $is_invoicable ); ?> />
					<?php _e( 'Project is invoicable', 'orbis' ); ?>
				</label>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="_orbis_project_is_invoiced">
					<?php _e( 'Invoiced', 'orbis' ); ?>
				</label>
			</th>
			<td>
				<label for="_orbis_project_is_invoiced">
					<input type="checkbox" value="yes" id="_orbis_project_is_invoiced" name="_orbis_project_is_invoiced" <?php checked( $is_invoiced ); ?> />
					<?php _e( 'Project is invoiced', 'orbis' ); ?>
				</label>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="orbis_project_invoice_number">
					<?php _e( 'Invoice Number', 'orbis' ); ?>
				</label>
			</th>
			<td>
				<input type="text" id="orbis_project_invoice_number" name="_orbis_project_invoice_number" value="<?php echo esc_attr( $invoice_number ); ?>" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="_orbis_project_is_finished">
					<?php _e( 'Finished', 'orbis' ); ?>
				</label>
			</th>
			<td>
				<label for="_orbis_project_is_finished">
					<input type="checkbox" value="yes" id="_orbis_project_is_finished" name="_orbis_project_is_finished" <?php checked( $is_finished ); ?> />
					<?php _e( 'Project is finished', 'orbis' ); ?>
				</label>
			</td>
		</tr>
	</tbody>
</table>