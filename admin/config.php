<?php
require_once("include.php");

$currentPage="config";
//View Pages
$pid=1;
?>

<!DOCTYPE html>
<html>

<head>
	<?php require_once("include_head.php"); ?>

	<script type="text/javascript" src="jscolor/jscolor.js"></script>
</head>

<body>
	<div class="row-fluid bodycontent">
		<?php include("include_menu.php"); ?>

		<div class="span9 maincontainer shadow">
			<?php include("include_message.php"); ?>

			<div>
				<form name="form" method="post" enctype="multipart/form-data">
				<input type="hidden" name="configID" value="<?php echo $row_getConfig['configID']; ?>" />

				<h2>Host Details</h2>
				<i>This tells the website how to create links and find files as it generates itself.</i>
				<br><br>
				<div style='margin-left: 32px;'>
					<?php
					echo "<b>Current Host: </b>" . $sitehost . "<br>";
					echo "<b>Current Directory: </b>" . $sitedir . "<br>";
					echo "<b>Current Site Path: </b>" . $sitepath . "<br>";
					?>
				</div>
				<br>
				<div><label for="siteSub">Subdirectory <i>(e.g. /cu) (be sure to include an opening slash, but no closing slash)</i></label>  <input type="text" name="siteSub" value="<?php echo $row_getConfig['siteDir']; ?>" /></div>
				<br><br>
				
				<h2>Google Analytics Details</h2>
				<i>This tells the website which Google Analytics Property visitors should be tracked to. This is required to track visitors as well as seeker engagement on the website. Leave this field blank to disable Google Analytics.</i>
				<br><br>
				<div><label for="gaTrackingID">Google Analytics Property ID</label> <input type="text" name="gaTrackingID" id="gaTrackingID" value="<?php echo $row_getConfig['gaTrackingID']; ?>"/></div>
				<br><br>
				
				<h2>Theme Customization</h2>
				<i>Note: Some colors may be so light that it is hard to see them in their textbox without highlighting them.</i><br><br>
				<div><label for="primaryColor">Primary Color (navigation buttons, search box, etc...)</label>  <input type="text" class='color {hash:true}' name="primaryColor" id="primaryColor" onKeyUp="updateColor('primaryColor');" value="<?php echo $row_getConfig['primaryColor']; ?>" style="color: <?php echo $row_getConfig['primaryColor']; ?>;" /></div>
				<div><label for="secondaryColor">Secondary Color (links, etc...)</label>  <input type="text" class='color {hash:true}' name="secondaryColor" id="secondaryColor" onKeyUp="updateColor('secondaryColor');" value="<?php echo $row_getConfig['secondaryColor']; ?>" style="color: <?php echo $row_getConfig['primaryColor']; ?>;" /></div>
				<div><label for="linkHoverColor">Link Hover Color</label>  <input type="text" class='color {hash:true}' name="linkHoverColor" id="linkHoverColor" onKeyUp="updateColor('linkHoverColor');" value="<?php echo $row_getConfig['linkHoverColor']; ?>" style="color: <?php echo $row_getConfig['linkHoverColor']; ?>;" /></div>
				<div><label for="bodyBackgroundColor">Body Background Color</label>  <input type="text" class='color {hash:true}' name="bodyBackgroundColor" id="bodyBackgroundColor" onKeyUp="updateColor('bodyBackgroundColor');" value="<?php echo $row_getConfig['bodyBackgroundColor']; ?>" style="color: <?php echo $row_getConfig['bodyBackgroundColor']; ?>;" /></div>
				<div><label for="bodyBackgroundImage">Body Background Image</label> <input type="text" name="bodyBackgroundImage" id="fieldID" style="cursor:pointer;" onClick="openFilemanager(1,'fieldID');" value="<?php echo $row_getConfig['bodyBackgroundImage']; ?>" /></div>
				<div style="display: none;"><label for="customCSS">Custom CSS</label> <textarea name="customCSS" id="customCSS" class="nomce"><?php echo $row_getConfig['customCSS']; ?></textarea></div>
				<br><br>

				<h2>Stream Details</h2>
				<i>This tells the website how to connect to and display the streaming radio station</i>
				<div class="pull-left articlefriendlyURL" style="border: none;">
					Show Stream Player/Links in Sidebar?<br>
					<span style="margin-right: 8px;"><input type="radio" name="streamButton" id="yes" value="yes" style="width: 16px; height: 16px; display: inline-block; position: relative;"  <?php if($row_getConfig['sidebarStream'] == 1){ echo "checked"; } ?>><label for="yes">Yes</label></span>
					<span style="margin-left: 8px;"><input type="radio" name="streamButton" id="no" value="no" style="width: 16px; height: 16px; display: inline-block; position: relative;" <?php if($row_getConfig['sidebarStream'] == 0){ echo "checked"; } ?>><label for="no">No</label></span>
				</div>
				<br><br>
				<div><label for="embeddedLink">Embedded Stream Link</label>  <input type="text" name="embeddedLink" value="<?php echo $row_getConfig['embeddedLink']; ?>" /></div><br>
				<div><label for="streamLink">External Stream Link (exclude extension)</label>  <input type="text" name="streamLink" value="<?php echo $row_getConfig['streamLink']; ?>" /></div><br>
				<div><label for="radioName">Radio Name</label>  <input type="text" name="radioName" value="<?php echo $row_getConfig['radioName']; ?>" /></div><br>
				<div><label for="programName">Program Name</label>  <input type="text" name="programName" value="<?php echo $row_getConfig['programName']; ?>" /></div><br>
				<div><label for="albumName">Album Name</label>  <input type="text" name="albumName" value="<?php echo $row_getConfig['albumName']; ?>" /></div><br>
				
				<h2>Sidebar Style</h2>
				<div class="pull-left articlefriendlyURL" style="border: none;">
					<span style="margin-right: 8px;"><input type="radio" name="sidebarStyleButton" id="left" value="left" style="width: 16px; height: 16px; display: inline-block; position: relative;"  <?php if($row_getConfig['sidebarSide'] == 0){ echo "checked"; } ?>><label for="left">Left-to-Right</label></span>
					<span style="margin-left: 8px;"><input type="radio" name="sidebarStyleButton" id="right" value="right" style="width: 16px; height: 16px; display: inline-block; position: relative;" <?php if($row_getConfig['sidebarSide'] == 1){ echo "checked"; } ?>><label for="right">Right-to-Left</label></span>
				</div>
				<br><br>

				<h2>Meet Jesus Page Details</h2>
				<i>This contains the text that will be displayed in the various areas of the Meet Jesus page.</i><br><br>
				<div class="pull-left articlefriendlyURL" style="border: none;">
					Display an audio player for a recorded prayer?<br>
					<span style="margin-right: 8px;"><input type="radio" name="mjAudio" id="yes" value="yes" style="width: 16px; height: 16px; display: inline-block; position: relative;"  <?php if($row_getConfig['mjAudio'] == 1){ echo "checked"; } ?>><label for="yes">Yes</label></span>
					<span style="margin-left: 8px;"><input type="radio" name="mjAudio" id="no" value="no" style="width: 16px; height: 16px; display: inline-block; position: relative;" <?php if($row_getConfig['mjAudio'] == 0){ echo "checked"; } ?>><label for="no">No</label></span>
				</div>
				<div><label for="mjAudioSource">Audio Source</label>  <input type="text" name="mjAudioSource" id="mjAudioSource" style="cursor:pointer;" onclick="openFilemanager(1,'mjAudioSource');" value="<?php echo $row_getConfig['mjAudioSource']; ?>" /></div><br>
				<label for="mjAudioCaption">Caption</label> <textarea name="mjAudioCaption" id="mjAudioCaption" class="nomce"><?php echo $row_getConfig['mjAudioCaption']; ?></textarea>
				<label for="mjAudioIntro">Intro</label><textarea name="mjAudioIntro" id="mjAudioIntro" class="nomce"><?php echo $row_getConfig['mjAudioIntro']; ?></textarea>
				<label for="mjAudioRecieveJesus">Recieve Jesus</label> <textarea name="mjAudioRecieveJesus" id="mjAudioRecieveJesus" class="nomce"><?php echo $row_getConfig['mjAudioRecieveJesus']; ?></textarea>
				<label for="mjAudioRecieveJesusResult">Recieve Jesus Result</label> <textarea name="mjAudioRecieveJesusResult" id="mjAudioRecieveJesusResult" class="nomce"><?php echo $row_getConfig['mjAudioRecieveJesusResult']; ?></textarea>
				<label for="mjAudioRequestDiscipleship">Request Discipleship</label> <textarea name="mjAudioRequestDiscipleship" id="mjAudioRequestDiscipleship" class="nomce"><?php echo $row_getConfig['mjAudioRequestDiscipleship']; ?></textarea>
				<label for="mjAudioRequestDiscipleshipResult">Request Discipleship Result</label> <textarea name="mjAudioRequestDiscipleshipResult" id="mjAudioRequestDiscipleshipResult" class="nomce"><?php echo $row_getConfig['mjAudioRequestDiscipleshipResult']; ?></textarea>

				<div class="clearboth"></div>

				<div class="marginTop20"><input type="submit" name="editConfig" id="editConfig" value="Edit Information" class="btn btn-primary" /></div>
				</form>
			</div>


		</div>
	</div>

	<?php require_once("include_foot.php"); ?>
</body>

</html>
