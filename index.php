<?php

// INCLUDES

include_once("config.inc.php");
include_once("functions.inc.php");
include_once("smarty.inc.php");

// INIT

$smarty = s_init();
$lang = getLang();
$langNot = getLangNot();
header("Content-language: $lang");

// TODO: make url nonstatic
define('API_PATH', 'http://localhost/petergrassbergerat/api');

// DISPLAY

$site = isset($_GET['site']) ? sanitize($_GET['site']) : 'bio';
$subsite = isset($_GET['subsite']) ? sanitize($_GET['subsite']) : '';

// translateURL
$smarty->assign('lang', $lang);
$smarty->assign('baseUrl', BASEURL);
$translateURL = buildTranslateURL($lang, $site, $subsite);
$smarty->assign('translateURL', $translateURL);


// display site
switch ($site) {
    case '404':
        display404($smarty);
        break;

    case 'portfolio':
        if (empty($subsite)) {
            displayPortfolio($smarty, $lang);
        } else {
            displayProjectPage($smarty, $lang, $subsite);
        }
        break;

    case 'blog':
        if (empty($subsite)) {
            displayBlog($smarty, $lang);
        } else {
            displayBlogPost($smarty, $lang, $subsite);
        }
        break;

    default:
        if (empty($subsite)) {
            displayPage($smarty, $site, $subsite, $lang);
        } else {
            display404($smarty);
        }
        break;
}

function displayPage($smarty, $site, $subsite, $lang) {
    $parameters = array(
        'lang' => $lang,
        'title_clean' => $site
    );
    $result = getJsonFromUrl(API_PATH . '/page', $parameters);
    if (isset($result[0]['downloadlink']) &&
            $result[0]['downloadlink'] != '') {
        $smarty->assign('downloadLink', $result[0]['downloadlink']);
    }
    if ($result) {
        $smarty->assign('title', $result[0]['title']);
        $smarty->assign('content', $result[0]['content']);
        $smarty->display('page.tpl');
    } else {
        display404($smarty);
    }
}

function displayPortfolio($smarty, $lang) {
    $parameters = array(
        'lang' => $lang,
        'wip' => 1,
        'orderby' => 'wip DESC,year DESC,title'
    );
    $wipProjectList = getJsonFromUrl(API_PATH . '/project', $parameters);

    $parameters = array(
        'lang' => $lang,
        'wip' => 0,
        'orderby' => 'year DESC,title'
    );
    $yearsProjectList = getJsonFromUrl(API_PATH . '/project', $parameters);

    if (count($wipProjectList) + count($yearsProjectList) != 0 ) {
        $smarty->assign('wipProjectList', $wipProjectList);
        $smarty->assign('yearsProjectList', $yearsProjectList);
        $smarty->display('portfolio.tpl');
    } else {
        display404($smarty);
    }
}

function displayProjectPage($smarty, $lang, $subsite) {
    $parameters = array(
        'lang' => $lang,
        'title_clean' => $subsite
    );
    $result = getJsonFromUrl(API_PATH . '/project', $parameters);

    if ($result) {
        $smarty->assign('title', $result[0]['title']);
        $smarty->assign('content', $result[0]['content']);
        $smarty->display('page.tpl');
    } else {
        display404($smarty);
    }
}

function displayBlog($smarty, $lang) {
    $parameters = array(
        'lang' => $lang,
        'orderby' => 'datetimeCreated DESC,title'
    );
    $blogPostList = getJsonFromUrl(API_PATH . '/post', $parameters);

    $smarty->assign('blogPostList', $blogPostList);
    $smarty->display('blog.tpl');
}

function displayBlogPost($smarty, $lang, $subsite) {
    $parameters = array(
        'lang' => $lang,
        'title_clean' => $subsite,
    );

    $blogPost = getJsonFromUrl(API_PATH . '/post', $parameters);

    if ($blogPost) {
        $smarty->assign('blogPostList', $blogPost);
        $smarty->display('blog.tpl');
    } else {
        display404($smarty);
    }
}

function display404($smarty) {
    header('HTTP/1.0 404 Not Found');
    $smarty->display('404.tpl');
}

?>
