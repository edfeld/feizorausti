<?php
/**
subnav.php
----------
This file allows for compatible browsing on mobile devices that do not support
the css :hover call (for example, devices running iOS). If the page that is
currently being viewed has child pages, they will be parsed into links and
displayed.
**/

//get the current page id
$query = $conCreative->prepare("SELECT pageID,pageTitle FROM pages WHERE pagefriendlyURL=:pid");
$query->bindParam(':pid', $pid);
$query->execute();
$fetch = $query->fetch();
$pageID = $fetch[0];
$pageTitle = $fetch[1];

//query pages that belong to the current page
$query = $conCreative->prepare("SELECT * FROM pages WHERE pageBelongs=:currentPageId");
$query->bindParam(':currentPageId', $pageID);
$query->execute();

if($query->rowCount() > 0) {
	echo "<div id=\"rightsidebar\" class=\"rightsidebarnav\">";
	
	$counter = 0;
	while($row = $query->fetch(PDO::FETCH_ASSOC)) {
		switch ($row['pageType']) {
			case "1":
				$subLink = "page.php?pid=" . $row['pagefriendlyURL'];
				break;
			case "2":
				$subLink = "articles.php?pid=" . $row['pagefriendlyURL'];
				break;
			case "4":
				$subLink = $row_getSubPage['pageLink'] . "?pid=" . $row['pagefriendlyURL'];
				break;
		}
		$subLink = $sitepath . "/" . $subLink;
		
		echo "<a href='" . $subLink . "'>" . $row['pageTitle'] . "</a>";
		
		$counter++;
		
		if ($counter < $query->rowCount())
			echo ", ";
	}
	
	echo "</div>";
}
?>