<?php

function sanitize($str) {
	return htmlspecialchars($str, ENT_QUOTES, "UTF-8");
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

function getURL($lang) {
	if ($lang === 'de-AT') {
		$url = 'http://petergrassberger.at';
	} else {
		$url = 'http://petergrassberger.com';
	}
	return $url;
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
