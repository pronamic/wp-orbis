<?php

/* Connect to an ODBC database using driver invocation */
$dsn = sprintf( 'mysql:dbname=%s;host=%s', DB_NAME, DB_HOST );
$user = DB_USER;
$password = DB_PASSWORD;

$pdo = new PDO($dsn, $user, $password);
$pdo->exec('SET CHARACTER SET utf8');

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
		NOT project.finished
			%s
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

// Filter
$filter = "AND project.name NOT LIKE '%Strippenkaart%'";
if(isset($_GET['filter'])) {
	switch($_GET['filter']) {
		case 'false':
			$filter = '';
            break;
	}
}

// Build query
$sql = sprintf($sql, $filter, $orderBy);

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
	$project->availableSeconds = $project->availableSeconds;
	$project->registeredSeconds = $project->registeredSeconds;

	$manager = $managers[$project->managerId];
	$manager->projects[] = $project;
}

ksort($managers);

$parameters = $_GET;

?>
<h1>Projecten</h1>

<p>
	Sorteer op: <a href="?order=name">Naam</a> | <a href="?order=id">Nummer</a><br />
	Filter: <a href="?filter=strippenkaart">Strippenkaart</a> | <a href="?filter=false">Geen</a><br />
</p>

<?php 

include 'projects-table-view.php';