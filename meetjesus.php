<?php
/**
 * This page is similar to page.php, but functions as the Meet Jesus page for
 * the website.
 * 
 * History
 * 2020-10-20 - Ed Einfeld - Add code to write out Salvations and 
 * Discipleship requests to a separate consolidated 'salavtion'
 *  table on the GRO database.
 * 2020-11-19 - Ed Einfeld - Moved statsEnv.php to this file
 *  
 */

//basic page setup 
$pagename = "meetjesus";
require_once("includes/include.php");
require_once("./config/statsEnv.php"); // Moved StatsEnv.php out of ConnectionGROStats.php - ERE20201119 
require_once("./objects/salvations.php"); // ERE20201014
require_once("./objects/database.php"); //ERE20201014
require_once('./config/connectionGROStats.php'); // ERE20201015 - stats environment module

//generate audio player
$audioPlayerEnable = $row_getConfig['mjAudio'];
$audioPlayerSource = $row_getConfig['mjAudioSource'];
$audioPlayerScript = "
	<script type=\"text/javascript\">
		var audioPlayerEnable = $audioPlayerEnable,
			audLoaded,
			aud;

		if (audioPlayerEnable) {
			audLoaded = false;
			aud = new Audio();

			aud.addEventListener('loadeddata', function() {
			    audLoaded = true;
			    //aud.play();
			}, false);

			aud.addEventListener('error' , function() {
			    alert('error loading audio');
			}, false);

			aud.src = '$audioPlayerSource';
		}
	</script>
";

//propegate data
$data['yourName'] = translate_dom("Name...");
$data['yourEmail'] = translate_dom("Email...");
$data['letUsKnow'] = translate_dom("Let us know!");
$data['getInTouch'] = translate_dom("Get in touch!");
$data['audioPlayerCode'] = "";
if ($audioPlayerEnable == 1)
	$data['audioPlayerCode'] = "<div id=\"audioPlayer\"><div class=\"information\" id=\"audioInfo\">" . $row_getConfig['mjAudioCaption'] . "</div><div class=\"button\" id=\"audioPlay\" onclick=\"if (aud.paused) { aud.play(); document.getElementById(\'audioPauseIcon\').style.display = \'inline-block\'; document.getElementById(\'audioPlayIcon\').style.display = \'none\'; } else { aud.pause(); document.getElementById(\'audioPauseIcon\').style.display = \'none\'; document.getElementById(\'audioPlayIcon\').style.display = \'inline-block\'; }\"><i class=\"fa fa-play\" id=\"audioPlayIcon\"></i><i class=\"fa fa-pause\" id=\"audioPauseIcon\"></i></div><div class=\"button\" id=\"audioRewind\" onclick=\"aud.currentTime = 0.0;\"><i class=\"fa fa-step-backward\"></i></div></div>";
$data['recieveJesusDialog'] = "<div id=\"close\" onclick=\"document.getElementById(\'dialogPopup\').style.display = \'none\';\">X</div>" . $row_getConfig['mjAudioRecieveJesus'] . $data['audioPlayerCode'] . "<form id=\"recieveJesus\" method=\"post\"><input type=\"hidden\" name=\"recieveJesus\" value=\"true\"><input type=\"hidden\" value=\"\" name=\"visitorStamp\" id=\"recieveVisitorStamp\"><input type=\"text\" name=\"visitorName\" placeholder=\"" . $data['yourName'] . "\"><input type=\"text\" name=\"visitorEmail\" placeholder=\"" . $data['yourEmail'] . "\"><button onclick=\"updateVisitorStampHiddenFields();\" name=\"recieveJesus\">" . $data['letUsKnow'] . "</button></form>";
$data['recieveDiscipleshipDialog'] = "<div id=\"close\" onclick=\"document.getElementById(\'dialogPopup\').style.display = \'none\';\">X</div>" . $row_getConfig['mjAudioRequestDiscipleship'] . "<form id=\"recieveJesus\" method=\"post\"><input type=\"hidden\" name=\"recieveDiscipleship\" value=\"true\"><input type=\"hidden\" value=\"\" name=\"visitorStamp\" id=\"requestVisitorStamp\"><input type=\"text\" name=\"visitorName\" placeholder=\"" . $data['yourName'] . "\"><input type=\"text\" name=\"visitorEmail\" placeholder=\"" . $data['yourEmail'] . "\"><button onclick=\"updateVisitorStampHiddenFields();\" name=\"recieveDiscipleship\">" . $data['getInTouch'] . "</button></form>";
$data['confRecieveJesusDialog'] = "<div id=\"close\" onclick=\"document.getElementById(\'dialogPopup\').style.display = \'none\';\">X</div>" . $row_getConfig['mjAudioRecieveJesusResult'];
$data['confRecieveDiscipleshipDialog'] = "<div id=\"close\" onclick=\"document.getElementById(\'dialogPopup\').style.display = \'none\';\">X</div>" . $row_getConfig['mjAudioRequestDiscipleshipResult'];

//process forms
$pageLoadJavaScript = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	//start error handling
	$error = 0;

	//generate visitor information
	$data['timetag'] = time();
	$data['visitor']['stamp'] = $_POST['visitorStamp'];
	$data['visitor']['name'] = cleanInputBasic($_POST['visitorName']);
	$data['visitor']['email'] = cleanInputBasic($_POST['visitorEmail']);
	if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
		$data['visitor']['ipAddress'] = $_SERVER['HTTP_X_FORWARDED_FOR']; //try to sniff out if client is behind a proxy
	else
		$data['visitor']['ipAddress'] = $_SERVER['REMOTE_ADDR'];
	$data['visitor']['stamp'] = $_POST['visitorStamp'];
	$data['visitor']['location'] = "";
	if (isset($_SESSION['SIMPLY_ANALYTICS']['analytics_city']) && $_SESSION['SIMPLY_ANALYTICS']['analytics_city'] != null && $_SESSION['SIMPLY_ANALYTICS']['analytics_city'] != "") {
		$data['visitor']['location'] .= $_SESSION['SIMPLY_ANALYTICS']['analytics_city'];
	}
	if (isset($_SESSION['SIMPLY_ANALYTICS']['analytics_region']) && $_SESSION['SIMPLY_ANALYTICS']['analytics_region'] != null && $_SESSION['SIMPLY_ANALYTICS']['analytics_region'] != "") {
		if (strlen($data['visitor']['location']) > 0)
			$data['visitor']['location'] .= ", ";
		$data['visitor']['location'] .= $_SESSION['SIMPLY_ANALYTICS']['analytics_region'];
	}
	if (isset($_SESSION['SIMPLY_ANALYTICS']['analytics_country']) && $_SESSION['SIMPLY_ANALYTICS']['analytics_country'] != null && $_SESSION['SIMPLY_ANALYTICS']['analytics_country'] != "") {
		if (strlen($data['visitor']['location']) > 0)
			$data['visitor']['location'] .= ", ";
		$data['visitor']['location'] .= $_SESSION['SIMPLY_ANALYTICS']['analytics_country'];
	}
	/* NOTE: The above method of generating a alphabetic location name is not useful anymore. The
			 method below of determining the latitude and longitude is now needed for use by the
			 Analytics Tracker map. The above is simply left in as a fall back for the database.
			 And because I'm lazy and don't want to deal with breaking anything by just cutting it
			 out.
	*/
	$data['visitor']['location'] = $_SESSION['SIMPLY_ANALYTICS']['analytics_location'];

	//generate all posible email content
	$email['recipient'] = $row_getDetails['companyEmail'];
	$email['headers'] = "From: " . $data['visitor']['email'] . "\r\n";
	$email['headers'] .= "Content-type: text/html" . "\r\n";
	if (!isset($data['visitor']['name']) || $data['visitor']['name'] == "")
		$email['name'] = "Someone who did not state their name";
	else
		$email['name'] = $data['visitor']['name'];
	if (!isset($data['visitor']['email']) || $data['visitor']['email'] == "")
		$email['email'] = "no email provided";
	else
		$email['email'] = $data['visitor']['email'];

	//handle forms
	if (isset($_POST['recieveJesus'])) {
		//set the appropriate type
		$data['type'] = 0;

		//generate the rest of the email content
		$email['subject'] = "[RECIEVE] Someone Wants to Recieve Jesus!";
		$email['content'] = "
			<div style='padding: 16px; border: 4px solid " . $row_getConfig['primaryColor'] . "; font-family: sans-serif; font-size: 12px;'>
			<p>" . $email['name'] . " has stated that they would like
			to recieve Jesus! Respond by either replying to this email, or
			by directly sending a new message to the email that they supplied.
			Here is their information:</p>
			<ul>
				<li><strong>Name:</strong> " . $email['name'] . "</li>
				<li><strong>Email:</strong> <a href='mailto:" . $email['email'] . "' style='color: " . $row_getConfig['secondaryColor'] . ";'>" . $email['email'] . "</a></li>
				<li><strong>Location:</strong> " . $data['visitor']['location'] . "</li>
			</ul>
			</div>
			<div style='color: " . $row_getConfig['primaryColor'] . "; font-family: sans-serif; font-size: 12px; text-align: center;'>
				<p><em>Note that this is an automatic email from the content
				management system for <strong>" . $row_getDetails['companyName'] . "
				</strong></em></p>
			</div>";

		$pageLoadJavaScript .= "showDialogPopup('" . $data['confRecieveJesusDialog'] . "');";
	}
	else if (isset($_POST['recieveDiscipleship'])) {
		//set the appropriate type
		$data['type'] = 1;

		//generate the rest of the email content
		$email['subject'] = "[REQUEST] Someone has Requested Discipleship!";
		$email['content'] = "
			<div style='padding: 16px; border: 4px solid " . $row_getConfig['primaryColor'] . "; font-family: sans-serif; font-size: 12px;'>
			<p>" . $email['name'] . " has requested
			discipleship! Respond by either replying to this email, or
			by directly sending a new message to the email that they supplied.
			Here is their information:</p>
			<ul>
				<li><strong>Name:</strong> " . $email['name'] . "</li>
				<li><strong>Email:</strong> <a href='mailto:" . $email['email'] . "' style='color: " . $row_getConfig['secondaryColor'] . ";'>" . $email['email'] . "</a></li>
				<li><strong>Location:</strong> " . $data['visitor']['location'] . "</li>
			</ul>
			</div>
			<div style='color: " . $row_getConfig['primaryColor'] . "; font-family: sans-serif; font-size: 12px; text-align: center;'>
				<p><em>Note that this is an automatic email from the content
				management system for <strong>" . $row_getDetails['companyName'] . "
				</strong></em></p>
			</div>";

		$pageLoadJavaScript .= "showDialogPopup('" . $data['confRecieveDiscipleshipDialog'] . "');";
	}

	//attempt to send the email
	if (mail($email['recipient'], $email['subject'], $email['content'],  $email['headers'])) {
		$message     = "Thank you! Your contact form has been submitted successfully.";
		$messageType = "success";
	}
	else {
		$message     = "<div style='font-weight: bold;'><i class='fa fa-exclamation-triangle'></i> An Error Occured:</div><div>A problem occured while trying to send your information to us. Please try to send it again so that we can get in touch with you. If the problem persists, try using the regular contact form.</div>";
		$messageType = "error";

		$error = 1;
	}

	//attempt to insert salvation request into the blog site database
	try {
		$conCreative->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$saveSalvationQuery = $conCreative->prepare("INSERT Into salvations (simpleAnalyticsVisitorId, type, name, email, ipAddress, location, timetag)
													 VALUES (:vid, :type, :name, :email, :ip, :location, :timetag)");
		$saveSalvationQuery->bindParam(':vid', $data['visitor']['stamp']);
		$saveSalvationQuery->bindParam(':type', $data['type']);
		$saveSalvationQuery->bindParam(':name', $data['visitor']['name']);
		$saveSalvationQuery->bindParam(':email', $data['visitor']['email']);
		$saveSalvationQuery->bindParam(':ip', $data['visitor']['ipAddress']);
		$saveSalvationQuery->bindParam(':location', $data['visitor']['location']);
		$saveSalvationQuery->bindParam(':timetag', $data['timetag']);
		$saveSalvationQuery->execute();
	}
	catch (PDOException $e) {
		$error = 2;

		$message     = "<div style='font-weight: bold;'><i class='fa fa-exclamation-triangle'></i> An Error Occured:</div><div>A problem occured while trying to send your information to us. Please try to send it again so that we can get in touch with you. If the problem persists, try using the regular contact form.</div>";
		$messageType = "error";
  }
  // ERE20201014 - Add code to write out the Salvations and Discipleship reqeusts to a new consolidated table on the GRO Database.  i.e. We are writing a duplicate records of the original salvation table connected to the property A.K.A company.  The duplicated table will be used for the statistical reporting.  

    $companyStatsId = getCompanyStatsId($conCreative);
    // Use Database and Salvation Object to write to MYSQL
    $statsDB = new Database($currentEnv, 'GRO');
    $dbConn = $statsDB->getConnection();
    $statsSalvation = new Salvation($dbConn);
    $statsSalvation->companyStatsId = $companyStatsId;
    $statsSalvation->simpleAnalyticsVisitorId = $data['visitor']['stamp'];
    $statsSalvation->type = $data['type'];
    $statsSalvation->name = $data['visitor']['name'];
    $statsSalvation->email = $data['visitor']['email'];
    $statsSalvation->ipAddress = $data['visitor']['ipAddress'];
    $statsSalvation->location = $data['visitor']['location'];
    $statsSalvation->timetag = $data['timetag'];
    $statsSalvation->contacted = 0;
    if($statsSalvation->createForStats() === true) {
      echo("<script>console.log('The salvation stats record loaded - Success!!!'); </script>");
    } else {
      echo("<script>console.log('The salvation stats record load - Failed!!!'); </script>");
    }
    $statsDB = null;
    $statsSalvation = null;
}

// ERE20201015 - This function was added to get the company Stats ID from the companyDetails table.  
function getCompanyStatsId($conn) {
  $db_co_table = 'companyDetails';
  $saveCompanyStatId = $conn->prepare("SELECT companyStatsId FROM $db_co_table WHERE companyDetailsID = 1");
  $saveCompanyStatId->execute();
  $row_getCoStatsID = $saveCompanyStatId->fetch();
  $totalRows_getCoStatId = $saveCompanyStatId->rowCount();
  if ($totalRows_getCoStatId > 0) {
    return $row_getCoStatsID['companyStatsId'];
  } else {
    return "Empty Value for companyStatsId";
  }
}
?>

<!DOCTYPE html>
<html>

<head>
	<?php
	include("includes/head.php");

	echo $audioPlayerScript;
  ?>
  <!-- This is where we added scripting to record site visits -- ERE20200922 -->
  <!-- Change file name from analytics.php to analytix.php - ERE20210119 -->
  <script type='text/javascript' src='analytics/analytix.php/?action=7' note='MeetJesus Page' id='analytics'></script>
</head>

<body>
	<?php
	include("includes/message.php");
	include("includes/header.php");
	include("includes/carousel.php");
	?>

	<div id="dialogPopup">
		<div id="background"></div>
		<div id="content">
			<div id="close" onclick="document.getElementById('dialogPopup').style.display = 'none';">X</div>
			This is placeholder dialog popup content.
		</div>
	</div>

	<div id="wrapper">
		<?php if ($row_getConfig['sidebarSide'] == 0) { ?>
			<div id="leftsidebar">
				<?php include("includes/sidebar.php"); ?>
			</div>
			
			<div id="rightsidebar">
				<br><br>
				<?php echo $row_getConfig['mjAudioIntro']; ?>
				<div class="meetjesus">
					<div class="meetjesusButton" onclick="showRecieveJesus();"><?php echo translate_dom("I would like to receive Jesus."); ?></div>
					<div class="meetjesusButton" onclick="showRecieveDiscipleship();"><?php echo translate_dom("I would like to receive Christian discipleship."); ?></div>
				</div>
			</div>
		<?php } else { ?>
			<div id="leftsidebar">
				<br><br>
				<?php echo $row_getConfig['mjAudioIntro']; ?>
				<div class="meetjesus">
					<div class="meetjesusButton" onclick="showRecieveJesus();"><?php echo translate_dom("I would like to receive Jesus."); ?></div>
					<div class="meetjesusButton" onclick="showRecieveDiscipleship();"><?php echo translate_dom("I would like to receive Christian discipleship."); ?></div>
				</div>
			</div>
			
			<div id="rightsidebar">
				<?php include("includes/sidebar.php"); ?>
			</div>
		<?php } ?>

		<div class="clearer"></div>
	</div>

	<?php
	include("includes/footer.php");
	?>

	<script type="text/javascript">
		//default pop-up function
		function showDialogPopup(content) {
			document.getElementById('dialogPopup').style.display = 'block';
			document.getElementById('content').innerHTML = content;
		}

		//functions to call the default pop-up function with specific content
		function showRecieveJesus() {
      showDialogPopup('<?php echo $data['recieveJesusDialog']; ?>');
      if (typeof gtag !== "undefined") { // ERE20201015 - check for the gatracking flag to be true
        gtag('event', 'recieve_jesus', {'event_category': 'seeker_engagement'});
      }
		}
		function showRecieveDiscipleship() {
      showDialogPopup('<?php echo $data['recieveDiscipleshipDialog']; ?>');
      if (typeof gtag !== "undefined") { // ERE20201015 - check for the gatracking flag to be true
        gtag('event', 'recieve_discipleship', {'event_category': 'seeker_engagement'});
      }
		}

		//try to retrive visitor stamp until it is available, then submit the
		//form
		function updateVisitorStampHiddenFields() {
			if (typeof simpleAnalytics_data === 'undefined' || simpleAnalytics_data.visitorStamp == null) {
				console.log("simpleAnalytics_data.visitorStamp undefined");
				setTimeout(updateVisitorStampHiddenFields, (500));
			}
			else {
				console.log("simpleAnalytics_data.visitorStamp = " + simpleAnalytics_data.visitorStamp);
				submitMeetJesusForm(simpleAnalytics_data.visitorStamp);
			}
		}

		//function to submit the form properly (put the proper visitorStamp in
		//the appropriate hidden field of the appropriate hidden form, and then
		//submit everything)
		function submitMeetJesusForm(stamp) {
			console.log("(submitting form)");
			if ($('#recieveVisitorStamp').length > 0) {
				document.getElementById('recieveVisitorStamp').value = stamp;
			}
			if ($('#requestVisitorStamp').length > 0) {
				document.getElementById('requestVisitorStamp').value = stamp;
			}
			document.getElementById('recieveJesus').submit();
    }

		//this function fires page load
		$(document).ready ( function () {
			<?php echo $pageLoadJavaScript; ?>
		});
	</script>
</body>

</html>
