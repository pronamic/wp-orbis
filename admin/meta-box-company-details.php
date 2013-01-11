<?php 

$kvk_number = get_post_meta( $post->ID, '_orbis_company_kvk_number', true );

wp_nonce_field( 'orbis_save_company_details', 'orbis_company_details_meta_box_nonce' );

?>
<p>
	<label for="orbis_company_kvk_number"><?php _e( 'KvK Number:', 'orbis' ); ?></label> <br />

	<input type="text" id="orbis_company_kvk_number" name="_orbis_company_kvk_number" value="<?php echo esc_attr( $kvk_number ); ?>" size="30" />
</p>