<div class="wrap">

	<h2>
		<?php _e( 'Settings', 'orbis' ); ?>
	</h2>
</div>


<div class="wrap">

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<!-- @TODO: Provide markup for your options page here. -->
	
    <form action="options.php" method="POST">
        <?php settings_fields( 'wp_whitelabel' ); ?>
        <?php do_settings_sections( 'wp_whitelabel' ); ?>
        <?php submit_button(); ?>
    </form>
    
</div>