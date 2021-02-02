<?php
/**
 * An API interface for the GRO Analytics Tracker to access visits, views,
 * salvations, and discipleships.
 *
 * @version 1.00
 * @author Luke Hollenback <luke@mynamewasluke.com>
 */

// Allow external Ajax requests
header('Access-Control-Allow-Origin: *');

// Connect to the database
require_once("../config/connection.php");

// Setup some basic error handling
$errors = array(
    "0001" => "failure while preparing query statement"
);
$error = array();

// Set up the data object
$data = array();

// Figure out the timestamp to retrieve on forward from
if (isset($_GET["ts"]))
    $data["timetag"] = $_GET["ts"];
else
    $data["timetag"] = time();
$data["timetagMilliseconds"] = floor($data["timetag"] * 1000);

// Figure out the type of data that is needed
// (and set up some partial query strings based on it)
if (isset($_GET["cf"]) && $_GET["cf"] == "timeloc") {
    $statsSelect = "id, visitorId, location";
    $salvationsSelect = "id, location";
}
else {
    $statsSelect = "*";
    $salvationsSelect = "*";
}

// Count total visits
$sql = "SELECT COUNT(DISTINCT visitorId) FROM stats";
$stmt = $conCreative->prepare($sql);
$stmt->execute();
$data["totalVisits"] = $stmt->fetch()[0];

// Get a list of new visits (since timetag)
$visits = array();
$sql = "SELECT {$statsSelect} FROM stats WHERE dateStamp>=:timetagMilliseconds GROUP BY visitorId";
$stmt = $conCreative->prepare($sql);
$stmt->bindParam(":timetagMilliseconds", $data["timetagMilliseconds"]);
$stmt->execute();
while ($row = $stmt->fetch()) {
    $sql_cnt = "SELECT COUNT(*) FROM stats WHERE visitorId=:visitorId";
    $stmt_cnt = $conCreative->prepare($sql_cnt);
    $stmt_cnt->bindParam(":visitorId", $row["visitorId"]);
    $stmt_cnt->execute();
    $cnt = $stmt_cnt->fetch()[0];
    
    if ($cnt == 1)
        $visits[] = $row;
}
$data["visits"] = $visits;

// Count total views
$sql = "SELECT COUNT(id) FROM stats";
$stmt = $conCreative->prepare($sql);
$stmt->execute();
$data["totalViews"] = $stmt->fetch()[0];

// Get a list of new views (since timetag)
$views = array();
$sql = "SELECT {$statsSelect} FROM stats WHERE dateStamp>=:timetagMilliseconds";
$stmt = $conCreative->prepare($sql);
$stmt->bindParam(":timetagMilliseconds", $data["timetagMilliseconds"]);
$stmt->execute();
while ($row = $stmt->fetch()) {
    $views[] = $row;
}
$data["views"] = $views;

// Count total salvations
$sql = "SELECT COUNT(id) FROM salvations WHERE type=0";
$stmt = $conCreative->prepare($sql);
$stmt->execute();
$data["totalSalvations"] = $stmt->fetch()[0];

// Get a list of new salvations (since timetag)
$salvations = array();
$sql = "SELECT {$salvationsSelect} FROM salvations WHERE type=0 AND timetag>=:timetag";
$stmt = $conCreative->prepare($sql);
$stmt->bindParam(":timetag", $data["timetag"]);
$stmt->execute();
while ($row = $stmt->fetch()) {
    $salvations[] = $row;
}
$data["salvations"] = $salvations;

// Count total discipleship requests
$sql = "SELECT COUNT(id) FROM salvations WHERE type=1";
$stmt = $conCreative->prepare($sql);
$stmt->execute();
$data["totalDisciples"] = $stmt->fetch()[0];

// Get a list of new disciples (since timetag)
$disciples = array();
$sql = "SELECT {$salvationsSelect} FROM salvations WHERE type=1 AND timetag>=:timetag";
$stmt = $conCreative->prepare($sql);
$stmt->bindParam(":timetag", $data["timetag"]);
$stmt->execute();
while ($row = $stmt->fetch()) {
    $disciples[] = $row;
}
$data["disciples"] = $disciples;

// Encode and output the JSON
echo json_encode($data);

// Close the connection to the database
$conCreative = null;
?>
