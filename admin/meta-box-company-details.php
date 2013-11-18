<?php

global $post;

$kvk_number = get_post_meta( $post->ID, '_orbis_company_kvk_number', true );
$email      = get_post_meta( $post->ID, '_orbis_company_email', true );
$website    = get_post_meta( $post->ID, '_orbis_company_website', true );

$address    = get_post_meta( $post->ID, '_orbis_company_address', true );
$postcode   = get_post_meta( $post->ID, '_orbis_company_postcode', true );
$city       = get_post_meta( $post->ID, '_orbis_company_city', true );
$country    = get_post_meta( $post->ID, '_orbis_company_country', true );

wp_nonce_field( 'orbis_save_company_details', 'orbis_company_details_meta_box_nonce' );

?>
<table class="form-table">
	<tbody>
		<tr>
			<th scope="row">
				<label for="orbis_company_kvk_number"><?php _e( 'KvK Number', 'orbis' ); ?></label>
			</th>
			<td>
				<input id="orbis_company_kvk_number" name="_orbis_company_kvk_number" value="<?php echo esc_attr( $kvk_number ); ?>" type="text" size="10" />
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="orbis_company_email"><?php _e( 'E-Mail', 'orbis' ); ?></label>
			</th>
			<td>
				<input id="orbis_company_email" name="_orbis_company_email" value="<?php echo esc_attr( $email ); ?>" type="email" size="42" />
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="orbis_company_website"><?php _e( 'Website', 'orbis' ); ?></label>
			</th>
			<td>
				<input id="orbis_company_website" name="_orbis_company_website" value="<?php echo esc_attr( $website ); ?>" type="url" size="42" />
			</td>
		</tr>
		<tr>
			<th scope="col" colspan="2">
				<h4 class="title"><?php _e( 'Addresses', 'orbis' ); ?></h4>
			</th>
		</tr>
		<tr>
			<th scope="row">
				<label for="orbis_company_address"><?php _e( 'Address', 'orbis' ); ?></label>
			</th>
			<td>
				<input id="orbis_company_address" name="_orbis_company_address" placeholder="<?php esc_attr_e( 'Address', 'orbis' ); ?>" value="<?php echo esc_attr( $address ); ?>" type="text" size="42" />
				<br />
				<input id="orbis_company_postcode" name="_orbis_company_postcode" placeholder="<?php esc_attr_e( 'Postcode', 'orbis' ); ?>" value="<?php echo esc_attr( $postcode ); ?>" type="text" size="10" />
				<input id="orbis_company_city" name="_orbis_company_city" placeholder="<?php esc_attr_e( 'City', 'orbis' ); ?>" value="<?php echo esc_attr( $city ); ?>" type="text" size="25" />
				<br />
				<input id="orbis_company_country" name="_orbis_company_country" placeholder="<?php esc_attr_e( 'Country', 'orbis' ); ?>" value="<?php echo esc_attr( $country ); ?>" type="text" size="42" />
			</td>
		</tr>
	</tbody>
</table>