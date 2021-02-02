<?php
/**
index.php
---------
This is the page that wraps up the home page of the website. Other pages are
wrapped in different pages.
**/

$pagename = "index";
require_once("includes/include.php");

//get the information from the database where page is set as the homepage
$query_getPage = $conCreative->query("SELECT * FROM pages WHERE pageisHome=1");
$row_getPage = $query_getPage->fetch();

//set a pid
$pid = $row_getPage['pagefriendlyURL'];

//we use this to set class 'active' in menu
$currentPage = $row_getPage['pageID'];
?>

<!DOCTYPE html>
<html>

<head>
  <?php include("includes/head.php"); ?>
  <!-- This is where we added scripting to record site visits -- ERE20200720 -->
  <!-- Change file from analytics.php to analytix.php - ERE20210119 -->
  <script type='text/javascript' src='analytics/analytix.php/?action=7' note='This is the Home Page' id='analytics'></script>
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
