<table class="form-table">
	<tbody>
		<tr valign="top">
			<th scope="row">
				<label for="orbis_project_principal_id"><?php _e( 'Principal ID', 'orbis' ); ?></label>
			</th>
			<td>
				<input type="text" id="orbis_project_principal_id" name="_orbis_project_principal_id" value="" size="5" class="orbis_company_id_field" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e( 'Invoicable', 'orbis' ); ?></th>
			<td>
				<fieldset>
					<legend class="screen-reader-text"><span><?php _e( 'Invoicable', 'orbis' ); ?></span></legend>

					<label for="users_can_register">
						<input type="checkbox" value="1" id="users_can_register" name="users_can_register">
						<?php _e( 'Project is invoicable', 'orbis' ); ?>
					</label>
				</fieldset>
			</td>
		</tr>
	</tbody>
</table>