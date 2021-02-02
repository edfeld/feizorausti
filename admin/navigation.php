<?php
require_once("include.php");

$currentPage="navigation";

if(isset($_GET['action']) && $_GET['action'] == "save"){
	$conCreative->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	try{
		$query_getAllPagesEtc = $conCreative->prepare("SELECT * FROM pages WHERE pageType<>4 ORDER BY pagetabPosition ASC");
		$query_getAllPagesEtc->execute();
		$totalRows_getAllPagesEtc=$query_getAllPagesEtc->rowCount();

		if($totalRows_getAllPagesEtc > 0){
			while($row = $query_getAllPagesEtc->fetch(PDO::FETCH_ASSOC)){
			if($row['pageType'] != 4){
				$pageposnew = $_POST['' . $row['pageID'] . ''];
				$query_updatePage = $conCreative->prepare("UPDATE pages SET pagetabPosition='" . $pageposnew . "' WHERE pageID=" . $row['pageID']);
				$query_updatePage->execute();
			}
			}
		}

		$displayMessage[0] = "<div class='alert fade in'>Navigation order saved successfully.</div>";
	}
	catch(Exception $e){
		echo 'Exception -> ';
		var_dump($e->getMessage());
		$displayMessage[0] = "<div class='alert alert-block alert-error fade in'><h4 class='alert-heading'>Error!</h4> Could not save page order.</div>";
	}
}

$query_getAllPages = $conCreative->prepare("SELECT * FROM pages WHERE pageType<>4 ORDER BY pagetabPosition ASC");
$query_getAllPages->execute();
$totalRows_getAllPages=$query_getAllPages->rowCount();
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

			<h2>Navigation Order</h2>
			Use the below form to change the order of the pages as they appear in the navigation on the top of the website. Pages that belong to other pages are noted, and their page orders refer to their page order within their submenu.
			<br><br>
			<form method="post" action="?action=save">
			<?php
			$counterPages=0;
				if($totalRows_getAllPages > 0){
					while($row = $query_getAllPages->fetch(PDO::FETCH_ASSOC)){
						if($row['pageType'] != 4){
							echo "<label>";
							echo "<input type='text' name='" . $row['pageID'] . "' value='" . $row['pagetabPosition'] . "' style='width: 18px'></input>";
							echo " " . $row['pageTitle'];
							if($row['pageBelongs'] != 0){
								$query_getPage = $conCreative->prepare("SELECT * FROM pages WHERE pageID=" . $row['pageBelongs']);
								$query_getPage->execute();
								$rowPage = $query_getPage->fetch();
								echo " (belongs to <i>" . $rowPage['pageTitle'] . "</i>)";
							}
							echo "<br>";
							echo "</label>";
						}
					}
				}
			?>
			<button type="submit" class="btn"><i class="icon-ok"></i> Save Navigation Order</button>
			</form>
		</div>
	</div>

	<?php require_once("include_foot.php"); ?>
</body>

</html>
