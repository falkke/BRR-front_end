<?php
	/* INITIALISATION */
	
	session_start();

	// Database information :
	$dbhost = "s679.loopia.se";
	$dbname = "sebastianoveland_com_db_1";
	$dbuser = "group5@s243341";
	$dbpassword = "BlackRiver2019";

	// Try to connect to the database.
	try {
		$db = new PDO(
			"mysql:host=".$dbhost.";dbname=".$dbname.";charset=utf8mb4",
			$dbuser, 
			$dbpassword
		);
	}
	
	catch(PDOexception $e) {
		die("Error while trying to connect to the database...");
	}
?>