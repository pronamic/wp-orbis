<div class="wrap">
	<?php screen_icon(Orbis::SLUG); ?>

	<h2>
		<?php _e('Stats', Orbis::TEXT_DOMAIN); ?>
	</h2>
	
	<?php 
	
	global $wpdb;
	
	?>
	
	<h2>Werk registraties</h2>
	
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
	
	<h2>Taken</h2>
	
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
	
	<h2>Bedrijven</h2>
	
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
	
	<h2>Projecten</h2>
	
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
</div>