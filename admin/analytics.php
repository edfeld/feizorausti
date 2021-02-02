<?php
require_once("include.php");

$currentPage="analytics";
//View Pages
$pid=1;
$query_getAnalytics = $conCreative->query("SELECT * FROM analytics WHERE analyticID=1");
$row_getAnalytics = $query_getAnalytics->fetch();
$totalRows_getAnalytics=$query_getAnalytics->rowCount();
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
				<h2>Edit Client-Side Code</h2><br>
				Use this to put, for example, third-party analytic code that must be included on every page.
				<form name="form" method="post" enctype="multipart/form-data">
				<input type="hidden" name="analyticID" value="<?php echo $row_getAnalytics['analyticID']; ?>" />
				<br><div><label for="analyticDescription">Insert Code Here
				<em>(remember to include &lt;script&gt; &lt;/script&gt; tags)</em></label> <textarea id="analyticDescription" class="nomce" name="analyticDescription"><?php echo $row_getAnalytics['analyticDescription']; ?></textarea></div>

				<div class="clearboth"></div>

				<div class="marginTop20"><input type="submit" name="editAnalytic" id="editAnalytic" value="Edit Analytic Code" class="btn btn-primary" /></div>
				</form>
			</div>


		</div>
	</div>

	<?php require_once("include_foot.php"); ?>
</body>

</html>
