<?php 

// Define our current environment
define('ENV', 'dev');

$YT_KEY = '';

// DEFAULT global variables
$DEFAULT_VID_LIMIT = 30;

if(ENV === 'dev'){
	// SETUP our DB connection
	$host = 'localhost';
	$db   = 'mytube';
	$user = 'root';
	$pass = '';
	$charset = 'utf8mb4';
}else{
	// Production connection info
	$host = 'localhost';
	$db   = 'mytube';
	$user = 'root';
	$pass = '';
	$charset = 'utf8mb4';
}
