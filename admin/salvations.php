<?php
require_once("include.php");
require_once("../includes/inc_various.php");

$currentPage = "salvations";

$conCreative->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
try {
	//determine set statement
	$setStatement = "";
	$commentID = -1;
	if (isset($_GET['uncontact'])) {
		$setStatement = "contacted=0";
		$salvationID = $_GET['uncontact'];
	}
	if (isset($_GET['contact'])) {
		$setStatement = "contacted=1";
		$salvationID = $_GET['contact'];
	}

	//execute comment actions
	if ($setStatement != "") {
		$query_actSalvations = $conCreative->prepare("UPDATE salvations SET " . $setStatement . " WHERE id=:salvationID");
		$query_actSalvations->bindParam(':salvationID', $salvationID, PDO::PARAM_INT);
		$query_actSalvations->execute();
	}
}
catch (PDOException $e) {
	echo $e->getMessage();
}

//get default filters
if (isset($_POST['submit']) && $_POST['submit'] == 'submit') {
	$contacted = @checkboxToBool($_POST['contacted']);
	$notContacted = @(checkboxToBool($_POST['notContacted']) == 1 ? 0 : 1);

	$searchPhrasePre = $_POST['searchString'];
	$searchPhrase = '%' . $searchPhrasePre . '%';
}
else {
	$contacted = 1;
	$notContacted = 0;

	$searchPhrasePre = '';
	$searchPhrase = '%';
}

//determine custom filter
if (isset($_POST['customFilterType'])) {
	$customFilterType = $_POST['customFilterType'];
	switch ($customFilterType) {
		case "Email Address":
			$queryStringAddon = "AND (email LIKE :searchPhrase)";
			break;
		case "IP Address":
			$queryStringAddon = "AND (ipAddress LIKE :searchPhrase)";
			break;
		default:
			$queryStringAddon = "AND (name LIKE :searchPhrase)";
			break;
	}
}
else {
	$customFilterType = "Name";
	$queryStringAddon = "AND (name LIKE :searchPhrase)";
}

//retrieve comments
$queryString = "SELECT * FROM salvations
				WHERE (contacted=:contacted OR contacted=:notContacted)"
						. $queryStringAddon
						. "ORDER BY id DESC";
try {
	$query_getSalvations = $conCreative->prepare($queryString);
	$query_getSalvations->bindParam(':contacted', $contacted, PDO::PARAM_BOOL);
	$query_getSalvations->bindParam(':notContacted', $notContacted, PDO::PARAM_BOOL);
	$query_getSalvations->bindParam(':searchPhrase', $searchPhrase, PDO::PARAM_STR);
	$query_getSalvations->execute();
}
catch (PDOException $e) {
	echo $e->getMessage();
}


function checkboxToBool($in) {
	if ($in == 'on')
		return 1;
	else
		return 0;
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

		<?php include("include_message.php"); ?>

		<div class="span9 maincontainer shadow">
			<h2>Salvations</h2>
			Here you can explore the people who have either stated that they
			have, or are seeking to, receive Jesus, or who have requested
			discipleship.
			<hr />

			<form id="filterForm" method="post" class="form-inline" role="form" tabindex="-1">
				<div class="form-group">
					<div class="btn-toolbar" style="margin: 0px;">
						<div class="btn-group">
				            <button type="button" tabindex="0" class="btn dropdown-toggle" data-toggle="dropdown"><span id="searchFilterBy"><?php if (isset($customFilterType)  && $searchPhrasePre != '') { echo "$customFilterType"; } else { echo "Filter by..."; } ?></span> <span class="caret"></span></button>
				            <ul class="dropdown-menu">
								<li><a href="#" onclick="changeFilterBy(this);" id='filterByDropdown'>Filter by...</a></li>
								<li class="divider"></li>
								<li><a href="#" onclick="changeFilterBy(this);">Name</a></li>
								<li><a href="#" onclick="changeFilterBy(this);">Email Address</a></li>
								<li><a href="#" onclick="changeFilterBy(this);">IP Address</a></li>
				            </ul>
			          	</div>

						<input type="hidden" name="customFilterType" id="searchFilterByHidden" class="form-control" <?php if (isset($customFilterType)) { echo "value=\"$customFilterType\""; } ?>></input>
						<input type="text" name="searchString" id="searchString" class="form-control" tabindex="1" <?php if (isset($searchPhrasePre)) { echo "value=\"$searchPhrasePre\""; } ?>></input>
						<button type="submit" name="submit" id="filterFormSubmit" value="submit" class="btn form-control" tabindex="2">Go</button>
					</div>
				</div>

				<div class="form-group commentsSearchFilters">
					<label><input type="checkbox" name='contacted' tabindex="3" <?php if ($contacted == 1) { echo "checked"; } ?>> Contacted</label>
					<label><input type="checkbox" name='notContacted' tabindex="4" <?php if ($notContacted == 0) { echo "checked"; } ?>> Not Contacted</label>
				</div>
			</form>

			<?php
			$counter_getSalvations = 0;
			if ($query_getSalvations->rowCount() > 0) {
				if ($searchPhrasePre != '')
					echo "<div class='alert alert-info'>" . $query_getSalvations->rowCount() . " comments were found matching your query (\"$searchPhrasePre\" in $customFilterType).</div>";

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
						<div class='commentTags'>
							<?php if ($row['contacted'] == 1) { echo "<span class='label label-info'>Contacted</span>"; } ?>
						</div>
						<div class="commentButtons btn-toolbar">
							<div class="btn-group">
								<?php
								if ($row['contacted'] == 1) { echo "<button class='btn btn-mini' onclick='uncontactSalvation(" . $row['id'] . ");'>Un-Contact</button>"; }
								else { echo "<button class='btn btn-mini' onclick='contactSalvation(" . $row['id'] . ");'>Contact</button>"; }
								?>
							</div>

							<div class="btn-group">
								<button class="btn btn-mini disabled">Track by...</button>
								<button class="btn btn-mini dropdown-toggle" data-toggle="dropdown">
									<span class="caret"></span>
								</button>
								<ul class="dropdown-menu">
									<li><a href="#" onclick="trackBy('<?php echo $row['name']; ?>', 'Name');">Name</a></li>
									<li><a href="#" onclick="trackBy('<?php echo $row['email']; ?>', 'Email Address');">Email Address</a></li>
									<li><a href="#" onclick="trackBy('<?php echo $row['ipAddress']; ?>', 'IP Address');">IP Address</a></li>
								</ul>
							</div>
						</div>
					</div>

					<?php

					$counter_getSalvations++;
				}
			}
			else
				echo "<div class='alert'>No entries were found matching your query.</div>";
			?>
		</div>
	</div>

	<?php require_once('include_foot.php'); ?>

	<!--custom page JavaScript-->
	<script type="text/javascript">
		function changeFilterBy(optionReference) {
			document.getElementById('searchFilterBy').innerHTML = optionReference.innerHTML;
			document.getElementById('searchFilterByHidden').value = optionReference.innerHTML;
		}

		function uncontactSalvation(id) {
			window.location = window.location.pathname + "?uncontact=" + id;
		}
		function contactSalvation(id) {
			window.location = window.location.pathname + "?contact=" + id;
		}

		function trackBy(meta, type) {
			//fill out the form
			document.getElementById('searchFilterBy').innerHTML = type;
			document.getElementById('searchFilterByHidden').value = type;
			document.getElementById('searchString').value = meta;

			//submit the form
			document.getElementById('filterFormSubmit').click();
		}
	</script>
</body>

</html>
