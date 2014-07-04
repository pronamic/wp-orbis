<div class="wrap">
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

    <div class="orbis-plugins-message below-h2"></div>

	<table class="orbis-plugins">

		<?php foreach ( Orbis_Plugin_Manager::$recommended_plugins as $plugin_slug => $plugin_details ) : ?>

			<tr>
				<th>
					<?php echo esc_html( $plugin_details['title'] ); ?>
				</th>
				<td>
					<a href="<?php echo esc_attr( self_admin_url( 'plugin-install.php?tab=plugin-information&amp;plugin=' . $plugin_slug . '&amp;TB_iframe=true&amp;width=600&amp;height=550' ) ); ?>" class="thickbox"><?php _e( 'Details', 'orbis' ); ?></a>
				</td>
				<td>
					<?php

					if ( ! Orbis_Plugin_Manager::is_plugin_installed( $plugin_slug ) ) {
						printf(
							'<input type="button" class="%s" value="%s" data-plugin-slug="%s" data-nonce="%s" />',
							esc_attr( 'orbis-install-plugin button' ),
							esc_attr__( 'Install', 'orbis' ),
							esc_attr( $plugin_slug ),
							esc_attr( wp_create_nonce( 'manage-plugin-' . $plugin_slug ) )
						);
					} elseif ( ! Orbis_Plugin_Manager::is_plugin_active( $plugin_slug ) ) {
						printf(
							'<input type="button" class="%s" value="%s" data-plugin-slug="%s" data-nonce="%s" />',
							esc_attr( 'orbis-activate-plugin button' ),
							esc_attr__( 'Activate', 'orbis' ),
							esc_attr( $plugin_slug ),
							esc_attr( wp_create_nonce( 'manage-plugin-' . $plugin_slug ) )
						);
					} else {
						printf(
							'<input type="button" class="button" disabled="disabled" value="%s" />',
							esc_attr__( 'Active', 'orbis' )
						);
					}

					?>
				</td>
				<td>
					<img src="images/loading.gif" class="loading-icon" width="15" height="15" />
				</td>
			</tr>

		<?php endforeach; ?>

	</table>
</div>
