<?php
require_once("include.php");

$currentPage="pages";
//View Pages

if (!isset($_GET['pid'])) {
	header("location:pages.php");
}else{
	$pid=$_GET['pid'];
}

$query_getPages = $conCreative->prepare("SELECT * FROM pages WHERE pageID=:pid");
$query_getPages->bindParam(':pid', $pid, PDO::PARAM_INT);
$query_getPages->execute();
$row_getPages= $query_getPages->fetch();


//Get Pages to put in the Parent Pages List
$query_getMainPages = $conCreative->prepare("SELECT * FROM pages WHERE pageType>0 AND pageType<3 AND pageID!='$pid' ORDER BY pageTitle ASC");
$query_getMainPages->bindParam(':pid', $pid, PDO::PARAM_INT);
$query_getMainPages->execute();

//Check if we have a page assigns as Home page
$query_getHomepage = $conCreative->query("SELECT pageID,pageisHome,pageTitle FROM pages WHERE pageisHome = 1");
$row_getHomepage = $query_getHomepage->fetch();
$totalRows_getHomepage=$query_getHomepage->rowCount();
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
			<a href="pages.php" class="btn btn-primary"><i class="icon-arrow-left icon-white"></i> Back</a>
				<h2>Edit page</h2>
				<form name="form" method="post" enctype="multipart/form-data">
				<input type="hidden" name="pageID" value="<?php echo $row_getPages['pageID']; ?>" />
				<div><label for="pageTitle">Title</label> <input type="text" name="pageTitle" value="<?php echo $row_getPages['pageTitle']; ?>" /></div>
				<?php
					$pagelink = "";
					if($row_getPages['pageisHome'] == 1){
						$pagelink = $sitepath . "/";
					}
					else{
						$pagelink = $sitepath . "/page.php?pid=" . $row_getPages['pagefriendlyURL'];
					}
				?>
				<br><div>Page URL<br><span style="" id="selectable" onclick="selectText('selectable');"><i><?php echo $pagelink; ?></i></span></div><br>
				<br><div><label for="pageDescription">Main Page Content</label><center><textarea id="pageDescription" name="pageDescription"><?php echo $row_getPages['pageDescription']; ?></textarea></center></div>
				<br><div><label for="pageDescription">Sidebar</label><center><textarea id="pageSidebar" name="pageSidebar"><?php echo $row_getPages['pageSidebar']; ?></textarea></center></div>
				<br>

				<div class="marginTop20 marginLeft0 pageoptions">
					<?php if($row_getPages['pageisHome']==1){ ?>
					<div class="pull-left pageisHomechk">
						<!--<input name="pageisHome" id="pageisHome" type="checkbox" checked="checked" /> <label for="pageisHome">--><i class='icon-home'></i> This page is the Home Page<!--</label>-->
					</div>
					<?php } ?>
					<?php /*<div class="pull-left pagetabPosition">
						<input name="pagetabPosition" id="pagetabPosition" type="text" value="<?php echo $row_getPages['pagetabPosition']; ?>" /> <label for="pagetabPosition">Page Order</label>
					</div> */ ?>
					<?php if($row_getPages['pageisHome']!=1){ ?>
					<div class="pull-left pagefriendlyURL">
						<input name="pageOldfriendlyURL" id="pageOldfriendlyURL" type="hidden" value="<?php echo $row_getPages['pagefriendlyURL']; ?>" />
						<label for="pagefriendlyURL">Friendly URL</label> <input name="pagefriendlyURL" id="pagefriendlyURL" type="text" value="<?php echo $row_getPages['pagefriendlyURL']; ?>" />
					</div>
					<?php } ?>
					<div class="clearboth"></div>
				</div>

				<div class="span3 marginTop20 marginLeft0">
					<label for="pageBelongs">Parent Page</label>
										  <select name="pageBelongs" id="pageBelongs" style="padding:5px;">
											<option value="0">(No Parent)</option>
											<?php
												while($row_getMainPages = $query_getMainPages->fetch(PDO::FETCH_ASSOC)) {
												?>
												<option <?php if ($row_getPages['pageBelongs'] == $row_getMainPages['pageID']){ ?>
																selected="selected" <?php } ?> value="<?php echo $row_getMainPages['pageID']?>"><?php echo $row_getMainPages['pageTitle']?></option>
												<?php
												}
												?>
										  </select>
				</div>
				<div class="span3 marginTop20">
					<label for="pageType">Type of Page</label>
										  <select name="pageType" id="pageType" style="padding:5px;">
											<option value="1" <?php if($row_getPages['pageType']==1){ ?> selected="selected" <?php } ?> onclick="javascript:LinkDisappear()">Basic Page</option>
											<option value="2"<?php if($row_getPages['pageType']==2){ ?> selected="selected" <?php } ?> onclick="javascript:LinkDisappear()">Article Page</option>
											<!--<option value="3"<?php if($row_getPages['pageType']==3){ ?> selected="selected" <?php } ?> onclick="javascript:LinkDisappear()">Footer Page</option>-->
											<option value="4"<?php if($row_getPages['pageType']==4){ ?> selected="selected" <?php } ?> onclick="javascript:LinkAppear()">Stub Page (won't appear in main navigation)</option>
										  </select>
				</div>
				<div class="<?php if($row_getPages['pageLink']==""){ ?>pageLinkDiv <?php } ?>clearboth" id="pageLinkDiv">
						  <label for="pageLink">Enter Link</label>
						  <input type="text" value="<?php echo $row_getPages['pageLink']; ?>" name="pageLink" id="pageLink"/>
				</div>
				<div class="pull-left articlefriendlyURL" style="border: none;">
					Show Stream Player/Links in Sidebar?<br>
					<span style="margin-right: 8px;"><input type="radio" name="streamButton" id="yes" value="yes" style="width: 16px; height: 16px; display: inline-block; position: relative;"  <?php if($row_getPages['sidebarRadio'] == 1){ echo "checked"; } ?>><label for="yes">Yes</label></span>
					<span style="margin-left: 8px;"><input type="radio" name="streamButton" id="no" value="no" style="width: 16px; height: 16px; display: inline-block; position: relative;" <?php if($row_getPages['sidebarRadio'] == 0){ echo "checked"; } ?>><label for="no">No</label></span>
				</div>
				<div class="clearboth">
				<button type="button" class="btn" data-toggle="collapse" data-target="#seo"><i class="icon-globe"></i> Manage SEO</button>
				</div>
				<div id="seo" class="collapse">
					<div class="marginTop20"><label for="pageMetaTitle">metaTitle</label> <input type="text" name="pageMetaTitle" value="<?php echo $row_getPages['pageMetaTitle']; ?>" /></div>
					<div><label for="pageMetaDescription">metaDescription</label><center><textarea class="nomce" name="pageMetaDescription"><?php echo $row_getPages['pageMetaDescription']; ?></textarea></center></div>
					<br><div><label for="pageMetaKeywords">metaKeywords</label><center><textarea class="nomce" name="pageMetaKeywords"><?php echo $row_getPages['pageMetaKeywords']; ?></textarea></center></div>
				</div>
				<div class="marginTop20"><input type="submit" name="editPage" id="editPage" value="Edit Page" class="btn btn-primary" /></div>
				</form>
			</div>


		</div>
	</div>

	<?php require_once("include_foot.php"); ?>
</body>

</html>
