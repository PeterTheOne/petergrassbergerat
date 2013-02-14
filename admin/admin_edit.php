<?php

// INCLUDES

include_once("../config.inc.php");
include_once("../functions.inc.php");
include_once("admin_functions.inc.php");
include_once("smarty.inc.php");
include_once("../database.inc.php");

// LOGIC

session_start();

if (HTTPS_REDIRECT) {
	redirectToHTTPS();
}

if (!userLoginValid()) {
	header('Location: admin_login.php');
	exit;
}

if (isset($_GET['type']) && 
		isset($_GET['title_clean']) && 
		isset($_GET['lang'])) {
	$type = sanitize($_GET['type']);
	$title_clean = sanitize($_GET['title_clean']);
	$lang = sanitize($_GET['lang']);
} else {
	header('Location: index.php');
	exit;
}

$smarty = s_init();
$smarty->assign('baseUrl', BASEURL);
$smarty->assign('type', $type);
$db_con = db_connect();

if (isset($_POST['title']) && 
		isset($_POST['title_clean']) && 
		isset($_POST['lang']) && 
		isTokenValid()) {
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
		header('Location: index.php?info=update_success');
		exit;
	} else {
		$smarty->assign('info', 'update failed');
	}
}

$smarty->assign('token', createToken());
$smarty->assign('state', 'edit');
if ($type === 'page') {
	$result = db_getPage($db_con, $lang, $title_clean);
} else {
	$result = db_getProject($db_con, $lang, $title_clean);
}
if ($result) {
	$smarty->assign('data', $result);
	$smarty->display('admin_page.tpl');
} else {
	header('Location: index.php?error=404');
}

?>
