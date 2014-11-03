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

			<?php foreach ( $managers as $manager ) : ?>

				<tr>
					<th rowspan="<?php echo esc_attr( count( $manager->projects ) + 1 ); ?>">
						<?php echo esc_html( $manager->name ); ?>
					</th>
				</tr>

				<?php foreach ( $manager->projects as $project ) : ?>

					<tr>
						<td>
							<a href="<?php echo esc_attr( get_permalink( $project->project_post_id ) ); ?>" style="color: #000;">
								<?php echo esc_html( $project->id ); ?>
							</a>
						</td>
						<td>
							<a href="<?php echo esc_attr( get_permalink( $project->principal_post_id ) ); ?>" style="color: #000;">
								<?php echo esc_html( $project->principalName ); ?>
							</a>
						</td>
						<td>
							<a href="<?php echo esc_attr( get_permalink( $project->project_post_id ) ); ?>" style="color: #000;">
								<?php echo esc_html( $project->name ); ?>
							</a>
						</td>
						<td>
							<span style="color: <?php echo esc_attr( $project->failed ? 'Red' : 'Green' ); ?>;"><?php echo esc_html( rbis_time( $project->registeredSeconds ) ); ?></span>
						</td>
						<td>
							<?php echo esc_html( orbis_time( $project->availableSeconds ) ); ?>
						</td>
						<td>
							<?php echo esc_html( $project->invoicable ? 'Ja' : 'Nee' ); ?>
						</td>
						<td>
							<?php echo esc_html( $project->invoiceNumber ); ?>
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
