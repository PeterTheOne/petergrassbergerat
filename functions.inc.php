<?php

function sanitize($str) {
	return htmlspecialchars($str, ENT_QUOTES, "UTF-8");
}

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
	return (
		isset($_GET['token']) &&
		isset($_SESSION['token']) &&
		sanitize($_GET['token']) === $_SESSION['token']
	);
}

function getLang() {
	if(substr($_SERVER['HTTP_HOST'], -4) === '.com') {
		$lang = 'en';
	} else {
		$lang = 'de-AT';
	}
	return $lang;
}

function getLangNot() {
	if (getLang() === 'en') {
		return 'de-AT';
	}
	return 'en';
}

function buildTranslateURL($currentLang, $site, $subsite) {
	$translateURL = 'http://petergrassberger.';
	if ($currentLang == 'en') {
		$translateURL .= 'at';
	} else {
		$translateURL .= 'com';
	}
	if (isset($_GET['site']) && $site !== '') {
		$translateURL .= '/' . $site . '/';
		if (isset($_GET['subsite']) && sanitize($_GET['subsite']) !== '') {
			$translateURL .= $subsite . '/';
		}
	}
	return $translateURL;
}

?>
