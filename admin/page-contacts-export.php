<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<form method="get" action="">
		<?php

		wp_nonce_field( 'orbis_contacts_export', 'orbis_contacts_export_nonce' );

		submit_button(
			__( 'Export', 'orbis' ),
			'primary',
			'orbis_contacts_export'
		);

		?>
	</form>
</div>
