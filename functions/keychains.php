<?php

/**
 * Add keychain meta boxes
 */
function orbis_keychain_add_meta_boxes() {
	add_meta_box(
		'orbis_keychain' , 
		__('Keychain Details', 'orbis') , 
		'orbis_keychain_details_meta_box' , 
        'orbis_keychain' , 
		'normal' , 
		'high'
    );
}

add_action('add_meta_boxes', 'orbis_keychain_add_meta_boxes');

/**
 * Keychain details meta box
 * 
 * @param array $post
 */
function orbis_keychain_details_meta_box($post) {
	include dirname(Orbis::$file) . '/admin/meta-box-keychain-details.php';
}

/**
 * Save keychain details
 */
function orbis_save_keychain_details($post_id, $post) {
	// Doing autosave
	if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) { 
		return;
	}

	// Verify nonce
	$nonce = filter_input(INPUT_POST, 'orbis_keychain_details_meta_box_nonce', FILTER_SANITIZE_STRING);
	if(!wp_verify_nonce($nonce, 'orbis_save_keychain_details')) {
		return;
	}

	// Check permissions
	if(!($post->post_type == 'orbis_keychain' && current_user_can('edit_post', $post_id))) {
		return;
	}

	// OK
	$definition = array(
		'_orbis_keychain_url' => FILTER_VALIDATE_URL , 
		'_orbis_keychain_email' => FILTER_VALIDATE_EMAIL , 
		'_orbis_keychain_username' => FILTER_SANITIZE_STRING ,
		'_orbis_keychain_password' => FILTER_SANITIZE_STRING  
	);
	
	$data = filter_input_array(INPUT_POST, $definition);

	foreach($data as $key => $value) {		
		if(empty($value)) {
			delete_post_meta($post_id, $key);
		} else {
			update_post_meta($post_id, $key, $value);
		}
	}
}

add_action('save_post', 'orbis_save_keychain_details', 10, 2);

/**
 * Comment form defaults
 */
function orbis_keychain_comment_form($post_id) {
	// Some themes call this function, don't show the checkbox again
	remove_action('comment_form', __FUNCTION__);

	if(get_post_type($post_id) == 'orbis_keychain') {
		$str  = '';

		$str .= '<p class="comment-subscription-form">';
		$str .= '	<input type="checkbox" name="orbis_keychain_password_request" id="orbis_keychain_password_request" value="true" /> ';
		$str .=	'	<label for="orbis_keychain_password_request">' . __( 'Request password.', 'orbis' ) . '</label>';
		$str .= '</p>';

		echo $str;
	}
}

add_filter('comment_form', 'orbis_keychain_comment_form');

/**
 * Keychain comment post
 * 
 * @param string $comment_id
 * @param string $approved
 */
function orbis_keychain_comment_post($comment_id, $approved) {
	$isPasswordRequest = filter_input(INPUT_POST, 'orbis_keychain_password_request', FILTER_VALIDATE_BOOLEAN);

	if($isPasswordRequest) {
		add_comment_meta($comment_id, 'orbis_keychain_password_request', $isPasswordRequest, true);
	}
}

add_action('comment_post', 'orbis_keychain_comment_post', 50, 2);

/**
 * Keychain comment text
 */
function orbis_keychain_get_comment_text($text, $comment) {
	$isPasswordRequest = get_comment_meta($comment->comment_ID, 'orbis_keychain_password_request', true);

	if($isPasswordRequest) {
		$visibleDate = new DateTime($comment->comment_date);
		$visibleDate->modify('+1 hour');

		$str  = '';

		$str .= '<p>';
		$str .= '	' . sprintf(__('This comment was an password request, the user can view the password till %s', 'orbis'), $visibleDate->format(DATE_W3C));
		$str .= '</p>';

		$currentUser = wp_get_current_user();

		if($currentUser->ID == $comment->user_id) {
			$password = get_post_meta($comment->comment_post_ID, '_orbis_keychain_password', true);
	
			$str .= '<pre>';
			$str .= $password;
			$str .= '</pre>';
		}

		$text .= $str;
	}

	return $text;
}

add_filter('comment_text', 'orbis_keychain_get_comment_text', 20, 2);

/**
 * Keychain content
 */
function orbis_keychain_the_content($content) {
	$id = get_the_ID();
	
	if(get_post_type() == 'orbis_keychain') {
		$url = get_post_meta($id, '_orbis_keychain_url', true);
		$email = get_post_meta($id, '_orbis_keychain_email', true);
		$username = get_post_meta($id, '_orbis_keychain_username', true);

		$str  = '';

		$str .= '<dl>';
		$str .= '	<dt>' . __('URL', 'orbis') . '</dt>';
		$str .= '	<dd>' . sprintf('<a href="%s">%s</a>', $url, $url) . '</dd>';

		$str .= '	<dt>' . __('Username', 'orbis') . '</dt>';
		$str .= '	<dd>' . $username . '</dd>';

		$str .= '	<dt>' . __('Password', 'orbis') . '</dt>';
		$str .= '	<dd>' . '********' . '</dd>';

		$str .= '	<dt>' . __('E-mail Address', 'orbis') . '</dt>';
		$str .= '	<dd>' . sprintf('<a href="mailto:%s">%s</a>', $email, $email) . '</dd>';
		$str .= '</dl>';

		$content .= $str;
	}

	return $content;
}

add_filter('the_content', 'orbis_keychain_the_content');