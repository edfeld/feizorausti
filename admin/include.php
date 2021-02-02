<?php
// Change History
// ERE20201117 - Ed Einfeld - comment out $aff_currentUser code
//initialize the session
if (!isset($_SESSION)) {
	session_set_cookie_params(0, dirname($_SERVER["REQUEST_URI"]));
	session_start();
}

//connect to database
require_once("../config/connection.php");

//set the version veriables
include("../version.php");

//check if the user session id declared
// $aff_currentUser = $_SESSION['snail_user_id'];  // I commented this out. $aff_currentUser is not used anywhere that I can see. - ERE20201117
if (!isset($_SESSION['snail_user_id'])) {
	header("Location: index.php");
}

//logout the user if necessary
$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")) {
	$logoutAction .= "&" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) && ($_GET['doLogout'] == "true")) {
	//clear the session varialbles to fully log out a visitor
	$_SESSION['MM_Username'] = NULL;
	$_SESSION['PrevUrl'] = NULL;
	$_SESSION['name']= NULL;
	$_SESSION['snail_user_id']= NULL;

	unset($_SESSION['MM_Username']);
	unset($_SESSION['name']);
	unset($_SESSION['snail_user_id']);
	unset($_SESSION['PrevUrl']);

	header("Location: index.php");
}

//get site config
$query_getConfig = $conCreative->query("SELECT * FROM configurations
										WHERE configID=1");
$row_getConfig = $query_getConfig->fetch();
$totalRows_getConfig=$query_getConfig->rowCount();

//get site details
$query_getDetails = $conCreative->query("SELECT * FROM companydetails
										 WHERE companyDetailsID = 1");
$row_getDetails = $query_getDetails->fetch();
$totalRows_getDetails=$query_getDetails->rowCount();

//set the base site path to the correct web address
$sitehost = "http://" . $_SERVER['HTTP_HOST'];
$sitedir    = $row_getConfig['siteDir'];
$sitepath = $sitehost . $sitedir;

//set the message display value as an array to push messages later
$displayMessage = array();

//get the current time to use this on friendlyURL
$currentTime = time();


/**
 * add new page
 */
if (array_key_exists('addPage', $_POST)) {
	$expected = array('pageTitle', 'pageDescription', 'pageBelongs','pageType', 'pageMetaTitle', 'pageMetaDescription', 'pageMetaKeywords', 'pageisHome','pageLink','pageSidebar');

	if($_POST['streamButton'] == "yes")
		$showstream = 1;
	else
		$showstream = 0;

	foreach ($_POST as $key => $value)
	{
		//assign to temporary variable and strip whitespace if not an array
		$temp = is_array($value) ? $value : trim($value);

		//otherwise, assign to a variable of the same name as $key
		if (in_array($key, $expected))
			${$key} = $temp;
	}

	$pageTitle = addslashes($pageTitle);

	if ($pageisHome == "") {
		$pageisHome=0;
	} else{
		$pageisHome=1;
	}

	//create a friendly URL based on the title
	$url_searches = array('&agrave;', '&eacute;'); //URL unfriendly characters
	$url_replacements = array('a', 'e'); //URL friendly characters
	$pagefriendlyURL = str_replace($url_searches, $url_replacements, $pageTitle);
	$pagefriendlyURL = preg_replace("/[^A-Za-z0-9 -]/", "", trim($pagefriendlyURL));
	$pagefriendlyURL = str_replace(array(" ", "_"), '-', $pagefriendlyURL); //replace spaces and underscores with dashes

	//check if we have a page with the same friendlyYRL
	$query_getSamefriendlyURL = $conCreative->prepare("SELECT pagefriendlyURL FROM pages WHERE pagefriendlyURL=:pagefriendlyURL");
	$query_getSamefriendlyURL->bindParam(':pagefriendlyURL', $pagefriendlyURL, PDO::PARAM_INT);
	$query_getSamefriendlyURL->execute();

	if ($totalRows_getSamefriendlyURL!=0) {
		$pagefriendlyURL= strtolower($pagefriendlyURL)."-".$currentTime;
	}
	else {
		$pagefriendlyURL= strtolower($pagefriendlyURL);
	}


	//get the next available position
	$query_getMaxTab = $conCreative->query("SELECT pagetabPosition
											FROM pages
											ORDER BY pagetabPosition DESC
											LIMIT 0,1");
	$row_getMaxTab = $query_getMaxTab->fetch();
	$totalRows_getMaxTab = $query_getMaxTab->rowCount();

	$pagetabPositionMax = $row_getMaxTab['pagetabPosition'];

	if ($pageBelongs=="0") {
		$pagetabPosition = $pagetabPositionMax + 1;
	}
	else {
		$pagetabPosition = 0;
	}
	$pageDescription = stripslashes($pageDescription);
	$addPagesQuery = $conCreative->prepare("INSERT INTO pages (pageTitle,pageDescription,pageisHome,pageBelongs,hiddenPage,pageType,pageLink,pagefriendlyURL,pageMetaTitle,pageMetaDescription,pageMetaKeywords,pagetabPosition,pageSidebar,sidebarRadio) VALUES (:pageTitle,:pageDescription,:pageisHome,:pageBelongs,1,:pageType,:pageLink,:pagefriendlyURL,:pageMetaTitle,:pageMetaDescription,:pageMetaKeywords,:pagetabPosition,:pageSidebar,:sidebarRadio)");
			$addPagesQuery->bindParam(':pageTitle', $pageTitle, PDO::PARAM_STR);
			$addPagesQuery->bindParam(':pageDescription', $pageDescription, PDO::PARAM_STR);
			$addPagesQuery->bindParam(':pageisHome', $pageisHome, PDO::PARAM_INT);
			$addPagesQuery->bindParam(':pageBelongs', $pageBelongs, PDO::PARAM_INT);
			$addPagesQuery->bindParam(':pageType', $pageType, PDO::PARAM_INT);
			$addPagesQuery->bindParam(':pageLink', $pageLink, PDO::PARAM_STR);
			$addPagesQuery->bindParam(':pagefriendlyURL', $pagefriendlyURL, PDO::PARAM_STR);
			$addPagesQuery->bindParam(':pageMetaTitle', $pageMetaTitle, PDO::PARAM_STR);
			$addPagesQuery->bindParam(':pageMetaDescription', $pageMetaDescription, PDO::PARAM_STR);
			$addPagesQuery->bindParam(':pageMetaKeywords', $pageMetaKeywords, PDO::PARAM_STR);
			$addPagesQuery->bindParam(':pagetabPosition', $pagetabPosition, PDO::PARAM_INT);
			$addPagesQuery->bindParam(':pageSidebar', $pageSidebar, PDO::PARAM_STR);
			$addPagesQuery->bindParam(':sidebarRadio', $showstream, PDO::PARAM_BOOL);

	if ($addPagesQuery->execute()) {
		//display the success message
		array_push($displayMessage,"<div class='alert fade in'><button type='button' class='close' data-dismiss='alert'>&times;</button>Page created Successfully</div>");
    }
    else {
		//display the warning message
		array_push($displayMessage,"<div class='alert alert-block alert-error fade in'><button type='button' class='close' data-dismiss='alert'>&times;</button><h4 class='alert-heading'>Warning!</h4> Could not create Page</div>");
	}
}


/**
 * edit page
 */
if (array_key_exists('editPage', $_POST)) {
	$expected = array('pageID','pageTitle', 'pageDescription', 'pageBelongs','pageType', 'pageMetaTitle', 'pageMetaDescription', 'pageMetaKeywords','pagetabPosition','pageOldfriendlyURL','pagefriendlyURL','pageLink','pageSidebar');

	if ($_POST['streamButton'] == "yes")
		$showstream = 1;
	else
		$showstream = 0;

	foreach ($_POST as $key => $value) {
		//assign to temporary variable and strip whitespace if not an array
		$temp = is_array($value) ? $value : trim($value);

		//otherwise, assign to a variable of the same name as $key
		if (in_array($key, $expected))
			${$key} = $temp;
	}

	$pageTitle = addslashes($pageTitle);

	if ($pageOldfriendlyURL != $pagefriendlyURL) { //check if friendly URL changed
		$url_searches = array('&agrave;', '&eacute;'); //URL unfriendly characters
		$url_replacements = array('a', 'e'); //URL friendly characters
		$pagefriendlyURL = str_replace($url_searches, $url_replacements, $pagefriendlyURL);
		$pagefriendlyURL = preg_replace("/[^A-Za-z0-9 -]/", "", trim($pagefriendlyURL));
		$pagefriendlyURL = str_replace(array(" ", "_"), '-', $pagefriendlyURL); //replace spaces and underscores with dashes

		//check if we have a page with the same friendlyYRL
		$query_getSamefriendlyURL = $conCreative->prepare("SELECT pagefriendlyURL
														   FROM pages
														   WHERE pagefriendlyURL = :pagefriendlyURL");
		$query_getSamefriendlyURL->bindParam(':pagefriendlyURL', $pagefriendlyURL, PDO::PARAM_INT);
		$query_getSamefriendlyURL->execute();
		$totalRows_getSamefriendlyURL = $query_getSamefriendlyURL->rowCount();

		if ($totalRows_getSamefriendlyURL != 0) {
			$pagefriendlyURL = strtolower($pagefriendlyURL)."-".$currentTime;
		}
		else{
			$pagefriendlyURL = strtolower($pagefriendlyURL);
		}
	}


	$pageDescription = stripslashes($pageDescription);
	$updatePageQuery = $conCreative->prepare("UPDATE pages
											  SET pageTitle=:pageTitle,pageDescription=:pageDescription,pageBelongs=:pageBelongs,pageType=:pageType,pageLink=:pageLink,pageMetaTitle=:pageMetaTitle,pageMetaDescription=:pageMetaDescription,pageMetaKeywords=:pageMetaKeywords,pagetabPosition=:pagetabPosition,pagefriendlyURL=:pagefriendlyURL,pageSidebar=:pageSidebar,sidebarRadio=:sidebarRadio
											  WHERE pageID=:pageID");
	$updatePageQuery->bindParam(':pageTitle', $pageTitle, PDO::PARAM_STR);
	$updatePageQuery->bindParam(':pageDescription', $pageDescription, PDO::PARAM_STR);
	$updatePageQuery->bindParam(':pageBelongs', $pageBelongs, PDO::PARAM_INT);
	$updatePageQuery->bindParam(':pageType', $pageType, PDO::PARAM_INT);
	$updatePageQuery->bindParam(':pageLink', $pageLink, PDO::PARAM_STR);
	if ($pagefriendlyURL == null) //fix "pagefriendlyURL can not except null" error when editing home page
		$pagefriendlyURL = "";
	$updatePageQuery->bindParam(':pagefriendlyURL', $pagefriendlyURL, PDO::PARAM_STR);
	$updatePageQuery->bindParam(':pageMetaTitle', $pageMetaTitle, PDO::PARAM_STR);
	$updatePageQuery->bindParam(':pageMetaDescription', $pageMetaDescription, PDO::PARAM_STR);
	$updatePageQuery->bindParam(':pageMetaKeywords', $pageMetaKeywords, PDO::PARAM_STR);
	$updatePageQuery->bindParam(':pagetabPosition', $pagetabPosition, PDO::PARAM_INT);
	$updatePageQuery->bindParam(':pageID', $pageID, PDO::PARAM_INT);
	$updatePageQuery->bindParam(':pageSidebar', $pageSidebar, PDO::PARAM_STR);
	$updatePageQuery->bindParam(':sidebarRadio', $showstream, PDO::PARAM_BOOL);

	try {
		if ($updatePageQuery->execute()) {
			// display the success message
			array_push($displayMessage,"<div class='alert fade in'><button type='button' class='close' data-dismiss='alert'>&times;</button>Page updated Successfully</div>");
		}
		else {
			//display the warning message
			array_push($displayMessage,"<div class='alert alert-block alert-error fade in'><button type='button' class='close' data-dismiss='alert'>&times;</button><h4 class='alert-heading'>Warning!</h4> Could not edit Page</div>");
		}
	}
	catch(Exception $e) {
		echo 'Exception -> ';
		var_dump($e->getMessage());
	}
}


/**
 * delete pages
 */
if (array_key_exists('deletePage', $_POST)) {
	$selectedPages = $_POST['selectPage'];

	foreach ($selectedPages as $value) {
		$currentCheckedID = $value;

		//check if page is parent
		$query_getCheckParent = $conCreative->prepare("SELECT * FROM pages
													   WHERE pageBelongs =:currentCheckedID");
		$query_getCheckParent->bindParam(':currentCheckedID', $currentCheckedID, PDO::PARAM_INT);
		$query_getCheckParent->execute();
		$totalRows_getCheckParent = $query_getCheckParent->rowCount();

		if ($totalRows_getCheckParent == 0) {
			$query_DeleteNewPer = $conCreative->prepare("DELETE FROM pages
														 WHERE pageID=:currentCheckedID");
			$query_DeleteNewPer->bindParam(':currentCheckedID', $currentCheckedID, PDO::PARAM_INT);
			$query_DeleteNewPer->execute();

			if ($query_DeleteNewPer) {
			//display the success message
				array_push($displayMessage, "<div class='alert fade in'><button type='button' class='close' data-dismiss='alert'>&times;</button>Page deleted Successfully</div>");
			}
			else {
			//display the warning message
				array_push($displayMessage, "<div class='alert alert-block alert-error fade in'><button type='button' class='close' data-dismiss='alert'>&times;</button><h4 class='alert-heading'>Warning!</h4> Could not delete Page</div>");
			}

		}
		else {
			//display the warning message
			array_push($displayMessage, "<div class='alert alert-block alert-error fade in'><button type='button' class='close' data-dismiss='alert'>&times;</button><h4 class='alert-heading'>Warning!</h4> Could not delete Page. Page is Parent page. Please delete all child pages first</div>");
		}
	}
}


/**
 * add new article
 */
if(array_key_exists('addArticle', $_POST)) {
	$expected = array('articleTitle', 'articleDescription', 'articleShortDescription', 'articleBelongs','articleImage','allowCommentsButton');

	foreach ($_POST as $key => $value) {
		//assign to temporary variable and strip whitespace if not an array
		$temp = is_array($value) ? $value : trim($value);

		//otherwise, assign to a variable of the same name as $key
		if (in_array($key, $expected))
			${$key} = $temp;
	}

	if ($_POST['articleDate'] == "") {
		$articleDate=date( 'Y-m-d H:i:s', time());
	}
	else {
		$articleDate = date('Y-m-d', strtotime($_POST['articleDate']));
	}

	$articleTitle = addslashes($articleTitle);

	//create a friendly URL based on the Title
	$url_searches = array('&agrave;', '&eacute;'); //URL unfriendly characters
	$url_replacements = array('a', 'e'); //URL friendly characters
	$articlefriendlyURL = str_replace($url_searches, $url_replacements, $articleTitle);
	$articlefriendlyURL = preg_replace("/[^A-Za-z0-9 -]/", "", trim($articlefriendlyURL));
	$articlefriendlyURL = str_replace(array(" ", "_"), '-', $articlefriendlyURL); //replace spaces and underscores with dashes

	//check if we have an article with the same friendlyYRL
	$query_getSamefriendlyURL = $conCreative->prepare("SELECT articlefriendlyURL FROM articles WHERE articlefriendlyURL=:articlefriendlyURL");
	$query_getSamefriendlyURL->bindParam(':articlefriendlyURL', $articlefriendlyURL, PDO::PARAM_STR);
	$query_getSamefriendlyURL->execute();
	$totalRows_getSamefriendlyURL=$query_getSamefriendlyURL->rowCount();

	if ($totalRows_getSamefriendlyURL != 0) {
		$articlefriendlyURL= strtolower($articlefriendlyURL) . "-" . $currentTime;
	}
	else {
		$articlefriendlyURL = strtolower($articlefriendlyURL);
	}


	$articleDescription = stripslashes($articleDescription);

	//convert radio buttons/checkboxes to booleans
	if ($_POST['allowCommentsButton'] == "yes")
		$allowComments = 1;
	else
		$allowComments = 0;

	$addArticle = $conCreative->prepare("INSERT INTO articles (articleTitle,articleDescription,articleShortDescription,articlefriendlyURL,articleBelongs,articleDate,articleImage,allowComments)
										 VALUES (:articleTitle,:articleDescription,:articleShortDescription,:articlefriendlyURL,:articleBelongs,:articleDate,:articleImage,:allowComments)");
	$addArticle->bindParam(':articleTitle', $articleTitle, PDO::PARAM_STR);
	$addArticle->bindParam(':articleDescription', $articleDescription, PDO::PARAM_STR);
	$addArticle->bindParam(':articleShortDescription', $articleShortDescription, PDO::PARAM_STR);
	$addArticle->bindParam(':articlefriendlyURL', $articlefriendlyURL, PDO::PARAM_STR);
	$addArticle->bindParam(':articleBelongs', $articleBelongs, PDO::PARAM_INT);
	$addArticle->bindParam(':articleDate', $articleDate, PDO::PARAM_STR);
	$addArticle->bindParam(':articleImage', $articleImage, PDO::PARAM_STR);
	$addArticle->bindParam(':allowComments', $allowComments, PDO::PARAM_BOOL);

	if ($addArticle->execute()) {
	//display the success message
		array_push($displayMessage,"<div class='alert fade in'><button type='button' class='close' data-dismiss='alert'>&times;</button>Article created Successfully</div>");
	}
	else {
	//display the warning message
		array_push($displayMessage,"<div class='alert alert-block alert-error fade in'><button type='button' class='close' data-dismiss='alert'>&times;</button><h4 class='alert-heading'>Warning!</h4> Could not create Article</div>");
	}
}


/**
 * edit article
 */
if (array_key_exists('editArticle', $_POST)) {
	$expected = array('articleID','articleTitle', 'articleDescription','articleShortDescription', 'articleBelongs','articleDate','articleImage','articleOldfriendlyURL','articlefriendlyURL','allowCommentsButton');

	foreach ($_POST as $key => $value) {
		//assign to temporary variable and strip whitespace if not an array
		$temp = is_array($value) ? $value : trim($value);

		//otherwise, assign to a variable of the same name as $key
		if (in_array($key, $expected))
			${$key} = $temp;
	}

	$articleTitle = addslashes($articleTitle);
	$articleDate = date('Y-m-d',strtotime($_POST['articleDate']));

	if ($articleOldfriendlyURL!=$articlefriendlyURL) { //check if friendly URL changed
		$url_searches = array('&agrave;', '&eacute;'); //URL unfriendly characters
		$url_replacements = array('a', 'e'); //URL friendly characters
		$articlefriendlyURL = str_replace($url_searches, $url_replacements, $articlefriendlyURL);
		$articlefriendlyURL = preg_replace("/[^A-Za-z0-9 -]/", "", trim($articlefriendlyURL));
		$articlefriendlyURL = str_replace(array(" ", "_"), '-', $articlefriendlyURL); //replace spaces and underscores with dashes

		//check if we have an article with the same friendlyYRL
		$query_getSamefriendlyURL = $conCreative->prepare("SELECT articlefriendlyURL FROM articles WHERE articlefriendlyURL=:articlefriendlyURL");
		$query_getSamefriendlyURL->bindParam(':articlefriendlyURL', $articlefriendlyURL, PDO::PARAM_STR);
		$query_getSamefriendlyURL->execute();
		$totalRows_getSamefriendlyURL=$query_getSamefriendlyURL->rowCount();

		if ($totalRows_getSamefriendlyURL != 0) {
			$articlefriendlyURL= strtolower($articlefriendlyURL)."-".$currentTime;
		}
		else{
			$articlefriendlyURL= strtolower($articlefriendlyURL);
		}
	}


	//convert radio buttons/checkboxes to booleans
	if ($_POST['allowCommentsButton'] == "yes")
		$allowComments = 1;
	else
		$allowComments = 0;

	$articleDescription = stripslashes($articleDescription);
	$updateArticleQuery = $conCreative->prepare("UPDATE articles
												 SET articleTitle=:articleTitle,articleDescription=:articleDescription,articleShortDescription=:articleShortDescription,articlefriendlyURL=:articlefriendlyURL,articleBelongs=:articleBelongs,articleDate=:articleDate,articleImage=:articleImage,allowComments=:allowComments
												 WHERE articleID=:articleID");
	$updateArticleQuery->bindParam(':articleTitle', $articleTitle, PDO::PARAM_STR);
	$updateArticleQuery->bindParam(':articleDescription', $articleDescription, PDO::PARAM_STR);
	$updateArticleQuery->bindParam(':articleShortDescription', $articleShortDescription, PDO::PARAM_STR);
	$updateArticleQuery->bindParam(':articlefriendlyURL', $articlefriendlyURL, PDO::PARAM_STR);
	$updateArticleQuery->bindParam(':articleBelongs', $articleBelongs, PDO::PARAM_INT);
	$updateArticleQuery->bindParam(':articleDate', $articleDate, PDO::PARAM_STR);
	$updateArticleQuery->bindParam(':articleImage', $articleImage, PDO::PARAM_STR);
	$updateArticleQuery->bindParam(':articleID', $articleID, PDO::PARAM_INT);
	$updateArticleQuery->bindParam(':allowComments', $allowComments, PDO::PARAM_BOOL);

	if ($updateArticleQuery->execute()) {
		//display the success message
		array_push($displayMessage,"<div class='alert fade in'><button type='button' class='close' data-dismiss='alert'>&times;</button>Article updated Successfully</div>");
	}
	else {
		//display the warning message
		array_push($displayMessage,"<div class='alert alert-block alert-error fade in'><button type='button' class='close' data-dismiss='alert'>&times;</button><h4 class='alert-heading'>Warning!</h4> Could not edit Article</div>");
	}
}


/**
 * delete articles
 */
if (array_key_exists('deleteArticle', $_POST)) {
	$selectedArticles=$_POST['selectArticle'];

	foreach ($selectedArticles as  $value) {
		$currentCheckedID = $value;

		$query_DeleteArticle = $conCreative->prepare("DELETE FROM articles
													  WHERE articleID=:currentCheckedID");
		$query_DeleteArticle->bindParam(':currentCheckedID', $currentCheckedID, PDO::PARAM_INT);
		$query_DeleteArticle->execute();

		if ($query_DeleteArticle->execute()) {
		//display the success message
			array_push($displayMessage,"<div class='alert fade in'><button type='button' class='close' data-dismiss='alert'>&times;</button>Article deleted Successfully</div>");
		}
		else {
			//display the warning message
			array_push($displayMessage,"<div class='alert alert-block alert-error fade in'><button type='button' class='close' data-dismiss='alert'>&times;</button><h4 class='alert-heading'>Warning!</h4> Could not delete Article</div>");
		}
	}
}


/**
 * add new banner
 */
if (array_key_exists('addBanner', $_POST)) {
	$expected = array('bannerTitle', 'bannerImage');

	foreach ($_POST as $key => $value) {
		//assign to temporary variable and strip whitespace if not an array
		$temp = is_array($value) ? $value : trim($value);

		//otherwise, assign to a variable of the same name as $key
		if (in_array($key, $expected))
			${$key} = $temp;
	}


	$bannerTitle = addslashes($bannerTitle);

		$addBannerQuery = $conCreative->prepare("INSERT INTO banners (bannerTitle,bannerImage)
												 VALUES (:bannerTitle,:bannerImage)");
		$addBannerQuery->bindParam(':bannerTitle', $bannerTitle);
		$addBannerQuery->bindParam(':bannerImage', $bannerImage);

		if ($addBannerQuery->execute()) {
			//display the success message
			array_push($displayMessage,"<div class='alert fade in'><button type='button' class='close' data-dismiss='alert'>&times;</button>Banner added Successfully</div>");
		}
		else {
			//display the warning message
			array_push($displayMessage,"<div class='alert alert-block alert-error fade in'><button type='button' class='close' data-dismiss='alert'>&times;</button><h4 class='alert-heading'>Warning!</h4> Could not create Banner</div>");
		}
}


/**
 * delete banners
 */
if(array_key_exists('deleteBanner', $_POST)) {
	$selectBanner=$_POST['selectBanner'];

	foreach ($selectBanner as  $value) {
		$currentCheckedID = $value;

		$updateBanner = $conCreative->prepare("DELETE FROM banners
											   WHERE bannerID=:currentCheckedID");
		$updateBanner->bindParam(':currentCheckedID', $currentCheckedID, PDO::PARAM_STR);

		if ($updateBanner->execute()) {
		//display the success message
			array_push($displayMessage,"<div class='alert fade in'><button type='button' class='close' data-dismiss='alert'>&times;</button>BAnner deleted Successfully</div>");
		}
		else {
		//display the warning message
			array_push($displayMessage,"<div class='alert alert-block alert-error fade in'><button type='button' class='close' data-dismiss='alert'>&times;</button><h4 class='alert-heading'>Warning!</h4> Could not delete Banner</div>");
		}
	}
}


/**
 * add new user
 */
if (array_key_exists('addUser', $_POST)) {
	$expected = array('name', 'username', 'password');

	foreach ($_POST as $key => $value) {
		//assign to temporary variable and strip whitespace if not an array
		$temp = is_array($value) ? $value : trim($value);

		//otherwise, assign to a variable of the same name as $key
		if (in_array($key, $expected))
			${$key} = $temp;
	}

	$name = addslashes($name);
	$username = addslashes($username);
	$password = md5($password);
	$password = sha1($password);
	$password = crypt($password,fx);

	$addUserQuery = $conCreative->prepare("INSERT INTO users (name,username,password)
										   VALUES (:name,:username,:password)");
	$addUserQuery->bindParam(':name', $name, PDO::PARAM_INT);
	$addUserQuery->bindParam(':username', $username, PDO::PARAM_INT);
	$addUserQuery->bindParam(':password', $password, PDO::PARAM_INT);

	if ($addUserQuery->execute()) {
		//display the success message
		array_push($displayMessage,"<div class='alert fade in'><button type='button' class='close' data-dismiss='alert'>&times;</button>User created Successfully</div>");
	}
	else {
		//display the warning message
		array_push($displayMessage,"<div class='alert alert-block alert-error fade in'><button type='button' class='close' data-dismiss='alert'>&times;</button><h4 class='alert-heading'>Warning!</h4> Could not create User</div>");
	}
}


/**
 * edit user
 */
if (array_key_exists('editUser', $_POST)) {
	$expected = array('name','username', 'password', 'userID');

	foreach ($_POST as $key => $value) {
		//assign to temporary variable and strip whitespace if not an array
		$temp = is_array($value) ? $value : trim($value);

		//otherwise, assign to a variable of the same name as $key
		if (in_array($key, $expected))
			${$key} = $temp;
	}

	$name = addslashes($name);
	$username = addslashes($username);

	if ($password != "") {
		$password = md5($password);
		$password = sha1($password);
		$password = crypt($password,fx);
		$updateUserQuery = $conCreative->prepare("UPDATE users
												  SET name=:name,username=:username,password=:password
												  WHERE userID=:userID");
		$updateUserQuery->bindParam(':password', $password, PDO::PARAM_STR);
	}
	else {
		$updateUserQuery = $conCreative->prepare("UPDATE users
												  SET name=:name,username=:username
												  WHERE userID=:userID");
	}
	$updateUserQuery->bindParam(':name', $name, PDO::PARAM_STR);
	$updateUserQuery->bindParam(':username', $username, PDO::PARAM_STR);
	$updateUserQuery->bindParam(':userID', $userID, PDO::PARAM_INT);

	if ($updateUserQuery->execute()) {
		//display the success message
		array_push($displayMessage,"<div class='alert fade in'><button type='button' class='close' data-dismiss='alert'>&times;</button>User updated Successfully</div>");
	}
	else {
		//display the warning message
		array_push($displayMessage,"<div class='alert alert-block alert-error fade in'><button type='button' class='close' data-dismiss='alert'>&times;</button><h4 class='alert-heading'>Warning!</h4> Could not edit User</div>");
	}
}


/**
 * delete users
 */
if (array_key_exists('deleteUser', $_POST)) {
	$selectedUsers = $_POST['selectUser'];

	foreach ($selectedUsers as  $value) {
		$currentCheckedID=$value;

		$updateUser = $conCreative->prepare("DELETE FROM users WHERE userID=:currentCheckedID");
		$updateUser->bindParam(':currentCheckedID', $currentCheckedID, PDO::PARAM_STR);

		if ($updateUser->execute()) {
			//display the success message
			array_push($displayMessage,"<div class='alert fade in'><button type='button' class='close' data-dismiss='alert'>&times;</button>User deleted Successfully</div>");
		}
		else {
			//display the warning message
			array_push($displayMessage,"<div class='alert alert-block alert-error fade in'><button type='button' class='close' data-dismiss='alert'>&times;</button><h4 class='alert-heading'>Warning!</h4> Could not delete User</div>");
		}
	}
}


/**
 * edit analytic
 */
if (array_key_exists('editAnalytic', $_POST)) {
	$expected = array('analyticDescription','analyticID');

	foreach ($_POST as $key => $value) {
		//assign to temporary variable and strip whitespace if not an array
		$temp = is_array($value) ? $value : trim($value);

		//otherwise, assign to a variable of the same name as $key
		if (in_array($key, $expected))
			${$key} = $temp;
	}
	$updateAnalytic = $conCreative->prepare("UPDATE analytics
											 SET analyticDescription=:analyticDescription
											 WHERE analyticID=:analyticID");
	$updateAnalytic->bindParam(':analyticDescription', $analyticDescription, PDO::PARAM_STR);
	$updateAnalytic->bindParam(':analyticID', $analyticID, PDO::PARAM_STR);

	if ($updateAnalytic->execute()) {
	//display the success message
		array_push($displayMessage,"<div class='alert fade in'><button type='button' class='close' data-dismiss='alert'>&times;</button>Analytic Code updated Successfully</div>");
	}
	else {
	//display the warning message
		array_push($displayMessage,"<div class='alert alert-block alert-error fade in'><button type='button' class='close' data-dismiss='alert'>&times;</button><h4 class='alert-heading'>Warning!</h4> Could not edit Analytic Code</div>");
	}
}


/**
 * edit details
 */
if (array_key_exists('editDetails', $_POST)) {
	$expected = array('companyName','companyTelephone','companyEmail','companyAddress','companyDetailsID','companyImage','teleDisp','emailDisp','addrDisp');

	foreach ($_POST as $key => $value) {
		//assign to temporary variable and strip whitespace if not an array
		$temp = is_array($value) ? $value : trim($value);

		//otherwise, assign to a variable of the same name as $key
		if (in_array($key, $expected))
			${$key} = $temp;
	}

	//convert radio buttons/checkboxes to boolean
	if ($_POST['teleDisp'] == "yes")
		$td = 1;
	else
		$td = 0;

	if ($_POST['emailDisp'] == "yes")
		$ed = 1;
	else
		$ed = 0;

	if ($_POST['addrDisp'] == "yes")
		$ad = 1;
	else
		$ad = 0;

	$updateDetailsQuery = $conCreative->prepare("UPDATE companydetails
												 SET companyName=:companyName,companyTelephone=:companyTelephone,companyEmail=:companyEmail,companyAddress=:companyAddress,companyImage=:companyImage,teleDisp=:teleDisp,emailDisp=:emailDisp,addrDisp=:addrDisp
												 WHERE companyDetailsID=:companyDetailsID");
	$updateDetailsQuery->bindParam(':companyName', $companyName, PDO::PARAM_STR);
	$updateDetailsQuery->bindParam(':companyTelephone', $companyTelephone, PDO::PARAM_STR);
	$updateDetailsQuery->bindParam(':companyEmail', $companyEmail, PDO::PARAM_STR);
	$updateDetailsQuery->bindParam(':companyAddress', $companyAddress, PDO::PARAM_STR);
	$updateDetailsQuery->bindParam(':companyImage', $companyImage, PDO::PARAM_STR);
	$updateDetailsQuery->bindParam(':companyDetailsID', $companyDetailsID, PDO::PARAM_INT);
	$updateDetailsQuery->bindParam(':teleDisp', $td, PDO::PARAM_BOOL);
	$updateDetailsQuery->bindParam(':emailDisp', $ed, PDO::PARAM_BOOL);
	$updateDetailsQuery->bindParam(':addrDisp', $ad, PDO::PARAM_BOOL);

	if ($updateDetailsQuery->execute()) {
		//display the success message
		array_push($displayMessage,"<div class='alert fade in'><button type='button' class='close' data-dismiss='alert'>&times;</button>Information updated Successfully</div>");
	}
	else {
		//display the warning message
		array_push($displayMessage,"<div class='alert alert-block alert-error fade in'><button type='button' class='close' data-dismiss='alert'>&times;</button><h4 class='alert-heading'>Warning!</h4> Could not edit Information</div>");
	}
}


/**
 * edit config
 */
if (array_key_exists('editConfig', $_POST)) {
	$expected = array('configID','primaryColor','secondaryColor','linkHoverColor','bodyBackgroundColor','bodyBackgroundImage','customCSS','streamButton','streamLink','embeddedLink','sidebarStyleButton','radioName','programName','albumName','siteSub','mjAudio','mjAudioSource','mjAudioIntro','mjAudioCaption','mjAudioRecieveJesus','mjAudioRecieveJesusResult','mjAudioRequestDiscipleship','mjAudioRequestDiscipleshipResult');

	foreach ($_POST as $key => $value) {
		//assign to temporary variable and strip whitespace if not an array
		$temp = is_array($value) ? $value : trim($value);

		//otherwise, assign to a variable of the same name as $key
		if (in_array($key, $expected))
			${$key} = $temp;
	}

	try {
		//convert radio buttons/checkboxes to boolean
		/*if ($_POST['sidebarStyleButton'] == "left")
			$sidebar = 0;
		else
			$sidebar = 1;*/
		$sidebar = 0;

		if ($_POST['streamButton'] == "yes")
			$showstream = 1;
		else
			$showstream = 0;

		if ($_POST['mjAudio'] == "yes")
			$mjAudio = 1;
		else
			$mjAudio = 0;

		$updateConfigQuery = $conCreative->prepare("UPDATE configurations
													SET primaryColor=:primaryColor,secondaryColor=:secondaryColor,linkHoverColor=:linkHoverColor,bodyBackgroundColor=:bodyBackgroundColor,bodyBackgroundImage=:bodyBackgroundImage,customCSS=:customCSS,streamLink=:streamLink,embeddedLink=:embeddedLink,sidebarStream=:streamButton,sidebarSide=:sidebarSide,radioName=:radioName,albumName=:albumName,programName=:programName,siteDir=:siteSub,mjAudio=:mjAudio,mjAudioSource=:mjAudioSource,mjAudioIntro=:mjAudioIntro,mjAudioCaption=:mjAudioCaption,mjAudioRecieveJesus=:mjAudioRecieveJesus,mjAudioRecieveJesusResult=:mjAudioRecieveJesusResult,mjAudioRequestDiscipleship=:mjAudioRequestDiscipleship,mjAudioRequestDiscipleshipResult=:mjAudioRequestDiscipleshipResult
													WHERE configID=:configID");
		$updateConfigQuery->bindParam(':configID', $configID, PDO::PARAM_INT);
		$updateConfigQuery->bindParam(':primaryColor', $primaryColor, PDO::PARAM_STR);
		$updateConfigQuery->bindParam(':secondaryColor', $secondaryColor, PDO::PARAM_STR);
		$updateConfigQuery->bindParam(':linkHoverColor', $linkHoverColor, PDO::PARAM_STR);
		$updateConfigQuery->bindParam(':bodyBackgroundColor', $bodyBackgroundColor, PDO::PARAM_STR);
		$updateConfigQuery->bindParam(':bodyBackgroundImage', $bodyBackgroundImage, PDO::PARAM_STR);
		$updateConfigQuery->bindParam(':customCSS', $customCSS, PDO::PARAM_STR);
		$updateConfigQuery->bindParam(':streamButton', $showstream, PDO::PARAM_BOOL);
		$updateConfigQuery->bindParam(':streamLink', $streamLink, PDO::PARAM_STR);
		$updateConfigQuery->bindParam(':embeddedLink', $embeddedLink, PDO::PARAM_STR);
		$updateConfigQuery->bindParam(':sidebarSide', $sidebar, PDO::PARAM_BOOL);
		$updateConfigQuery->bindParam(':radioName', $radioName, PDO::PARAM_STR);
		$updateConfigQuery->bindParam(':albumName', $albumName, PDO::PARAM_STR);
		$updateConfigQuery->bindParam(':programName', $programName, PDO::PARAM_STR);
		$updateConfigQuery->bindParam(':siteSub', $siteSub, PDO::PARAM_STR);
		$updateConfigQuery->bindParam(':mjAudio', $mjAudio, PDO::PARAM_BOOL);
		$updateConfigQuery->bindParam(':mjAudioSource', $mjAudioSource, PDO::PARAM_STR);
		$updateConfigQuery->bindParam(':mjAudioIntro', $mjAudioIntro, PDO::PARAM_STR);
		$updateConfigQuery->bindParam(':mjAudioCaption', $mjAudioCaption, PDO::PARAM_STR);
		$updateConfigQuery->bindParam(':mjAudioRecieveJesus', $mjAudioRecieveJesus, PDO::PARAM_STR);
		$updateConfigQuery->bindParam(':mjAudioRecieveJesusResult', $mjAudioRecieveJesusResult, PDO::PARAM_STR);
		$updateConfigQuery->bindParam(':mjAudioRequestDiscipleship', $mjAudioRequestDiscipleship, PDO::PARAM_STR);
		$updateConfigQuery->bindParam(':mjAudioRequestDiscipleshipResult', $mjAudioRequestDiscipleshipResult, PDO::PARAM_STR);

		if ($updateConfigQuery->execute()) {
			//display the success message
			array_push($displayMessage,"<div class='alert fade in'><button type='button' class='close' data-dismiss='alert'>&times;</button>Information updated Successfully</div>");
		}
		else {
			//display the warning message
			array_push($displayMessage,"<div class='alert alert-block alert-error fade in'><button type='button' class='close' data-dismiss='alert'>&times;</button><h4 class='alert-heading'>Warning!</h4> Could not edit Information</div>");
		}
	}
	catch (PDOException $e) {
		array_push($displayMessage,"<div class='alert alert-block alert-error fade in'><button type='button' class='close' data-dismiss='alert'>&times;</button><h4 class='alert-heading'>ERROR!</h4>"   . $e->getMessage() . "</div>");
	}
}


/**
 * edit config
 */
if (array_key_exists('editFooter', $_POST)) {
	$expected = array('footerDescription', 'footerDescriptionLeft', 'footerDescriptionRight', 'footerID');

	foreach ($_POST as $key => $value) {
	//assign to temporary variable and strip whitespace if not an array
	$temp = is_array($value) ? $value : trim($value);

	//otherwise, assign to a variable of the same name as $key
	if (in_array($key, $expected))
		${$key} = $temp;
	}

	$updateFooterQuery = $conCreative->prepare("UPDATE footer
												SET footerDescription=:footerDescription
												WHERE footerID=1");
	$updateFooterQuery->bindParam(':footerDescription', $footerDescription, PDO::PARAM_STR);

	if ($updateFooterQuery->execute()) {
	//display the success message
		array_push($displayMessage,"<div class='alert fade in'><button type='button' class='close' data-dismiss='alert'>&times;</button>Footer updated Successfully</div>");
	}
	else {
	//display the warning message
		array_push($displayMessage,"<div class='alert alert-block alert-error fade in'><button type='button' class='close' data-dismiss='alert'>&times;</button><h4 class='alert-heading'>Warning!</h4> Could not edit Footer</div>");
	}

	$updateFooterQuery = $conCreative->prepare("UPDATE footer
												SET footerDescription=:footerDescription
												WHERE footerID=2");
	$updateFooterQuery->bindParam(':footerDescription', $footerDescriptionLeft, PDO::PARAM_STR);

	if ($updateFooterQuery->execute()) {
	//display the success message
		array_push($displayMessage,"<div class='alert fade in'><button type='button' class='close' data-dismiss='alert'>&times;</button>Footer updated Successfully</div>");
	}
	else {
	//display the warning message
		array_push($displayMessage,"<div class='alert alert-block alert-error fade in'><button type='button' class='close' data-dismiss='alert'>&times;</button><h4 class='alert-heading'>Warning!</h4> Could not edit Footer</div>");
	}

	$updateFooterQuery = $conCreative->prepare("UPDATE footer
												SET footerDescription=:footerDescription
												WHERE footerID=3");
	$updateFooterQuery->bindParam(':footerDescription', $footerDescriptionRight, PDO::PARAM_STR);

	if ($updateFooterQuery->execute()) {
		//display the success message
		array_push($displayMessage,"<div class='alert fade in'><button type='button' class='close' data-dismiss='alert'>&times;</button>Footer updated Successfully</div>");
	}
	else {
		//display the warning message
		array_push($displayMessage,"<div class='alert alert-block alert-error fade in'><button type='button' class='close' data-dismiss='alert'>&times;</button><h4 class='alert-heading'>Warning!</h4> Could not edit Footer</div>");
	}
}
?>
