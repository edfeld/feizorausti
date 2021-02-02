<?php
/**
sidebar.php
-----------
This displays the radio player, as well as any user generated content for the
sidebar that is stored in the database.
**/
?>

<?php
if ($row_getConfig['sidebarStream'] == 1) {
	if ((isset($row_getPage['sidebarRadio']) && $row_getPage['sidebarRadio'] == 1) || $pagename == "meetjesus") {
?>
	<div id="player">
		<?php include("player.php"); ?>
	</div>
<?php
	}
}
?>

<?php
if ($pagename != "search" && $pagename != "meetjesus") {
?>
	<?php
		//display archive links for article pages
		if ($pagename == "articles" || $pagename == "single article") {
			echo "<div id=\"leftsidebar-inner\">";
			echo translate_dom("<h2>Article Archive</h2>");
			$query_getArticleMonths = $conCreative->prepare("SELECT * FROM articles GROUP BY (DATE_FORMAT(articleDate,'%Y-%m')) ORDER BY articleID DESC");
			$query_getArticleMonths->bindParam(':filter', $filter);
			$query_getArticleMonths->execute();
			$totalRows_getArticleMonths= $query_getArticleMonths->rowCount();

			if ($totalRows_getArticleMonths > 0) {
				while ($row_getArticleMonths = $query_getArticleMonths->fetch(PDO::FETCH_ASSOC)) {
					echo "<a href=\"" . $sitepath . "/articles.php?pid=" . $pid . "&mid=" . date('m',strtotime($row_getArticleMonths['articleDate'])) . "&yid=" . date('Y',strtotime($row_getArticleMonths ['articleDate'])) . "\">" . translate_dom(strftime("%B %Y", strtotime($row_getArticleMonths ['articleDate']))) . "</a><br>";
				}
			}
			echo "</div>";
		}
        else if ($pagename == "contactus") {
            echo "<div id=\"leftsidebar-inner\">";
			echo translate_dom("<h2>Contact Us</h2>");
            echo translate_dom("Use this form to send us an email. Let us know what you are looking for, and we will get back to you as soon as possible.");
            echo "</div>";
        }

		//display the author-generated lower sidebar content
		if (strlen($row_getPage['pageSidebar']) > 0) {
			echo "<div id=\"leftsidebar-inner\">";
			echo $row_getPage['pageSidebar'];
			echo "</div>";
		}
	?>
<?php
}
?>
