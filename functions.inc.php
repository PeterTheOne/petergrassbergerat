<?php

function sanitize($str) {
	return htmlspecialchars($str, ENT_QUOTES, "UTF-8");
}

/**
 * Return a subset of the parameter array with only keys of the whitelist.
 * Only checks keys, not values.
 *
 * @author meisterluk
 * @author Peter Grassberger <petertheone@gmail.com>
 *
 * @param $array array to select from
 * @param $whitelist array of keys to select
 * @return array a subset of array
 */
function whitelist($array, $whitelist) {
    $new_array = array();
    foreach ($array as $key => $value) {
        if (isset($whitelist[$key]))
            $new_array[$key] = $value;
    }
    return $new_array;
}

/**
 *
 * Return a subset of the parameter array with only keys not in blacklist.
 * Only checks keys, not values.
 *
 * @author meisterluk
 * @author Peter Grassberger <petertheone@gmail.com>
 *
 * @param $array array to select from
 * @param $blacklist array of keys to throw away
 * @return array a subset of array
 */
function blacklist($array, $blacklist) {
    $new_array = array();
    foreach ($array as $key => $value) {
        if (!isset($blacklist[$key]))
            $new_array[$key] = $value;
    }
    return $new_array;
}

/**
 * Check whether parameter starts with a given substring or not.
 *
 * @author meisterluk
 *
 * @param $string string parameter to search in
 * @param $substring substring needle to search for
 * @return bool string starts with substring
 */
function startswith($string, $substring) {
    return substr($string, 0, strlen($substring)) === $substring;
}

/**
 * Check whether parameter ends with a given substring or not.
 *
 * @author meisterluk
 *
 * @param $string string parameter to search in
 * @param $substring substring needle to search for
 * @return bool string ends with substring
 */
function endswith($string, $substring) {
    return substr($string, -strlen($substring)) === $substring;
}

/**
 * Check whether parameter starts with a given substring or not.
 *
 * @author meisterluk
 * @author Peter Grassberger <petertheone@gmail.com>
 *
 * @param $string string parameter to search in
 * @param $substring string needle to search for
 * @return bool string starts with substring
 */
function startswithCrop($string, $substring) {
    if (startswith($string, $substring)) {
        return substr($string, strlen($substring));
    }
    return $string;
}

/**
 * Check whether parameter ends with a given substring or not.
 *
 * @author meisterluk
 * @author Peter Grassberger <petertheone@gmail.com>
 *
 * @param $string string parameter to search in
 * @param $substring string needle to search for
 * @return bool string ends with substring
 */
function endswithCrop($string, $substring) {
    if (endswith($string, $substring)) {
        return substr($string, 0, -strlen($substring));
    }
    return $string;
}

function getJsonFromUrl($url, $parameters) {
    $ch = curl_init();
    $header = array('HTTP_ACCEPT: application/json', 'HTTP_ACCEPT_LANGUAGE: fr, en, da, nl', 'HTTP_CONNECTION: Something');
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parameters));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header );
    $fileContents = curl_exec($ch);
    curl_close($ch);

    return json_decode($fileContents, true);
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
