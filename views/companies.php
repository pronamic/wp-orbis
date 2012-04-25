<div class="wrap">
	<?php screen_icon(Orbis::SLUG); ?>

	<h2>
		<?php _e('Companies', Orbis::TEXT_DOMAIN); ?>
	</h2>
	
	<?php 
	
	global $wpdb;
	
	// Companies
	$query = '
		SELECT 
			id , 
			name , 
			added_on_date AS date , 
			post_id AS postId  
		FROM 
			orbis_companies 
		ORDER BY
			id
	';
	
	$companies = $wpdb->get_results($query);

	if(empty($companies)): ?>
	
	<p>
		<?php _e('No companies founds.', Orbis::TEXT_DOMAIN); ?>
	</p>

	<?php else: ?>

	<table cellspacing="0" class="widefat fixed">

		<?php foreach(array('thead', 'tfoot') as $tag): ?>

		<<?php echo $tag; ?>>
			<tr>
				<th scope="col" class="manage-column"><?php _e('ID', Orbis::TEXT_DOMAIN) ?></th>
				<th scope="col" class="manage-column"><?php _e('Name', Orbis::TEXT_DOMAIN) ?></th>
				<th scope="col" class="manage-column"><?php _e('Post ID', Orbis::TEXT_DOMAIN) ?></th>
			</tr>
		</<?php echo $tag; ?>>

		<?php endforeach; ?>

		<tbody>

			<?php foreach($companies as $company): ?>

			<tr>
				<td>
					<?php echo $company->id; ?>
				</td>
				<td>
					<?php echo $company->name; ?>
				</td>
				<td>
					<?php 
					
					if(empty($company->postId)) {
						$post = array(
							'post_title' => $company->name ,
							'post_date' => date('Y-m-d H:i:s', $company->date) , 
							'post_status' => 'publish' , 
							'post_type' => 'orbis_company'
						);

						$result = wp_insert_post($post, true);
						
						if(is_wp_error($result)) {
							echo $result;
						} else {
							$company->postId = $result;

							$updated = $wpdb->update(
								'orbis_companies' , 
								array('post_id' => $company->postId) , 
								array('id' => $company->id) , 
								array('%d') , 
								array('%d') 
							);
						}
					}
					
					edit_post_link(__('Edit Company Post', Orbis::TEXT_DOMAIN), '', '', $company->postId);
					
					?>
				</td>
			</tr>

			<?php endforeach; ?>

		</tbody>
	</table>
	
	<?php endif; ?>
</div>