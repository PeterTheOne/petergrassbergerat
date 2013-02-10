<?php

// INCLUDES

include_once("config.inc.php");
include_once("functions.inc.php");
include_once("smarty.inc.php");

// INIT

$smarty = s_init();
$lang = getLang();
$langNot = getLangNot();
$url = getURL($lang);
header("Content-language: $lang");

// TODO: make url nonstatic
$apiPath = 'http://localhost/petergrassbergerat/api';

// DISPLAY

$site = isset($_GET['site']) ? sanitize($_GET['site']) : 'bio';
$subsite = isset($_GET['subsite']) ? sanitize($_GET['subsite']) : '';

// translateURL
$smarty->assign('lang', $lang);
$smarty->assign('url', $url);
$translateURL = buildTranslateURL($lang, $site, $subsite);
$smarty->assign('translateURL', $translateURL);

// display site
if ($site === '404') {
	header('HTTP/1.0 404 Not Found');
	$smarty->display('404.tpl');
} else if ($site === 'portfolio' && $subsite === '') {
    // get portfolio overview:
    $parameters = array(
        'lang' => $lang,
        'wip' => 1,
        'orderby' => 'wip DESC,year DESC,title'
    );
    $wipProjectList = getJsonFromUrl($apiPath . '/project', $parameters);

    $parameters = array(
        'lang' => $lang,
        'wip' => 0,
        'orderby' => 'year DESC,title'
    );
    $yearsProjectList = getJsonFromUrl($apiPath . '/project', $parameters);

	if (count($wipProjectList) + count($yearsProjectList) != 0 ) {
        $smarty->assign('wipProjectList', $wipProjectList);
        $smarty->assign('yearsProjectList', $yearsProjectList);
		$smarty->display('portfolio.tpl');
	} else {
		header('HTTP/1.0 404 Not Found');
		$smarty->display('404.tpl');
	}
} else {
	if ($subsite === '') {
        // get page
        $parameters = array(
            'lang' => $lang,
            'title_clean' => $site
        );
        $result = getJsonFromUrl($apiPath . '/page', $parameters);
        //print_r($result);
		if (isset($result[0]['downloadlink']) &&
				$result[0]['downloadlink'] != '') {
			$smarty->assign('downloadLink', $result[0]['downloadlink']);
		}
	} else if ($site === 'portfolio') {
        $parameters = array(
            'lang' => $lang,
            'title_clean' => $subsite
        );
        $result = getJsonFromUrl($apiPath . '/project', $parameters);
	}
	if ($result) {
		$smarty->assign('title', $result[0]['title']);
		$smarty->assign('content', $result[0]['content']);
		$smarty->display('page.tpl');
	} else {
		header('HTTP/1.0 404 Not Found');
		$smarty->display('404.tpl');
	}
}

?>
