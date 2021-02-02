<?php 
/**
search.php
----------
The search bar directs the user to this page. It searches page content and
article content inside of the database and displays the results.
**/


//change the page name to be correct
$pagename = "search";

//get and parse search query
$search = cleanInputBasic($_POST['search']);

//display header
?>
<div class="pageDescription">
	<h2><?php echo translate_dom("Search Results for"); ?> <i><?php echo "\"" . $search . "\""; ?></i>...</h2>
<?php

//query the pages database
$query_searchPages = $conCreative->prepare("SELECT * FROM pages WHERE pageDescription LIKE '%" . $search . "%' OR pageTitle LIKE '%" . $search . "%' ORDER BY pageID DESC");
$query_searchPages->execute();
$totalRows_searchPages = $query_searchPages->rowCount();

//query the articles database
$query_searchArticles = $conCreative->prepare("SELECT * FROM articles WHERE articleDescription LIKE '%" . $search . "%' OR articleTitle LIKE '%" . $search . "%' ORDER BY articleDate DESC");
$query_searchArticles->execute();
$totalRows_searchArticles = $query_searchArticles->rowCount();

//output result counts
echo translate_dom($totalRows_searchPages . " pages found // " . $totalRows_searchArticles . " articles found") . "</div>";

//output results
$counterArticles=0;
if($totalRows_searchPages > 0 || $totalRows_searchArticles > 0){
	if($totalRows_searchPages > 0){
		echo "<div class='article' style='padding-bottom: 16px;'>";
		while($row_searchPages = $query_searchPages->fetch(PDO::FETCH_ASSOC)){
			?>
			<a href="<?php echo $sitepath; if($row_searchPages['pageType'] == 1 && $row_searchPages['pagefriendlyURL'] == ""){ ?>/index.php<?php } else  if($row_searchPages['pageType'] == 1){ ?>/page.php<?php }else{ ?>/articles.php<?php } ?>?pid=<?php echo $row_searchPages['pagefriendlyURL']; ?>"><?php echo $row_searchPages['pageTitle']; ?></a><br>
			<?php
			$counterArticles++;
		}
		echo "</div>";
	}
	if($totalRows_searchArticles > 0){
		while($row_searchArticles = $query_searchArticles->fetch(PDO::FETCH_ASSOC)){
			?>
			<div class="article"  style="padding-bottom: 16px;">
				<div class="articleTitle"><a href="<?php echo $sitepath; ?>/single_article.php?pid=<?php echo $pid; ?>&aid=<?php echo $row_searchArticles['articlefriendlyURL']; ?>"><?php echo $row_searchArticles['articleTitle']; ?></a></div>
				<div class="articleDate"><?php echo translate_dom(date("F d, Y",strtotime($row_searchArticles['articleDate']))); ?></div>
				<div class="articleContent">
					<?php echo $row_searchArticles['articleShortDescription']; ?> 
				</div>
			</div>
			<?php
			$counterArticles++;
		}
	}
}
else{
	echo translate_dom("The search returned no results.");
}
?>
