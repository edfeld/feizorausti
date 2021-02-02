<?php
require_once("include.php");

$currentPage="users";
//View Pages

$maxRows_getUsers = 10; //Set how many pages it will show in the table
$pageNum_getUsers = 0;

if (isset($_GET['pageNum_getUsers'])) {
  $pageNum_getUsers = $_GET['pageNum_getUsers'];
}
$startRow_getUsers = $pageNum_getUsers * $maxRows_getUsers;

$query_getUsers = $conCreative->prepare("SELECT * FROM users WHERE userID !=1 ORDER BY userID DESC LIMIT :startRow_getUsers,:maxRows_getUsers");
$query_getUsers->bindParam(':startRow_getUsers', $startRow_getUsers, PDO::PARAM_INT);
$query_getUsers->bindParam(':maxRows_getUsers', $maxRows_getUsers, PDO::PARAM_INT);
$query_getUsers->execute();

if (isset($_GET['totalRows_getUsers'])) {
  $totalRows_getUsers = $_GET['totalRows_getUsers'];
} else {
  $totalRows_getUsers=$query_getUsers->rowCount();
}
$totalPages_getUsers = ceil($totalRows_getUsers/$maxRows_getUsers)-1;
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
			<button type="button" class="btn" data-toggle="collapse" data-target="#addpages"><i class="icon-plus"></i> Add New User</button>
			</div>
			<div id="addpages" class="collapse">
				<h2>Add new user</h2>
				<form name="form" method="post" enctype="multipart/form-data">
				<div><label for="name">Name</label> <input type="text" name="name" /></div>
				<div><label for="username">User Name</label> <input type="text" name="username" /></div>
				<div><label for="password">Password</label> <input type="password" name="password" /></div>

				<div class="clearboth"></div>

				<div class="marginTop20"><input type="submit" name="addUser" id="addUser" value="Add User" class="btn btn-primary" /></div>
				</form>
			</div>

			<?php if($totalRows_getUsers>0){ ?>
			<hr />
			<div class="span3 marginTop20 marginLeft0 clearboth">
				Total Users: <?php echo $totalRows_getUsers; ?>
			</div>
			<!--Pagination Starts-->
			<div class="pull-right">
			<div class="pagination">
				<ul>
					<?php if($totalPages_getUsers>0){ ?>
									<?php
										  for($i=0; $i<=$totalPages_getUsers; $i++)
										  { if( (($i<$pageNum_getUsers+2)&&($i>$pageNum_getUsers-2)) || (($i<2) &&($pageNum_getUsers>=2))  || (($i>$totalPages_getUsers-2) && ($pageNum_getUsers<=$totalPages_getUsers-2)))
										  {	if(($pageNum_getUsers!=$i)){
										  ?>
										  <!-- style of pagination when NOT selected -->
									<li><a href="<?php  $url=$_SERVER["REQUEST_URI"];
													if((strpos($url,'?'))==FALSE)
													{echo $url."?pageNum_getUsers=".$i."#1"; }else{
														if((strpos($url,'pageNum_getUsers'))==FALSE){echo $url."&pageNum_getUsers=".$i."#1"; }
														else {
														$oldPageNumValue=$_GET['pageNum_getUsers'];
														$oldArgument="pageNum_getUsers=".$oldPageNumValue;
														$newArgument="pageNum_getUsers=".$i;
														$newUrl=str_replace($oldArgument,$newArgument,$url);
														echo $newUrl."#1";
														}
													}
													?>"><?php echo $i+1;?></a></li>
									<?php
													}else {?>
													<!-- style of pagination when selected -->
									<li class="active"><a href="#"><?php echo $i+1;?></a></li>
									<?php } } if( (($i==$pageNum_getUsers+2)&&($pageNum_getUsers<$totalPages_getUsers-3)) || (($i==$pageNum_getUsers-2)&&($pageNum_getUsers>=4))){echo "...";}}?>
					<?php } ?>
				</ul>
			</div>
			</div>
			<div class="clearboth"></div>
			<!--Paginations Ends-->
			<form id="viewForm" name="form2" method="post" action="">
			<table class="table table-bordered">
					<tr>
						<td class="tablehead"></td>
						<td class="tablehead"></td>
						<td class="tablehead">Name</td>
					</tr>
					<?php if($totalRows_getUsers>0) { while($row_getUsers = $query_getUsers->fetch(PDO::FETCH_ASSOC)) { ?>
					<tr>
						<td width="25"><input name="selectUser[]" type="checkbox" value="<?php echo $row_getUsers['userID']; ?>" style="background:none;" /></td>
						<td width="45"><a href="edit_user.php?pid=<?php echo $row_getUsers['userID']; ?>"><i class="icon-edit"></i > Edit</a></td>
						<td><a href="edit_user.php?pid=<?php echo $row_getUsers['userID']; ?>"><?php echo $row_getUsers['name']; ?></a></td>

					</tr>
					<?php } }?>

			</table>
			<div class="pull-left">
					<div class="btn pull-left" onclick="javascript:displayDiv('#deletePage')"><i class="icon-ban-circle"></i> DELETE</div>
					<div class="pull-left marginLeft10" style="display:none;" id="deletePage">
					<input name="deleteUser" type="submit" id="deleteUser" class="btn btn-danger" value="Yes I am sure. Please Delete!" />
					</div>
			 </div>
			</form>
			<?php } ?>

		</div>
	</div>

    <?php require_once("include_foot.php"); ?>
</body>

</html>
