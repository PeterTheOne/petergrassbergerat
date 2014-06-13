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
$app->add(new \Slim\Middleware\SessionCookie());

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

$authenticate = function ($app, $config) {
    return function () use ($app, $config) {
        if (!isset($_SESSION['login']) ||
                $_SESSION['login'] !== true ||
                !isset($_SESSION['HTTP_USER_AGENT']) ||
                $_SESSION['HTTP_USER_AGENT'] !== sha1($config->sessionSalt . $_SERVER['HTTP_USER_AGENT'])) {
            $_SESSION['urlRedirect'] = $app->request()->getPathInfo();
            $app->redirect('/admin/login/');
        }
    };
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

$app->get('/admin(/)', $trackView, $authenticate($app, $config), function() use($app, $config, $pdo, $mustache) {
    $app->response->setBody($mustache->loadTemplate('admin')->render());
})->setName('admin');

$app->get('/admin/login(/)', $trackView, function() use($app, $config, $pdo, $mustache) {
    $loginTemplate = $mustache->loadTemplate('login');
    $app->response->setBody($loginTemplate->render());
})->setName('adminLogin');

$app->post('/admin/login(/)', $trackView, function() use($app, $config, $pdo, $mustache) {
    $username = $app->request()->post('username');
    $password = $app->request()->post('password');
    if ($username === null || $username == '' ||
            $password === null || $password == ''){
        // todo: change exception!!!
        throw new Exception('Not all params set.');
    }

    if ($username !== $config->adminUsername &&
            $password !== $config->adminPassword) {
        $app->redirect('/admin/login/');
    }

    $_SESSION['login'] = true;
    $_SESSION['HTTP_USER_AGENT'] = sha1($config->sessionSalt . $_SERVER['HTTP_USER_AGENT']);

    if (isset($_SESSION['urlRedirect'])) {
        $urlRedirect = $_SESSION['urlRedirect'];
        unset($_SESSION['urlRedirect']);
        $app->redirect($urlRedirect);
    }

    $app->redirect('/admin/');
})->setName('adminLoginPost');

$app->get('/admin/logout(/)', $trackView, function() use($app, $config, $pdo, $mustache) {
    unset($_SESSION['login']);
    unset($_SESSION['HTTP_USER_AGENT']);
    $app->redirect('/admin/login/');
})->setName('adminLogout');

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



