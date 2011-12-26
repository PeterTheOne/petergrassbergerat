<?php

// INCLUDES

include_once("functions.inc.php");
include_once("smarty.inc.php");
include_once("database.inc.php");

// INIT

$smarty = s_init();
$db_con = db_connect();

// DISPLAY

// prepare variables
if (isset($_GET['state']) && isset($_GET['type'])) {
	$state = sanitize($_GET['state']);
	$type = sanitize($_GET['type']);
	$smarty->assign('type', $type);
	$title_clean = sanitize($_GET['title_clean']);
	$lang = sanitize($_GET['lang']);
} else {
	$state = 'overview';
}

// state switch
if ($state === 'edit' || $state === 'create') {
	$smarty->assign('state', $state);
	if ($type === 'page') {
		$result = db_getPage($db_con, $lang, $title_clean);
	} else {
		$result = db_getProject($db_con, $lang, $title_clean);
	}
	if ($result) {
		$smarty->assign('data', $result);
		$smarty->display('admin-page.tpl');
	} else {
		$smarty->assign('info', 'page or project not found');
		$state = 'overview';
	}
} else if ($state === 'insert') {
	// TODO: insert
	$success = true;
	if ($success) {
		$smarty->assign('info', 'insert was success');
	} else {
		$smarty->assign('info', 'insert failed');
	}
	$state = 'overview';
} else if ($state === 'update') {
	if ($type === 'page') {
		$result = db_updatePage(
			$db_con, 
			$lang, 
			$title_clean, 
			sanitize($_GET['title']), 
			sanitize($_GET['downloadlink']), 
			$_GET['content']);
	} else {
		$result = db_updateProject(
			$db_con, 
			$lang, 
			$title_clean, 
			sanitize($_GET['title']), 
			sanitize($_GET['year']), 
			sanitize($_GET['wip']), 
			sanitize($_GET['tags']), 
			sanitize($_GET['description']), 
			$_GET['content']);
	}
	if ($result) {
		$smarty->assign('info', 'update was success');
	} else {
		$smarty->assign('info', 'update failed');
	}
	$state = 'overview';
} else if ($state === 'delete') {
	// TODO: delete
	$success = true;
	if ($success) {
		$smarty->assign('info', 'delete was success');
	} else {
		$smarty->assign('info', 'delete failed');
	}
	$state = 'overview';
}

// fallback
if ($state === 'overview') {
	$pagelist = db_getPageList($db_con);
	$smarty->assign('pagelist', $pagelist);
	
	$projectlist = db_getProjectList($db_con);
	$smarty->assign('projectlist', $projectlist);
	
	$smarty->display('admin-overview.tpl');
}

?>
