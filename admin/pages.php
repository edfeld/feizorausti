<?php
require_once("include.php");

$currentPage="pages";
//View Pages

$maxRows_getPages = 10; //Set how many pages it will show in the table
$pageNum_getPages = 0;

if (isset($_GET['pageNum_getPages'])) {
  $pageNum_getPages = $_GET['pageNum_getPages'];
}
$startRow_getPages = $pageNum_getPages * $maxRows_getPages;

$query_getPages = $conCreative->prepare("SELECT * FROM pages ORDER BY pageID DESC LIMIT :startRow_getPages,:maxRows_getPages");
$query_getPages->bindParam(':startRow_getPages', $startRow_getPages, PDO::PARAM_INT);
$query_getPages->bindParam(':maxRows_getPages', $maxRows_getPages, PDO::PARAM_INT);
$query_getPages->execute();

$query_getAllPages = $conCreative->prepare("SELECT * FROM pages ORDER BY pageID DESC");
$query_getAllPages->execute();
$totalRows_getAllPages=$query_getAllPages->rowCount();

if (isset($_GET['totalRows_getPages'])) {
  $totalRows_getPages = $_GET['totalRows_getPages'];
} else {
  $totalRows_getPages=$query_getPages->rowCount();
}
$totalPages_getPages = ceil($totalRows_getAllPages/$maxRows_getPages)-1;

//Get Pages to put in the Parent Pages List
$query_getMainPages = $conCreative->query("SELECT * FROM pages WHERE pageType>0 AND pageType<3 " . /*AND pageID!='$pid'*/ " ORDER BY pageTitle ASC");
$totalRows_getMainPages=$query_getMainPages->rowCount();

//Check if we have a page assigns as Home page
$query_getHomepage = $conCreative->query("SELECT pageID,pageisHome,pageTitle FROM pages WHERE pageisHome = 1");
$totalRows_getHomepage=$query_getHomepage->rowCount();
?>

<!DOCTYPE html>
<html>

<head>
	<?php require_once("include_head.php"); ?>
</head>

<body>
<div class="row-fluid bodycontent">
<?php include("include_menu.php"); ?>

<div class="span9 maincontainer shadow">
    <?php include("include_message.php"); ?>

<div>
<button type="button" class="btn" data-toggle="collapse" data-target="#addpages"><i class="icon-plus"></i> Add New Page</button>
</div>
<div id="addpages" class="collapse">
<h2>Add new page</h2>
<form name="form" method="post" enctype="multipart/form-data">
<div class="admindevideddivs"><label for="pageTitle">Title</label> <input type="text" name="pageTitle" /></div>
<div class="admindevideddivs"><label for="pageDescription">Main Page Content</label><center><textarea id="pageDescription" name="pageDescription"></textarea></center></div>
<div class="admindevideddivs"><label for="pageSidebar">Sidebar</label><center><textarea id="pageSidebar" name="pageSidebar"></textarea></center></div>

<?php if($totalRows_getHomepage<=0){ ?>
<div class="marginTop20 marginLeft0 pageoptions">
<div class="pageisHomechk">
<input name="pageisHome" id="pageisHome" type="checkbox" /> <label for="pageisHome">This page is my Home page</label>
</div>
</div>

<?php } ?>

<div class="span3 marginTop20 marginLeft0 clearboth">
<label for="pageBelongs">Parent Page</label>
<select name="pageBelongs" id="pageBelongs" style="padding:5px;">
<option value="0" selected="selected">(No Parent)</option>

<?php
	{ while($row_getMainPages= $query_getMainPages->fetch(PDO::FETCH_ASSOC)) {
?>

<option value="<?php echo $row_getMainPages['pageID']?>"><?php echo $row_getMainPages['pageTitle']?></option>

<?php } } ?>
</select>
</div>

<div class="span3 marginTop20">
<label for="pageType">Type of Page</label>
<select name="pageType" id="pageType" style="padding:5px;">
<option value="1" onclick="javascript:LinkDisappear()" selected="selected">Basic Page</option>
<option value="2" onclick="javascript:LinkDisappear()">Article Page</option>

<!--
the following allow you to create pages that are only accessible from certain parts of the website (for example, the footer)...uncomment to add them back in
-->
<!--<option value="3" onclick="javascript:LinkDisappear()">Footer Page</option>-->
<option value="4" onclick="javascript:LinkAppear()">Stub Page (won't appear in main navigation)</option>

</select>
</div>

<div class="pageLinkDiv clearboth" id="pageLinkDiv">
<label for="pageLink">Enter Link</label>
<input type="text" name="pageLink" id="pageLink"/>
</div>
<div class="pull-left articlefriendlyURL" style="border: none;">
	Show Stream Player/Links in Sidebar?<br>
	<span style="margin-right: 8px;"><input type="radio" name="streamButton" id="yes" value="yes" style="width: 16px; height: 16px; display: inline-block; position: relative;" checked><label for="yes">Yes</label></span>
	<span style="margin-left: 8px;"><input type="radio" name="streamButton" id="no" value="no" style="width: 16px; height: 16px; display: inline-block; position: relative;"><label for="no">No</label></span>
</div>

<div class="clearboth">
<button type="button" class="btn" data-toggle="collapse" data-target="#seo"><i class="icon-globe"></i> Manage SEO</button>
</div>

<div id="seo" class="collapse">
<div class="marginTop20"><label for="pageMetaTitle">metaTitle</label> <input type="text" name="pageMetaTitle" /></div>
<div><label for="pageMetaDescription">metaDescription</label><center><textarea class="nomce" name="pageMetaDescription"></textarea></center></div>
<div><label for="pageMetaKeywords">metaKeywords</label><center><textarea class="nomce" name="pageMetaKeywords"></textarea></center></div>
</div>

<div class="marginTop20"><input type="submit" name="addPage" id="addPage" value="Add Page" class="btn btn-primary" /></div>
</form>
</div>

<?php if($totalRows_getPages>0){ ?>

<div class="span3 marginTop20 marginLeft0 clearboth">
Total Pages: <?php echo $totalRows_getPages; ?>
</div>

<!--Pagination Starts-->
<div class="pull-right">
<?php if($totalPages_getPages>0){ ?>
<div class="pagination">
<ul>

<?php
for($i=0; $i<=$totalPages_getPages; $i++)
{ if( (($i<$pageNum_getPages+2)&&($i>$pageNum_getPages-2)) || (($i<2) &&($pageNum_getPages>=2))  || (($i>$totalPages_getPages-2) && ($pageNum_getPages<=$totalPages_getPages-2)))
{	if(($pageNum_getPages!=$i)){
?>

<!-- style of pagination when NOT selected -->
<li>
<a href="<?php  $url=$_SERVER["REQUEST_URI"];
if((strpos($url,'?'))==FALSE)
{echo $url."?pageNum_getPages=".$i."#1"; }else{
if((strpos($url,'pageNum_getPages'))==FALSE){echo $url."&pageNum_getPages=".$i."#1"; }
else {
$oldPageNumValue=$_GET['pageNum_getPages'];
$oldArgument="pageNum_getPages=".$oldPageNumValue;
$newArgument="pageNum_getPages=".$i;
$newUrl=str_replace($oldArgument,$newArgument,$url);
echo $newUrl."#1";
}
}
?>">

<?php echo $i+1;?></a>
</li>

<?php
}else {?>

<!-- style of pagination when selected -->
<li class="active"><a href="#"><?php echo $i+1;?></a></li>
<?php } } if( (($i==$pageNum_getPages+2)&&($pageNum_getPages<$totalPages_getPages-3)) || (($i==$pageNum_getPages-2)&&($pageNum_getPages>=4))){echo "...";}}?>

</ul>
</div>

<?php } ?>
</div>

<div class="clearboth"></div>

<!--Paginations Ends-->

<form id="viewForm" name="form2" method="post" action="">
<table class="table table-bordered">
<tr>
<td class="tablehead"></td>
<td class="tablehead" style="text-align: center;"><i class="icon-edit"></i ></td>
<td class="tablehead" style="text-align: center;"><i class="icon-eye-open"></i ></td>
<td class="tablehead">Title</td>
<td class="tablehead">Page Information</td>
<td class="tablehead"></td>
</tr>

<?php if($totalRows_getPages>0) { while($row_getPages = $query_getPages->fetch(PDO::FETCH_ASSOC)) { ?>

<?php
	$pagelink = "";
	if($row_getPages['pageisHome'] == 1){
	$pagelink = $sitepath . "/";
	}
	else{
	$pagelink = $sitepath . "/page.php?pid=" . $row_getPages['pagefriendlyURL'];
	}
?>

<tr>
<td width="25" style="text-align: center;">

<?php
	if($row_getPages['pageisHome'] == 1){
	echo "<i class='icon-home' alt='This is the Home Page.'></i>";
	}
	else if($row_getPages['pagefriendlyURL'] == 'contact-us'){
	echo "<i class='icon-envelope' alt='This is the Contact Page.'></i>";
	}
	else{
?>

<input name="selectPage[]" type="checkbox" value="<?php echo $row_getPages['pageID']; ?>" style="background:none;" />

<?php
	}
?>

</td>

<td width="45" style="text-align: center;"><a href="edit_page.php?pid=<?php echo $row_getPages['pageID']; ?>" style="color: gray;">Edit</a></td>
<td width="45" style="text-align: center;"><a href="#" onclick="window.open('<?php echo $pagelink; ?>');" style="color: gray";>View</a></td>
<td><a href="edit_page.php?pid=<?php echo $row_getPages['pageID']; ?>"><?php echo $row_getPages['pageTitle']; ?></a></td>
<td>
<?php
//Check if the Page is Home Page
	if($row_getPages['pageisHome'] == 1){
		echo "This is the Home Page";
	}
	else
	{
		if($row_getPages['pageType'] == 3){
			echo "<i class='icon-arrow-down'></i> This is a Footer Page";
		}
		else if($row_getPages['pageType'] == 4){
			echo "This is a stub page.";
		}
		else{
			//Get Parent Page Title
			$pageBelongsID =$row_getPages['pageBelongs'];
			$query_getParentPage = $conCreative->prepare("SELECT * FROM pages WHERE pageID=:pageBelongsID");
			$query_getParentPage->bindParam(':pageBelongsID', $pageBelongsID, PDO::PARAM_INT);
			$query_getParentPage->execute();
			$row_getParentPage=$query_getParentPage->fetch();
			$totalRows_getParentPage=$query_getParentPage->rowCount();
			if($totalRows_getParentPage>0){
				echo "Belongs to: ".$row_getParentPage['pageTitle'];
			}
		}
	}
?>
</td>
<td width="300" style="color: lightgray;" id="selectable<?php  echo $row_getPages['pageID']; ?>" onclick="selectText('selectable<?php  echo $row_getPages['pageID']; ?>');">
<?php echo $pagelink; ?>
</td>
</tr>

<?php }}?>

</table>

<div class="pull-left">
<div class="btn pull-left" onclick="javascript:displayDiv('#deletePage')"><i class="icon-ban-circle"></i> DELETE</div>
<div class="pull-left marginLeft10" style="display:none;" id="deletePage">
<input name="deletePage" type="submit" id="deletePage" class="btn btn-danger" value="Yes I am sure. Please Delete!" />
</div>
</div>
</form>

<?php } ?>
</div>
</div>

<?php require_once("include_foot.php"); ?>
</body>

</html>
