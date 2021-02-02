<?php
require_once("include.php");

$currentPage="users";
//View Pages

if (!isset($_GET['pid'])) {
	header("location:users.php");
}else{
	$pid=$_GET['pid'];
}

$query_getUsers = $conCreative->prepare("SELECT * FROM users WHERE userID=:pid");
$query_getUsers->bindParam(':pid', $pid, PDO::PARAM_INT);
$query_getUsers->execute();
$row_getUsers=$query_getUsers->fetch();
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
			<a href="users.php" class="btn btn-primary"><i class="icon-arrow-left icon-white"></i> Back</a>
				<h2>Edit User</h2>
				<form name="form" method="post" enctype="multipart/form-data">
				<input type="hidden" name="userID" value="<?php echo $row_getUsers['userID']; ?>" />
				<div><label for="name">Name</label> <input type="text" name="name" value="<?php echo $row_getUsers['name']; ?>" /></div>
				<div><label for="username">User Name</label> <input type="text" name="username" value="<?php echo $row_getUsers['username']; ?>" /></div>
				<div><label for="password">Password</label> <input type="password" name="password" value="" /></div>

				<div class="clearboth"></div>

				<div class="marginTop20"><input type="submit" name="editUser" id="editUser" value="Edit User" class="btn btn-primary" /></div>
				</form>
			</div>


		</div>
	</div>

	<?php require_once("include_foot.php"); ?>
</body>

</html>
