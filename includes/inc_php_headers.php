<?php
/*
inc_php_headers.php
-------------------
This contains the code that must go above <head> HTML DOM elements on various
pages. It mostly contains page-specific database queries.
*/


if($pagename == "index"){
	//get the information from the database where page is set as the homepage
	$query_getPage = $conCreative->query("SELECT * FROM pages WHERE pageisHome=1");
	$row_getPage = $query_getPage->fetch();

	//set a pid
	$pid = $row_getPage['pagefriendlyURL'];

	//we use this to set class 'active' in menu
	$currentPage=$row_getPage['pageID'];

	//get the banner images to display on the carousel
	$query_getBanners = $conCreative->prepare("SELECT * FROM banners ORDER BY bannerID DESC");
	$query_getBanners->execute();
	$totalRows_getBanners=$query_getBanners->rowCount();

	//get the banners to generate the bullet points
	$query_getBanners1 = $conCreative->prepare("SELECT * FROM banners ORDER BY bannerID DESC");
	$query_getBanners1->execute();
	$totalRows_getBanners1=$query_getBanners1->rowCount();
}
else if($pagename == "articles"){
	//check if the variable $pid was send, and clean the string.
	//$pid is the variable that contains the friendlyURL we use to get the page from the database.
	//if we did not get the $pid redirect to index page to prevent direct access to the page.
	if (isset($_GET['pid'])){
		$pid = $_GET['pid'];
		$pid = stripslashes($pid);
	}else{header("location:index.php");}

		//Get the page that pagefriendlyURL matches $pid.
		$query_getPage = $conCreative->prepare("SELECT * FROM pages WHERE pagefriendlyURL=:pid");
		$query_getPage->bindParam(':pid', $pid);
		$query_getPage->execute();
		$row_getPage = $query_getPage->fetch();
		$totalRows_getPage = $query_getPage->rowCount();
		
		//We use this to set class 'active' in menu
		if($row_getPage['pageBelongs']==0){
			$currentPage=$row_getPage['pageID'];
		}else{
			$currentPage=$row_getPage['pageBelongs'];
		}

		//If the page does not exist redirect to index.php
		if ($totalRows_getPage==0){
			header("location:index.php");
		}
		
		
		//If visitor selected a month from archive
		if(isset($_GET['mid']) && isset($_GET['yid']))
		{
			$month=$_GET['mid'];
			$year=$_GET['yid'];
			
			$dateCompare="$year-$month-01";
			
			$pageID=$row_getPage['pageID'];
			$query_getArticles = $conCreative->prepare("SELECT * FROM articles WHERE articleBelongs=:pageID AND ( (DATE_FORMAT(articleDate,'%Y-%m')) = (DATE_FORMAT(:dateCompare,'%Y-%m')) ) ORDER BY articleDate DESC");
			$query_getArticles->bindParam(':pageID', $pageID);
			$query_getArticles->bindParam(':dateCompare', $dateCompare);
			$query_getArticles->execute();
			
		
		} else{
			
			$pageID=$row_getPage['pageID'];
			$query_getArticles = $conCreative->prepare("SELECT * FROM articles WHERE articleBelongs=:pageID ORDER BY articleDate DESC");
			$query_getArticles->bindParam(':pageID', $pageID);
			$query_getArticles->execute();
			
		}
		 $totalRows_getArticles = $query_getArticles->rowCount();
}
else if($pagename == "page"){
	//check if the variable $pid was send, and clean the string.
	//$pid is the variable that contains the friendlyURL we use to get the page from the database.
	//if we did not get the $pid redirect to index page to prevent direct access to the page.
	if (isset($_GET['pid'])){
		$pid = $_GET['pid'];
		$pid = stripslashes($pid);
	}else{header("location:index.php");}

		$query_getPage = $conCreative->prepare("SELECT * FROM pages WHERE pagefriendlyURL=:pid");
		$query_getPage->bindParam(':pid', $pid);
		$query_getPage->execute();
		$row_getPage = $query_getPage->fetch();
		$totalRows_getPage = $query_getPage->rowCount();

	//We use this to set class 'active' in menu
		if($row_getPage['pageBelongs']==0){
			$currentPage=$row_getPage['pageID'];
		}else{
			$currentPage=$row_getPage['pageBelongs'];
		}

	//If the page does not exist redirect to index.php
	if ($totalRows_getPage==0){
		//header("location:index.php");
	}
}
else if($pagename == "single article"){
	//check if the variable $aid was send, and clean the string.
	//$aid is the variable that contains the friendlyURL we use to get the article from the database.
	//if we did not get the $aid redirect to index page to prevent direct access to the page.
	if (isset($_GET['aid'])){
		$aid = $_GET['aid'];
		$aid = stripslashes($aid);
		$aid = preg_replace("/[^-A-Za-z0-9]/","",$aid); 
	}
	else{
		//header("location:index.php");
	}

	//retrieve the article
	$query_getArticle = $conCreative->prepare("SELECT * FROM articles WHERE articlefriendlyURL=:aid");
	$query_getArticle->bindParam(':aid', $aid);
	$query_getArticle->execute();
	$row_getArticle = $query_getArticle->fetch();
	$totalRows_getArticle = $query_getArticle->rowCount();

	//retrieve the comments [LH]
	$query_getComments = $conCreative->prepare("SELECT * FROM comments WHERE articleId=:aid AND isDeleted=0 AND isApproved=1");
	$query_getComments->bindParam(':aid', $aid);
	$query_getComments->execute();
	$totalRows_getComments = $query_getComments->rowCount();

	//check if the variable $pid was send, and clean the string.
	//$pid is the variable that contains the friendlyURL we use to get the page from the database.
	//if we did not get the $pid redirect to index page to prevent direct access to the page.
	if (isset($_GET['pid'])){
		$pid = $_GET['pid'];
		$pid = stripslashes($pid);
		$pid = preg_replace("/[^-A-Za-z0-9]/","",$pid); 
	}
	else{
		//header("location:index.php");
	}

	//retrieve the page
	$query_getPage = $conCreative->prepare("SELECT * FROM pages WHERE pagefriendlyURL=:pid");
	$query_getPage->bindParam(':pid', $pid);
	$query_getPage->execute();
	$row_getPage = $query_getPage->fetch();
	$totalRows_getPage = $query_getPage->rowCount();


	//we use this to set class 'active' in menu
		if($row_getPage['pageBelongs']==0){
			$currentPage=$row_getPage['pageID'];
		}
		else{
			$currentPage=$row_getPage['pageBelongs'];
		}

	//if the page does not exist, redirect to index.php
	if ($totalRows_getArticle==0){
		header("location:index.php");
	}
}
?>