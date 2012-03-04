<?php

// INCLUDES

include_once("functions.inc.php");
include_once("smarty.inc.php");
include_once("database.inc.php");

// INIT

$smarty = s_init();
$db_con = db_connect();
$lang = getLang();
$url = getURL($lang);
header("Content-Type:text/xml");

// FETCH DATA

$pagelist = db_getPageList($db_con, $lang);
$projectlist = db_getProjectList($db_con, $lang);

// DISPLAY
$smarty->assign('url', $url);
$smarty->assign('pagelist', $pagelist);
$smarty->assign('projectlist', $projectlist);
$smarty->display('sitemap.tpl');

?>
