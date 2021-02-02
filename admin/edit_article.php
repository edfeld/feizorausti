<?php
require_once("include.php");

$currentPage="articles";
//View Pages

if (!isset($_GET['pid'])) {
	header("location:articles.php");
}else{
	$pid=$_GET['pid'];
}

$query_getArticles = $conCreative->prepare("SELECT * FROM articles WHERE articleID=:pid");
$query_getArticles->bindParam(':pid', $pid, PDO::PARAM_INT);
$query_getArticles->execute();
$row_getArticles=$query_getArticles->fetch();

//Get Pages to put in the Parent Pages List
$query_getMainPages = $conCreative->query("SELECT * FROM pages WHERE pageType=2 ORDER BY pageTitle ASC");
//$row_getMainPages = $query_getMainPages->fetch();
$totalRows_getMainPages=$query_getMainPages->rowCount();
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

			<a href="articles.php" class="btn btn-primary"><i class="icon-arrow-left icon-white"></i> Back</a>
			<div id="addpages">
				<h2>Edit article</h2>
				<form name="form" method="post" enctype="multipart/form-data">
				<input type="hidden" name="articleID" value="<?php echo $row_getArticles['articleID']; ?>" />
				<div><label for="articleTitle">Title</label> <input type="text" name="articleTitle" value="<?php echo $row_getArticles['articleTitle']; ?>" /></div>
				<?php
					$articlelink = $sitepath . "/single_article.php?aid=" . $row_getArticles['articlefriendlyURL'];
				?>
				<br><div>Article URL<br><span style="" id="selectable" onclick="selectText('selectable');"><i><?php echo $articlelink; ?></i></span></div><br>


				<div class="pull-left">
					<label for="articleBelongs">Parent Page</label>
										  <select name="articleBelongs" id="articleBelongs" style="padding:5px;">
											<?php
														while($row_getMainPages = $query_getMainPages->fetch(PDO::FETCH_ASSOC)) {
														?>
														<option <?php if ($row_getArticles['articleBelongs'] == $row_getMainPages['pageID']){ ?>
																		selected="selected" <?php } ?> value="<?php echo $row_getMainPages['pageID']?>"><?php echo $row_getMainPages['pageTitle']?></option>
														<?php
														}
														?>
										  </select>
				</div>
				<div class="pull-left marginLeft10">
					<label for="articleDate">Date</label> <input type="text" name="articleDate" id="datepicker" value="<?php $strdate = date('d-m-Y',strtotime($row_getArticles['articleDate'])); echo $strdate; ?>" placeholder="<?php $strdate = date('d-m-Y',strtotime($row_getArticles['articleDate'])); echo $strdate; ?>">
				</div>
				<div class="clearboth"></div>
				<div class="browseimage">
						<label for="articleImage">Image</label>
						<input type="text" name="articleImage" id="fieldID" style="cursor: pointer;" onclick="openFilemanager(1,'fieldID');" value="<?php echo $row_getArticles['articleImage']; ?>" />
				</div>
				<div><label for="articleShortDescription">Short Description (a short glimpse at or description of what is inside of the article) (minimum 30 words)</label><center><textarea class="nomce" id="articleShortDescription" name="articleShortDescription" style="height:80px;"><?php echo $row_getArticles['articleShortDescription']; ?></textarea></center></div>
				<div class="pull-left articlefriendlyURL">
					Allow Comments?<br>
					<input type="radio" name="allowCommentsButton" id="yes" value="yes" style="width: 16px; height: 16px; display: inline-block; position: relative;"  <?php if($row_getArticles['allowComments'] == 1){ echo "checked"; } ?>><label for="yes">Yes</label>
					<input type="radio" name="allowCommentsButton" id="no" value="no" style="width: 16px; height: 16px; display: inline-block; position: relative;" <?php if($row_getArticles['allowComments'] == 0){ echo "checked"; } ?>><label for="no">No</label>
				</div>
				<div><label for="articleDescription">Article Content</label><center><textarea class="articleDescription" id="articleDescription" name="articleDescription"><?php echo $row_getArticles['articleDescription']; ?></textarea></center></div>

				<div class="clearboth"></div>

				<div class="pull-left articlefriendlyURL">
						<input name="articleOldfriendlyURL" id="articleOldfriendlyURL" type="hidden" value="<?php echo $row_getArticles['articlefriendlyURL']; ?>" />
						<label for="articlefriendlyURL">Friendly URL</label> <input name="articlefriendlyURL" id="articlefriendlyURL" type="text" value="<?php echo $row_getArticles['articlefriendlyURL']; ?>" />
				</div>

				<div class="clearboth"></div>

				<div class="marginTop20"><input type="submit" name="editArticle" id="editArticle" value="Edit Article" class="btn btn-primary" /></div>
				</form>
			</div>

		</div>
	</div>

	<?php require_once("include_foot.php"); ?>
</body>

</html>
