<?php
require_once("../config/connection.php");

//SITE DETAILS
mysql_select_db($database_SnailConnect, $SnailConnect);
$query_getDetails = "SELECT * FROM companydetails WHERE companyDetailsID = 1";
$getDetails = mysql_query($query_getDetails, $SnailConnect) or die(mysql_error());
$row_getDetails = mysql_fetch_assoc($getDetails);
$totalRows_getDetails = mysql_num_rows($getDetails);
?>

<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $row_getDetails['companyName']; ?> - Admin</title>
	<link href="Assets/css/bootstrap.css" rel="stylesheet" media="screen"/>
	<link href="Assets/css/styles.css" rel="stylesheet" media="screen"/>
</head>

<body>
	<div class="container">
		<div class="login shadow">
			<p>The username or password you entered is incorrect!</p>
			<form id="loginInformationForm" name="form1" method="POST" action="index.php">
				<input type="submit" name="loginbtn" class="btn" value="back" />
			</form>
	
		</div>

		<div class="credits">© <?php echo date(Y); ?> <?php echo $row_getDetails['companyName']; ?></div>
	</div>
</body>

</html>