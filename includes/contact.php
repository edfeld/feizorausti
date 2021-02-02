<?php
if(isset($_POST['send'])) {
	$emailTo = $ownerEmail;
	$subject = "Contact Form Submission";
	$name = strip_tags($_POST['name']);
	$emailFrom = strip_tags($_POST['email']);
	$subject = strip_tags($_POST['subject']);
	$message = strip_tags(stripslashes($_POST['message']));
    
	$body = "Name: " . $name . "\n";
	$body .= "Email: " . $emailFrom . "\n";
	$body .= "Subject: " . $subject . "\n";
	$body .= "Message: " . $message . "\n";
	
	$headers = "From: " . $emailFrom . "\n";
	$headers .= "Reply-To:" . $emailFrom . "\n";
	
	if($_POST['honeypot'] == "" || $_POST['submitSlider'] >= 75){
		if (mail($emailTo, $subject, $body, $headers)){
			echo '<p class="approve">' . translate_dom("Your comment has been sent successfully.") . '</p>';
		}
		else {
			echo '<p class="deny">' . translate_dom("The server failed to email your message. This is likely a configuration issue on our end. The following error was returned: ");
			print_r(error_get_last());
			echo '</p>';
		}
	} else {
		echo '<p class="deny">' . translate_dom("Something went wrong! Please click on Contact Us button and start over again.") . '</p>';
	}
} else {
?>

<div class="form">
	<form id="contactForm" name="contactForm">
		<div class="row">
			<section class="col col-6">
				<label class="input">
				<i class="fa fa-append fa-user"></i>
				<input placeholder="<?php echo translate_dom("Name"); ?>" type="text" id="name" name="name" minlength="2">
				</label>
			</section>
			<section class="col col-6">
				<label class="input">
				<i class="fa fa-append fa-envelope-o"></i>
				<input placeholder="<?php echo translate_dom("Email"); ?>" type="text" name="email" class="required email">
				</label>
			</section>
		</div>

		<section>      
			<label class="input">
			<i class="fa fa-append fa-tag"></i>
			<input placeholder="<?php echo translate_dom("Subject"); ?>" type="text" id="subject" name="subject" class="required" minlength="2">
			</label>
		</section>
		
		<section>
			<label class="textarea">
			<i class="fa fa-append fa-comment"></i>
			<textarea placeholder="<?php echo translate_dom("Message"); ?>" id="message" name="message" cols="50" rows="10" class="required" minlength="15"></textarea>
			</label>
		</section>
		
		<input type="hidden" id="send" name="send" value="sending">
	</form>
</div>

<?php } ?>

<script type="text/javascript" src="js/honeypot-and-submit-slider.js"></script>
<script type="text/javascript">
	/* Setup the contact form */
	setupForm('#contactForm', "<?php echo $_SERVER['PHP_SELF']; ?>", "POST");
	addHoneypotAndSubmitSlider('#contactForm', "<?php echo translate_dom("> > > > >"); ?>");
</script>