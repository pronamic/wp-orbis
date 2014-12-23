<div class="wrap">
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<div id="dashboard-widgets-wrap">
		<div id="dashboard-widgets" class="metabox-holder columns-2">
			<div id="postbox-container-1" class="postbox-container">
				<div id="normal-sortables" class="meta-box-sortables ui-sortable">
					<div class="postbox">
						<h3 class="hndle"><span><?php _e( 'Recent Projects', 'orbis' ); ?></span></h3>

						<div class="inside">
							<?php

							$projects = get_posts( array(
								'post_type'      => 'pronamic_project',
								'posts_per_page' => 5,
							) );

							if ( empty( $projects ) ) : ?>

								<p>
									<?php _e( 'No recent projects found.', 'orbis' ); ?>
								</p>

							<?php else : ?>

								<div id="dashboard_recent_drafts">
									<ul>

										<?php foreach ( $projects as $project ) : ?>

											<li>
												<h4>
													<?php

													printf(
														'<a href="%s">%s</a>',
														get_edit_post_link( $project ),
														get_the_title( $project )
													);

													?>
													<?php

													printf( '<abbr title="%s">%s</abbr>',
														/* translators: comment date format. See http://php.net/date */
														get_the_time( __( 'c', 'orbis' ), $project ),
														get_the_time( get_option( 'date_format' ), $project )
													);

													?>
												</h4>
											</li>

										<?php endforeach; ?>

									</ul>
								</div>

							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>

			<div id="postbox-container-2" class="postbox-container">
				<div id="side-sortables" class="meta-box-sortables ui-sortable">
					<div class="postbox">
						<h3 class="hndle"><span><?php _e( 'Pronamic News', 'orbis' ); ?></span></h3>

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
