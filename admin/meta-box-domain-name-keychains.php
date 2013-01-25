<?php 

wp_nonce_field( 'orbis_save_domain_name_keychains', 'orbis_domain_name_keychains_meta_box_nonce' );

?>
<table class="form-table">
	<tr valign="top">
		<th scope="row">
			<label for="orbis_domain_name_ftp_keychain_id"><?php _e( 'FTP', 'orbis' ) ?></label>
		</th>
		<td>
			<input id="orbis_domain_name_ftp_keychain_id" name="_orbis_domain_name_ftp_keychain_id" value="<?php echo get_post_meta( $post->ID, '_orbis_domain_name_ftp_keychain_id', true ); ?>" type="text" class="regular-text" />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="orbis_domain_name_google_apps_keychain_id"><?php _e('Google Apps', 'orbis') ?></label>
		</th>
		<td>
			<input id="orbis_domain_name_google_apps_keychain_id" name="_orbis_domain_name_google_apps_keychain_id" value="<?php echo get_post_meta( $post->ID, '_orbis_domain_name_google_apps_keychain_id', true ); ?>" type="text" class="regular-text" />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="orbis_domain_name_wordpress_keychain_id"><?php _e('WordPress', 'orbis') ?></label>
		</th>
		<td>
			<input id="orbis_domain_name_wordpress_keychain_id" name="_orbis_domain_name_wordpress_keychain_id" value="<?php echo get_post_meta( $post->ID, '_orbis_domain_name_wordpress_keychain_id', true ); ?>" type="text" class="regular-text" />
		</td>
	</tr>
</table>