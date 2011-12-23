<?php

// INCLUDES

include_once("functions.inc.php");
include_once("smarty.inc.php");
include_once("database.inc.php");

// INIT

$smarty = s_init();
$db_con = db_connect();

// DISPLAY

$site = sanitize($_GET['site']);
$subsite = sanitize($_GET['subsite']);
$lang = sanitize($_GET['lang']);

// display site

if ($subsite === '') {
	$result = db_getPage($db_con, $lang, $site);
	$smarty->assign('page', true);
} else if ($site === 'portfolio') {
	$result = db_getProject($db_con, $lang, $subsite);
}
if ($result) {
	$smarty->assign('data', $result);
	$smarty->display('admin-page.tpl');
} else {
	$pagelist = db_getPageList($db_con);
	$smarty->assign('pagelist', $pagelist);
	
	$projectlist = db_getProjectList($db_con);
	$smarty->assign('projectlist', $projectlist);
	
	$smarty->display('admin-overview.tpl');
}



?>
