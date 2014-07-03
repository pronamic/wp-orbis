<div class="wrap">

	<h2>
		<?php _e( 'Stats', 'orbis' ); ?>
	</h2>
	
	<?php 
	
	global $wpdb;
	
	?>
	
	<h2>Work records</h2>
	
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
			<?php echo number_format($numberRegistrations, 0, ',', '.'); ?> registrations
		</li>
		<li>
			<?php echo number_format($totalSeconds, 0, ',', '.'); ?> seconds
		</li>
		<li>
			<?php echo number_format($totalSeconds / 60, 0, ',', '.'); ?> minutes
		</li>
		<li>
			<?php echo number_format($totalSeconds / 3600, 0, ',', '.'); ?> hours
		</li>
		<li>
			<?php echo number_format($totalSeconds / (3600 * 24), 0, ',', '.'); ?> days
		</li>
		<li>
			<?php echo number_format($totalSeconds / (3600 * 24 * 7), 0, ',', '.'); ?> weeks
		</li>
	</ul>
	
	<h2>Tasks</h2>
	
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
			<?php echo number_format($numberTasks, 0, ',', '.'); ?> tasks
		</li>
	</ul>
	
	<h2>Companies</h2>
	
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
			<?php echo number_format($numberCompanies, 0, ',', '.'); ?> companies
		</li>
	</ul>
	
	<h2>Projects</h2>
	
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
			<?php echo number_format($numberProjects, 0, ',', '.'); ?> projects
		</li>
	</ul>
	
	<h2>Domains</h2>
	
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
	
	<h2>Subscriptions</h2>
	
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