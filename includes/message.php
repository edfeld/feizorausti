<?php
/**
message.php
-----------
The below displays a message (either $message or $_GET['msg']) of a certain
type ($messageType or $_GET['msgType']) at the top of the page.
**/
?>

<div id="message">
	<?php
	if (isset($_GET['msg'])) {
		if ($_GET['msgType'] == "success")
			echo "<div class='articleCommentsSuccess'>" . translate_dom($_GET['msg']) . "</div>";
		else if ($_GET['msgType'] == "error")
			echo "<div class='error'>" . translate_dom($_GET['msg']) . "</div>";
		else
			echo "<div class='message'>" . translate_dom($_GET['msg']) . "</div>";
	}
	else if (isset($message)) {
		if ($messageType == "success")
			echo "<div class='articleCommentsSuccess'>" . translate_dom($message) . "</div>";
		else if ($messageType == "error")
			echo "<div class='error'>" . translate_dom($message) . "</div>";
		else
			echo "<div class='message'>" . translate_dom($message) . "</div>";
	}
	?>
</div>