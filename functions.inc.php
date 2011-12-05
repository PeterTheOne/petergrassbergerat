<?php

function sanitizeFilter($str) {
	return htmlspecialchars($str, ENT_QUOTES, "UTF-8");
}

function getLang() {
	if(substr($_SERVER['HTTP_HOST'], -4) === '.com') {
		$lang = 'en';
	} else {
		$lang = 'de-AT';
	}
}

function buildTranslateURL($currentLang) {
	$translateURL = 'http://petergrassberger.';
	if ($currentLang == 'en') {
		$translateURL .= 'at';
	} else {
		$translateURL .= 'com';
	}
	if (isset($_GET['site']) && sanitizeFilter($_GET['site']) !== '') {
		$translateURL .= '/' . sanitizeFilter($_GET['site']) . '/';
		if (isset($_GET['subsite']) && sanitizeFilter($_GET['subsite']) !== '') {
			$translateURL .= sanitizeFilter($_GET['subsite']) . '/';
		}
	}
	return $translateURL;
}

?>
