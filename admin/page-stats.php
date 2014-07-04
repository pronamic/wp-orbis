<div class="wrap">
	<h2><?php echo get_admin_page_title(); ?></h2>

	<?php

	global $wpdb;

	?>

	<h3><?php _e( 'Timesheets', 'orbis' ); ?></h3>

	<?php

	// Registrations
	$query = '
		SELECT
			COUNT(id) AS numberRegistrations ,
			SUM(number_seconds) AS totalSeconds
		FROM
			orbis_hours_registration ;
	';

	$result = $wpdb->get_row($query);
	$numberRegistrations = $result->numberRegistrations;
	$totalSeconds = $result->totalSeconds;

	?>

	<ul>
		<li>
			<?php echo number_format($numberRegistrations, 0, ',', '.'); ?> registraties
		</li>
		<li>
			<?php echo number_format($totalSeconds, 0, ',', '.'); ?> seconden
		</li>
		<li>
			<?php echo number_format($totalSeconds / 60, 0, ',', '.'); ?> minuten
		</li>
		<li>
			<?php echo number_format($totalSeconds / 3600, 0, ',', '.'); ?> uren
		</li>
		<li>
			<?php echo number_format($totalSeconds / (3600 * 24), 0, ',', '.'); ?> dagen
		</li>
		<li>
			<?php echo number_format($totalSeconds / (3600 * 24 * 7), 0, ',', '.'); ?> weken
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

	$numberTasks = $wpdb->get_var($query);

	?>

	<ul>
		<li>
			<?php echo number_format($numberTasks, 0, ',', '.'); ?> taken
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

	$numberCompanies = $wpdb->get_var($query);

	?>

	<ul>
		<li>
			<?php echo number_format($numberCompanies, 0, ',', '.'); ?> bedrijven
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

	$numberProjects = $wpdb->get_var($query);

	?>

	<ul>
		<li>
			<?php echo number_format($numberProjects, 0, ',', '.'); ?> projecten
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

	$numberDomains = $wpdb->get_var($query);

	?>
	<dl>
		<dt><?php _e('Number domains', 'orbis'); ?></dt>
		<dd><?php echo $numberDomains; ?></dd>
	</dl>

	<h3><?php _e( 'Subscriptions', 'orbis' ); ?></h3>

	<?php

	$query = '
		SELECT
			COUNT(id)
		FROM
			orbis_subscriptions  AS subscription
		;
	';

	$numberSubscriptions = $wpdb->get_var($query);

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

	$total = $wpdb->get_var($query);

	?>
	<dl>
		<dt><?php _e('Number subscriptions', 'orbis'); ?></dt>
		<dd><?php echo $numberSubscriptions; ?></dd>

		<dt><?php _e('Annual Revenue', 'orbis'); ?></dt>
		<dd>&euro;&nbsp;<?php echo number_format($total, 2, ',', '.'); ?></dd>
	</dl>
</div>
