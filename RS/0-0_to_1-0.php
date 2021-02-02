<?php
/**
0-0_to_1-0.php
--------------
This script upgrades the necessary parts of a 0.0 version of the content
management system to version 1.0. A list of what running this script will do
follows.

	- consolidate sidebar content on every necessary page by moving content
	  from pageLeftSidebar to the beginning of pageSidebar on every page
	- drop the pageLeftSidebar column from the pages table
	- drop the old footer table and replace it with a new footer table that is
	  used to store quick-links that are displayed in the footer
	- create stats table for the analytics system to use
	- add sidebarRadio column to pages table and set its default value to 1
	- add emailDisp column to company details table and set its default value
	  to 0
	- add hideEmail column to comments table and set its default value
	  to 1
	- add commentReply column to comments table and set its default value to -1
	- add ipAddress column to comments table
	- add locale column to configurations table
	- add siteDir column to configurations table
	- create translations table used for the translation system

Occasionally during automatic upgrade, strange bugs may occur. A list of known
bugs that may need to be fixed manually follows.

	- the navigation order(pageTabPosition in the pages table) can get messed
	  up, causing some pages to have a NULL pageTabePosition and puting others
	  out of order//
**/


//connect to database, set the error mode, and start a try-catch block
require_once("../config/connection.php");
$conCreative->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
try {
	//consolidate sidebar content on every necessary page
	echo "<div>Moving <em>pageLeftSidebar</em> content to <em>pageSidebar</em> for all necessary pages...</div>";
	$query = $conCreative->query("SELECT * FROM pages");
	$counter=0;
	if($query->rowCount() > 0){
		while($row = $query->fetch(PDO::FETCH_ASSOC)){
			if (isset($row['pageLeftSidebar']) && trim($row['pageLeftSidebar']) != "" && strlen(trim($row['pageLeftSidebar'])) > 0) {
				$sidebarData = $row['pageLeftSidebar'] . "<p>&nbsp;</p>" . $row['pageSidebar'];

				$subquery = $conCreative->prepare("UPDATE pages
													SET pageSidebar=:sidebarData
													WHERE pageID=" . $row['pageID']);
				$subquery->bindParam(':sidebarData', $sidebarData);
				if($subquery->execute())
					echo "<div style=\"color: green;\">Moved page <em>" . $row['pageID'] . "</em> sidebar content successfully</div>";
				else
					echo "<div style=\"color: red;\">Error moving page <em>" . $row['pageID'] . "</em> sidebar content</div>";
			}
			$counter++;
		}
	}

	//delete right sidebar column in pages table
	echo "<div>Dropping <em>pageLeftSidebar</em> column in <em>pages</em> table...<div>";
	$subquery = $conCreative->prepare("ALTER TABLE pages
										DROP COLUMN pageLeftSidebar");
	if($subquery->execute())
		echo "<div style=\"color: green;\">Dropping <em>pageLeftSidebar</em> column in <em>pages</em> table successfully</div>";
	else
		echo "<div style=\"color: red;\">Error dropping <em>pageLeftSidebar</em> column in <em>pages</em> table</div>";

	//drop the footer table
	echo "<div>Dropping <em>footer</em> table...<div>";
	$subquery = $conCreative->prepare("DROP TABLE footer");
	if($subquery->execute())
		echo "<div style=\"color: green;\">Dropped <em>footer</em> table successfully</div>";
	else
		echo "<div style=\"color: red;\">Error dropping <em>footer</em> table</div>";

	//create the new footer table
	echo "<div>Creating new <em>footer</em> table...<div>";
	$subquery = $conCreative->prepare("CREATE TABLE footer
										(
										id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
										name varchar(255),
										link varchar(255)
										);");
	if($subquery->execute())
		echo "<div style=\"color: green;\">Created new <em>footer</em> table successfully</div>";
	else
		echo "<div style=\"color: red;\">Error creating new <em>footer</em> table</div>";

	//create the stats table
	echo "<div>Creating <em>stats</em> table...<div>";
	$subquery = $conCreative->prepare("CREATE TABLE stats
										(
										id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
										visitId text,
										visitorId text,
										ipAddress text,
										intervalCounter int(11),
										city text,
										region text,
										country text,
										postal text,
										location text,
										userAgent text,
										windowLocation text,
										specialNote text,
										dateStamp bigint(20)
										);");
	if($subquery->execute())
		echo "<div style=\"color: green;\">Created <em>stats</em> table successfully</div>";
	else
		echo "<div style=\"color: red;\">Error creating <em>stats</em> table</div>";

	//add sidebar radio option column to pages table
	echo "<div>Add <em>sidebarRadio</em> to <em>pages</em> table...<div>";
	$subquery = $conCreative->prepare("ALTER TABLE pages
										ADD sidebarRadio int(1) NOT NULL
										DEFAULT 1
										");
	if($subquery->execute())
		echo "<div style=\"color: green;\">Added <em>sidebarRadio</em> to <em>pages</em> table successfully</div>";
	else
		echo "<div style=\"color: red;\">Error adding <em>sidebarRadio</em> to <em>pages</em> table</div>";

	//add email display option column to company details table
	echo "<div>Add <em>emailDisp</em> to <em>companyDetails</em> table...<div>";
	$subquery = $conCreative->prepare("ALTER TABLE companyDetails
										ADD emailDisp int(1) NOT NULL
										DEFAULT 0
										");
	if($subquery->execute())
		echo "<div style=\"color: green;\">Added <em>emailDisp</em> to <em>companyDetails</em> table successfully</div>";
	else
		echo "<div style=\"color: red;\">Error adding <em>emailDisp</em> to <em>companyDetails</em> table</div>";

	//add hide email option column to comments table
	echo "<div>Add <em>hideEmail</em> to <em>comments</em> table...<div>";
	$subquery = $conCreative->prepare("ALTER TABLE comments
										ADD hideEmail int(1) NOT NULL
										DEFAULT 1
										");
	if($subquery->execute())
		echo "<div style=\"color: green;\">Added <em>hideEmail</em> to <em>comments</em> table successfully</div>";
	else
		echo "<div style=\"color: red;\">Error adding <em>hideEmail</em> to <em>comments</em> table</div>";

	//add comment reply column to comments table
	echo "<div>Add <em>commentReply</em> to <em>comments</em> table...<div>";
	$subquery = $conCreative->prepare("ALTER TABLE comments
										ADD commentReply int(11) NOT NULL
										DEFAULT -1
										");
	if($subquery->execute())
		echo "<div style=\"color: green;\">Added <em>commentReply</em> to <em>comments</em> table successfully</div>";
	else
		echo "<div style=\"color: red;\">Error adding <em>commentReply</em> to <em>comments</em> table</div>";

	//add ip address column to comments table
	echo "<div>Add <em>ipAddress</em> to <em>comments</em> table...<div>";
	$subquery = $conCreative->prepare("ALTER TABLE comments
										ADD ipAddress text NOT NULL
										");
	if($subquery->execute())
		echo "<div style=\"color: green;\">Added <em>ipAddress</em> to <em>comments</em> table successfully</div>";
	else
		echo "<div style=\"color: red;\">Error adding <em>ipAddress</em> to <em>comments</em> table</div>";

	//add locale column to configurations table
	echo "<div>Add <em>locale</em> to <em>configurations</em> table...<div>";
	$subquery = $conCreative->prepare("ALTER TABLE configurations
										ADD locale text NOT NULL
										");
	if($subquery->execute())
		echo "<div style=\"color: green;\">Added <em>locale</em> to <em>configurations</em> table successfully</div>";
	else
		echo "<div style=\"color: red;\">Error adding <em>locale</em> to <em>configurations</em> table</div>";

	//add site directory column to configurations table
	echo "<div>Add <em>siteDir</em> to <em>configurations</em> table...<div>";
	$subquery = $conCreative->prepare("ALTER TABLE configurations
										ADD siteDir text NOT NULL
										");
	if($subquery->execute())
		echo "<div style=\"color: green;\">Added <em>siteDir</em> to <em>configurations</em> table successfully</div>";
	else
		echo "<div style=\"color: red;\">Error adding <em>sitDir</em> to <em>configurations</em> table</div>";

	//create the translations table
	echo "<div>Creating <em>translations</em> table...<div>";
	$subquery = $conCreative->prepare("CREATE TABLE translations
										(
										ID int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
										translate text,
										translation text,
										locked int(1)
										);");
	if($subquery->execute())
		echo "<div style=\"color: green;\">Created <em>translations</em> table successfully</div>";
	else
		echo "<div style=\"color: red;\">Error creating <em>translations</em> table</div>";
}
catch (PDOException $e) {
	echo "<div style=\"color: red;\">" . $e->getMessage() . "</div>";
}
?>
