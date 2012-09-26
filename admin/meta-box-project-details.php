<?php 

wp_nonce_field( 'orbis_save_project_details', 'orbis_project_details_meta_box_nonce' );

?>
<table class="form-table">
	<tbody>
		<tr valign="top">
			<th scope="row">
				<label for="_orbis_project_principal_id"><?php _e( 'Principal ID', 'orbis' ); ?></label>
			</th>
			<td>
				<input type="text" id="_orbis_project_principal_id" name="_orbis_project_principal_id" value="" size="5" class="orbis_company_id_field" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e( 'Time', 'orbis' ); ?></th>
			<td>
				<span class="duration-field">
					<label for="_orbis_project_available_seconds_hours">Uren</label> 
					<input size="2" id="_orbis_project_available_seconds_hours" name="_orbis_project_available_seconds[hours]" value="" />
					<label for="_orbis_project_available_seconds_minutes">Minuten</label> 
					<input size="2" id="_orbis_project_available_seconds_minutes" name="_orbis_project_available_seconds[minutes]" value="" />
				</span>
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
					<input type="checkbox" value="yes" id="_orbis_project_is_invoicable" name="_orbis_project_is_invoicable" />
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
					<input type="checkbox" value="yes" id="_orbis_project_is_invoiced" name="_orbis_project_is_invoiced" />
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
				<input type="text" id="orbis_project_invoice_number" name="_orbis_project_invoice_number" value="" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="orbis_project_is_invoice_paid">
					<?php _e( 'Invoice Paid', 'orbis' ); ?>
				</label>
			</th>
			<td>
				<label for="orbis_project_is_invoice_paid">
					<input type="checkbox" value="yes" id="orbis_project_is_invoice_paid" name="orbis_project_is_invoice_paid" />
					<?php _e( 'Project invoice is paid', 'orbis' ); ?>
				</label>
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
					<input type="checkbox" value="yes" id="_orbis_project_is_finished" name="_orbis_project_is_finished" />
					<?php _e( 'Project is finished', 'orbis' ); ?>
				</label>
			</td>
		</tr>
	</tbody>
</table>