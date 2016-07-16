<?php
	require("vendor/autoload.php"); // Loads anything that's been added via composer
	$dotenv = new Dotenv\Dotenv(__DIR__); // for .env
	$dotenv->load();
	$dotenv->required(["GOOGLE_API_KEY"]);// required, since we'll be accessing the maps API
	if ( getenv( GOOGLE_API_SECRET ) ){ // We should only have this function ready if we have an API Secret
		require("utilities/mapSign.php");// A handy function for signing G-maps requests, adds security
	}
?>
