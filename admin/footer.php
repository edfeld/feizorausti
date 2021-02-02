<?php
require_once("include.php");

$currentPage="footer";

//add new quick-link rows
if(isset($_GET['action']) && $_GET['action'] == "add"){
	$conCreative->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	try{
		$query = $conCreative->prepare("INSERT INTO footer (name, link) VALUES ('', '')");
		$query->execute();
	}
	catch(Exception $e){
		echo 'Exception -> ';
		var_dump($e->getMessage());
		$displayMessage[0] = "<div class='alert alert-block alert-error fade in'><h4 class='alert-heading'>Error!</h4> Could not add new quick-link row.</div>";
	}
}

//delete quick-link rows
if(isset($_GET['delete']) && $_GET['delete'] >= 0){
	$conCreative->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	try{
		$query = $conCreative->prepare("DELETE FROM footer WHERE id=" . $_GET['delete'] . "");
		$query->execute();

		$displayMessage[0] = "<div class='alert fade in'>Quick-link deleted successfully.</div>";
	}
	catch(Exception $e){
		echo 'Exception -> ';
		var_dump($e->getMessage());
		$displayMessage[0] = "<div class='alert alert-block alert-error fade in'><h4 class='alert-heading'>Error!</h4> Could not delete quick-link row.</div>";
	}
}

//save quick-links
if(isset($_GET['action']) && $_GET['action'] == "save"){
	$conCreative->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	try{
		//query database
		$query_getFooter = $conCreative->prepare("SELECT * FROM footer ORDER BY ID ASC");
		$query_getFooter->execute();
		$totalRows_getFooter = $query_getFooter->rowCount();

		//update translations
		$counter = 0;
		if($totalRows_getFooter > 0){
			while($row = $query_getFooter->fetch(PDO::FETCH_ASSOC)){
				$query = $conCreative->prepare("UPDATE footer SET name=?, link=? WHERE id=?");
				$query->execute(array($_POST['name' . $row['id']], $_POST['link' . $row['id']], $row['id']));
			}
		}

		$displayMessage[0] = "<div class='alert fade in'>Quick-links saved successfully.</div>";
	}
	catch(Exception $e){
		echo 'Exception -> ';
		var_dump($e->getMessage());
		$displayMessage[0] = "<div class='alert alert-block alert-error fade in'><h4 class='alert-heading'>Error!</h4> Could not save quick-link settings.</div>";
	}
}

//query database
$query_getFooter = $conCreative->prepare("SELECT * FROM footer ORDER BY ID ASC");
$query_getFooter->execute();
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

			<h2>Footer Quick-Links</h2>
			Add or remove links that will be displayed in the footer of the website under "quick-links".
			<br><br>
			<form method="post" action="?action=save">
				<table class='inOutTable'>
					<tr>
						<td></td>
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td>Name</td>
						<td>Link</td>
						<td class='right-text'><a href='?action=add'>+ Add Quick-Link Row</a></td>
					</tr>
					<?php
					$counter = 0;
						if($query_getFooter->rowCount() > 0){
							while($row = $query_getFooter->fetch(PDO::FETCH_ASSOC)){
								echo "<tr>";
								echo "<td><input type='text' class='normalField' name='name" . $row['id'] . "' value='" . htmlentities($row['name'], ENT_QUOTES) . "'></input></td>";
								echo "<td><input type='text' class='normalField' name='link" . $row['id'] . "' value='" . htmlentities($row['link'], ENT_QUOTES) . "'></input></td>";
								echo "<td class='right-text'><a href='?delete=" . $row['id'] . "'>- Delete</a></td>";
								echo "</tr>";
								$counter++;
							}
						}
						else{
							echo "<tr>";
								echo "<td style='text-align: center;'><i>no quick-links to display</i></td>";
								echo "<td style='text-align: center;'><i>no quick-links to display</i></td>";
								echo "<td></td>";
								echo "</tr>";
						}
					?>
					<tr>
						<td></td>
						<td></td>
						<td class='right-text'><a href='?action=add'>+ Add Quick-Link Row</a></td>
					</tr>
				</table>
			<button type="submit" class="btn"><i class="icon-ok"></i> Save Quick-Links</button>
			</form>
		</div>
	</div>

	<?php include("include_foot.php"); ?>
</body>

</html>
