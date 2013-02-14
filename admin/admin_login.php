<?php

// INCLUDES

include_once("../config.inc.php");
include_once("../functions.inc.php");
include_once("admin_functions.inc.php");
include_once("smarty.inc.php");

// LOGIC

session_start();

if (HTTPS_REDIRECT) {
	redirectToHTTPS();
}

if (userLoginValid()) {
	header('Location: index.php');
	exit;
} else {
	$smarty = s_init();
    $smarty->assign('baseUrl', BASEURL);
	if (isset($_POST['username']) && isset($_POST['password']) && isTokenValid()) {
		$smarty->assign('token', createToken());
		$username = sanitize($_POST['username']);
		$password = $_POST['password']; // no sanitize: html tags in password..
		if ($username === ADMIN_USER && sha1(PASSWORD_SALT . $password) === ADMIN_PASS) {
			session_regenerate_id();
			$_SESSION['login'] = true;
			$_SESSION['HTTP_USER_AGENT'] = sha1(SESSION_SALT . $_SERVER['HTTP_USER_AGENT']);
			header('Location: index.php');
			exit;
		} else {
			$smarty->assign('info', 'wrong login data');
			$smarty->display('admin_login.tpl');
		}
	} else {
		$smarty->assign('token', createToken());
		$smarty->display('admin_login.tpl');
	}
}

?>
