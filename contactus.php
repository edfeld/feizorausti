<?php
//basic page setup
$pagename = "contactus";
require_once("includes/include.php");
?>

<!DOCTYPE html>
<html>

<head>
	<?php
	include("includes/head.php");
  ?>
  <!-- This is where we added scripting to record site visits -- ERE20200922 -->
  <!-- Change file from analytics.php to analytix.php - ERE20210119 -->
  <script type='text/javascript' src='analytics/analytix.php/?action=7' note='ContactUs' id='analytics'></script>
</head>
<body>
	<?php
	include("includes/message.php");
	include("includes/header.php");
	include("includes/carousel.php");
	?>
	<div id="wrapper">
		<?php if ($row_getConfig['sidebarSide'] == 0) { ?>
			<div id="leftsidebar">
				<div id="leftsidebar-inner">
				<?php
				include("includes/sidebar.php");
				?>
				</div>
			</div>
			<div id="rightsidebar">
				<div id="rightsidebar-inner">
				<?php
				include("includes/contact.php");
				?>   
				</div>
			</div>
		<?php } else { ?>
			<div id="leftsidebar">
				<div id="leftsidebar-inner">
				<?php
				include("includes/contact.php");
				?>   
				</div>
			</div>
			<div id="rightsidebar">
				<div id="rightsidebar-inner">
				<?php
				include("includes/sidebar.php");
				?>
				</div>
			</div>
		<?php } ?>
	</div>
	<div class="clearer"></div>
	<?php
	include("includes/footer.php");
	?>
</body>

</html>