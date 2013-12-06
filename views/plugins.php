<div class="wrap">
	<?php screen_icon( 'orbis' ); ?>

	<h2>
		<?php _e( 'Plugins', 'orbis' ); ?>
	</h2>
	
	<?php 
	
	$plugins = array(
		'members'        => 'Members',
		'posts-to-posts' => 'Posts 2 Posts'
	);
	
	?>
	
	<table class="orbis-plugins">
		
		<?php foreach ( $plugins as $plugin_slug => $plugin_name ) : ?>
		
		<tr>
		
			<th><?php echo $plugin_name; ?></th>
			
			<td><a href="#" class="orbis-install-plugin" data-plugin-slug="<?php echo $plugin_slug; ?>" data-nonce="<?php echo wp_create_nonce( 'install-plugin-' . $plugin_slug ); ?>"><?php echo $plugin_name; ?></a></td>
		
		</tr>
		
		<?php endforeach; ?>
		
	</table>
</div>