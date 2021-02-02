<?php
/**
carousel.php
------------
This checks if there are banners that should be displayed in a carousel on the
page. If there are none, then create a small amount of space between the
navigation and the content so as to keep the site template consiststent.
**/


//get the banner images to display on the carousel
$query_getBanners = $conCreative->prepare("SELECT * FROM banners ORDER BY bannerID DESC");
$query_getBanners->execute();
$totalRows_getBanners = $query_getBanners->rowCount();

//display the banners
if ($totalRows_getBanners != 0) {
	echo "<div id=\"slider\">";
		echo "<div id=\"Fader\" class=\"fader\">";
			while ($row_getBanners = $query_getBanners->fetch(PDO::FETCH_ASSOC)) {
				echo "<img class=\"slide\" src=\"" . $row_getBanners['bannerImage'] . "\" alt=\"\" />";
			}
			echo "<div class=\"fader_controls\">";
				echo "<div class=\"page prev\" data-target=\"prev\">&lsaquo;</div>";
				echo "<div class=\"page next\" data-target=\"next\">&rsaquo;</div>";
			echo "</div>";
		echo "</div>";
	echo "</div>";
}
else {
	echo "<div id=\"spacer\"></div>";
}
?>
