<?php
/**
This page wraps code that displays full articles and allows for comments to be
posted on them if they have been turned on.
**/


$pagename = "single article";
require_once("includes/include.php");

//check if the variable $aid was send, and clean the string.
//$aid is the variable that contains the friendlyURL we use to get the article from the database.
//if we did not get the $aid redirect to index page to prevent direct access to the page.
if (isset($_GET['aid'])) {
	$aid = $_GET['aid'];
	$aid = stripslashes($aid);
	$aid = preg_replace("/[^-A-Za-z0-9]/","",$aid);
}
else {
	//header("location:index.php");
}

//retrieve the article
$query_getArticle = $conCreative->prepare("SELECT * FROM articles WHERE articlefriendlyURL=:aid");
$query_getArticle->bindParam(':aid', $aid);
$query_getArticle->execute();
$row_getArticle = $query_getArticle->fetch();
$totalRows_getArticle = $query_getArticle->rowCount();

//retrieve the comments [LH]
$query_getComments = $conCreative->prepare("SELECT * FROM comments WHERE articleId=:aid AND isDeleted=0 AND isApproved=1");
$query_getComments->bindParam(':aid', $aid);
$query_getComments->execute();
$totalRows_getComments = $query_getComments->rowCount();

//check if the variable $pid was sent, and clean the string.
//$pid is the variable that contains the friendlyURL we use to get the page from the database.
//if we did not get the $pid redirect to index page to prevent direct access to the page.
if (isset($_GET['pid'])) {
	$pid = $_GET['pid'];
	$pid = stripslashes($pid);
	$pid = preg_replace("/[^-A-Za-z0-9]/","",$pid);
}
else{
	//header("location:index.php");
}

//retrieve the page
$query_getPage = $conCreative->prepare("SELECT * FROM pages WHERE pagefriendlyURL=:pid");
$query_getPage->bindParam(':pid', $pid);
$query_getPage->execute();
$row_getPage = $query_getPage->fetch();
$totalRows_getPage = $query_getPage->rowCount();


//we use this to set class 'active' in menu
if ($row_getPage['pageBelongs'] == 0) {
	$currentPage=$row_getPage['pageID'];
}
else {
	$currentPage=$row_getPage['pageBelongs'];
}

//if the page does not exist, redirect to index.php
if ($totalRows_getArticle == 0) {
	header("location:index.php");
}
?>

<!DOCTYPE html>
<html>

<head>
  <?php include("includes/head.php"); ?>
  <!-- This is where we added scripting to record site visits -- ERE20200922 -->
  <!-- Change file from analytics.php to analytix.php - ERE20210119 -->
  <script type='text/javascript' src='analytics/analytix.php/?action=7' note='Single Article Page' id='analytics'></script>
</head>

<body>
	<?php
	include("includes/message.php");
	include("includes/header.php");
	include("includes/carousel.php");
	include("includes/wrapper.php");
	include("includes/footer.php");
	?>
</body>

</html>
