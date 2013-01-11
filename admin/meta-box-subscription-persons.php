<?php 

wp_nonce_field( 'orbis_save_subscription_persons', 'orbis_subscription_persons_meta_box_nonce' );

?>
<table class="form-table">
	<tr valign="top">
		<th scope="row">
			<label for="orbis_subscription_person_id"><?php _e( 'Person', 'orbis' ) ?></label>
		</th>
		<td>
			<input id="orbis_subscription_person_id" name="_orbis_subscription_person_id" value="<?php echo get_post_meta( $post->ID, '_orbis_subscription_person_id', true ); ?>" type="text" class="regular-text" />
		</td>
	</tr>
</table>