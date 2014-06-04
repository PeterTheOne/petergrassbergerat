<?php

require_once '../vendor/autoload.php';

// todo: autoload
require_once '../libraries/piwik/PiwikTracker.php';
require_once '../config.php';
require_once '../application/repositories/PagesRepository.php';
require_once '../application/controllers/PagesController.php';

$app = new \Slim\Slim(array(
    'debug' => false
));

$pdo = new PDO('mysql:host=' . $config->databaseHost . ';dbname=' . $config->databaseName,
    $config->databaseUser, $config->databasePassword);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

$mustache = new Mustache_Engine(array(
    'loader' => new Mustache_Loader_FilesystemLoader(dirname(__FILE__) . '/../application/templates')
));

$app->error(function(\Exception $exception) use ($app) {
    $app->response->headers->set('X-Status-Reason', $exception->getMessage());
    $app->response->setBody($exception->getMessage());
});

$trackView = function(\Slim\Route $route) {
    // todo: get siteId and url from config:
    $piwikTracker = new PiwikTracker(1, 'http://piwik.petergrassberger.com/');

    $ssl = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? true : false;
    $url = $ssl ? 'https://' : 'http://';
    $port = $_SERVER['SERVER_PORT'];
    $port = ((!$ssl && $port == '80') || ($ssl && $port == '443')) ? '' : ':' . $port;
    $url .= isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
    $url .= $port . $_SERVER['REQUEST_URI'];
    $piwikTracker->setUrl($url);

    // don't track when calling from local development
    if (strpos($url, 'localhost') !== false) {
        return;
    }

    $piwikTracker->setIp($_SERVER['REMOTE_ADDR']);

    $piwikTracker->doTrackPageView($route->getName());
};

$app->get('/', $trackView, function() use($app, $config, $pdo, $mustache) {
    $pagesController = new PagesController($config, $pdo);
    $page = $pagesController->getOneIndex();

    $pageTemplate = $mustache->loadTemplate('page');
    $app->response->setBody($pageTemplate->render(array('page' => $page)));
})->setName('index');

$app->get('/projects(/)', $trackView, function() use($app, $config, $pdo, $mustache) {
    $pagesController = new PagesController($config, $pdo);
    $projects = $pagesController->getAllByType('project');

    $projectsTemplate = $mustache->loadTemplate('projects');
    $app->response->setBody($projectsTemplate->render(array('projects' => $projects)));
})->setName('portfolio');

$app->get('/blog(/)', $trackView, function() use($app, $config, $pdo, $mustache) {
    $pagesController = new PagesController($config, $pdo);
    $posts = $pagesController->getAllByType('post');

    $blogTemplate = $mustache->loadTemplate('blog');
    $app->response->setBody($blogTemplate->render(array('posts' => $posts)));
})->setName('portfolio');

$app->get('/:pageTitle(/)', $trackView, function($pageTitle) use($app, $config, $pdo, $mustache) {
    $pagesController = new PagesController($config, $pdo);
    $page = $pagesController->getOneByTypeAndTitle('page', $pageTitle);

    $pageTemplate = $mustache->loadTemplate('page');
    $app->response->setBody($pageTemplate->render(array('page' => $page)));
})->setName('pages');

$app->get('/projects/:projectTitle(/)', $trackView, function($projectTitle) use($app, $config, $pdo, $mustache) {
    $pagesController = new PagesController($config, $pdo);
    $project = $pagesController->getOneByTypeAndTitle('project', $projectTitle);

    $projectTemplate = $mustache->loadTemplate('project');
    $app->response->setBody($projectTemplate->render(array('project' => $project)));
})->setName('projects');

$app->get('/blog/:postTitle(/)', $trackView, function($postTitle) use($app, $config, $pdo, $mustache) {
    $pagesController = new PagesController($config, $pdo);
    $post = $pagesController->getOneByTypeAndTitle('post', $postTitle);

    $postTemplate = $mustache->loadTemplate('post');
    $app->response->setBody($postTemplate->render(array('post' => $post)));
})->setName('projects');

// ...

$app->run();



