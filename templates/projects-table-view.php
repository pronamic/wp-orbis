<div class="panel">
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th scope="col">Projectleider</th>
				<th scope="col">ID</th>
				<th scope="col">Opdrachtgever</th>
				<th scope="col">Project</th>
				<th scope="col">Geregistreerde uren</th>
				<th scope="col">Beschikbare uren</th>
				<th scope="col">Factureerbaar</th>
				<th scope="col">Factuurnummer</th>
				<th scope="col">Acties</th>
			</tr>
		</thead>
	
		<tbody>
	
			<?php foreach($managers as $manager): ?>
	
			<tr>
				<th rowspan="<?php echo count($manager->projects) + 1; ?>">
					<?php echo $manager->name; ?>
				</th>
			</tr>
	
			<?php foreach($manager->projects as $project): ?>
	
			<tr>
				<td>
					<a href="http://orbis.pronamic.nl/projecten/details/<?php echo $project->id; ?>/" style="color: #000;">
						<?php echo $project->id; ?>
					</a>
				</td>
				<td>
					<a href="http://orbis.pronamic.nl/bedrijven/details/<?php echo $project->principalId ?>/" style="color: #000;">
						<?php echo $project->principalName; ?>
					</a>
				</td>
				<td>
					<a href="http://orbis.pronamic.nl/projecten/details/<?php echo $project->id; ?>/" style="color: #000;">
						<?php echo $project->name; ?>
					</a>
				</td>
				<td>
					<span style="color: <?php echo $project->failed ? 'Red' : 'Green'; ?>;"><?php echo $project->registeredSeconds; ?></span>
				</td>
				<td>
					<?php echo $project->availableSeconds; ?>
				</td>
				<td>
					<?php echo $project->invoicable ? 'Ja' : 'Nee'; ?>
				</td>
				<td>
					<?php echo $project->invoiceNumber; ?>
				</td>
				<td>
					<a href="http://orbis.pronamic.nl/projecten/wijzigen/<?php echo $project->id; ?>/" style="color: #000;">
						Wijzigen
					</a>
				</td>
			</tr>
	
			<?php endforeach; ?>
	
			<?php endforeach; ?>
	
		</tbody>
	</table>
</div>