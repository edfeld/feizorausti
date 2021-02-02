<?php
/*
connection.php
--------------------
This code connects to the database for the rest of the site. Edit the details
below to change the connection.
*/

try {
  // Get the connection parameters -- ERE20201012
  	$hostname = "localhost";
	$database = "feizorausti";
	$username = "root";
	// $username = "feizorausti";
	$password = "root";
	// $password = "xqz33SKWq6mExG8X";

	//conect to database and set error mode to work appropriately with try/catch
	//statements
	$conCreative = new PDO('mysql:host='.$hostname . ';dbname=' . $database, $username, $password);
	$conCreative->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	//ensure proper encoding
	$conCreative->query("SET NAMES utf8");
}
catch (PDOException $e) {
    echo 'ERROR: ' . $e->getMessage();
}
?>
