<div class="wrap">
	<?php screen_icon( 'orbis' ); ?>

	<h2>
		<?php _e( 'Projects', 'orbis' ); ?>
	</h2>
	
	<?php 
	
	global $wpdb;
	
	// Orbis users and WordPress users
	$orbisWordPressUsers = array(
		1 => 4 , // jelke
		2 => 6 , // martijn
		3 => 7 , // leo
		4 => 5 , // jl
		5 => 3 , // kj
		6 => 2 , // remco
		24 	=> 8 // martijnduker
	);
	
	// Projects
	$query = '
		SELECT 
			project.id , 
			project.name , 
			project.description AS description , 
			project.start_date AS startDate , 
			project.contact_id_1 AS contactId , 
			project.post_id AS postId ,   
			company.name AS companyName
		FROM 
			orbis_projects AS project
				LEFT JOIN
			orbis_companies AS company
					ON company.id = project.principal_id 
	';
	
	$projects = $wpdb->get_results($query);

	if(empty($projects)): ?>
	
	<p>
		<?php _e('No projects founds.', 'orbis'); ?>
	</p>

	<?php else: ?>

	<table cellspacing="0" class="widefat fixed">

		<?php foreach(array('thead', 'tfoot') as $tag): ?>

		<<?php echo $tag; ?>>
			<tr>
				<th scope="col" class="manage-column"><?php _e('ID', 'orbis') ?></th>
				<th scope="col" class="manage-column"><?php _e('Company', 'orbis') ?></th>
				<th scope="col" class="manage-column"><?php _e('Name', 'orbis') ?></th>
				<th scope="col" class="manage-column"><?php _e('Post ID', 'orbis') ?></th>
			</tr>
		</<?php echo $tag; ?>>

		<?php endforeach; ?>

		<tbody>

			<?php foreach($projects as $project): ?>

			<tr>
				<td>
					<?php echo $project->id; ?>
				</td>
				<td>
					<?php echo $project->companyName; ?>
				</td>
				<td>
					<?php echo $project->name; ?>
				</td>
				<td>
					<?php 

					if(empty($project->postId)) {
						$postTitle = $project->companyName . ' - ' . $project->name;
						$postDate = new DateTime($project->startDate);

						$post = array(
							'post_title' => $postTitle , 
							'post_content' => $project->description , 
							'post_status' => 'publish' , 
							'post_type' => 'orbis_project' , 
							'post_date' => $postDate->format('Y-m-d H:i:s') 
						);
						
						if(isset($orbisWordPressUsers[$project->contactId])) {
							$post['post_author'] = $orbisWordPressUsers[$project->contactId];
						}

						if(true) {
							$result = wp_insert_post($post, true);
							
							if(is_wp_error($result)) {
								echo $result;
							} else {
								$project->postId = $result;
	
								$updated = $wpdb->update(
									'orbis_projects' , 
									array('post_id' => $project->postId) , 
									array('id' => $project->id) , 
									array('%d') , 
									array('%d') 
								);
							}
						}
					}
					
					edit_post_link(__('Edit Project Post', 'orbis'), '', '', $project->postId);
					
					?>
				</td>
			</tr>

			<?php endforeach; ?>

		</tbody>
	</table>
	
	<?php endif; ?>
</div>