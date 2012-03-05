<?php

// INCLUDES

include_once("config.inc.php");
include_once("functions.inc.php");
include_once("admin_functions.inc.php");
include_once("smarty.inc.php");
include_once("database.inc.php");

// LOGIC

session_start();

if (HTTPS_REDIRECT) {
	redirectToHTTPS();
}

if (!userLoginValid()) {
	header('Location: admin_login.php');
	exit;
}

if (isset($_GET['type'])) {
	$type = sanitize($_GET['type']);
} else {
	header('Location: admin.php');
	exit;
}

$smarty = s_init();
$smarty->assign('type', $type);
$db_con = db_connect();

if (isset($_POST['title']) && 
		isset($_POST['title_clean']) && 
		isset($_POST['lang']) && 
		isTokenValid()) {
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
		header('Location: admin.php?info=insert_success');
		exit;
	} else {
		$smarty->assign('info', 'insert failed');
	}
}

$smarty->assign('token', createToken());
$smarty->assign('state', 'create');
$smarty->display('admin_page.tpl');

?>
