<?php
header("Content-type: text/css; charset=utf-8");

/**
cstyles.php
-----------
This file is included in the head.php so as to load custom styles that have been
saved into the database by the admin.
**/

//connect to database
require_once("../config/connection.php");

//set the base site path to the correct web address
$sitepath = "http://".$_SERVER['HTTP_HOST'];

//get the site configuration
$query_getConfig = $conCreative->query("SELECT * FROM configurations WHERE configID=1");
$row_getConfig = $query_getConfig->fetch();

//echo out the stylesheet
echo "
	@import url('//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css');

	body {
		background-color: " . $row_getConfig['bodyBackgroundColor'] . ";
		background-image: url(" . $row_getConfig['bodyBackgroundImage'] . ");
	}

	a {
		color: " . $row_getConfig['secondaryColor'] . ";
	}
	a:hover {
		color: " . $row_getConfig['linkHoverColor'] . ";
	}
	.articleReadMore {
		color: " . $row_getConfig['secondaryColor'] . ";
	}
	.articleReadMore:hover {
		color: " . $row_getConfig['linkHoverColor'] . ";
	}

	.top-menu form .button {
		background-color: " . $row_getConfig['primaryColor'] . ";
		border-color: " . $row_getConfig['primaryColor'] . ";
	}

	.top-menu form .input:hover input,
	.top-menu form .textarea:hover textarea,
	.top-menu form .checkbox:hover i {
		border-color: " . $row_getConfig['primaryColor'] . ";
	}

	.top-menu form .input input:focus,
	.top-menu form .textarea textarea:focus,
	.top-menu form .checkbox input:focus + i {
		border-color: " . $row_getConfig['primaryColor'] . ";
	}

	.top-menu li:hover > a,
	.top-menu li.current > a {
		background: " . $row_getConfig['primaryColor'] . ";
		color: #fff;
	}

	.form .input:hover input,
	.form .textarea:hover textarea,
	.form .checkbox:hover i {
		border-color: " . $row_getConfig['primaryColor'] . ";
	}

	.form .input input:focus,
	.form .textarea textarea:focus,
	.form .checkbox input:focus + i {
		border-color: " . $row_getConfig['primaryColor'] . ";
	}

	.form .button {
		background-color: " . $row_getConfig['primaryColor'] . ";
		border: 2px solid " . $row_getConfig['primaryColor'] . ";
	}
	
	.submitSlider::-webkit-slider-thumb {
		background: " . $row_getConfig['primaryColor'] . ";
	}
	.submitSlider::-moz-range-thumb {
		background: " . $row_getConfig['primaryColor'] . ";
	}
	.submitSlider::-ms-thumb {
		background: " . $row_getConfig['primaryColor'] . ";
	}


	/**
	 * CUSTOM STYLES
	 *
	 * These are here to avoid being put into the normal styles.css as it gets
	 * changed too often based on the language of the website being developed.
	 */
	.meetjesus {
		margin: 7% 0 7% 0;
	}
	.meetjesusButton {
		margin: 1%;
		padding: 2%;
		background-color: " . $row_getConfig['primaryColor'] . ";
		border: 4px solid " . $row_getConfig['primaryColor'] . ";
		color: #fff;
		font-size: 1.5em;
		text-align: center;

		-webkit-transition: background-color .5s, color .5s;
    	transition: background-color .5s, color .5s;
	}
	.meetjesusButton:hover {
		background-color: #fff;
		color:  " . $row_getConfig['primaryColor'] . ";
		cursor: pointer;

		-webkit-transition: background-color .5s, color .5s;
    	transition: background-color .5s, color .5s;
	}
	#dialogPopup {
		display: none;
		position: fixed;
		top: 0;
		bottom: 0;
		left: 0;
		right: 0;
		z-index: 99999;
	}
	#dialogPopup #background {
		position: fixed;
		top: 0;
		bottom: 0;
		left: 0;
		right: 0;
		background-color: #000;
		opacity: .5;
		z-index: -999999;
	}
	#dialogPopup #content {
		position: fixed;
		top: 25vh;
		/*bottom: 25vh;*/
		left: 30vw;
		right: 30vw;
		border: 4px solid " . $row_getConfig['primaryColor'] . ";
		padding: 2%;
		background-color: #fff;
	}
	#dialogPopup #content #close {
		position: absolute;
		top: -12px;
		right: -12px;
		width: 24px;
		height: 24px;
		background-color: " . $row_getConfig['primaryColor'] . ";
		border: 4px solid " . $row_getConfig['primaryColor'] . ";
		border-radius: 24px;
		color: #fff;
		font-weight: bold;
		text-align: center;
		line-height: 24px;

		-webkit-transition: background-color .5s, color .5s;
    	transition: background-color .5s, color .5s;
	}
	#dialogPopup #content #close:hover {
		background-color: #fff;
		color: " . $row_getConfig['primaryColor'] . ";
		cursor: pointer;

		-webkit-transition: background-color .5s, color .5s;
    	transition: background-color .5s, color .5s;
	}
	#recieveJesus {
		margin: 7% 0 0 0;
		text-align: center;
	}
	#recieveJesus input {
		width: 96%;
		height: 32px;
		margin: 1%;
		padding: 1%;
		border: 2px solid #b2b2b2;
		color: " . $row_getConfig['primaryColor'] . ";
		text-align: center;
		font-size: 1em;
		font-weight: bold;

		/*-webkit-transition: border .5s;
    	transition: border .5s;*/
	}
	#recieveJesus input:hover {
		border: 2px solid " . $row_getConfig['primaryColor'] . ";

		-webkit-transition: border .5s;
    	transition: border .5s;
	}
	#recieveJesus input:focus {
		border: 2px solid " . $row_getConfig['primaryColor'] . ";
		outline: none;

		-webkit-transition: border .5s;
    	transition: border .5s;
	}
	#recieveJesus button {
		width: 99%;
		height: 48px;
		margin: 1%;
		border: 2px solid " . $row_getConfig['primaryColor'] . ";
		background: " . $row_getConfig['primaryColor'] . ";
		color: #fff;
		text-align: center;
		font-size: 1em;
		font-weight: bold;

		-webkit-transition: background-color .5s, color .5s;
    	transition: background-color .5s, color .5s;
	}
	#recieveJesus button:hover {
		background-color: #fff;
		color: " . $row_getConfig['primaryColor'] . ";
		cursor: pointer;

		-webkit-transition: background-color .5s, color .5s;
    	transition: background-color .5s, color .5s;
	}
	#audioPlayer {
		width: 98%;
		margin: 8% auto 0 auto;
		padding: 1%;
		background-color: " . $row_getConfig['primaryColor'] . ";
		text-align: center;
	}
	#audioPlayer .button {
		display: inline-block;
		width: 24px;
		height: 24px;
		margin: 1% 1% 0 0;
		padding: 1%;
		background-color: #fff;
		border: 4px solid #fff;
		color: " . $row_getConfig['primaryColor'] . ";
		font-weight: bold;
		text-align: center;
		line-height: 24px;

		-webkit-transition: background-color .5s, color .5s;
    	transition: background-color .5s, color .5s;
	}
	#audioPlayer .button:hover {
		background-color: " . $row_getConfig['primaryColor'] . ";
		color: #fff;
		cursor: pointer;

		-webkit-transition: background-color .5s, color .5s;
    	transition: background-color .5s, color .5s;
	}
	#audioPlayer .information {
		width: 100%;
		display: block;
		color: #fff;
		font-weight: bold;
	}
	#audioPlayIcon {
		display: inline;
	}
	#audioPauseIcon {
		display: none;
	}

	" . $row_getConfig['customCSS'] . ";
";
?>
