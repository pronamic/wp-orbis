<div class="wrap">
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<form action="options.php" method="POST">
		<?php settings_fields( 'orbis' ); ?>

		<?php do_settings_sections( 'orbis' ); ?>

		<?php submit_button(); ?>
	</form>
</div>
