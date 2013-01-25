<?php 

wp_nonce_field( 'orbis_save_keychain_details', 'orbis_keychain_details_meta_box_nonce' );

?>
<table class="form-table">
	<tr valign="top">
		<th scope="row">
			<label for="orbis_keychain_url"><?php _e('URL', 'orbis') ?></label>
		</th>
		<td>
			<input id="orbis_keychain_url" name="_orbis_keychain_url" value="<?php echo get_post_meta( $post->ID, '_orbis_keychain_url', true ); ?>" type="url" class="regular-text" />
			<span class="description"><br /><?php _e( 'Use an full URL: for HTTP <code>http://</code>, for FTP <code>ftp://</code>, for SFTP <code>sftp://</code>', 'orbis' ); ?></span>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="orbis_keychain_username"><?php _e('Username', 'orbis') ?></label>
		</th>
		<td>
			<input id="orbis_keychain_username" name="_orbis_keychain_username" value="<?php echo get_post_meta( $post->ID, '_orbis_keychain_username', true ); ?>" type="text" class="regular-text" />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="orbis_keychain_password"><?php _e('Password', 'orbis') ?></label>
		</th>
		<td>
			<input id="orbis_keychain_password" name="_orbis_keychain_password" value="<?php echo get_post_meta( $post->ID, '_orbis_keychain_password', true ); ?>" type="password" class="regular-text" />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="orbis_keychain_email"><?php _e('E-mail Address', 'orbis') ?></label>
		</th>
		<td>
			<input id="orbis_keychain_email" name="_orbis_keychain_email" value="<?php echo get_post_meta( $post->ID, '_orbis_keychain_email', true ); ?>" type="email" class="regular-text" />
		</td>
	</tr>
</table>