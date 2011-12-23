<?php

// INCLUDES

include_once("functions.inc.php");
include_once("smarty.inc.php");
include_once("database.inc.php");

// INIT

$smarty = s_init();
$db_con = db_connect();
$lang = getLang();
$langNot = getLangNot();
header("Content-language: $lang");

// DISPLAY

$site = sanitize($_GET['site']);
$site = $site === '' ? 'bio' : $site;
$subsite = sanitize($_GET['subsite']);

// translateURL
$smarty->assign('lang', $lang);
if (checkExists($db_con, $langNot, $site, $subsite)) {
	$translateURL = buildTranslateURL($lang, $site, $subsite);
	$smarty->assign('translateURL', $translateURL);
}

// display site
if ($site === '404') {
	header('HTTP/1.0 404 Not Found');
	$smarty->display('404.tpl');
} else if ($site === 'portfolio' && 
		$subsite === '') {
	$projectlist = db_getProjectList($db_con, $lang);
	if ($projectlist) {
		$smarty->assign('projectlist', $projectlist);
		$smarty->display('portfolio.tpl');
	} else {
		header('HTTP/1.0 404 Not Found');
		$smarty->display('404.tpl');
	}
} else {
	if ($subsite === '') {
		$result = db_getPage($db_con, $lang, $site);
		if (isset($result['downloadlink']) && 
				$result['downloadlink'] != '') {
			$smarty->assign('downloadLink', $result['downloadlink']);
		}
	} else if ($site === 'portfolio') {
		$result = db_getProject($db_con, $lang, $subsite);
	}
	if ($result) {
		$smarty->assign('title', $result['title']);
		$smarty->assign('content', $result['content']);
		$smarty->display('page.tpl');
	} else {
		header('HTTP/1.0 404 Not Found');
		$smarty->display('404.tpl');
	}
}

?>
