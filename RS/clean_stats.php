<?php
/**
0-0_to_1-0.php
--------------
This script cleans out the stats table so that it will start tracking visitors
as if no one has visited the site yet. Only use this if you are making a clean
install with a dirty database dump or if you are sure what you are doing. THIS
DOES NOT BACKUP THE OLD STATS ENTRIES BEFORE REMOVING THEM!
*/


//connect to database, set the error mode, and start a try-catch block
require_once("../config/connection.php");
$conCreative->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
try {
	//clean up the the stats table
	echo "<div>Cleaning up the <em>stats</em> table...<div>";
	$subquery = $conCreative->prepare("DELETE FROM stats;");
	if($subquery->execute())
		echo "<div style=\"color: green;\">Cleaned up the <em>stats</em> table successfully</div>";
	else
		echo "<div style=\"color: red;\">Error cleaning up the <em>stats</em> table</div>";
	
	//reset the auto-increment of the stats table
	echo "<div>Resetting the auto-increment on the <em>stats</em> table...<div>";
	$subquery = $conCreative->prepare("ALTER TABLE stats AUTO_INCREMENT = 1");
	if($subquery->execute())
		echo "<div style=\"color: green;\">The auto-increment on the <em>stats</em> table is successfully reset</div>";
	else
		echo "<div style=\"color: red;\">Error resetting the auto-increment on the <em>stats</em> table</div>";
}
catch (PDOException $e) {
	echo "<div style=\"color: red;\">" . $e->getMessage() . "</div>";
}
?>