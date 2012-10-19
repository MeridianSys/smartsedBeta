<?php
/**
  * This script establishes connection to the database.
  *
  * @author Nikesh kumar
  * @version 12/07/2011
  */


	//Define constants for security reasons
	DEFINE('DB_USER', 'root');
	DEFINE('DB_PASSWORD', 'Learn2give!');
	DEFINE('DB_HOST', 'localhost');
	DEFINE('DB_NAME', 'smartsedcharlee');

	//Establish connection to database
	$db_connection = @mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) OR die('Could not connect to MySQL: '.mysql_error());
	//echo "db user";
	//Select database
	@mysql_select_db(DB_NAME) OR die('Could not select the database: '.mysql_error());

?>
