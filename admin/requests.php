<?php
require_once("include.php");
require_once("../includes/inc_various.php");

$currentPage = "requests";

$conCreative->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
try {
	//set entry as contacted
	if (isset($_GET['contacted'])) {
		$query_contactedSalvation = $conCreative->prepare("UPDATE salvations SET contacted=1 WHERE id=:id");
		$query_contactedSalvation->bindParam(':id', $_GET['contacted'], PDO::PARAM_INT);
		$query_contactedSalvation->execute();
	}

	//retrieve request discipleships that need to be contacted
	$query_getSalvations = $conCreative->prepare("SELECT * FROM salvations WHERE contacted=0 AND type=1");
	$query_getSalvations->execute();
	$totalRows_getSalvations = $query_getSalvations->rowCount();
}
catch (PDOException $e) {
	echo $e->getMessage();
}
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

			<h2>Request Discipleship</h2>
			Brand new entries of people who have noted that they would like to,
			recieve discipleship. These people have not been contacted before
			and must be.
			<hr />

			<?php
			$counterSalvations = 0;
			if ($totalRows_getSalvations > 0) {
				while ($row = $query_getSalvations->fetch(PDO::FETCH_ASSOC)) {
					//parse the date
					$parsedDate = date("d M Y", $row['timetag']);
			?>

					<div class='well'>
						<a name='<?php echo $row['id']; ?>'><!--anchor with salvation id--></a>
						<ul>
							<li><strong>Name:</strong> <?php echo $row['name']; ?></li>
							<li><strong>Email: </strong> <a href="mailto:<?php echo $row['email']; ?>"><?php echo $row['email']; ?></a></li>
							<li><strong>Location: </strong>  <?php echo $row['location']; ?></li>
						</ul>
						<div class='commentByline'>
							<?php echo $parsedDate . ' // '; ?>
							<?php if (isset($row['ipAddress']) && $row['ipAddress'] != null && $row['ipAddress'] != '') { echo $row['ipAddress'] . ' // '; } ?>
							<?php if (isset($row['simpleAnalyticsVisitorId']) && $row['simpleAnalyticsVisitorId'] != null && $row['simpleAnalyticsVisitorId'] != '') { echo $row['simpleAnalyticsVisitorId']; } ?>
						</div>
						<div class="commentButtons btn-toolbar">
							<div class="btn-group">
								<button class='btn btn-mini' onclick='contacted(<?php echo $row['id']; ?>);'>Has Been Contacted</button>
							</div>
						</div>
					</div>

					<?php

					$counterSalvations++;
				}
			}
			if($counterSalvations <= 0){
				echo "<div class='alert'>No new Recieve Jesus entries were found.</div>";
			}
			?>
		</div>
	</div>

	<?php require_once('include_foot.php'); ?>

	<!--custom page JavaScript-->
	<script type="text/javascript">
	function contacted(id){
		window.location = window.location.pathname + "?contacted=" + id;
	}
	</script>
</body>

</html>
