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

// TODO: session timeout?
if (!userLoginValid()) {
	header('Location: admin_login.php');
	exit;
}

// DISPLAY

if (isset($_GET['error'])) {
	$error = sanitize($_GET['error']);
	if ($error == '404') {
		$smarty->assign('error', 'page or project not found');
	}
} else if (isset($_GET['info'])) {
	$info = sanitize($_GET['info']);
	if ($info == 'update_success') {
		$smarty->assign('info', 'update was successful');
	} else if ($info == 'insert_success') {
		$smarty->assign('info', 'insert was successful');
	}
}

$pagelist = db_getPageList($db_con);
$smarty->assign('pagelist', $pagelist);

$projectlist = db_getProjectList($db_con);
$smarty->assign('projectlist', $projectlist);

$smarty->assign('token', createToken());
$smarty->display('admin_overview.tpl');

?>
