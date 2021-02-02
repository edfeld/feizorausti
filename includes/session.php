<?php
/**
This file starts up the session for the website on a page load so that session
variables can be accessed and edited.

Note: Use require_once rather than include to add a dependency on this file to
another file. Although there is a check below to prevent such a situation,
multiple session starts can cause some pretty serious issues if the session
files are locked.
**/

if (!isset($_SESSION)) {
    session_set_cookie_params(300);
    session_start();
}

?>