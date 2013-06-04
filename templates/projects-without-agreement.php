<?php

// Create a new filtering function that will add our where clause to the query
function filter_where( $where = '' ) {
	// posts for March 1 to March 15, 2010
	$where .= " AND post_date >= '2013-01-01'";
	return $where;
}

add_filter( 'posts_where', 'filter_where' );

$query = new WP_Query( array(
	'post_type'  => 'orbis_project',
	'nopaging'   => true,
	'meta_query' => array(
		array(
			'key'     => '_orbis_project_agreement_id',
			'compare' => 'NOT EXISTS'
		),
		array(
			'key'     => '_orbis_project_is_invoicable',
			'compare' => 'EXISTS'
		)
	)
) );

remove_filter( 'posts_where', 'filter_where' );

if ( $query->have_posts() ) : ?>

	<div class="panel">
		<table class="table table-striped table-bordered">
			<thead>
				<tr>
					<th scope="col"><?php _e( 'Orbis ID', 'orbis' ); ?></th>
					<th scope="col"><?php _e( 'Project Manager', 'orbis' ); ?></th>
					<th scope="col"><?php _e( 'Principal', 'orbis' ); ?></th>
					<th scope="col"><?php _e( 'Title', 'orbis' ); ?></th>
					<th scope="col"><?php _e( 'Actions', 'orbis' ); ?></th>
				</tr>
			</thead>
			
			<tbody>
	
				<?php while ( $query->have_posts() ) : $query->the_post(); ?>
				
					<tr>
						<td>
							<?php echo get_post_meta( get_the_ID(), '_orbis_project_id', true ); ?>
						</td>
						<td>
							<?php the_author(); ?>
						</td>
						<td>
							<?php 
							
							if ( orbis_project_has_principal() ) {
								printf( 
									'<a href="%s">%s</a>',
									esc_attr( orbis_project_principal_get_permalink() ),
									orbis_project_principel_get_the_name()
								);
							}
		
							?>
						</td>
						<td>
							<a href="<?php the_permalink(); ?>">
								<?php the_title(); ?>
							</a>
						</td>
						<td>
							<a href="<?php echo get_edit_post_link( get_the_ID() ); ?>">
								<?php _e( 'Edit', 'orbis' ); ?>
							</a>
						</td>
					</tr>
		
				<?php endwhile; ?>
			
			</tbody>
		</table>
	</div>
	

<?php endif; ?>