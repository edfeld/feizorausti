<?php
// *** Validate request to login to this site.
if (!isset($_SESSION)) {
    // (TODO: See include.php aswell as whatever fix gets applied needs to be
    // applied there as well. See http://php.net/manual/en/function.session-set-cookie-params.php
    // for documentation. I believe that there may be a number of things
    // happening here:
    //   1.) Setting the lifetime to 0 looks like it may litterally be setting
    //       it to 0 seconds. The documentation says nothing about 0 being
    //       special. (Also note that we may need to override the cookie with
    //       an even longer lifetime manually...see comments in the PHP
    //       documentation for more info.)
    //   2.) The path paramater needs to include the sub-website path. I don't
    //       know why we don't just use subdomains, but we don't...so we need
    //       to do this right.
    //   3.) Maybe the server's time or garbage collector is screwed up?
    // )
    session_set_cookie_params(0, dirname($_SERVER["REQUEST_URI"]));
    session_start();
}

require_once("../config/connection.php");

$displayMessage=array();

//query :: site details
$query_getDetails = $conCreative->query("SELECT * FROM companydetails WHERE companyDetailsID = 1");
$row_getDetails = $query_getDetails->fetch();
$totalRows_getDetails=$query_getDetails->rowCount();

//query :: site config
$query_getConfig = $conCreative->query("SELECT * FROM configurations WHERE configID=1");
$row_getConfig = $query_getConfig->fetch();
$totalRows_getConfig=$query_getConfig->rowCount();

$sitehost = "http://" . $_SERVER['HTTP_HOST'];
$sitedir  = $row_getConfig['siteDir'];
$sitepath = $sitehost . $sitedir;

$loginFormAction = $_SERVER['PHP_SELF'];
if (isset($_GET['accesscheck'])) {
  $_SESSION['PrevUrl'] = $_GET['accesscheck'];
}

if (isset($_POST['username'])) {
  $snailUsername=$_POST['username'];
  $snailPassword=$_POST['password'];
  $snailPassword = md5($snailPassword);
  $snailPassword = sha1($snailPassword);
  $snailPassword = crypt($snailPassword, 'fx');

$query_getUsers = $conCreative->prepare("SELECT userID,name,username, password,loggedIn,ipAddress,lastActive FROM users WHERE username=:snailUsername AND password=:snailPassword");
$query_getUsers->bindValue(':snailUsername', $snailUsername);
$query_getUsers->bindValue(':snailPassword', $snailPassword);
$query_getUsers->execute();

$row_getUsers = $query_getUsers->fetch();
$loginFoundUser=$query_getUsers->rowCount();

  if ($loginFoundUser>=1) { // If the username and password are correct

    $loginID  = $row_getUsers['userID'];//mysql_result($LoginRS,0,'userID');
	$loginName  = $row_getUsers['name'];//mysql_result($LoginRS,0,'name');

    if (isset($_SESSION['PrevUrl']) && false) {
      $MM_redirectLoginSuccess = $_SESSION['PrevUrl'];
    }


			//declare two session variables and assign them
			$_SESSION['MM_Username'] = $snailUsername;
			$_SESSION['name']=$loginName;
			$_SESSION['snail_user_id']=$loginID;

            header("Location: admin.php");

			//array_push($displayMessage,"<div class='alert alert-block alert-error fade in'><button type='button' class='close' data-dismiss='alert'>&times;</button><h4 class='alert-heading'>Warning!</h4> Login Faild. Please try again</div>");
  }
  else { // If the username and password are NOT correct
   array_push($displayMessage,"<div class='alert alert-block alert-error fade in'><h4 class='alert-heading'>Warning!</h4> Login failed, please try again.</div>");

  }
}
?>

<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Admin</title>
	<link href="Assets/img/favicon.ico" rel="shortcut icon" type="image/x-icon" />
	<link href="Assets/css/bootstrap.css" rel="stylesheet" media="screen"/>
	<link href="Assets/css/styles.css" rel="stylesheet" media="screen"/>
	<link href="Assets/css/login.css" rel='stylesheet' type='text/css'>
</head>

<body>
	<div id="login-container">
		<form id="loginInformationForm" name="form1" class="login-form" method="POST" action="<?php echo $loginFormAction; ?>">
			<div class="content">
				<h1><?php echo $row_getDetails['companyName']; ?></h1>
				<input name="username" type="text" class="input username" placeholder="Username" id="username" />
				<div class="user-icon"></div>
				<input name="password" type="password" class="input password" placeholder="Password" id="password" />
				<div class="pass-icon"></div>
				<input type="submit" name="loginbtn" value="Login" class="button" />
			</div>
		</form>
		<div class="copyright">© 2014 - <?php echo date('Y'); ?> <?php echo $row_getDetails['companyName']; ?></div>
		<div class="copyright">Back to <a href="<?php echo $sitepath; ?>">Public Website</a></div>
		<div class="error">

        <?php include("include_message.php"); ?>
		</div>
	</div>
</body>

</html>
