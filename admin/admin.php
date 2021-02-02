<?php
require_once("include.php");

$currentPage="admin";

//Details
$query_getDetails = $conCreative->query("SELECT * FROM companydetails WHERE companyDetailsID = 1");
$totalRows_getDetails=$query_getDetails->rowCount();

//Pages
$query_getPages = $conCreative->query("SELECT * FROM pages ORDER BY pageID DESC LIMIT 0,5");
$totalRows_getPages=$query_getPages->rowCount();

//Posts
$query_getPosts = $conCreative->query("SELECT * FROM articles ORDER BY articleID DESC LIMIT 0,5");
$totalRows_getPosts=$query_getPosts->rowCount();

//Users
$query_getUsers = $conCreative->query("SELECT * FROM users ORDER BY userID DESC LIMIT 0,5");
$totalRows_getUsers=$query_getUsers->rowCount();
?>

<!DOCTYPE html>
<html>

<head>
	<?php require_once("include_head.php"); ?>
</head>

<body>
	<div class="row-fluid bodycontent">
		<?php
		//If you deleted the install folder you can delete this
		$fname = "../install/index.php";
		if (file_exists($fname)) {
				echo "<div class='alert alert-block alert-error fade in'><button type='button' class='close' data-dismiss='alert'>&times;</button><h4 class='alert-heading'>Warning!</h4>WARNING! Please delete the install folder from your root.</div>";
		}
		?>

		<?php include("include_menu.php"); ?>

		<div class="span9 maincontainer">
			<?php include("include_message.php"); ?>

			<h1>Dashboard</h1>
			<hr />
			<div class="span3 box marginLeft0">
				<h4>Site Information</h4>
				<ul>
					<li><strong>Name:</strong> <?php echo $row_getDetails['companyName']; ?></li>
					<li><strong>Telephone:</strong> <?php echo $row_getDetails['companyTelephone']; ?></li>
					<li><strong>E-mail:</strong> <?php echo $row_getDetails['companyEmail']; ?></li>
					<li><strong>Address:</strong> <?php echo $row_getDetails['companyAddress']; ?></li>
				</ul>
				<a href="details.php">view all</a>
			</div>
			<div class="span3 box">
				<h4>Total Pages: <?php echo $totalRows_getPages; ?></h4>
				<ul class="breadcrumb">
					<?php if($totalRows_getPages>0) { while($row_getPages= $query_getPages->fetch(PDO::FETCH_ASSOC)) {  ?>
					<li><i class="icon-edit"></i> <a href="edit_page.php?pid=<?php echo $row_getPages['pageID']; ?>"><?php echo $row_getPages['pageTitle']; ?></a></li><br />
					<?php } }?>
				</ul>
				<a href="pages.php">view all</a>
			</div>
			<div class="span3 box">
				<h4>Total Posts: <?php echo $totalRows_getPosts; ?></h4>
				<ul class="breadcrumb">
					<?php if($totalRows_getPosts>0) { while($row_getPosts= $query_getPosts->fetch(PDO::FETCH_ASSOC)) { ?>
					<li><i class="icon-edit"></i> <a href="edit_article.php?pid=<?php echo $row_getPosts['articleID']; ?>"><?php echo $row_getPosts['articleTitle']; ?></a></li><br />
					<?php } }?>
				</ul>
				<a href="articles.php">view all</a>
			</div>
			<div class="span3 box">
				<h4>Total Users: <?php echo $totalRows_getUsers; ?></h4>
				<ul class="breadcrumb">
					<?php if($totalRows_getUsers>0) { while($row_getUsers= $query_getUsers->fetch(PDO::FETCH_ASSOC)) { ?>
					<li><i class="icon-edit"></i> <a href="edit_user.php?pid=<?php echo $row_getUsers['userID']; ?>"><?php echo $row_getUsers['name']; ?></a></li><br />
					<?php } }?>
				</ul>
				<a href="users.php">view all</a>
			</div>
		</div>
	</div>

	<?php require_once("include_foot.php"); ?>
</body>

</html>
