<div class="wrap">
	<?php screen_icon( 'orbis' ); ?>

	<h2>
		<?php _e( 'Plugins', 'orbis' ); ?>
	</h2>

    <div class="orbis-plugins-message below-h2"></div>

	<table class="orbis-plugins">
		
		<?php foreach ( Orbis_Plugin_Manager::$recommended_plugins as $plugin_slug => $plugin_details ) : ?>
		
		<tr>
		
			<th><?php echo $plugin_details[ 'title' ]; ?></th>

            <td><a href="<?php echo self_admin_url( 'plugin-install.php?tab=plugin-information&amp;plugin=' . $plugin_slug . '&amp;TB_iframe=true&amp;width=600&amp;height=550' ); ?>" class="thickbox"><?php _e( 'Details', 'orbis' ); ?></a></td>

            <?php if ( ! Orbis_Plugin_Manager::is_plugin_installed( $plugin_slug ) ) : ?>

            <td><input type="button" class="orbis-install-plugin button" value="<?php _e( 'Install', 'orbis' ); ?>" data-plugin-slug="<?php echo $plugin_slug; ?>" data-nonce="<?php echo wp_create_nonce( 'manage-plugin-' . $plugin_slug ); ?>" /></td>

            <?php elseif ( ! Orbis_Plugin_Manager::is_plugin_active( $plugin_slug ) ) : ?>

            <td><input type="button" class="orbis-activate-plugin button" value="<?php _e( 'Activate', 'orbis' ); ?>" data-plugin-slug="<?php echo $plugin_slug; ?>" data-nonce="<?php echo wp_create_nonce( 'manage-plugin-' . $plugin_slug ); ?>" /></td>

		    <?php else : ?>

            <td><input type="button" class="button" disabled="disabled" value="<?php _e( 'Active', 'orbis' ); ?>" /></td>

            <?php endif; ?>

            <td><img src="images/loading.gif" class="loading-icon" width="15" height="15" /></td>
		</tr>
		
		<?php endforeach; ?>
		
	</table>
</div>