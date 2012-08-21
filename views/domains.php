<div class="wrap">
	<?php screen_icon( 'orbis' ); ?>

	<h2>
		<?php _e('Domain Names', 'orbis'); ?>
	</h2>
	
	<?php 
	
	global $wpdb;
	
	// Domain Names
	$query = '
		SELECT 
			id , 
			domain_name AS name , 
			post_id AS postId  
		FROM 
			orbis_domain_names 
		ORDER BY
			DAYOFYEAR(order_date)
	';
	
	$domains = $wpdb->get_results($query);

	if(empty($domains)): ?>
	
	<p>
		<?php _e('No domain names founds.', 'orbis'); ?>
	</p>

	<?php else: ?>

	<table cellspacing="0" class="widefat fixed">

		<?php foreach(array('thead', 'tfoot') as $tag): ?>

		<<?php echo $tag; ?>>
			<tr>
				<th scope="col" class="manage-column"><?php _e('ID', 'orbis') ?></th>
				<th scope="col" class="manage-column"><?php _e('Domain Name', 'orbis') ?></th>
				<th scope="col" class="manage-column"><?php _e('Post ID', 'orbis') ?></th>
			</tr>
		</<?php echo $tag; ?>>

		<?php endforeach; ?>

		<tbody>

			<?php foreach($domains as $domain): ?>

			<tr>
				<td>
					<?php echo $domain->id; ?>
				</td>
				<td>
					<?php echo $domain->name; ?>
				</td>
				<td>
					<?php 
					
					if(empty($domain->postId)) {
						$post = array(
							'post_title' => $domain->name , 
							'post_status' => 'publish' , 
							'post_type' => 'orbis_domain_name'
						);

						$result = wp_insert_post($post, true);
						
						if(is_wp_error($result)) {
							echo $result;
						} else {
							$domain->postId = $result;

							$updated = $wpdb->update(
								'orbis_domain_names' , 
								array('post_id' => $domain->postId) , 
								array('id' => $domain->id) , 
								array('%d') , 
								array('%d') 
							);
						}
					}
					
					edit_post_link(__('Edit Domain Name Post', 'orbis'), '', '', $domain->postId);
					
					?>
				</td>
			</tr>

			<?php endforeach; ?>

		</tbody>
	</table>
	
	<?php endif; ?>
</div>