<?php
/**
 * This script outputs the main content for every type of page according to the
 * pagename variable that is set on the pages in the main directory.
 */


//index page
if ($pagename == "index") {
	if (isset($_GET['aid'])) {
		$pageHierarchy =  " / " . $row_getArticle['articleTitle'];
	}
	else {
		$pageHierarchy = "";
	}
	echo "<h2 class=\"pageTitle\">" . $row_getPage['pageTitle'] . $pageHierarchy . "</h2>";
	echo $row_getPage['pageDescription'];
}

//article pages
if ($pagename == "articles") {
	echo "<div class=\"pageDescription\">";
		if (isset($_GET['aid'])) {
			$pageHierarchy = " / " . $row_getArticle['articleTitle'];
		}
		else {
			$pageHierarchy = "";
		}
		echo "<h2 class=\"pageTitle\">" . $row_getPage['pageTitle'] . $pageHierarchy . "</h2>";
		if ($row_getPage['pageDescription'] != "") {
			echo $row_getPage['pageDescription'];
		}
	echo "</div>";

	$counterArticles = 0;

	//pagination config
	$strt = 0;
	if (isset($_GET['strt']))
		$strt = $_GET['strt'];
	$cnt = 5;
	if (isset($_GET['cnt']) && $_GET['cnt'] != 0)
		$cnt = $_GET['cnt'];
	$finish = ($strt + $cnt);
	$pages = ceil(($totalRows_getArticles / $cnt));
	$page = floor(($strt / $cnt) + 1);

	if ($totalRows_getArticles > 0) {
		//pagination nav
		echo "<div class='pagination articlePagination' id='paginationTop' style='text-align: right'>";
		if ($strt > 0)
			echo "<a href='articles.php?pid=" . $pid . "&cnt=" . $cnt . "&strt=" . ($strt - $cnt) . "'><< </a>";
		echo translate_dom(" Page  " . $page . " / " . $pages . " ");
		if ($finish < $totalRows_getArticles)
			echo "<a href='articles.php?pid=" . $pid . "&cnt=" . $cnt . "&strt=" . $finish . "'> >></a>";
		echo "</div><br><br>";

		while ($row_getArticles = $query_getArticles->fetch(PDO::FETCH_ASSOC)) {
			if ($counterArticles >= $strt && $counterArticles < $finish) {
				echo "<div class=\"article\">";
					echo "<div class=\"articleTitle\">" . $row_getArticles['articleTitle'] . "</div>";
					echo "<div class=\"articleDate\">" . translate_dom(strftime("%d %B %Y", strtotime($row_getArticles['articleDate']))) . "</div>";
					echo "<div class=\"articleContent\">";
						if ($row_getArticles['articleImage'] != "" && $row_getArticles['articleImage'] != null) {
							echo "<div class=\"articleImageHolder\" style=\"position: relative; float: left; margin-right: 8px; margin-bottom: 8px;\">";
								echo "<img src=\"" . $row_getArticles['articleImage'] . "\" alt=\"" . $row_getArticles['articleTitle'] . "\" class=\"articleImage\" style=\"max-width: 180px;\">";
							echo "</div>";
						}
						echo $row_getArticles['articleShortDescription'];
						echo "<div><a href=\"" . $sitepath . "/single_article.php?pid=" . $pid . "&aid=" . $row_getArticles['articlefriendlyURL'] . "\" class=\"articleReadMore\">" . translate_dom("Read More...") . "</a></div>";
						echo "<div style=\"clear: both;\"></div>";
					echo "</div>";
				echo "</div>";
			}
			$counterArticles++;
		}

		//pagination nav
		echo "<div class='pagination' id='paginationBottom' style='text-align: right'>";
		if ($strt > 0)
			echo "<a href='articles.php?pid=" . $pid . "&cnt=" . $cnt . "&strt=" . ($strt - $cnt) . "'><< </a>";
		echo translate_dom(" Page  " . $page . " / " . $pages . " ");
		if ($finish < $totalRows_getArticles)
			echo "<a href='articles.php?pid=" . $pid . "&cnt=" . $cnt . "&strt=" . $finish . "'> >></a>";
		echo "</div>";
	}
	else {
		echo "There are no articles to display here.";
	}
}

//single articles
if ($pagename == "single article") {
	include("sarticle.php");
}

//basic and stub pages
if ($pagename == "page") {
	if ($_GET['pid'] == "search") {
		include("search.php");
	}
	else {
		if (isset($_GET['aid'])) {
			$pageHierarchy = " / " . $row_getArticle['articleTitle'];
		}
		else {
			$pageHierarchy = "";
		}

		$prevButtonHTML = ($totalRows_getPrevPage > 0) ? "<a href=\"page.php?pid={$row_getPrevPage['pagefriendlyURL']}\" class=\"pageLink\"><i class=\"fa fa-chevron-left\"></i> {$row_getPrevPage['pageTitle']}</a>" : "";
		$nextButtonHTML = ($totalRows_getNextPage > 0) ? "<a href=\"page.php?pid={$row_getNextPage['pagefriendlyURL']}\" class=\"pageLink\">{$row_getNextPage['pageTitle']} <i class=\"fa fa-chevron-right\"></i></a>" : "";
		$dividerHTML = ($prevButtonHTML != "" && $nextButtonHTML != "") ? " / " : "";

		echo "<div class=\"pageHeading\"><div class=\"pageTitle\">{$row_getPage['pageTitle']}{$pageHierarchy}</div>{$prevButtonHTML}{$dividerHTML}{$nextButtonHTML}</div>";
		echo $row_getPage['pageDescription'];
	}
}
