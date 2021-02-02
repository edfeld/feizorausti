<?php
/*
connectionGROStats.php
--------------------
This code connects to the GRO database to access the consolidated stats table. Edit the details
below to change the connection.
*/

// include_once "statsEnv.php"; // Moved statsEnv.php into Meetjesus.php - ERE20201119 

switch ($currentEnv) {
  case "Sandbox":
    $hostname = "localhost";
    $database = "isa2islam";
    $username = "isa2islam";
    $password = "EKkZGGGoknh2vYTo"; 
      break;
  // ERE20200422 - update  the webServer case with the production GRO database values
  case "WebServer":
    $hostname = "96.93.106.82";
    $database = "gro";
    $username = "core";
    $password = "6VzYKFN7QYb71gKW"; 
      break;
  default:
  // Default to localhost
    $hostname = "localhost";
    $database = "GRO";
    $username = "root";
    $password = "root";  
}