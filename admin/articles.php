<?php
require_once("include.php");

$currentPage="articles";

$maxRows_getArticles = 10; //set how many pages it will show in the table
$pageNum_getArticles = 0;

//set how many pages it will show in the table
if (isset($_POST['viewallpages'])) {
  $maxRows_getArticles = $_POST['viewallpages'];
}
else {
	$maxRows_getArticles = 10;
	if (isset($_GET['pageNum_getArticles'])) {
		$pageNum_getArticles = $_GET['pageNum_getArticles'];
	}

}
$startRow_getArticles = ($pageNum_getArticles * $maxRows_getArticles);

$query_getArticles = $conCreative->prepare("SELECT * FROM articles
                                            ORDER BY articleID DESC
                                            LIMIT :startRow_getArticles,:maxRows_getArticles");
$query_getArticles->bindParam(':startRow_getArticles', $startRow_getArticles, PDO::PARAM_INT);
$query_getArticles->bindParam(':maxRows_getArticles', $maxRows_getArticles, PDO::PARAM_INT);
$query_getArticles->execute();

$query_getALLArticles = $conCreative->prepare("SELECT * FROM articles
                                               ORDER BY articleID DESC");
$query_getALLArticles->execute();
$totalRows_getALLArticles=$query_getALLArticles->rowCount();

if (isset($_POST['totalRows_getArticles'])) {
  $totalRows_getArticles = $_POST['totalRows_getArticles'];
}
else {
  $totalRows_getArticles=$query_getArticles->rowCount();
}
$totalPages_getArticles = ceil($totalRows_getALLArticles/$maxRows_getArticles)-1;

//check if an article page exists
$query_getMainPagesexists = $conCreative->query("SELECT * FROM pages WHERE pageType=2
                                                 ORDER BY pageTitle ASC");
$row_getMainPagesexists = $query_getMainPagesexists->fetch();
$totalRows_getMainPagesexists=$query_getMainPagesexists->rowCount();

//get pages to put in the parent pages list
$query_getMainPages = $conCreative->query("SELECT * FROM pages WHERE pageType=2 ORDER BY pageTitle ASC");
$totalRows_getMainPages=$query_getMainPages->rowCount();

//generate a string that can go into the date box by default
$strdate = date('d-m-Y', time()); 
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

    <?php if($row_getMainPagesexists>0){ ?>
    <div>

    <button type="button" class="btn" data-toggle="collapse" data-target="#addpages"><i class="icon-plus"></i> Add New Article</button>
    </div>
    <div id="addpages" class="collapse">
    <h2>Add new article</h2>
    <form name="form" method="post" enctype="multipart/form-data">
    <div class="admindevideddivs">
    <label for="articleTitle">Title</label>
    <input type="text" name="articleTitle" />
    </div>

    <div class="pull-left admindevideddivs">
    <label for="articleBelongs">Parent Page</label>
    <select name="articleBelongs" id="articleBelongs">
    <?php
    	{ while($row_getMainPages= $query_getMainPages->fetch(PDO::FETCH_ASSOC)) {
    ?>
    <option value="<?php echo $row_getMainPages['pageID']?>"><?php echo $row_getMainPages['pageTitle']?></option>
    												<?php } } ?>
    </select>
    </div>

    <div class="pull-left marginLeft10 admindevideddivs">
    <label for="articleDate">Date</label>
    <input type="text" name="articleDate" id="datepicker" value="<?php echo $strdate; ?>" placeholder="<?php echo $strdate; ?>">
    <!--id="articleDate" class="tcal articleBelongs" value="" />-->
    </div>
    <div class="clearboth"></div>
    <div class="browseimage admindevideddivs">
    <label for="articleImage">Image</label>
    <input type="text" name="articleImage" id="fieldID" style="cursor: pointer;" onClick="openFilemanager(1,'fieldID');"/>
    </div>
    <div class="admindevideddivs">
    <label for="articleShortDescription">Short Description (a short glimpse at or description of what is inside of the article) (minimum 30 words)</label><center><textarea class="nomce" id="articleShortDescription" name="articleShortDescription" style="height:80px;"></textarea></center>
    </div>
    <div>
    <label for="articleDescription" class="admindevideddivs">Article Content</label>
    <center><textarea class="articleDescription" id="articleDescription" name="articleDescription"></textarea></center>
    </div>

    <div class="pull-left articlefriendlyURL">
    Allow Comments?<br>
    <input type="radio" name="allowCommentsButton" id="yes" value="yes" style="width: 16px; height: 16px; display: inline-block; position: relative;"  checked><label for="yes">Yes</label>
    <input type="radio" name="allowCommentsButton" id="no" value="no" style="width: 16px; height: 16px; display: inline-block; position: relative;"><label for="no">No</label>
    </div>
    <div class="clearboth"></div>
    <div><input type="submit" name="addArticle" id="addArticle" value="Add Article" class="btn btn-primary" /></div>
    </form>
    </div>
    <?php } else{ ?>
    <div class='alert alert-block alert-error fade in'><h4 class='alert-heading'>Warning!</h4> Please create an Articles page under Pages first</div>
    <?php } ?>
    <?php if($totalRows_getArticles>0){ ?>
    <hr />

    <div class="span3 marginTop20 marginLeft0 clearboth">
    Total Articles: <?php echo $totalRows_getArticles; ?>
    </div>

    <!--Pagination Starts-->
    <div class="pull-right">
    <?php if($totalPages_getArticles>0){ ?>
    <div class="pagination">

    <ul>
    <?php
    	for($i=0; $i<=$totalPages_getArticles; $i++)
    	{ if( (($i<$pageNum_getArticles+2)&&($i>$pageNum_getArticles-2)) || (($i<2) &&($pageNum_getArticles>=2))  || (($i>$totalPages_getArticles-2) && ($pageNum_getArticles<=$totalPages_getArticles-2)))
    	{	if(($pageNum_getArticles!=$i)){
    ?>
    <!-- style of pagination when NOT selected -->
    <li><a href="<?php  $url=$_SERVER["REQUEST_URI"];
    	if((strpos($url,'?'))==FALSE)
    	{echo $url."?pageNum_getArticles=".$i."#1"; }else{
    	if((strpos($url,'pageNum_getArticles'))==FALSE){echo $url."&pageNum_getArticles=".$i."#1"; }
    	else {
    	$oldPageNumValue=$_GET['pageNum_getArticles'];
    	$oldArgument="pageNum_getArticles=".$oldPageNumValue;
    	$newArgument="pageNum_getArticles=".$i;
    	$newUrl=str_replace($oldArgument,$newArgument,$url);
    	echo $newUrl."#1";
    	}
    	}
    ?>"><?php echo $i+1;?></a></li>

    <?php
    	}else {?>

    <!-- style of pagination when selected -->
    <li class="active"><a href="#"><?php echo $i+1;?></a></li>
    <?php } } if( (($i==$pageNum_getArticles+2)&&($pageNum_getArticles<$totalPages_getArticles-3)) || (($i==$pageNum_getArticles-2)&&($pageNum_getArticles>=4))){echo "...";}}?>

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
    <td class="tablehead">Parent Page</td>
    <td class="tablehead"></td>
    </tr>

    <?php
    	if($totalRows_getArticles>0){
    	while($row_getArticles = $query_getArticles->fetch(PDO::FETCH_ASSOC)){
    	$articlelink = $sitepath . "/single_article.php?aid=" . $row_getArticles['articlefriendlyURL'];
    ?>

    <tr>

    <td width="25"><input name="selectArticle[]" type="checkbox" value="<?php echo $row_getArticles['articleID']; ?>" style="background:none;" /></td>
    <td width="45" style="text-align: center;"><a href="edit_article.php?pid=<?php echo $row_getArticles['articleID']; ?>" style="color: gray;">Edit</a></td>
    <td width="45" style="text-align: center;"><a href="#"  onclick="window.open('<?php echo $articlelink; ?>');" style="color: gray;">View</a></td>
    <td><a href="edit_article.php?pid=<?php echo $row_getArticles['articleID']; ?>"><?php echo $row_getArticles['articleTitle']; ?></a></td>

    <td><?php
    //Get Parent Page Title
    	$parentID= $row_getArticles['articleBelongs'];
    	$query_getParentPage = $conCreative->prepare("SELECT * FROM pages WHERE pageID=:parentID");
    	$query_getParentPage->bindParam(':parentID', $parentID, PDO::PARAM_INT);
    	$query_getParentPage->execute();
    	$row_getParentPage=$query_getParentPage->fetch();
    	echo $row_getParentPage['pageTitle']; ?></td>
    	<td width="364" style="color: lightgray;" id="selectable<?php  echo $row_getArticles['articleID']; ?>" onClick="selectText('selectable<?php  echo $row_getArticles['articleID']; ?>');">
    <?php echo $articlelink; ?>
    </td>

    </tr>

    <?php } }?>
    </table>

    <div class="pull-left">
    <div class="btn pull-left" onClick="javascript:displayDiv('#deletePage')"><i class="icon-ban-circle"></i> DELETE</div>
    <div class="pull-left marginLeft10" style="display:none;" id="deletePage">
    <input name="deleteArticle" type="submit" id="deleteArticle" class="btn btn-danger" value="Yes I am sure. Please Delete!" />
    </div>
    </div>

    </form>

    <?php } ?>

    </div>
    </div>

    <?php require_once("include_foot.php"); ?>
</body>

</html>
