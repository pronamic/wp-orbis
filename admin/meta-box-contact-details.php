<?php

global $post;

$contact = new Orbis_Contact( $post );

$person_phone_number  = get_post_meta( $post->ID, '_orbis_phone_number', true );
$person_mobile_number = get_post_meta( $post->ID, '_orbis_mobile_number', true );

$title        = get_post_meta( $post->ID, '_orbis_title', true );
$organization = get_post_meta( $post->ID, '_orbis_organization', true );
$department   = get_post_meta( $post->ID, '_orbis_department', true );

$address  = get_post_meta( $post->ID, '_orbis_address', true );
$postcode = get_post_meta( $post->ID, '_orbis_postcode', true );
$city     = get_post_meta( $post->ID, '_orbis_city', true );
$country  = get_post_meta( $post->ID, '_orbis_country', true );

$birth_date = get_post_meta( $post->ID, '_orbis_birth_date_string', true );
$iban       = get_post_meta( $post->ID, '_orbis_iban', true );

$person_twitter  = get_post_meta( $post->ID, '_orbis_twitter', true );
$person_facebook = get_post_meta( $post->ID, '_orbis_facebook', true );
$person_linkedin = get_post_meta( $post->ID, '_orbis_linkedin', true );

wp_nonce_field( 'orbis_save_person_details', 'orbis_person_details_meta_box_nonce' );

?>
<table class="form-table">
	<tbody>
		<tr valign="top">
			<th scope="row">
				<?php _e( 'Company', 'orbis' ); ?>
			</th>
			<td>
				<input type="text" id="_orbis_title" name="_orbis_title" value="<?php echo esc_attr( $title ); ?>" class="regular-text" placeholder="<?php echo esc_attr( _x( 'Title', 'contact', 'orbis' ) ); ?>" style="width: 10em;" />

				<input type="text" id="_orbis_organization" name="_orbis_organization" value="<?php echo esc_attr( $organization ); ?>" class="regular-text" placeholder="<?php echo esc_attr( _x( 'Organization', 'contact', 'orbis' ) ); ?>" />

				<input type="text" id="_orbis_department" name="_orbis_department" value="<?php echo esc_attr( $department ); ?>" class="regular-text" placeholder="<?php echo esc_attr( _x( 'Department', 'contact', 'orbis' ) ); ?>" style="width: 10em;" />
			</td>
		</tr>

		<tr valign="top">
			<th scope="row">
				<label for="orbis_person_gender"><?php _e( 'Gender', 'orbis' ); ?></label>
			</th>
			<td>
				<?php

				$taxonomy = 'orbis_gender';

				$gender = $contact->get_gender();

				wp_dropdown_categories( array(
					'show_option_none' => __( '— Select Gender —', 'orbis' ),
					'hide_empty'       => false,
					'name'             => sprintf( 'tax_input[%s]', $taxonomy ),
					'id'               => 'orbis_person_gender',
					'selected'         => $gender ? $gender->term_id : null,
					'taxonomy'         => $taxonomy,
				) );

				?>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row">
				<label for="orbis_email"><?php _e( 'Email Address', 'orbis' ); ?></label>
			</th>
			<td>
				<input type="text" id="orbis_email" name="_orbis_email" value="<?php echo esc_attr( $contact->get_email() ); ?>" class="regular-text" />
			</td>
		</tr>

		<tr valign="top">
			<th scope="row">
				<label for="orbis_phone_number"><?php _e( 'Phone Number', 'orbis' ); ?></label>
			</th>
			<td>
				<input type="text" id="orbis_person_phone_number" name="_orbis_person_phone_number" value="<?php echo esc_attr( $person_phone_number ); ?>" class="regular-text" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="orbis_mobile_number"><?php _e( 'Mobile Number', 'orbis' ); ?></label>
			</th>
			<td>
				<input type="text" id="orbis_person_mobile_number" name="_orbis_person_mobile_number" value="<?php echo esc_attr( $person_mobile_number ); ?>" class="regular-text" />
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label for="orbis_address"><?php _e( 'Address', 'orbis' ); ?></label>
			</th>
			<td>
				<input id="orbis_address" name="_orbis_address" placeholder="<?php esc_attr_e( 'Address', 'orbis' ); ?>" value="<?php echo esc_attr( $address ); ?>" type="text" size="42" />
				<br />
				<input id="orbis_postcode" name="_orbis_postcode" placeholder="<?php esc_attr_e( 'Postcode', 'orbis' ); ?>" value="<?php echo esc_attr( $postcode ); ?>" type="text" size="10" />
				<input id="orbis_city" name="_orbis_city" placeholder="<?php esc_attr_e( 'City', 'orbis' ); ?>" value="<?php echo esc_attr( $city ); ?>" type="text" size="25" />
				<br />
				<input id="orbis_country" name="_orbis_country" placeholder="<?php esc_attr_e( 'Country', 'orbis' ); ?>" value="<?php echo esc_attr( $country ); ?>" type="text" size="42" />
			</td>
		</tr>

		<tr valign="top">
			<th scope="row">
				<label for="orbis_birth_date"><?php _e( 'Birth Date', 'orbis' ); ?></label>
			</th>
			<td>
				<input type="text" id="orbis_birth_date" name="_orbis_birth_date_string" value="<?php echo esc_attr( $birth_date ); ?>" class="regular-text" />
			</td>
		</tr>

		<tr valign="top">
			<th scope="row">
				<label for="orbis_iban"><?php _e( 'IBAN', 'orbis' ); ?></label>
			</th>
			<td>
				<input type="text" id="orbis_iban" name="_orbis_iban" value="<?php echo esc_attr( $iban ); ?>" class="regular-text" />
			</td>
		</tr>

		<tr valign="top">
			<th scope="row">
				<label for="orbis_twitter"><?php _e( 'Twitter Username', 'orbis' ); ?></label>
			</th>
			<td>
				<input type="text" id="orbis_twitter" name="_orbis_twitter" value="<?php echo esc_attr( $person_twitter ); ?>" class="regular-text" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="orbis_facebook"><?php _e( 'Facebook URL', 'orbis' ); ?></label>
			</th>
			<td>
				<input type="text" id="orbis_facebook" name="_orbis_facebook" value="<?php echo esc_attr( $person_facebook ); ?>" class="regular-text" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="orbis_person_linkedin"><?php _e( 'LinkedIn URL', 'orbis' ); ?></label>
			</th>
			<td>
				<input type="text" id="orbis_linkedin" name="_orbis_linkedin" value="<?php echo esc_attr( $person_linkedin ); ?>" class="regular-text" />
			</td>
		</tr>
	</tbody>
</table>
