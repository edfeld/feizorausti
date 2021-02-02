<?php
/**
include.php
--------------------
This code connects to the database and gathers basic information. More
page-specific database querries are found inside of "inc_php_headers.php", which
is required below, or inside of particular php files.
**/


//go to install if necessary
$fname = "config/connection.php";
if (!file_exists($fname)) {
	header('location:install/index.html');
}

//set up the session
require_once("includes/session.php");

//connect to database
require_once("config/connection.php");

//get site configuration
$query_getConfig = $conCreative->query("SELECT * FROM configurations WHERE configID=1");
$row_getConfig   = $query_getConfig->fetch();

//get site details
$query_getDetails = $conCreative->query("SELECT * FROM companydetails WHERE companyDetailsID = 1");
$row_getDetails   = $query_getDetails->fetch();

//set the base site path to the correct web address
$sitehost = "http://" . $_SERVER['HTTP_HOST'];
$sitedir  = $row_getConfig['siteDir'];
$sitepath = $sitehost . $sitedir;

//set the current url and requesturl
$requesturl = $_SERVER['REQUEST_URI'];
$url        = "http://" . $_SERVER['HTTP_HOST'] . $requesturl;

//set a variable for the owner of the site
$ownerEmail = $row_getDetails['companyEmail'];

//include various scripts
require_once("version.php");
require_once("inc_various.php");
require_once("language_func.php");
require_once("scripts/bibleLinkedList.php");

//handle contact form
/*if (isset($_POST['submitContact']) && $_POST['submitContact'] == "contact form submitted") {
	//declare and initialize the error flag
	$postingError = 0;

	//retrieve, clean, and verify the content content
	$name = cleanInputBasic($_POST['name']);
		if ($name == "" || !isset($name)) { $postingError = 1; }
	$email = cleanInputBasic($_POST['email']);
		if ($email == "" || !isset($email)) { $postingError = 1; }
	$reason = cleanInputBasic($_POST['reason']);
	$content = cleanInputBasic($_POST['content']);
		if ($content == "" || !isset($content)) { $postingError = 1; }

	//verify the captcha
	$image = new Securimage();
	if (!($image->check($_POST['captcha_code']) == true)) {
		$postingError = 2;
	}

	if ($postingError == 0) {
		//set up basic email variables
		$emailSubject   = "[CONTACT] " . $reason;
		$emailRecipient = $row_getDetails['companyEmail'];
		$emailContent   = "
							<b>From: </b> $name ($email)<br>
							<b>Reason: </b> $reason<br>
							<br><br>
							$content
						";
		$emailHeaders = "From: $email" . "\r\n";
		$emailHeaders .= "Content-type: text/html" . "\r\n";

		//attempt to send the email
		if (mail($emailRecipient, $emailSubject, $emailContent,  $emailHeaders)) {
			$message     = "Thank you! Your contact form has been submitted successfully.";
			$messageType = "success";
		}
		else {
			$message     = "Error: A problem occured while trying to submit your contact form.";
			$messageType = "error";
		}
	}
	else if ($postingError == 1) {
		$message     = "Error: All the fields of the contact form must be filled out.";
		$messageType = "error";
	}
	else if ($postingError == 2) {
		$message     = "Error:  Please enter the numbers and letters in the image correctly.";
		$messageType = "error";
	}
	else {
		$message     = "Error: Unknown error. Please try again.";
		$messageType = "error";
	}
}*/
?>
