<?php
/**
articles.php
------------
This page wraps pages that list articles on the website (article) pages.
**/


$pagename = "articles";
require_once("includes/include.php");

//check if the variable $pid was sent, and clean the string.
//$pid is the variable that contains the friendlyURL we use to get the page from the database.
//if we did not get the $pid redirect to index page to prevent direct access to the page.
if (isset($_GET['pid'])) {
	$pid = $_GET['pid'];
	$pid = stripslashes($pid);
}
else
	header("location:index.php");

//get the page that pagefriendlyURL matches $pid.
$query_getPage = $conCreative->prepare("SELECT * FROM pages WHERE pagefriendlyURL=:pid");
$query_getPage->bindParam(':pid', $pid);
$query_getPage->execute();
$row_getPage = $query_getPage->fetch();
$totalRows_getPage = $query_getPage->rowCount();

//we use this to set class 'active' in menu
if ($row_getPage['pageBelongs'] == 0) {
	$currentPage = $row_getPage['pageID'];
}
else {
	$currentPage = $row_getPage['pageBelongs'];
}

//if the page does not exist redirect to index.php
if ($totalRows_getPage == 0) {
	header("location:index.php");
}

//if visitor selected a month from archive
if (isset($_GET['mid']) && isset($_GET['yid'])) {
	$month = $_GET['mid'];
	$year = $_GET['yid'];

	$dateCompare = "$year-$month-01";

	$pageID = $row_getPage['pageID'];
	$query_getArticles = $conCreative->prepare("SELECT * FROM articles WHERE articleBelongs=:pageID AND ( (DATE_FORMAT(articleDate,'%Y-%m')) = (DATE_FORMAT(:dateCompare,'%Y-%m')) ) ORDER BY articleDate DESC");
	$query_getArticles->bindParam(':pageID', $pageID);
	$query_getArticles->bindParam(':dateCompare', $dateCompare);
	$query_getArticles->execute();


}
else {
	$pageID = $row_getPage['pageID'];
	$query_getArticles = $conCreative->prepare("SELECT * FROM articles WHERE articleBelongs=:pageID ORDER BY articleDate DESC");
	$query_getArticles->bindParam(':pageID', $pageID);
	$query_getArticles->execute();

}
$totalRows_getArticles = $query_getArticles->rowCount();
?>

<!DOCTYPE html>
<html>

<head>
  <?php include("includes/head.php"); ?>
  <!-- This is where we added scripting to record site visits -- ERE20200720 -->
  <!-- Change file from analytics.php to analytix.php - ERE20210119 -->
  <script type='text/javascript' src='analytics/analytix.php/?action=7' note='This is the Articles Page' id='analytics'></script>
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
