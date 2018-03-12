<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<div id="dashboard-widgets-wrap">
		<div id="dashboard-widgets" class="metabox-holder columns-2">
			<div id="postbox-container-1" class="postbox-container">
				<div id="normal-sortables" class="meta-box-sortables ui-sortable">
					<div class="postbox">
						<h2 class="hndle"><span><?php _e( 'Recent Projects', 'orbis' ); ?></span></h2>

						<div class="inside">
							<?php

							$query = new WP_Query( array(
								'post_type'      => 'orbis_project',
								'posts_per_page' => 5,
							) );

							if ( $query->have_posts() ) :
							?>

								<div id="dashboard_recent_drafts">
									<ul>

										<?php

										while ( $query->have_posts() ) :
											$query->the_post();

										?>

											<li>
												<h4>
													<?php

													printf(
														'<a href="%s">%s</a>',
														get_edit_post_link(),
														get_the_title()
													);

													?>
													<?php

													printf( '<abbr title="%s">%s</abbr>',
														/* translators: comment date format. See http://php.net/date */
														get_the_time( __( 'c', 'orbis' ) ),
														get_the_time( get_option( 'date_format' ) )
													);

													?>
												</h4>
											</li>

										<?php endwhile; ?>

									</ul>
								</div>

							<?php else : ?>

								<p>
									<?php esc_html_e( 'No recent projects found.', 'orbis' ); ?>
								</p>

							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>

			<div id="postbox-container-2" class="postbox-container">
				<div id="side-sortables" class="meta-box-sortables ui-sortable">
					<div class="postbox">
						<h2 class="hndle"><span><?php _e( 'Pronamic News', 'orbis' ); ?></span></h2>

						<div class="inside">
							<?php

							wp_widget_rss_output( 'http://feeds.feedburner.com/pronamic', array(
								'link'  => __( 'http://www.pronamic.eu/', 'orbis' ),
								'url'   => 'http://feeds.feedburner.com/pronamic',
								'title' => __( 'Pronamic News', 'orbis' ),
								'items' => 5,
							) );

							?>
						</div>
					</div>
				</div>
			</div>

			<div class="clear"></div>
		</div>
	</div>
</div>
