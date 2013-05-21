<div class="panel">
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th scope="col">Projectleider</th>
				<th scope="col">ID</th>
				<th scope="col">Opdrachtgever</th>
				<th scope="col">Project</th>
				<th scope="col">Geregistreerde uren</th>
				<th scope="col">Beschikbare uren</th>
				<th scope="col">Factureerbaar</th>
				<th scope="col">Factuurnummer</th>
				<th scope="col">Acties</th>
			</tr>
		</thead>
	
		<tbody>
	
			<?php foreach($managers as $manager): ?>
	
				<tr>
					<th rowspan="<?php echo count($manager->projects) + 1; ?>">
						<?php echo $manager->name; ?>
					</th>
				</tr>
	
				<?php foreach($manager->projects as $project): ?>
		
					<tr>
						<td>
							<a href="<?php echo get_permalink( $project->project_post_id ); ?>" style="color: #000;">
								<?php echo $project->id; ?>
							</a>
						</td>
						<td>
							<a href="<?php echo get_permalink( $project->principal_post_id ); ?>" style="color: #000;">
								<?php echo $project->principalName; ?>
							</a>
						</td>
						<td>
							<a href="<?php echo get_permalink( $project->project_post_id ); ?>" style="color: #000;">
								<?php echo $project->name; ?>
							</a>
						</td>
						<td>
							<span style="color: <?php echo $project->failed ? 'Red' : 'Green'; ?>;"><?php echo orbis_time( $project->registeredSeconds ); ?></span>
						</td>
						<td>
							<?php echo orbis_time( $project->availableSeconds ); ?>
						</td>
						<td>
							<?php echo $project->invoicable ? 'Ja' : 'Nee'; ?>
						</td>
						<td>
							<?php echo $project->invoiceNumber; ?>
						</td>
						<td>
							<?php edit_post_link( __( 'Edit', 'orbis' ), '', '', $project->project_post_id ); ?>
						</td>
					</tr>
		
				<?php endforeach; ?>
	
			<?php endforeach; ?>
	
		</tbody>
	</table>
</div>