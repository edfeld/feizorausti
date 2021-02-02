<?php
/**
0-0_to_1-0.php
--------------
This script cleans out and resets the comments table. It should be used when
installing a new version of the content management system with a dirty database
image. THIS DOES NOT BACKUP THE OLD COMMENTS ENTRIES BEFORE REMOVING THEM!
*/


//connect to database, set the error mode, and start a try-catch block
require_once("../config/connection.php");
$conCreative->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
try {
	//clean up the the comments table
	echo "<div>Cleaning up the <em>comments</em> table...<div>";
	$subquery = $conCreative->prepare("DELETE FROM comments;");
	if($subquery->execute())
		echo "<div style=\"color: green;\">Cleaned up the <em>comments</em> table successfully</div>";
	else
		echo "<div style=\"color: red;\">Error cleaning up the <em>comments</em> table</div>";
	
	//reset the auto-increment of the stats table
	echo "<div>Resetting the auto-increment on the <em>comments</em> table...<div>";
	$subquery = $conCreative->prepare("ALTER TABLE comments AUTO_INCREMENT = 1");
	if($subquery->execute())
		echo "<div style=\"color: green;\">The auto-increment on the <em>comments</em> table is successfully reset</div>";
	else
		echo "<div style=\"color: red;\">Error resetting the auto-increment on the <em>comments</em> table</div>";
}
catch (PDOException $e) {
	echo "<div style=\"color: red;\">" . $e->getMessage() . "</div>";
}
?>