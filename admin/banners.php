<?php
require_once("include.php");

$currentPage="banners";
//View Pages

$maxRows_getBanners = 10; //Set how many pages it will show in the table
$pageNum_getBanners = 0;
//Set how many pages it will show in the table
if (isset($_POST['viewallpages'])) {
  $maxRows_getBanners = $_POST['viewallpages'];
}else{
	$maxRows_getBanners = 10;
	if (isset($_GET['pageNum_getBanners'])) {
		$pageNum_getBanners = $_GET['pageNum_getBanners'];
	}

}
$startRow_getBanners = $pageNum_getBanners * $maxRows_getBanners;

$query_getBanners = $conCreative->prepare("SELECT * FROM banners ORDER BY bannerID DESC LIMIT :startRow_getBanners,:maxRows_getBanners");
$query_getBanners->bindParam(':startRow_getBanners', $startRow_getBanners, PDO::PARAM_INT);
$query_getBanners->bindParam(':maxRows_getBanners', $maxRows_getBanners, PDO::PARAM_INT);
$query_getBanners->execute();

$query_getALLBanners = $conCreative->prepare("SELECT * FROM banners ORDER BY bannerID DESC");
$query_getALLBanners->execute();
$totalRows_getALLBanners=$query_getALLBanners->rowCount();

if (isset($_POST['totalRows_getBanners'])) {
  $totalRows_getBanners = $_POST['totalRows_getBanners'];
} else {
  $totalRows_getBanners=$query_getBanners->rowCount();
}
$totalPages_getBanners = ceil($totalRows_getALLBanners/$maxRows_getBanners)-1;
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
				<button type="button" class="btn" data-toggle="collapse" data-target="#addpages"><i class="icon-plus"></i> Add New Banner</button>
				</div>
				<div id="addpages" class="collapse">
					<h2>Add new banner</h2>
					<form name="form" method="post" enctype="multipart/form-data">
					<div><label for="bannerTitle">Title</label> <input type="text" name="bannerTitle" /></div>


					<div class="clearboth"></div>
					<div class="browseimage">
						<label for="bannerImage">Image</label>
						<input type="text" name="bannerImage" id="fieldID" style="cursor:pointer;" onclick="openFilemanager(1,'fieldID');"/>
					</div>

					<div class="clearboth"></div>
					<div class="marginTop20"><input type="submit" name="addBanner" id="addBanner" value="Add Banner" class="btn btn-primary" /></div>
					</form>
				</div>

			<?php if($totalRows_getBanners>0){ ?>
			<hr />
			<div class="span3 marginTop20 marginLeft0 clearboth">
				Total Banners: <?php echo $totalRows_getBanners; ?>
			</div>
			<!--Pagination Starts-->
			<div class="pull-right">
			<?php if($totalPages_getBanners>0){ ?>
			<div class="pagination">
				<ul>

									<?php
										  for($i=0; $i<=$totalPages_getBanners; $i++)
										  { if( (($i<$pageNum_getBanners+2)&&($i>$pageNum_getBanners-2)) || (($i<2) &&($pageNum_getBanners>=2))  || (($i>$totalPages_getBanners-2) && ($pageNum_getBanners<=$totalPages_getBanners-2)))
										  {	if(($pageNum_getBanners!=$i)){
										  ?>
										  <!-- style of pagination when NOT selected -->
									<li><a href="<?php  $url=$_SERVER["REQUEST_URI"];
													if((strpos($url,'?'))==FALSE)
													{echo $url."?pageNum_getBanners=".$i."#1"; }else{
														if((strpos($url,'pageNum_getBanners'))==FALSE){echo $url."&pageNum_getBanners=".$i."#1"; }
														else {
														$oldPageNumValue=$_GET['pageNum_getBanners'];
														$oldArgument="pageNum_getBanners=".$oldPageNumValue;
														$newArgument="pageNum_getBanners=".$i;
														$newUrl=str_replace($oldArgument,$newArgument,$url);
														echo $newUrl."#1";
														}
													}
													?>"><?php echo $i+1;?></a></li>
									<?php
													}else {?>
													<!-- style of pagination when selected -->
									<li class="active"><a href="#"><?php echo $i+1;?></a></li>
									<?php } } if( (($i==$pageNum_getBanners+2)&&($pageNum_getBanners<$totalPages_getBanners-3)) || (($i==$pageNum_getBanners-2)&&($pageNum_getBanners>=4))){echo "...";}}?>

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
						<td class="tablehead">Title</td>
						<td class="tablehead">Image</td>
					</tr>
					<?php if($totalRows_getBanners>0) { while($row_getBanners = $query_getBanners->fetch(PDO::FETCH_ASSOC)) { ?>
					<tr>
						<td width="25"><input name="selectBanner[]" type="checkbox" value="<?php echo $row_getBanners['bannerID']; ?>" style="background:none;" /></td>
						<td><?php echo $row_getBanners['bannerTitle']; ?></td>
						<td><img src="<?php echo $row_getBanners['bannerImage']; ?>" style="max-width:100px; max-height:100px;" /></td>
					</tr>
					<?php } }?>

			</table>
			<div class="pull-left">
					<div class="btn pull-left" onclick="javascript:displayDiv('#deletePage')"><i class="icon-ban-circle"></i> DELETE</div>
					<div class="pull-left marginLeft10" style="display:none;" id="deletePage">
					<input name="deleteBanner" type="submit" id="deleteBanner" class="btn btn-danger" value="Yes I am sure. Please Delete!" />
					</div>
			 </div>
			</form>
			<?php } ?>

		</div>
	</div>

    <?php require_once("include_foot.php"); ?>
</body>

</html>
