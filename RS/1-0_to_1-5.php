<?php
/**
1-0_to_1-5.php
--------------
This script upgrades the necessary parts of a 1.0 version of the content
management system to version 1.5. A list of what running this script will do
follows.

	- add isTagged column to comments table

Occasionally during automatic upgrade, strange bugs may occur. A list of known
bugs that may need to be fixed manually follows.

	-
**/


//connect to database, set the error mode, and start a try-catch block
require_once("../config/connection.php");
$conCreative->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
try {
	//add sidebar radio option column to pages table
	echo "<div>Adding <em>isTagged</em> column to <em>comments</em> table...<div>";
	$subquery = $conCreative->prepare("ALTER TABLE comments
										ADD isTagged int(1) NOT NULL
										DEFAULT 0
										");
	if($subquery->execute())
		echo "<div style=\"color: green;\">Added <em>isTagged</em> to <em>pages</em> successfully</div>";
	else
		echo "<div style=\"color: red;\">Error adding <em>isTagged</em> to <em>pages</em></div>";
}
catch (PDOException $e) {
	echo "<div style=\"color: red;\">" . $e->getMessage() . "</div>";
}
?>
