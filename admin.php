<?php

// INCLUDES

include_once("functions.inc.php");
include_once("smarty.inc.php");
include_once("database.inc.php");

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
if (isset($_GET['state'])) {
	if ($_GET['state'] === 'logout' && isTokenValid()) {
		$smarty->assign('token', createToken());
		$_SESSION = array();
		session_destroy();
	} else if ($_GET['state'] === 'login' && isTokenValid()) {
		$smarty->assign('token', createToken());
		$username = sanitize($_POST['username']);
		$password = sanitize($_POST['password']);
		if ($username === ADMIN_USER && sha1(PASSWORD_SALT . $password) === ADMIN_PASS) {
			session_regenerate_id();
			$_SESSION['login'] = true;
			$_SESSION['HTTP_USER_AGENT'] = sha1(SESSION_SALT . $_SERVER['HTTP_USER_AGENT']);
		} else {
			$smarty->assign('info', 'wrong login data');
		}
	}
}

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true || 
		!isset($_SESSION['HTTP_USER_AGENT']) || 
		$_SESSION['HTTP_USER_AGENT'] != 
		sha1(SESSION_SALT . $_SERVER['HTTP_USER_AGENT'])) {
	$smarty->assign('token', createToken());
	$smarty->display('admin-login.tpl');
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
if ($state === 'edit' || $state === 'create') {
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
} else if ($state === 'insert' && isTokenValid()) {
	$smarty->assign('token', createToken());
	// TODO: insert
	$success = true;
	if ($success) {
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
