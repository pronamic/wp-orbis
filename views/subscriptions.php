<div class="wrap">
	<?php screen_icon(Orbis::SLUG); ?>

	<h2>
		<?php _e('Subscriptions', Orbis::TEXT_DOMAIN); ?>
	</h2>
	
	<?php 
	
	global $wpdb;
	
	// Subscriptions
	$query = '
		SELECT 
			subscription.id , 
			company.name AS companyName ,
			type.name AS typeName , 
			type.price AS price , 
			subscription.name AS subscriptionName , 
			activation_date AS activationDate , 
			expiration_date AS expirationDate 
		FROM 
			orbis_subscriptions AS subscription
				LEFT JOIN
			orbis_companies AS company
					ON subscription.company_id = company.id
				LEFT JOIN
			orbis_subscription_types AS type
					ON subscription.type_id = type.id
		ORDER BY
			subscription.expiration_date , 
			subscription.id 
	';
	
	$subscriptions = $wpdb->get_results($query);
	
	?>
	<table cellspacing="0" class="widefat fixed">

		<?php foreach(array('thead', 'tfoot') as $tag): ?>

		<<?php echo $tag; ?>>
			<tr>
				<th scope="col" class="manage-column" style="width: 3em"><?php _e('ID', Orbis::TEXT_DOMAIN) ?></th>
				<th scope="col" class="manage-column"><?php _e('Company', Orbis::TEXT_DOMAIN) ?></th>
				<th scope="col" class="manage-column"><?php _e('Type', Orbis::TEXT_DOMAIN) ?></th>
				<th scope="col" class="manage-column"><?php _e('Name', Orbis::TEXT_DOMAIN) ?></th>
				<th scope="col" class="manage-column"><?php _e('Activation Date', Orbis::TEXT_DOMAIN) ?></th>
				<th scope="col" class="manage-column"><?php _e('Expiration Date', Orbis::TEXT_DOMAIN) ?></th>
				<th scope="col" class="manage-column"><?php _e('Price', Orbis::TEXT_DOMAIN) ?></th>
			</tr>
		</<?php echo $tag; ?>>

		<?php endforeach; ?>

		<tbody>

			<?php foreach($subscriptions as $subscription): ?>

			<tr>
				<th scope="row"> 
					<?php echo $subscription->id; ?>
				</th>
				<td>
					<?php echo $subscription->companyName; ?>
				</td>
				<td>
					<?php echo $subscription->typeName; ?>
				</td>
				<td>
					<?php echo $subscription->subscriptionName; ?>
				</td>
				<td>
					<?php 

					$date = new DateTime($subscription->activationDate);
					echo $date->format('d-m-Y @ H:i'); 
					
					?>
				</td>
				<td>
					<?php 

					$date = new DateTime($subscription->expirationDate);
					echo $date->format('d-m-Y @ H:i'); 
					
					?>
				</td>
				<td>
					&euro;&nbsp;<?php echo number_format($subscription->price, 2, ',', '.'); ?>
				</td>
			</tr>

			<?php endforeach; ?>

		</tbody>
	</table>
</div>