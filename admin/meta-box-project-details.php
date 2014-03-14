<?php

global $wpdb, $post;

wp_nonce_field( 'orbis_save_project_details', 'orbis_project_details_meta_box_nonce' );

$orbis_id       = get_post_meta( $post->ID, '_orbis_project_id', true );
$principal_id   = get_post_meta( $post->ID, '_orbis_project_principal_id', true );
$is_finished    = filter_var( get_post_meta( $post->ID, '_orbis_project_is_finished', true ), FILTER_VALIDATE_BOOLEAN );
$seconds        = get_post_meta( $post->ID, '_orbis_project_seconds_available', true );
$agreement_id   = get_post_meta( $post->ID, '_orbis_project_agreement_id', true );
$is_finished    = filter_var( get_post_meta( $post->ID, '_orbis_project_is_finished', true ), FILTER_VALIDATE_BOOLEAN );

if ( true ) {
	$project = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->orbis_projects WHERE post_id = %d;", $post->ID ) );

	if ( $project ) {
		$orbis_id	    = $project->id;
		$principal_id   = $project->principal_id;
		$is_finished    = $project->finished;
		$seconds        = $project->number_seconds;
		$is_finished    = $project->finished;
	}
}

?>
<table class="form-table">
	<tbody>
		<tr valign="top">
			<th scope="row">
				<label for="orbis_project_id"><?php _e( 'Orbis ID', 'orbis' ); ?></label>
			</th>
			<td>
				<input id="orbis_project_id" name="_orbis_project_id" value="<?php echo esc_attr( $orbis_id ); ?>" type="text" class="regular-text" readonly="readonly" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="_orbis_project_principal_id"><?php _e( 'Client', 'orbis' ); ?></label>
			</th>
			<td>
				<input type="text" id="_orbis_project_principal_id" name="_orbis_project_principal_id" value="<?php echo esc_attr( $principal_id ); ?>" class="orbis-id-control orbis_company_id_field regular-text" data-text="<?php echo esc_attr( $principal_id ); ?>" placeholder="<?php _e( 'Select Client', 'orbis' ); ?>" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="_orbis_project_seconds_available"><?php _e( 'Time', 'orbis' ); ?></label>
			</th>
			<td>
				<input size="5" id="_orbis_project_seconds_available" name="_orbis_project_seconds_available" value="<?php echo esc_attr( orbis_time( $seconds ) ); ?>" type="text"  class="small-text" />

				<p class="description">
					<?php _e( 'You can enter time as 1.5 or 1:30 (they both mean 1 hour and 30 minutes).', 'orbis' ); ?>
				</p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="_orbis_project_agreement_id">
					<?php _e( 'Agreement ID', 'orbis' ); ?>
				</label>
			</th>
			<td>
				<input size="5" type="text" id="_orbis_project_agreement_id" name="_orbis_project_agreement_id" value="<?php echo esc_attr( $agreement_id ); ?>" />

				<a id="choose-from-library-link" class="button"
					data-choose="<?php esc_attr_e( 'Choose a Agreement', 'orbis' ); ?>"
					data-type="<?php echo esc_attr( 'application/pdf, plain/text' ); ?>"
					data-element="<?php echo esc_attr( '_orbis_project_agreement_id' ); ?>"
					data-update="<?php esc_attr_e( 'Set as Agreement', 'orbis' ); ?>"><?php _e( 'Choose a Agreement', 'orbis' ); ?></a>

				<p class="description">
					<?php _e( 'You can select an .PDF or .TXT file from the WordPress media library.', 'orbis' ); ?><br />
					<?php _e( 'If you received the agreement by mail print the complete mail conversation with an PDF printer.', 'orbis' ); ?>
				</p>
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

<?php

// @see https://github.com/WordPress/WordPress/blob/master/wp-admin/js/custom-background.js#L23

wp_enqueue_media();

?>

<script type="text/javascript">
	( function( $ ) {
		$( document ).ready( function() {
			var frame;

			$('#choose-from-library-link').click( function( event ) {
				var $el = $( this );

				event.preventDefault();

				// If the media frame already exists, reopen it.
				if ( frame ) {
					frame.open();
					return;
				}

				// Create the media frame.
				frame = wp.media.frames.projectAgreement = wp.media( {
					// Set the title of the modal.
					title: $el.data( 'choose' ),

					// Tell the modal to show only images.
					library: {
						type: $el.data( 'type' ),
					},

					// Customize the submit button.
					button: {
						// Set the text of the button.
						text: $el.data( 'update' ),
						// Tell the button not to close the modal, since we're
						// going to refresh the page when the image is selected.
						close: false
					}
				} );

				// When an image is selected, run a callback.
				frame.on( 'select', function() {
					// Grab the selected attachment.
					var attachment = frame.state().get( 'selection' ).first();

					var element_id = $el.data( 'element' );

					$( "#" + element_id ).val( attachment.id );

					frame.close();
				} );

				// Finally, open the modal.
				frame.open();
			} );
		} );
	} )( jQuery );
</script>