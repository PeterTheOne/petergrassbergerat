<?php

include_once("functions.inc.php");

function redirectToHTTPS() {
	if(!isset($_SERVER["HTTPS"]) || 
			strcmp($_SERVER["HTTPS"], "off") == 0) {
		header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["PHP_SELF"]);
		exit;
	}
}

function createToken() {
	$token = session_id() . time() . mt_rand();
	$_SESSION['token'] = $token;
	return $token;
}

function isTokenValid() {
	return isset($_POST['token']) &&
		isset($_SESSION['token']) &&
		sanitize($_POST['token']) === $_SESSION['token'];
}

function userLoginValid() {
	return isset($_SESSION['login']) && 
		$_SESSION['login'] === true && 
		isset($_SESSION['HTTP_USER_AGENT']) && 
		$_SESSION['HTTP_USER_AGENT'] === 
		sha1(SESSION_SALT . $_SERVER['HTTP_USER_AGENT']);
}

?>
