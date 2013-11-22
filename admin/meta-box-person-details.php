<?php

global $post;

$person_email_address = get_post_meta( $post->ID, '_orbis_person_email_address', true );
$person_phone_number  = get_post_meta( $post->ID, '_orbis_person_phone_number', true );
$person_mobile_number = get_post_meta( $post->ID, '_orbis_person_mobile_number', true );

$person_twitter  = get_post_meta( $post->ID, '_orbis_person_twitter', true );
$person_facebook = get_post_meta( $post->ID, '_orbis_person_facebook', true );
$person_linkedin = get_post_meta( $post->ID, '_orbis_person_linkedin', true );

wp_nonce_field( 'orbis_save_person_details', 'orbis_person_details_meta_box_nonce' );

?>
<p>
	<label for="orbis_person_email_address"><?php _e('Email Address:', 'orbis'); ?></label> <br />

	<input type="text" id="orbis_person_email_address" name="_orbis_person_email_address" value="<?php echo esc_attr( $person_email_address ); ?>" size="30" />
</p>

<p>
	<label for="orbis_person_phone_number"><?php _e('Phone Number:', 'orbis'); ?></label> <br />

	<input type="text" id="orbis_person_phone_number" name="_orbis_person_phone_number" value="<?php echo esc_attr( $person_phone_number ); ?>" size="30" />
</p>

<p>
	<label for="orbis_person_mobile_number"><?php _e('Mobile Number:', 'orbis'); ?></label> <br />

	<input type="text" id="orbis_person_mobile_number" name="_orbis_person_mobile_number" value="<?php echo esc_attr( $person_mobile_number ); ?>" size="30" />
</p>

<p>
	<label for="orbis_person_twitter"><?php _e('Twitter Username:', 'orbis'); ?></label> <br />

	<input type="text" id="orbis_person_twitter" name="_orbis_person_twitter" value="<?php echo esc_attr( $person_twitter ); ?>" size="30" />
</p>

<p>
	<label for="orbis_person_facebook"><?php _e('Facebook URL:', 'orbis'); ?></label> <br />

	<input type="text" id="orbis_person_facebook" name="_orbis_person_facebook" value="<?php echo esc_attr( $person_facebook ); ?>" size="30" />
</p>

<p>
	<label for="orbis_person_linkedin"><?php _e('LinkedIn:', 'orbis'); ?></label> <br />

	<input type="text" id="orbis_person_linkedin" name="_orbis_person_linkedin" value="<?php echo esc_attr( $person_linkedin ); ?>" size="30" />
</p>

