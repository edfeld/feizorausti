<?php
/**
footer.php
----------
This is the footer section of the website and should be included on every page.
It spits out, in proper markup for the website's design, a snippet from the
latest article in the database, the quick links in the database, a copyright
line, and links to any specific footer pages that have been created.
**/


//get content for footers
$query_getFooter = $conCreative->query("SELECT * FROM footer ORDER BY id DESC");

//get pages for footer
$query_getFooterPage = $conCreative->query("SELECT * FROM pages WHERE pageType=3");
$totalRows_getFooterPage=$query_getFooterPage->rowCount();

//get analytic code
$query_getAnalytics = $conCreative->query("SELECT * FROM analytics WHERE analyticID = 1");
$row_getAnalytics = $query_getAnalytics->fetch();
?>


<div id="footer">
	<div id="footer-inner">
		<div id="footer-left">
			<?php
			//display a snippet from the latest article
			echo translate_dom("<br><strong>Latest Article...</strong><br><br>");
			$query_latestArticle = $conCreative->query("SELECT * FROM articles ORDER BY articleID DESC LIMIT 1;");
			$row_latestArticle = $query_latestArticle->fetch();

			$query_latestArticlePID = $conCreative->query("SELECT pagefriendlyURL FROM pages WHERE pageID = " . $row_latestArticle['articleBelongs']);
			$row_latestArticlePID = $query_latestArticlePID->fetch();

			echo "<em>" . $row_latestArticle['articleTitle'] . "</em><br>";
			echo $row_latestArticle['articleShortDescription'] . "<br>";
			echo "<a href=\"" . $sitepath . "/single_article.php?pid=" . $row_latestArticlePID['pagefriendlyURL'] . "&aid=" . $row_latestArticle['articlefriendlyURL'] . "\" class=\"articleReadMore\">" . translate_dom("Read More...") . "</a>";

			//display contact information
			if ($row_getDetails['teleDisp'] == 1 || $row_getDetails['emailDisp'] == 1 || $row_getDetails['addrDisp'] == 1) {
				echo translate_dom("<br><br><strong>Contact Information</strong><br><br>");
			}
			if ($row_getDetails['teleDisp'] == 1) {
				echo translate_dom("Phone: ");
				echo "<em>" . $row_getDetails['companyTelephone'] . "</em><br>";
			}
			if ($row_getDetails['emailDisp'] == 1) {
				echo translate_dom("Email: ") . "<em><a href=\"mailto:" . $row_getDetails['companyEmail'] . "\">" . $row_getDetails['companyEmail'] . "</a></em><br>";
			}
			if ($row_getDetails['addrDisp'] == 1) {
				echo translate_dom("Address:");
				echo "<br>";
				echo "<div id=\"footer-address\">";
				echo "<i>" . nl2br($row_getDetails['companyAddress']) . "</i>";
				echo "</div>";
			}
			?>
		</div>

		<div id="footer-right">
			<?php
			//display the quick links if there are any
			echo translate_dom("<br><strong>Quick Links...</strong><br><br>");
			if ($query_getFooter->rowCount() > 0) {
				while ($row = $query_getFooter->fetch(PDO::FETCH_ASSOC)) {
					echo "<a href=\"" . $row['link'] . "\">" . $row['name'] . "</a><br>";
				}
			}
			else {
				echo translate_dom("<em>There are no quick-links saved.</em>");
			}
			?>
		</div>

		<div id="footer-copyright">
			<?php
			//display the CMS version
			echo "<!--" . translate_dom("<div>$versionString</div>") . "-->";

			//display the copyright
			echo translate_dom("<div>&copy; 2014-" . date("Y") . " " . $row_getDetails['companyName'] . "</div>");

			//display links to the footer pages
			if($totalRows_getFooterPage>0) {
				echo "<div id='footer-links'>";
				while ($row_getFooterPage = $query_getFooterPage->fetch(PDO::FETCH_ASSOC)) {
					echo "<a href=\"page.php?pid=" . $row_getFooterPage['pagefriendlyURL'] . "\" class=\"footerPageLink\"/>" . $row_getFooterPage['pageTitle'] . "</a>";
				}
				echo "</div>";
			}
			?>
		</div>
		<div class="clearer"></div>
	</div>
</div>

<?php
//spit out any third-party analytic code that is in the database
echo $row_getAnalytics['analyticDescription'];
?>

<!--other scripts-->
<script src="js/slider1.js" type="text/javascript"></script>
<script src="js/slider2.js" type="text/javascript"></script>

<!--radio stream; do not put any javascript after these-->
<script src="js/popupplayer.js" type="text/javascript"></script>

