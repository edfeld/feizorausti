<?php
/**
page.php
--------
This page wraps all normal, static (basic and stub) pages on the website.
**/


$pagename = "page";
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

$query_getPage = $conCreative->prepare("SELECT * FROM pages WHERE pagefriendlyURL=:pid");
$query_getPage->bindParam(':pid', $pid);
$query_getPage->execute();
$row_getPage = $query_getPage->fetch();
$totalRows_getPage = $query_getPage->rowCount();

//get info on the previous and next pages
$bible = new BibleLinkedList;
$biblePrevPid = $bible->getPrevChapter($pid);
$bibleNextPid = $bible->getNextChapter($pid);

$query_getPrevPage = $conCreative->prepare("SELECT pagefriendlyURL, pageTitle FROM pages " .
										   "WHERE pagefriendlyURL=:pid AND pageType=4");
$query_getPrevPage->bindValue(':pid', $biblePrevPid);
$query_getPrevPage->execute();
$row_getPrevPage = $query_getPrevPage->fetch();
$totalRows_getPrevPage = $query_getPrevPage->rowCount();

$query_getNextPage = $conCreative->prepare("SELECT pagefriendlyURL, pageTitle FROM pages " .
										   "WHERE pagefriendlyURL=:pid AND pageType=4");
$query_getNextPage->bindValue(':pid', $bibleNextPid);
$query_getNextPage->execute();
$row_getNextPage = $query_getNextPage->fetch();
$totalRows_getNextPage = $query_getNextPage->rowCount();

//we use this to set class 'active' in menu
if ($row_getPage['pageBelongs'] == 0) {
	$currentPage = $row_getPage['pageID'];
}
else {
	$currentPage = $row_getPage['pageBelongs'];
}

//if the page does not exist, redirect to index.php
if ($totalRows_getPage == 0) {
	//header("location:index.php");
}
?>

<!DOCTYPE html>
<html>

<head>
  <?php include("includes/head.php"); ?>
  <!-- This is where we added scripting to record site visits -- ERE20200803 -->
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
