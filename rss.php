<?php

// INCLUDES

include_once("functions.inc.php");
include_once("smarty.inc.php");
include_once("database.inc.php");

// INIT

$smarty = s_init();
$db_con = db_connect();
$lang = getLang();
header("Content-Type:application/rss+xml; charset=utf-8");

// FETCH DATA

$pageOrder = array('last_change DESC', 'title');
$pageprojectlist = db_getPageProjectList($db_con, $lang, $pageOrder);

// DISPLAY
$smarty->assign('lang', $lang);
if ($lang === 'de-AT') {
	$smarty->assign('url', 'http://petergrassberger.at');
} else {
	$smarty->assign('url', 'http://petergrassberger.com');
}
$smarty->assign('pageprojectlist', $pageprojectlist);
$smarty->display('rss.tpl');

?>
