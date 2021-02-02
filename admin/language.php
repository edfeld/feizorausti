<?php
require_once("include.php");
$currentPage="language";

//add new translation rows
if(isset($_GET['action']) && $_GET['action'] == "add"){
	$conCreative->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	try{
		$query = $conCreative->prepare("INSERT INTO translations (translate, translation) VALUES ('', '')");
		$query->execute();

		//$displayMessage[0] = "<div class='alert fade in'>New translation row added.</div>";
	}
	catch(Exception $e){
		echo 'Exception -> ';
		var_dump($e->getMessage());
		$displayMessage[0] = "<div class='alert alert-block alert-error fade in'><h4 class='alert-heading'>Error!</h4> Could not add new translation row.</div>";
	}
}

//delete translation rows
if(isset($_GET['delete']) && $_GET['delete'] >= 0){
	$conCreative->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	try{
		$query = $conCreative->prepare("DELETE FROM translations WHERE ID=" . $_GET['delete'] . "");
		$query->execute();

		$displayMessage[0] = "<div class='alert fade in'>Translation deleted successfully.</div>";
	}
	catch(Exception $e){
		echo 'Exception -> ';
		var_dump($e->getMessage());
		$displayMessage[0] = "<div class='alert alert-block alert-error fade in'><h4 class='alert-heading'>Error!</h4> Could not delete translation row.</div>";
	}
}

//save translations and locale
if(isset($_POST['locale'])){
	$conCreative->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	try{
		//update locale
		$query = $conCreative->prepare("UPDATE configurations SET locale='" . $_POST['locale'] . "' WHERE configID=1");
		$query->execute();

		//query database
		$query_getTranslations = $conCreative->prepare("SELECT * FROM translations ORDER BY ID ASC");
		$query_getTranslations->execute();
		$totalRows_getTranslations=$query_getTranslations->rowCount();

		//update translations
		$counter = 0;
		if($totalRows_getTranslations > 0){
			while($row = $query_getTranslations->fetch(PDO::FETCH_ASSOC)){
				$query = $conCreative->prepare("UPDATE translations SET translate=?, translation=? WHERE ID=?");
				$query->execute(array($_POST['translate' . $row['ID']], $_POST['translation' . $row['ID']], $row['ID']));
			}
		}

		$displayMessage[0] = "<div class='alert fade in'>Language translation settings saved successfully.</div>";
	}
	catch(Exception $e){
		echo 'Exception -> ';
		var_dump($e->getMessage());
		$displayMessage[0] = "<div class='alert alert-block alert-error fade in'><h4 class='alert-heading'>Error!</h4> Could not save language translation settings.</div>";
	}
}

//query database
$query_getTranslations = $conCreative->prepare("SELECT * FROM translations ORDER BY ID ASC");
$query_getTranslations->execute();
$totalRows_getTranslations=$query_getTranslations->rowCount();

$query_getConfig = $conCreative->query("SELECT * FROM configurations WHERE configID=1");
$row_getConfig = $query_getConfig->fetch();
$totalRows_getConfig=$query_getConfig->rowCount();
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

			<h2>Language Translation</h2>
			Create custom word and number translations below. Use this primarily to localize months, days, years, numbers, and so on.
			<br><br>
			<form method="post" action="?action=save">
			<!--locale input (using Windows Server 2012 locales)-->
			<div style='display: none;'><label>Locale: <input type='text' name='locale' value='<?php echo $row_getConfig['locale']; ?>'></input><i>(locales from <a href='http://msdn.microsoft.com/en-us/library/39cwe7zf%28v=vs.90%29.aspx'>this list</a> and <a href='http://msdn.microsoft.com/en-us/library/cdax410z%28v=vs.90%29.aspx'>this list</a> are supported)</i></label></div>

				Custom Translations:
				<table class='inOutTable'>
					<tr>
						<td></td>
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td>Translate</td>
						<td>Translation</td>
						<td class='right-text'><a href='?action=add'>+ Add Translation Row</a></td>
					</tr>
					<?php
					$counter = 0;
						if($totalRows_getTranslations > 0){
							while($row = $query_getTranslations->fetch(PDO::FETCH_ASSOC)){
								$isLocked = $row['locked'];
								if ($isLocked == 1)
									$isReadonly = "readonly";
								else
									$isReadonly = "";
								echo "<tr>";
								echo "<td><input type='text' class='normalField' name='translate" . $row['ID'] . "' value='" . htmlentities($row['translate'], ENT_QUOTES) . "' " . $isReadonly . "></input></td>";
								echo "<td><input type='text' class='formattedField' name='translation" . $row['ID'] . "' value='" . htmlentities($row['translation'], ENT_QUOTES) . "'></input></td>";
								if ($isLocked == 0)
									echo "<td class='right-text'><a href='?delete=" . $row['ID'] . "'>- Delete</a></td>";
								else
									echo "<td></td>";
								echo "</tr>";
								$counter++;
							}
						}
						else{
							echo "<tr>";
								echo "<td><i>no translates to display</i></td>";
								echo "<td><i>no translations to display</i></td>";
								echo "<td></td>";
								echo "</tr>";
						}
					?>
					<tr>
						<td></td>
						<td></td>
						<td class='right-text'><a href='?action=add'>+ Add Translation Row</a></td>
					</tr>
				</table>
			<button type="submit" class="btn"><i class="icon-ok"></i> Save Language Settings</button>
			</form>
		</div>
	</div>

	<?php require_once("include_foot.php"); ?>
</body>

</html>
