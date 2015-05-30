<?php do_action( 'orbis_email_header' ); ?>

<table border="0" cellpadding="10" width="100%">
	<tr>
		<td valign="top" width="33%">

			<table>
				<tr>
					<td>
						<strong><?php _e( 'Recent Companies', 'orbis' ); ?></strong>
					</td>
				</tr>

				<?php 

				$query = new WP_Query( array(
					'post_type'      => 'orbis_company',
					'posts_per_page' => 5,
				) );

				while ( $query->have_posts() ) : $query->the_post(); ?>

					<tr>
						<td>
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a><br />
						</td>
					</tr>

				<?php endwhile; ?>

			</table>

		</td>
		<td valign="top" width="33%">

			<table>
				<tr>
					<td>
						<strong><?php _e( 'Recent Persons', 'orbis' ); ?></strong>
					</td>
				</tr>

				<?php 

				$query = new WP_Query( array(
					'post_type'      => 'orbis_person',
					'posts_per_page' => 5,
				) );

				while ( $query->have_posts() ) : $query->the_post(); ?>

					<tr>
						<td>
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a><br />
						</td>
					</tr>

				<?php endwhile; ?>

			</table>

		</td>
		<td valign="top" width="33%">

			<table>
				<tr>
					<td>
						<strong><?php _e( 'Recent Projects', 'orbis' ); ?></strong>
					</td>
				</tr>

				<?php 

				$query = new WP_Query( array(
					'post_type'      => 'orbis_project',
					'posts_per_page' => 5,
				) );

				while ( $query->have_posts() ) : $query->the_post(); ?>

					<tr>
						<td>
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a><br />
						</td>
					</tr>

				<?php endwhile; ?>

			</table>

		</td>
	</tr>
	<tr>
		<td valign="top" width="33%">

			<table>
				<tr>
					<td colspan="2">
						<strong><?php _e( 'Recent Deals', 'orbis' ); ?></strong>
					</td>
				</tr>

				<?php 

				$query = new WP_Query( array(
					'post_type'      => 'orbis_deal',
					'posts_per_page' => 5,
				) );

				while ( $query->have_posts() ) : $query->the_post(); ?>

					<tr>
						<td>
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a><br />
						</td>
						<td>
							<?php

							$price = get_post_meta( $post->ID, '_orbis_deal_price', true );

							echo orbis_price( $price );

							?>
						</td>
					</tr>

				<?php endwhile; ?>

			</table>

		</td>
		<td valign="top" width="33%">

			<table>
				<tr>
					<td>
						<strong><?php _e( 'Recent Subscriptions', 'orbis' ); ?></strong>
					</td>
				</tr>

				<?php 

				$query = new WP_Query( array(
					'post_type'      => 'orbis_subscription',
					'posts_per_page' => 5,
				) );

				while ( $query->have_posts() ) : $query->the_post(); ?>

					<tr>
						<td>
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a><br />
						</td>
					</tr>

				<?php endwhile; ?>

			</table>

		</td>
		<td valign="top" width="33%">

			<table>
				<tr>
					<td>
						<strong><?php _e( 'Recent Tasks', 'orbis' ); ?></strong>
					</td>
				</tr>

				<?php 

				$query = new WP_Query( array(
					'post_type'      => 'orbis_task',
					'posts_per_page' => 5,
				) );

				while ( $query->have_posts() ) : $query->the_post(); ?>

					<tr>
						<td>
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a><br />
						</td>
					</tr>

				<?php endwhile; ?>

			</table>

		</td>
	</tr>
</table>

<?php do_action( 'orbis_email_footer' ); ?>
