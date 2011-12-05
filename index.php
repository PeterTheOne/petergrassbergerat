<?php

// INCLUDES

include_once("functions.inc.php");
include_once("smarty.inc.php");
include_once("database.inc.php");

// INIT

$smarty = s_init();
$db_con = db_connect();
$lang = getLang();
header("Content-language: $lang");

// DISPLAY

$smarty->assign('lang', $lang);
$translateURL = buildTranslateURL($lang);
$smarty->assign('translateURL', $translateURL);
if ((sanitizeFilter($_GET['site']) === '' || 
		sanitizeFilter($_GET['site']) === 'bio') &&
		sanitizeFilter($_GET['subsite']) === '' && 
		$result = db_getPage($db_con, 'bio', $lang)) {
	$smarty->assign('title', $result['title']);
	$smarty->assign('content', $result['content']);
	$smarty->display('page.tpl');
} else if (sanitizeFilter($_GET['site']) === 'vita' &&
		sanitizeFilter($_GET['subsite']) === '' && 
		$result = db_getPage($db_con, 'vita', $lang)) {
	$smarty->assign('title', $result['title']);
	$smarty->assign('content', $result['content']);
	$smarty->display('page.tpl');
} else if (sanitizeFilter($_GET['site']) === 'portfolio') {
	if (sanitizeFilter($_GET['subsite']) === '') {
		$projectlist = db_getProjectList($db_con, $lang);
		if ($projectlist) {
			$smarty->assign('projectlist', $projectlist);
			$smarty->display('portfolio.tpl');
		} else {
			header('HTTP/1.0 404 Not Found');
			$smarty->display('404.tpl');
		}
	} else {
		$result = db_getProject($db_con, sanitizeFilter($_GET['subsite']), $lang);
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