<?php

/**
 * database configuration
 */
$db = 'mysql';
$host = 'localhost';
$dbname = 'experiment_db';
$user = 'root';
$pass = 'password';

/**
 * don't change these
 */

return $config = [
	'db' => [
		'host' => $host,
		'dbname' => $dbname,
		'user' => $user,
		'pass' => $pass,
		'connection_string' => "$db:host=$host;dbname=$dbname"
	]
];