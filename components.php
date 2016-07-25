<?php
	require_once("vendor/autoload.php"); // Loads anything that's been added via composer
	$dotenv = new Dotenv\Dotenv(__DIR__); // for .env
	$dotenv->load();
	$dotenv->required(["GOOGLE_API_KEY", "DB_HOST","DB_USERNAME", "DB_PASSWORD", "DB_DATABASE"]);// required, since we'll be accessing the maps API
	if ( getenv( 'GOOGLE_API_SECRET' ) ){ // We should only have this function ready if we have an API Secret
		require_once("utilities/mapSign.php");// A handy function for signing G-maps requests, adds security
	}
	require_once("utilities/generateCoords.php");

	$mysqli = mysqli_init();
	if ( !mysqli_real_connect($mysqli, $_ENV['DB_HOST'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_DATABASE']) ) {
		echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}

	$mappings = array('UPC%07d' => 'UPCxxxxxxx ', 'eircom WPS' => 'eircomxxxx xxxx');
?>
