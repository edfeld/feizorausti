<?php
require_once("include.php");

$currentPage="stats";
?>

<!DOCTYPE html>
<html>

<head>
	<?php require_once("include_head.php"); ?>
</head>

<body>
	<div class="row-fluid bodycontent">
		<?php include("include_menu.php"); ?>

		<div class="span9 maincontainer shadow">
			<?php include("include_message.php"); ?>

			<div>
				<h2>Site Statistics</h2>
				Below is the analytic tracking data for the site.
				<br><br>

        <!-- Change the iframe reference - ERE20210119 -->
				<iframe src="../analytics/analytix.php?action=5&key=b0b9b8b7b6b5b4b3b2b1" style="width: 100%; height: 720px; border: none;"></iframe>
			</div>


		</div>
	</div>

	<?php require_once("include_foot.php"); ?>
</body>

</html>
