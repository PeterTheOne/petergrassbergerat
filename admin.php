<?php

// INCLUDES

include_once("config.inc.php");
include_once("functions.inc.php");
include_once("smarty.inc.php");
include_once("database.inc.php");
include_once("admin_functions.inc.php");

// REDIRECT TO HTTPS

if (HTTPS_REDIRECT) {
	redirectToHTTPS();
}

// INIT

session_start();
$smarty = s_init();
$db_con = db_connect();

// CHECK LOGIN

// TODO: cleanup!!
// TODO: session timeout
// TODO: split admin.php up in multiple files

if (!userLoginValid()) {
	header('Location: admin_login.php');
	exit;
}

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
if ($state === 'edit') {
	$smarty->assign('token', createToken());
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
} else if ($state === 'create') {
	$smarty->assign('token', createToken());
	$smarty->assign('state', $state);
	$smarty->display('admin-page.tpl');
} else if ($state === 'insert' && isTokenValid()) {
	$smarty->assign('token', createToken());
	if ($type === 'page') {
		$result = db_insertPage(
			$db_con, 
			sanitize($_POST['lang']),
			sanitize($_POST['title_clean']),
			sanitize($_POST['title']), 
			sanitize($_POST['downloadlink']), 
			$_POST['content']);
	} else {
		$result = db_insertProject(
			$db_con, 
			sanitize($_POST['lang']),
			sanitize($_POST['title_clean']),
			sanitize($_POST['title']), 
			sanitize($_POST['year']), 
			sanitize($_POST['wip']), 
			sanitize($_POST['tags']), 
			sanitize($_POST['description']), 
			$_POST['content']);
	}
	if ($result) {
		$smarty->assign('info', 'insert was successful');
	} else {
		$smarty->assign('info', 'insert failed');
	}
	$state = 'overview';
} else if ($state === 'update' && isTokenValid()) {
	$smarty->assign('token', createToken());
	if ($type === 'page') {
		$result = db_updatePage(
			$db_con, 
			$lang, 
			$title_clean, 
			sanitize($_POST['lang']),
			sanitize($_POST['title_clean']),
			sanitize($_POST['title']), 
			sanitize($_POST['downloadlink']), 
			$_POST['content']);
	} else {
		$result = db_updateProject(
			$db_con, 
			$lang, 
			$title_clean, 
			sanitize($_POST['lang']),
			sanitize($_POST['title_clean']),
			sanitize($_POST['title']), 
			sanitize($_POST['year']), 
			sanitize($_POST['wip']), 
			sanitize($_POST['tags']), 
			sanitize($_POST['description']), 
			$_POST['content']);
	}
	if ($result) {
		$smarty->assign('info', 'update was successful');
	} else {
		$smarty->assign('info', 'update failed');
	}
	$state = 'overview';
} else if ($state === 'delete' && isTokenValid()) {
	$smarty->assign('token', createToken());
	// TODO: delete
	$success = true;
	if ($success) {
		$smarty->assign('info', 'delete was successful');
	} else {
		$smarty->assign('info', 'delete failed');
	}
	$state = 'overview';
} else {
	$state = 'overview';
}

// fallback
if ($state === 'overview') {
	$pagelist = db_getPageList($db_con);
	$smarty->assign('pagelist', $pagelist);
	
	$projectlist = db_getProjectList($db_con);
	$smarty->assign('projectlist', $projectlist);
	
	$smarty->assign('token', createToken());
	$smarty->display('admin-overview.tpl');
}

?>
