<?php
	
global $wpdb;

if(!empty($_POST) && check_admin_referer('orbis_update_subscription', 'orbis_nonce')) {
	$id = filter_input(INPUT_POST, 'update', FILTER_SANITIZE_NUMBER_INT);

	$result = $wpdb->query($wpdb->prepare('UPDATE orbis_subscriptions SET update_date = NOW() WHERE id = %d', $id));
	
	if($result !== false) {
		
	}
}

?>
<div class="wrap">
	<?php screen_icon(Orbis::SLUG); ?>

	<h2>
		<?php _e('Subscriptions', Orbis::TEXT_DOMAIN); ?>
	</h2>
	
	<?php 
	
	// Subscriptions
	$query = '
		SELECT 
			subscription.id , 
			company.name AS companyName ,
			type.name AS typeName , 
			type.price AS price , 
			domain_name.domain_name AS domainName ,  
			subscription.name AS subscriptionName , 
			activation_date AS activationDate , 
			expiration_date AS expirationDate ,
			update_date AS updateDate 
		FROM 
			orbis_subscriptions AS subscription
				LEFT JOIN
			orbis_companies AS company
					ON subscription.company_id = company.id
				LEFT JOIN
			orbis_subscription_types AS type
					ON subscription.type_id = type.id
				LEFT JOIN
			orbis_domain_names AS domain_name
					ON subscription.domain_name_id = domain_name.id
		ORDER BY
			subscription.update_date , 
			subscription.id 
	';
	
	$subscriptions = $wpdb->get_results($query);
	
	?>
	<form method="post" action="">
		<?php wp_nonce_field('orbis_update_subscription', 'orbis_nonce'); ?>

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
					<th scope="col" class="manage-column"><?php _e('Update Date', Orbis::TEXT_DOMAIN) ?></th>
					<th scope="col" class="manage-column"><?php _e('Price', Orbis::TEXT_DOMAIN) ?></th>
					<th scope="col" class="manage-column"><?php _e('Actions', Orbis::TEXT_DOMAIN) ?></th>
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
						<?php 
						
						if($subscription->updateDate) {
							$date = new DateTime($subscription->updateDate);
							echo $date->format('d-m-Y @ H:i');
						} else {
							
						} 
						
						?>
					</td>
					<td>
						&euro;&nbsp;<?php echo number_format($subscription->price, 2, ',', '.'); ?>
					</td>
					<td>
						<button class="button-secondary" type="submit" name="update" value="<?php echo $subscription->id; ?>"><?php _e('Update', 'orbis'); ?></button>
					</td>
				</tr>
	
				<?php endforeach; ?>
	
			</tbody>
		</table>
	</form>
</div>