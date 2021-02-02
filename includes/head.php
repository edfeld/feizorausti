<?php

/**
 * History
 * ERE20201020 - Ed Einfeld - Bug fix.  I added isset() to two line to prevent access to an Undefined values.   
 */
if (isset($row_getPage['pageMetaTitle']) && $row_getPage['pageMetaTitle'] != "" && strlen($row_getPage['pageMetaTitle']) > 0) {
	$metatitle = " // " . $row_getPage['pageMetaTitle'];
}
else {
	$metatitle = "";
}
?>

<!-- Google Analytics -->
<?php
if (isset($row_getConfig['gaTrackingID']) && !is_null($row_getConfig['gaTrackingID'])) {
?>
<!-- Global Site Tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo $row_getConfig['gaTrackingID']; ?>"></script>
<script>
	window.dataLayer = window.dataLayer || [];
	function gtag(){dataLayer.push(arguments);}
	gtag('js', new Date());

	gtag('config', '<?php echo $row_getConfig['gaTrackingID']; ?>');
</script>
<?php
}
?>

<base href="<?php echo $sitepath; ?>/" />

<title><?php echo $row_getDetails['companyName'] . $metatitle  ?></title>
<link rel="shortcut icon" href="<?php echo $sitepath; ?>/favicon.ico" type="image/x-icon" />

<!--browser settings-->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name='viewport' content='width=device-width, height=device-height'>

<!--for search engines-->
<?php
if($pagename == "single article"){
?>
	<meta name="description" content="<?php echo substr(trim(strip_tags($row_getArticle['articleShortDescription'])),0,200); ?>" />
	<meta name="keywords" content="<?php echo $row_getPage['pageMetaKeywords']; ?>" />
<?php
}
else{ // ERE20201020 - Fixed bugs below when$row_getPage... is null.  Added isset().  
?>
	<meta name="description" content="<?php echo (isset($row_getPage['pageMetaDescription'])) ? $row_getPage['pageMetaDescription'] : '' ; ?>" />
	<meta name="keywords" content="<?php echo (isset($row_getPage['pageMetaKeywords'])) ? $row_getPage['pageMetaKeywords']: ''; ?>" />
<?php
}
?>

<!--stylesheets-->
<link rel="stylesheet" href="css/style.css" type="text/css" />
<link rel="stylesheet" href="css/honeypot-and-submit-slider.css" type="text/css" />
<link rel="stylesheet" href="includes/cstyles.php" type="text/css" />
<!--[if lt IE 9]>
<link rel="stylesheet" href="css/top-menu-ie8.css" type="text/css" />
<script src="js/html5.js"></script>
<![endif]-->
<?php
// Allow for CSS overrides for RTL websites (as specified by the 'sidebarSide'
// value of the site's configuration table)
// ERE20201020 - fix bug when $row_getConfig['sidebarSide'] is null
if (isset($row_getConfig['sidebarSide']) && $row_getConfig['sidebarSide'] == 1) {
	echo "<link rel=\"stylesheet\" href=\"css/style_rtl.css\" type=\"text/css\" />";
}
?>

<!--jQuery-->
<!--(NOTE: Don't load anything else here! Load heavy-lifting JavaScript after
rendering is complete! Otherwise rendering will be slow.)-->
<script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
