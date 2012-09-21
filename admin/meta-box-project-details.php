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
			<th scope="row"><?php _e( 'Time', 'orbis' ); ?></th>
			<td>
				<span class="duration-field">
					<label for="project-duration-field-hours-field">Uren</label> 
					<input size="2" id="project-duration-field-hours-field" name="project[duration][hours]" value="78" />
					<label for="project-duration-field-minutes-field">Minuten</label> 
					<input size="2" id="project-duration-field-minutes-field" name="project[duration][minutes]" value="0" />
				</span>
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
		<tr valign="top">
			<th scope="row"><?php _e( 'Invoiced', 'orbis' ); ?></th>
			<td>
				<fieldset>
					<legend class="screen-reader-text"><span><?php _e( 'Invoiced', 'orbis' ); ?></span></legend>

					<label for="users_can_register">
						<input type="checkbox" value="1" id="users_can_register" name="users_can_register">
						<?php _e( 'Project is invoiced', 'orbis' ); ?>
					</label>
				</fieldset>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e( 'Invoice Number', 'orbis' ); ?></th>
			<td>
				<input type="text" id="orbis_project_invoice_number" name="_orbis_project_invoice_number" value="" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e( 'Invoice Paid', 'orbis' ); ?></th>
			<td>
				<fieldset>
					<legend class="screen-reader-text"><span><?php _e( 'Invoice Paid', 'orbis' ); ?></span></legend>

					<label for="users_can_register">
						<input type="checkbox" value="1" id="users_can_register" name="users_can_register">
						<?php _e( 'Project invoice is paid', 'orbis' ); ?>
					</label>
				</fieldset>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e( 'Finished', 'orbis' ); ?></th>
			<td>
				<fieldset>
					<legend class="screen-reader-text"><span><?php _e( 'Invoice Paid', 'orbis' ); ?></span></legend>

					<label for="users_can_register">
						<input type="checkbox" value="1" id="users_can_register" name="users_can_register">
						<?php _e( 'Project is finished', 'orbis' ); ?>
					</label>
				</fieldset>
			</td>
		</tr>
	</tbody>
</table>