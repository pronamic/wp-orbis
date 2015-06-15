<?php

$table_style   = '';
$table_padding = 5;

$defaults = array(
	'posts_per_page' => 5,
	'no_found_rows'  => true,
	'date_query'     => array(
		array(
			'after' => '3 days ago',
		),
	),
);

?>

<?php do_action( 'orbis_email_header' ); ?>

<?php do_action( 'orbis_email_top' ); ?>

<div>
	<h2><?php esc_html_e( 'Recent Companies', 'orbis' ); ?></h2>

	<?php

	$query = new WP_Query( wp_parse_args( array( 'post_type' => 'orbis_company' ), $defaults ) );

	if ( $query->have_posts() ) : ?>

		<table style="<?php echo esc_attr( $table_style ); ?>" cellpadding="<?php echo esc_attr( $table_padding ); ?>">
			<thead>
				<tr>
					<th scope="col">
						<?php esc_html_e( 'Date', 'orbis' ); ?>
					</th>
					<th scope="col">
						<?php esc_html_e( 'Name', 'orbis' ); ?>
					</th>
				</tr>
			</thead>

			<tbody>

				<?php while ( $query->have_posts() ) : $query->the_post(); ?>

					<tr>
						<td>
							<?php the_time( 'D j M' ); ?>
						</td>
						<td>
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a><br />
						</td>
					</tr>

				<?php endwhile; ?>

			</tbody>
		</table>

	<?php else : ?>

		<p>
			<em><?php esc_html_e( 'No new posts in the last 3 days.', 'orbis' ); ?></em>
		</p>

	<?php endif; ?>

</div>

<div>
	<h2><?php esc_html_e( 'Recent Persons', 'orbis' ); ?></h2>

	<?php

	$query = new WP_Query( wp_parse_args( array( 'post_type' => 'orbis_person' ), $defaults ) );

	if ( $query->have_posts() ) : ?>

		<table style="<?php echo esc_attr( $table_style ); ?>" cellpadding="<?php echo esc_attr( $table_padding ); ?>">
			<thead>
				<tr>
					<th scope="col">
						<?php esc_html_e( 'Date', 'orbis' ); ?>
					</th>
					<th scope="col">
						<?php esc_html_e( 'Name', 'orbis' ); ?>
					</th>
				</tr>
			</thead>

			<tbody>

				<?php while ( $query->have_posts() ) : $query->the_post(); ?>

					<tr>
						<td>
							<?php the_time( 'D j M' ); ?>
						</td>
						<td>
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a><br />
						</td>
					</tr>

				<?php endwhile; ?>

			</tbody>
		</table>

	<?php else : ?>

		<p>
			<em><?php esc_html_e( 'No new posts in the last 3 days.', 'orbis' ); ?></em>
		</p>

	<?php endif; ?>

</div>

<div>
	<h2><?php esc_html_e( 'Recent Projects', 'orbis' ); ?></h2>

	<?php

	$query = new WP_Query( wp_parse_args( array( 'post_type' => 'orbis_project' ), $defaults ) );

	if ( $query->have_posts() ) : ?>

		<table style="<?php echo esc_attr( $table_style ); ?>" cellpadding="<?php echo esc_attr( $table_padding ); ?>">
			<thead>
				<tr>
					<th scope="col">
						<?php esc_html_e( 'Date', 'orbis' ); ?>
					</th>
					<td>
						<?php esc_html_e( 'Name', 'orbis' ); ?>
					</th>
				</tr>
			</thead>

			<tbody>

				<?php

				$query = new WP_Query( array(
					'post_type'      => 'orbis_project',
					'posts_per_page' => 5,
					'no_found_rows'  => true,
				) );

				while ( $query->have_posts() ) : $query->the_post(); ?>

					<tr>
						<td>
							<?php the_time( 'D j M' ); ?>
						</td>
						<td>
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a><br />
						</td>
					</tr>

				<?php endwhile; ?>

			</tbody>
		</table>

	<?php else : ?>

		<p>
			<em><?php esc_html_e( 'No new posts in the last 3 days.', 'orbis' ); ?></em>
		</p>

	<?php endif; ?>

</div>

<div>
	<h2><?php esc_html_e( 'Recent Deals', 'orbis' ); ?></h2>

	<?php

	$query = new WP_Query( wp_parse_args( array( 'post_type' => 'orbis_deal' ), $defaults ) );

	if ( $query->have_posts() ) : ?>

		<table style="<?php echo esc_attr( $table_style ); ?>" cellpadding="<?php echo esc_attr( $table_padding ); ?>">
			<thead>
				<tr>
					<th scope="col">
						<?php esc_html_e( 'Date', 'orbis' ); ?>
					</th>
					<th scope="col">
						<?php esc_html_e( 'Deal', 'orbis' ); ?>
					</th>
					<th scope="col">
						<?php esc_html_e( 'Price', 'orbis' ); ?>
					</th>
				</tr>
			</thead>

			<tbody>

				<?php while ( $query->have_posts() ) : $query->the_post(); ?>

					<tr>
						<td>
							<?php the_time( 'D j M' ); ?>
						</td>
						<td>
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a><br />
						</td>
						<td>
							<?php

							$price = get_post_meta( get_the_ID(), '_orbis_deal_price', true );

							echo orbis_price( $price );

							?>
						</td>
					</tr>

				<?php endwhile; ?>

			</tbody>
		</table>

	<?php else : ?>

		<p>
			<em><?php esc_html_e( 'No new posts in the last 3 days.', 'orbis' ); ?></em>
		</p>

	<?php endif; ?>

</div>

<div>
	<h2><?php esc_html_e( 'Recent Subscriptions', 'orbis' ); ?></h2>

	<?php

	$query = new WP_Query( wp_parse_args( array( 'post_type' => 'orbis_subscription' ), $defaults ) );

	if ( $query->have_posts() ) : ?>

		<table style="<?php echo esc_attr( $table_style ); ?>" cellpadding="<?php echo esc_attr( $table_padding ); ?>">
			<thead>
				<tr>
					<th scope="col">
						<?php esc_html_e( 'Date', 'orbis' ); ?>
					</th>
					<th scope="col">
						<?php esc_html_e( 'Name', 'orbis' ); ?>
					</th>
				</tr>
			</thead>

			<tbody>

				<?php

				$query = new WP_Query( array(
					'post_type'      => 'orbis_subscription',
					'posts_per_page' => 5,
					'no_found_rows'  => true,
				) );

				while ( $query->have_posts() ) : $query->the_post(); ?>

					<tr>
						<td>
							<?php the_time( 'D j M' ); ?>
						</td>
						<td>
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a><br />
						</td>
					</tr>

				<?php endwhile; ?>

			</tbody>
		</table>

	<?php else : ?>

		<p>
			<em><?php esc_html_e( 'No new posts in the last 3 days.', 'orbis' ); ?></em>
		</p>

	<?php endif; ?>

</div>

<div>
	<h2><?php esc_html_e( 'Recent Tasks', 'orbis' ); ?></h2>

	<?php

	$query = new WP_Query( wp_parse_args( array( 'post_type' => 'orbis_task' ), $defaults ) );

	if ( $query->have_posts() ) : ?>

		<table style="<?php echo esc_attr( $table_style ); ?>" cellpadding="<?php echo esc_attr( $table_padding ); ?>">
			<thead>
				<tr>
					<th scope="col">
						<?php esc_html_e( 'Task', 'orbis' ); ?>
					</th>
				</tr>
			</thead>

			<tbody>

				<?php

				$query = new WP_Query( array(
					'post_type'      => 'orbis_task',
					'posts_per_page' => 5,
					'no_found_rows'  => true,
				) );

				while ( $query->have_posts() ) : $query->the_post(); ?>

					<tr>
						<td>
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a><br />
						</td>
					</tr>

				<?php endwhile; ?>

			</tbody>
		</table>

	<?php else : ?>

		<p>
			<em><?php esc_html_e( 'No new posts in the last 3 days.', 'orbis' ); ?></em>
		</p>

	<?php endif; ?>

</div>

<?php do_action( 'orbis_email_bottom' ); ?>

<?php do_action( 'orbis_email_footer' ); ?>
