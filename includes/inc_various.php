<?php
/**
 * Various functions to simplify the website.
 *
 * @package GRO Custom Content Management System
 * @author Luke Hollenback <lukesterspy@yahoo.com>
 * @version 1.0.1
 */


/**
 * make a string safe to use in a SQL query
 * @param string $input the dirty string
 * @return string the cleaned and quoted string
 */
function cleanInput($input, $pdocon){
	return $pdocon->quote(htmlspecialchars(stripslashes(trim($input))));
}

/**
 * make a string safe for HTML parsing (similar to cleanInput() but without the PDO quote() cleanse and parse)
 * @param string $input the dirty string
 * @return string the cleaned string
 */
function cleanInputBasic($input){
	return htmlspecialchars(stripslashes(trim($input)));
}

/**
 * parse the date given into UTC and return it as a printable string
 * @param string $date a date string that can be understood by PHP's date functions
 * @return string a string of the parsed date (e.g. 23 March 2015)
 */
function parseDate($date) {
	$dateObj  = new DateTime($date);
	$dateObj->setTimeZone(new DateTimeZone('UTC'));
	return $dateObj->format('d F Y');
}

// This gets the user's timezone by their IP and saves it into $tz. The
// variable will be null if the timezone can not be found. The current
// DateTime of the user is also stored into $now.
/*$ip     = $_SERVER['REMOTE_ADDR'];
$json   = file_get_contents( 'http://www.telize.com/geoip/' . $ip);
$ipData = json_decode( $json, true);
if ($ipData['timezone']){
    $tz = new DateTimeZone( $ipData['timezone']);
    $now = new DateTime( 'now', $tz);
}
else {
   $tz = null;
}*/
?>
