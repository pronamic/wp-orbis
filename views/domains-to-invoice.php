<div class="wrap">
	<?php screen_icon( 'orbis' ); ?>

	<h2>
		<?php _e('Domains to invoice', 'orbis'); ?>
	</h2>
	
	<?php 
	
	global $wpdb;
	
	// Registrations
	$query = '
		SELECT 
			id , 
			domain_name AS name , 
			order_date AS orderDate , 
			notes 
		FROM 
			orbis_domain_names 
		WHERE 
			MONTH(order_date) BETWEEN (MONTH(NOW()) - 1)  AND (MONTH(NOW()) + 2) 
				AND 
			cancel_date IS NULL
		ORDER BY
			DAYOFYEAR(order_date)
	';
	
	$domains = $wpdb->get_results($query);
	
	?>
	<table cellspacing="0" class="widefat fixed">

		<?php foreach(array('thead', 'tfoot') as $tag): ?>

		<<?php echo $tag; ?>>
			<tr>
				<th scope="col" class="manage-column"><?php _e('ID', 'orbis') ?></th>
				<th scope="col" class="manage-column"><?php _e('Domain Name', 'orbis') ?></th>
				<th scope="col" class="manage-column"><?php _e('Order Date', 'orbis') ?></th>
				<th scope="col" class="manage-column"><?php _e('Notes', 'orbis') ?></th>
			</tr>
		</<?php echo $tag; ?>>

		<?php endforeach; ?>

		<tbody>

			<?php foreach($domains as $domain): ?>

			<tr>
				<td>
					<?php echo $domain->id; ?>
				</td>
				<td>
					<?php echo $domain->name; ?>
				</td>
				<td>
					<?php echo $domain->orderDate; ?>
				</td>
				<td>
					<?php echo nl2br($domain->notes); ?>
				</td>
			</tr>

			<?php endforeach; ?>

		</tbody>
	</table>
</div>