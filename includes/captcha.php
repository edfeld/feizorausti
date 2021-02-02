<?php
//set up the session
require_once("session.php");

//send a image
create_image();
exit();

function create_image(){
    //generate a random string
    $md5_hash = md5(rand(0,999));
	 
    //make it 5 characters long
    $security_code = substr($md5_hash, 15, 5); 

    //storing the security code in the session
    $_SESSION["security_code"] = $security_code;

    //create the image
    $image = @imagecreatefromjpeg("../img/captchaBG.jpg");  

    //making the font color
    $black = ImageColorAllocate($image, 0, 0, 0);

    //make the background black 
    //imageFill($image, 0, 0, $bgImg); 

    //set some variables for positioning and font-size, "5" is the largest I could get to work
	$vPos = 10;
	$hPos = 45;
	$fontSize = 5;
	
    ImageString($image, $fontSize, $hPos, $vPos, $security_code, $black); 
 
    //tell the browser what kind of file this is 
    header("Content-Type: image/jpeg"); 

    //output image as a jpeg 
    ImageJpeg($image);
   
    //free up stuff
    ImageDestroy($image);
}
?>