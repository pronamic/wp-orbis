<?php

/**
 * Add domain keychain meta boxes
 */
function orbis_sbuscriptions_add_meta_boxes() {
	add_meta_box(
		'orbis_subscription_persons' , 
		__('Persons', 'orbis') , 
		'orbis_subscription_persons_meta_box' , 
        'orbis_subscription' , 
		'normal' , 
		'high'
    );
}

add_action('add_meta_boxes', 'orbis_sbuscriptions_add_meta_boxes');

/**
 * Keychain details meta box
 * 
 * @param array $post
 */
function orbis_subscription_persons_meta_box($post) {
	include dirname(Orbis::$file) . '/admin/meta-box-subscription-persons.php';
}

/**
 * Save keychain details
 */
function orbis_save_subscription_persons($post_id, $post) {
	// Doing autosave
	if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) { 
		return;
	}

	// Verify nonce
	$nonce = filter_input(INPUT_POST, 'orbis_subscription_persons_meta_box_nonce', FILTER_SANITIZE_STRING);
	if(!wp_verify_nonce($nonce, 'orbis_save_subscription_persons')) {
		return;
	}

	// Check permissions
	if(!($post->post_type == 'orbis_subscription' && current_user_can('edit_post', $post_id))) {
		return;
	}

	// OK
	$definition = array(
		'_orbis_subscription_person_id' => FILTER_SANITIZE_STRING
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

add_action('save_post', 'orbis_save_subscription_persons', 10, 2);

/**
 * Keychain content
 */
function orbis_subscription_the_content($content) {
	if(get_post_type() == 'orbis_subscription') {
		$id = get_the_ID();

		$person_id = get_post_meta($id, '_orbis_subscription_person_id', true);

		$str  = '';
		
		$str .= '<h2>' . __('Persons', 'orbis') . '</h2>';

		$str .= '<dl>';

		if(!empty($person_id)) {
			$str .= '	<dt>' . __('Person', 'orbis') . '</dt>';
			$str .= '	<dd>' . sprintf('<a href="%s">%s</a>', get_permalink($person_id), get_the_title($person_id)) . '</dd>';
		}

		$str .= '</dl>';

		$content .= $str;
	}

	return $content;
}

add_filter('the_content', 'orbis_subscription_the_content');





/**
 * Keychain edit columns
 */
function orbis_subscription_edit_columns($columns) {
	return array(
			'cb' => '<input type="checkbox" />' ,
			'title' => __('Title', 'orbis') ,
			'orbis_subscription_person' => __('Person', 'orbis') ,
			'author' => __('Author', 'orbis') ,
			'comments' => __('Comments', 'orbis') ,
			'date' => __('Date', 'orbis') ,
	);
}

add_filter('manage_edit-orbis_subscription_columns' , 'orbis_subscription_edit_columns');

/**
 * Keychain column
 *
 * @param string $column
*/
function orbis_subscription_column($column) {
	$id = get_the_ID();

	switch($column) {
		case 'orbis_subscription_person':
			$person_id = get_post_meta($id, '_orbis_subscription_person_id', true);

			if(!empty($person_id)) {
				printf('<a href="%s" target="_blank">%s</a>', get_permalink( $person_id ), get_the_title( $person_id ) );
			}

			break;
	}
}

add_action('manage_posts_custom_column' , 'orbis_subscription_column');