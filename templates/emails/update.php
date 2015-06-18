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

$sections = array(
	'post'               => __( 'Recent Posts', 'orbis' ),
	'orbis_company'      => __( 'Recent Companies', 'orbis' ),
	'orbis_person'       => __( 'Recent Persons', 'orbis' ),
	'orbis_project'      => __( 'Recent Projects', 'orbis' ),
	'orbis_deal'         => __( 'Recent Deals', 'orbis' ),
	'orbis_subscription' => __( 'Recent Subscriptions', 'orbis' ),
	'orbis_keychain'     => __( 'Recent Keychains', 'orbis' ),
	'orbis_task'         => __( 'Recent Tasks', 'orbis' ),
);

?>

<?php do_action( 'orbis_email_header' ); ?>

<?php do_action( 'orbis_email_top' ); ?>

<?php foreach ( $sections as $post_type => $label ) : ?>

	<div>
		<h2><?php echo esc_html( $label ); ?></h2>

		<?php

		$query = new WP_Query( wp_parse_args( array( 'post_type' => $post_type ), $defaults ) );

		if ( $query->have_posts() ) : ?>

			<table style="<?php echo esc_attr( $table_style ); ?>" cellpadding="<?php echo esc_attr( $table_padding ); ?>">
				<thead>
					<tr>
						<th scope="col">
							<?php esc_html_e( 'Date', 'orbis' ); ?>
						</th>
						<th scope="col">
							<?php esc_html_e( 'Author', 'orbis' ); ?>
						</th>
						<th scope="col">
							<?php esc_html_e( 'Name', 'orbis' ); ?>
						</th>

						<?php if ( 'orbis_deal' === $post_type ) : ?>

							<th scope="col">
								<?php esc_html_e( 'Price', 'orbis' ); ?>
							</th>

						<?php endif; ?>
					</tr>
				</thead>

				<tbody>

					<?php while ( $query->have_posts() ) : $query->the_post(); ?>

						<tr>
							<td>
								<?php the_time( 'D j M' ); ?>
							</td>
							<td>
								<?php the_author(); ?> 
							</td>
							<td>
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a><br />
							</td>

							<?php if ( 'orbis_deal' === $post_type ) : ?>

								<td>
									<?php

									$price = get_post_meta( get_the_ID(), '_orbis_deal_price', true );

									echo orbis_price( $price );

									?>
								</td>

							<?php endif; ?>
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

<?php endforeach; ?>

<?php do_action( 'orbis_email_bottom' ); ?>

<?php do_action( 'orbis_email_footer' ); ?>
