
<audio controls>
	<source src="<?php echo $row_getConfig['embeddedLink']; ?>" type="audio/mpeg">
	Your browser does not support the audio element.
</audio> 

<div id="liveradio"><?php echo $row_getConfig['radioName']; ?></div>

<div id="song-information">
	<div id="now-playing-name"><?php echo $row_getConfig['programName']; ?></div>
	<div id="now-playing-album"><?php echo $row_getConfig['albumName']; ?></div>
</div>

<div id="popuplink">
<p><?php echo translate_dom("or"); ?></p>
<p><a href=javascript:popup('stream.php')><?php echo translate_dom("play radio in a popup player"); ?></a></p>

<?php
$streamLink = $row_getConfig['streamLink'];
?>

</div>
