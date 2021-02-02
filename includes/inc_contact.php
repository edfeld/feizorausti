<?php 
/**
contact.php
--------------------
The form on this page will send the message that the user has entered to the
email address that is configured inside of the admin panel for the website.
**/


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
	$emailSubject    = "[CONTACT] " . $reason;
	$emailRecipient = $row_getDetails['companyEmail'];
	$emailContent   = "<b>From: </b> $name ($email)<br>
								<b>Reason: </b> $reason<br>
								<br><br>
								$content";
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
?>