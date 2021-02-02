<?php

//connect to database
require_once("config/connection.php");

//get the site configuration
$query_getConfig = $conCreative->query("SELECT * FROM configurations WHERE configID=1");
$row_getConfig = $query_getConfig->fetch();
?>

<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $row_getConfig['radioName']; ?> (popup player)</title>
	<link rel="stylesheet" href="css/style.css" type="text/css" />
</head>

<body>

<audio controls>
	<source src="<?php echo $row_getConfig['embeddedLink']; ?>" type="audio/mpeg">
	Your browser does not support the audio element.
</audio> 

<div id="liveradio"><?php echo $row_getConfig['radioName']; ?></div>

</body>

</html>
