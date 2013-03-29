<?php

// Managers
$sql = '
	SELECT
		project.id , 
		project.name , 
		project.number_seconds AS availableSeconds , 
		project.invoice_number AS invoiceNumber , 
		project.invoicable , 
		principal.id AS principalId , 
		principal.name AS principalName , 
		manager.id AS managerId ,  
		manager.first_name AS managerName , 
		SUM(registration.number_seconds) AS registeredSeconds 
	FROM
		orbis_projects AS project
			LEFT JOIN
		orbis_companies AS principal
				ON project.principal_id = principal.id
			LEFT JOIN
		orbis_persons AS manager
				ON project.contact_id_1 = manager.id
			LEFT JOIN
		orbis_hours_registration AS registration
				ON project.id = registration.project_id 
	WHERE
		project.finished
			AND
		project.invoicable
			AND
		NOT project.invoiced
	GROUP BY
		project.id 
	ORDER BY
		%s ;
';

// Order by
$orderBy = 'principal.name , project.name';
if(isset($_GET['order'])) {
	switch($_GET['order']) {
		case 'id':
			$orderBy = 'project.id DESC';
			break;
	}
}

// Build query
$sql = sprintf($sql, $orderBy);

$statement = $pdo->prepare($sql);
$statement->execute();

// Projects
$projects = $statement->fetchAll(PDO::FETCH_CLASS);

// Managers
$managers = array();

// Projects and managers
foreach($projects as $project) {
	// Find manager
	if(!isset($managers[$project->managerId])) {
		$manager = new stdClass();
		$manager->id = $project->managerId;
		$manager->name = $project->managerName;
		$manager->projects = array();

		$managers[$manager->id] = $manager;
	}

	$project->failed = $project->registeredSeconds > $project->availableSeconds;
	$project->availableSeconds = new Duration($project->availableSeconds);
	$project->registeredSeconds = new Duration($project->registeredSeconds);

	$manager = $managers[$project->managerId];
	$manager->projects[] = $project;
}

ksort($managers);

$parameters = $_GET;

?>
<h1>Afgeronde projecten die niet zijn gefactureerd</h1>

<p>
	Sorteer op: <a href="?order=name">Naam</a> | <a href="?order=id">Nummer</a>
</p>

<?php 

include '../projects-table-view.php';
