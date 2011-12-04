<?php

// language
if(substr($_SERVER['HTTP_HOST'], -4) === '.com') {
	$lang = 'en';
} else {
	$lang = 'de-AT';
}
header("Content-language: $lang");

// set up smarty
include_once('Smarty-3.1.3/libs/Smarty.class.php');

$smarty = new Smarty;

//$smarty->force_compile = true;
$smarty->debugging = false;
$smarty->caching = false;
$smarty->cache_lifetime = 120;

$smarty->template_dir = "smarty/templates";
$smarty->compile_dir = "smarty/templates_c";
$smarty->cache_dir = "smarty/cache";
$smarty->config_dir = "smarty/configs";

function sanitizeFilter($str) {
	return htmlspecialchars($str, ENT_QUOTES, "UTF-8");
}

// set up db connection
include_once("config.inc.php");

$con = mysqli_connect(HOST, USERNAME, PASSWD, DBNAME);
	//TODO: error handling..
	if (!$con) {
		die('Could not connect: ' . mysqli_error($con));
	}
$con->set_charset("utf8");

function getPage($con, $title_clean, $lang) {
	$query = "SELECT * FROM pages WHERE lang = '$lang' AND title_clean = '$title_clean'";
	$success = mysqli_query($con, $query);
	if (!$success) {
		return false;
	}
	return mysqli_fetch_array($success);
}

function getProject($con, $title_clean, $lang) {
	$query = "SELECT * FROM projects WHERE lang = '$lang' AND title_clean = '$title_clean'";
	$success = mysqli_query($con, $query);
	if (!$success) {
		echo "Error message = ".mysqli_error($con); 
		return false;
	}
	return mysqli_fetch_array($success);
}

function getProjectList($con, $lang) {
	$query = "SELECT * FROM projects WHERE lang = '$lang' AND wip = 1 ORDER BY year DESC, title";
	$success = mysqli_query($con, $query);
	if (!$success) {
		return false;
	}
	$projectlist = array();
	while($project = mysqli_fetch_array($success)) {
		if ($lang == 'de-AT') {
			$projectlist['Laufende Arbeiten'][] = $project;
		} else {
			$projectlist['Work in Progress'][] = $project;
		}
	}
	$query = "SELECT * FROM projects WHERE lang = '$lang' AND wip = 0 ORDER BY year DESC, title";
	$success = mysqli_query($con, $query);
	if (!$success) {
		return false;
	}
	while($project = mysqli_fetch_array($success)) {
		$projectlist[$project['year']][] = $project;
	}	
	return $projectlist;
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

$smarty->assign('lang', $lang);
$translateURL = buildTranslateURL($lang);
$smarty->assign('translateURL', $translateURL);
if ((sanitizeFilter($_GET['site']) === '' || 
		sanitizeFilter($_GET['site']) === 'bio') &&
		sanitizeFilter($_GET['subsite']) === '' && 
		$result = getPage($con, 'bio', $lang)) {
	$smarty->assign('title', $result['title']);
	$smarty->assign('content', $result['content']);
	$smarty->display('page.tpl');
} else if (sanitizeFilter($_GET['site']) === 'vita' &&
		sanitizeFilter($_GET['subsite']) === '' && 
		$result = getPage($con, 'vita', $lang)) {
	$smarty->assign('title', $result['title']);
	$smarty->assign('content', $result['content']);
	$smarty->display('page.tpl');
} else if (sanitizeFilter($_GET['site']) === 'portfolio') {
	if (sanitizeFilter($_GET['subsite']) === '') {
		$projectlist = getProjectList($con, $lang);
		if ($projectlist) {
			$smarty->assign('projectlist', $projectlist);
			$smarty->display('portfolio.tpl');
		} else {
			header('HTTP/1.0 404 Not Found');
			$smarty->display('404.tpl');
		}
	} else {
		$result = getProject($con, sanitizeFilter($_GET['subsite']), $lang);
		if ($result) {
			$smarty->assign('title', $result['title']);
			$smarty->assign('content', $result['content']);
			$smarty->display('page.tpl');
		} else {
			header('HTTP/1.0 404 Not Found');
			$smarty->display('404.tpl');
		}
	}
} else {
	header('HTTP/1.0 404 Not Found');
	$smarty->display('404.tpl');
}

?>