<div class="wrap">
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<?php

	global $wpdb;

	?>

	<h3><?php _e( 'Timesheets', 'orbis' ); ?></h3>

	<?php

	// Registrations
	$query = '
		SELECT
			COUNT(id) AS number_registrations,
			SUM(number_seconds) AS total_seconds
		FROM
			orbis_hours_registration ;
	';

	$result = $wpdb->get_row( $query );

	$number_registrations = $result->number_registrations;

	$total_seconds = $result->total_seconds;

	?>

	<ul>
		<li>
			<?php printf( __( '%d registrations', 'orbis' ), number_format_i18n( $number_registrations ) ); ?>
		</li>
		<li>
			<?php printf( __( '%d seconds', 'orbis' ), number_format_i18n( $total_seconds ) ); ?>
		</li>
		<li>
			<?php printf( __( '%d minutes', 'orbis' ), number_format_i18n( $total_seconds / MINUTE_IN_SECONDS ) ); ?>
		</li>
		<li>
			<?php printf( __( '%d hours', 'orbis' ), number_format_i18n( $total_seconds / HOUR_IN_SECONDS ) ); ?>
		</li>
		<li>
			<?php printf( __( '%d days', 'orbis' ), number_format_i18n( $total_seconds / DAY_IN_SECONDS ) ); ?>
		</li>
		<li>
			<?php printf( __( '%d weeks', 'orbis' ), number_format_i18n( $total_seconds / WEEK_IN_SECONDS ) ); ?>
		</li>
	</ul>

	<h3><?php _e( 'Tasks', 'orbis' ); ?></h3>

	<?php

	$query = '
		SELECT
			COUNT(id)
		FROM
			orbis_tasks ;
	';

	$number_tasks = $wpdb->get_var( $query );

	?>

	<ul>
		<li>
			<?php printf( __( '%d tasks', 'orbis' ), number_format_i18n( $number_tasks ) ); ?>
		</li>
	</ul>

	<h3><?php _e( 'Companies', 'orbis' ); ?></h3>

	<?php

	$query = '
		SELECT
			COUNT(id)
		FROM
			orbis_companies ;
	';

	$number_companies = $wpdb->get_var( $query );

	?>

	<ul>
		<li>
			<?php printf( __( '%d companies', 'orbis' ), number_format_i18n( $number_companies ) ); ?>
		</li>
	</ul>

	<h3><?php _e( 'Projects', 'orbis' ); ?></h3>

	<?php

	$query = '
		SELECT
			COUNT(id)
		FROM
			orbis_projects ;
	';

	$number_projects = $wpdb->get_var( $query );

	?>

	<ul>
		<li>
			<?php printf( __( '%d projects', 'orbis' ), number_format_i18n( $number_projects ) ); ?>
		</li>
	</ul>

	<h3><?php _e( 'Domains', 'orbis' ); ?></h3>

	<?php

	$query = '
		SELECT
			COUNT(id)
		FROM
			orbis_domain_names AS domain
		WHERE
			cancel_date IS NULL
		;
	';

	$number_domains = $wpdb->get_var( $query );

	?>
	<dl>
		<dt><?php _e( 'Number domains', 'orbis' ); ?></dt>
		<dd><?php echo esc_html( $number_domains ); ?></dd>
	</dl>

	<h3><?php _e( 'Subscriptions', 'orbis' ); ?></h3>

	<?php

	$query = '
		SELECT
			COUNT(id)
		FROM
			orbis_subscriptions AS subscription
		;
	';

	$number_subscriptions = $wpdb->get_var( $query );

	$query = '
		SELECT
			SUM(price)
		FROM
			orbis_subscriptions AS subscription
				LEFT JOIN
			orbis_subscription_types AS type
					ON subscription.type_id = type.id
		;
	';

	$total = $wpdb->get_var( $query );

	?>
	<dl>
		<dt><?php _e( 'Number subscriptions', 'orbis' ); ?></dt>
		<dd><?php echo esc_html( $number_subscriptions ); ?></dd>

		<dt><?php _e( 'Annual Revenue', 'orbis' ); ?></dt>
		<dd><?php echo esc_html( orbis_price( $total ) ); ?></dd>
	</dl>
</div>
