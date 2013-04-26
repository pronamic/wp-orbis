<?php

class Orbis_Database {
	private $wpdb;

	public $projects;
	public $companies;

	public function __construct() {
		global $wpdb;

		$this->wpdb = $wpdb;
		$this->projects = 'orbis_projects2';
		$this->companies = 'orbis_companies2';
	}
}
