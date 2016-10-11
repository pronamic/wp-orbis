<div class="panel">
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th scope="col"><?php _e( 'Manager', 'orbis' ); ?></th>
				<th scope="col"><?php _e( 'ID', 'orbis' ); ?></th>
				<th scope="col"><?php _e( 'Client', 'orbis' ); ?></th>
				<th scope="col"><?php _e( 'Project', 'orbis' ); ?></th>
				<th scope="col"><?php _e( 'Date', 'orbis' ); ?></th>
				<th scope="col"><?php _e( 'Comment', 'orbis' ); ?></th>
				<th scope="col"><?php _e( 'Time', 'orbis' ); ?></th>
				<th scope="col"><?php _e( 'Invoiceable', 'orbis' ); ?></th>
				<th scope="col"><?php _e( 'Invoice Number', 'orbis' ); ?></th>
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
						<td style="white-space: nowrap;">
							<?php echo esc_html( get_the_time( 'j M Y', $project->project_post_id ) ); ?>
						</td>
						<td style="white-space: nowrap;">
							<?php

							$comments_query = new WP_Comment_Query();

							$comments = $comments_query->query( array(
								'number'  => 1,
								'post_id' => $project->project_post_id,
							) );

							foreach ( $comments as $comment ) {
								$title = sprintf(
									__( '%1$s says on %2$s:', 'orbis' ),
									'<strong>' . $comment->comment_author . '</strong>',
									'<strong>' . date_i18n( 'j M Y', strtotime( $comment->comment_date ) ) . '</strong>'
								);

								printf(
									'<a href="%s" data-container="body" data-trigger="hover" data-toggle="popover" data-placement="right" data-title="%s" data-content="%s" data-html="true" role="button">%s</a>',
									esc_attr( get_comment_link( $comment ) ),
									esc_attr( $title ),
									esc_attr( $comment->comment_content ),
									date_i18n( 'j M Y', strtotime( $comment->comment_date ) ) . ' <span class="glyphicon glyphicon-comment" aria-hidden="true"></span>'
								);
							}

							?>
						</td>
						<td style="white-space: nowrap;">
							<span style="color: <?php echo esc_attr( $project->failed ? 'Red' : 'Green' ); ?>;"><?php echo esc_html( orbis_time( $project->registeredSeconds ) ); ?></span>
							/
							<?php echo esc_html( orbis_time( $project->availableSeconds ) ); ?>
						</td>
						<td>
							<?php echo esc_html( $project->invoicable ? 'Ja' : 'Nee' ); ?>
						</td>
						<td>
							<div class="actions">
								<?php echo esc_html( $project->invoiceNumber ); ?>

								<div class="nubbin">
									<?php edit_post_link( __( 'Edit', 'orbis' ), '', '', $project->project_post_id ); ?>
								</div>
							</div>
						</td>
					</tr>

				<?php endforeach; ?>

			<?php endforeach; ?>

		</tbody>
	</table>
</div>

<script>
	jQuery( document ).ready( function( $ ) {
		$( '[data-toggle="popover"]' ).popover(); 
	} );
</script>
