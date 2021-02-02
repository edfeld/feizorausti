<?php
// Change History
// ERE20201005 - Ed Einfeld -  Add field CompanyStatsId to the stats table and the input form.

require_once("include.php");

$currentPage="details";
//View Pages
$pid=1;
$query_getDetails = $conCreative->query("SELECT * FROM companydetails WHERE companydetailsID=1");
$row_getDetails = $query_getDetails->fetch();
$totalRows_getDetails=$query_getDetails->rowCount();
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
				<h2>Edit Site Information</h2>
				<form name="form" method="post" enctype="multipart/form-data">
				<input type="hidden" name="companyDetailsID" value="<?php echo $row_getDetails['companyDetailsID']; ?>" />
				<div><label for="companyName">Name</label>  <input type="text" name="companyName" value="<?php echo $row_getDetails['companyName']; ?>" /></div><br>

				<div><label for="companyTelephone">Telephone</label>  <input type="text" name="companyTelephone" value="<?php echo $row_getDetails['companyTelephone']; ?>" /></div><br>

				<div><label for="companyEmail">E-mail</label>  <input type="text" name="companyEmail" value="<?php echo $row_getDetails['companyEmail']; ?>" /></div><br>

        <div><label for="companyAddress">Address</label> <textarea name="companyAddress" class="nomce"><?php echo $row_getDetails['companyAddress']; ?></textarea></div><br>
        
        <!-- ERE20201005 - Add the statistics ID to the input form -->
        <div><label for="companyStatsId">Statistics ID - This value needs to be assigned by the GRO administration team.</label>  

        <input disabled type="text" name="companyStatsId" value="<?php echo $row_getDetails['companyStatsId']; ?>" /></div><br>

				<div class="pull-left articlefriendlyURL">
					<span>
					Display telephone number to public?<br>
					<span style="margin-right: 8px;"><input type="radio" name="teleDisp" id="yes" value="yes" style="width: 16px; height: 16px; display: inline-block; position: relative;"  <?php if($row_getDetails['teleDisp'] == 1){ echo "checked"; } ?>><label for="yes">Yes</label></span>
					<span style="margin-left: 8px;"><input type="radio" name="teleDisp" id="no" value="no" style="width: 16px; height: 16px; display: inline-block; position: relative;" <?php if($row_getDetails['teleDisp'] == 0){ echo "checked"; } ?>><label for="no">No</label></span>
					</span>

					<br><br>

					<span>
					Display email to public?<br>
					<span style="margin-right: 8px;"><input type="radio" name="emailDisp" id="yess" value="yes" style="width: 16px; height: 16px; display: inline-block; position: relative;"  <?php if($row_getDetails['emailDisp'] == 1){ echo "checked"; } ?>><label for="yess">Yes</label></span>
					<span style="margin-left: 8px;"><input type="radio" name="emailDisp" id="noo" value="no" style="width: 16px; height: 16px; display: inline-block; position: relative;" <?php if($row_getDetails['emailDisp'] == 0){ echo "checked"; } ?>><label for="noo">No</label></span>
					</span>

					<br><br>

					<span>
					Display address to public?<br>
					<span style="margin-right: 8px;"><input type="radio" name="addrDisp" id="yesss" value="yes" style="width: 16px; height: 16px; display: inline-block; position: relative;"  <?php if($row_getDetails['addrDisp'] == 1){ echo "checked"; } ?>><label for="yesss">Yes</label></span>
					<span style="margin-left: 8px;"><input type="radio" name="addrDisp" id="nooo" value="no" style="width: 16px; height: 16px; display: inline-block; position: relative;" <?php if($row_getDetails['addrDisp'] == 0){ echo "checked"; } ?>><label for="nooo">No</label></span>
					</span>

				</div>
				<?php
				/*<div class="browseimage">
						<label for="companyImage">Logo</label>
						<!--<input type="text" name="companyImage" value="<?php //echo $row_getDetails['companyImage']; ?>" id="companyImage" /> <input type="button" value="Browse" onclick="openFileBrowser('companyImage');" class="btn" />-->
						<input type="text" onclick="openFilemanager(1,'companyImage');" name="companyImage" id="companyImage" value="<?php echo $row_getDetails['companyImage']; ?>" style="cursor:pointer;" />
				</div>*/
				?>
				<div class="clearboth"></div>

				<div class="marginTop20"><input type="submit" name="editDetails" id="editDetails" value="Edit Information" class="btn btn-primary" /></div>
				</form>
			</div>


		</div>
	</div>

	<?php require_once("include_foot.php"); ?>
</body>

</html>
