<?php
/**
wrapper.php
-----------
This file creates the template for both the sidebar, the main content, as well
as the occasionally subnavigation menu that must appear above the main content.
**/
?>

<div id="wrapper">
	<?php if ($row_getConfig['sidebarSide'] == 0) { ?>
		<div id="leftsidebar">
			<?php include("includes/sidebar.php"); ?>
		</div>

			<?php include("includes/subnav.php"); ?>

		<div id="rightsidebar">
			<?php include("includes/content.php"); ?>
		</div>
	<?php } else { ?>
		<div id="leftsidebar">
			<?php include("includes/content.php"); ?>
		</div>

			<?php include("includes/subnav.php"); ?>

		<div id="rightsidebar">
			<?php include("includes/sidebar.php"); ?>
		</div>
	<?php } ?>

	<div class="clearer"></div>
</div>
