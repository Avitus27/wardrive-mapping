<?php
	require("vendor/autoload.php");
	$dotenv = new Dotenv\Dotenv(__DIR__);
	$dotenv->load();
	$dotenv->required(["GOOGLE_API_KEY"]);
	require("utilities/mapSign.php");
?>
