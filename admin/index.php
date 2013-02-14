<?php

// INCLUDES

include_once("../config.inc.php");
include_once("../functions.inc.php");
include_once("smarty.inc.php");
include_once("admin_functions.inc.php");

// REDIRECT TO HTTPS

if (HTTPS_REDIRECT) {
	redirectToHTTPS();
}

// INIT

session_start();
$smarty = s_init();
$smarty->assign('baseUrl', BASEURL);

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


$site = isset($_GET['site']) ? sanitize($_GET['site']) : 'overview';

if ($site === 'overview') {
    assignPages($smarty);
    assignProjects($smarty);
    assignBlogPosts($smarty);

    $smarty->assign('token', createToken());
    $smarty->display('admin_overview.tpl');
} else if ($site === 'page') {
    assignPages($smarty);

    $smarty->assign('token', createToken());
    $smarty->display('admin_overview.tpl');
} else if ($site === 'project') {
    assignProjects($smarty);

    $smarty->assign('token', createToken());
    $smarty->display('admin_overview.tpl');
} else if ($site === 'post') {
    assignBlogPosts($smarty);

    $smarty->assign('token', createToken());
    $smarty->display('admin_overview.tpl');
} else {
    header('HTTP/1.0 404 Not Found');
    $smarty->display('404.tpl');
}

function assignPages($smarty) {
    $parameters = array(
        'orderby' => 'title_clean,lang'
    );
    $pageList = getJsonFromUrl(API_PATH . '/page', $parameters);
    $smarty->assign('pageList', $pageList);
}

function assignProjects($smarty) {
    $parameters = array(
        'wip' => 1,
        'orderby' => 'wip DESC,year DESC,title_clean,lang'
    );
    $wipProjectList = getJsonFromUrl(API_PATH . '/project', $parameters);
    $parameters = array(
        'wip' => 0,
        'orderby' => 'year DESC,title_clean,lang'
    );
    $yearsProjectList = getJsonFromUrl(API_PATH . '/project', $parameters);
    $smarty->assign('wipProjectList', $wipProjectList);
    $smarty->assign('yearsProjectList', $yearsProjectList);
}

function assignBlogPosts($smarty) {
    $parameters = array(
        'orderby' => 'datetimeCreated DESC,title_clean,lang'
    );
    $blogPostList = getJsonFromUrl(API_PATH . '/post', $parameters);
    $smarty->assign('blogPostList', $blogPostList);
}

?>
