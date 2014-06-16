<?php

require_once '../../vendor/autoload.php';

// todo: autoload
require_once '../../libraries/piwik/PiwikTracker.php';
require_once '../../config.php';
require_once '../../application/repositories/PagesRepository.php';
require_once '../../application/controllers/PagesController.php';

$app = new \Slim\Slim(array(
    'debug' => false,
    'templates.path' => '../../application/templates/api'
));
$app->response->headers->set('Content-Type', 'application/json');
$app->response->headers->set('Access-Control-Allow-Origin', '*');

$pdo = new PDO('mysql:host=' . $config->databaseHost . ';dbname=' . $config->databaseName,
    $config->databaseUser, $config->databasePassword);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

$app->error(function(\Exception $exception) use ($app) {
    $status = 400;
    $result = array(
        'exception' => array(
            'status' => $status,
            'message' => $exception->getMessage()
        )
    );
    $app->response->headers->set('X-Status-Reason', $exception->getMessage());
    $app->response->setStatus($status);
    $app->response->setBody(json_encode($result, JSON_PRETTY_PRINT));
});

$trackView = function(\Slim\Route $route) {
    global $config;
    // todo: get siteId and url from config:
    $piwikTracker = new PiwikTracker(2, 'http://piwik.petergrassberger.com/');

    $piwikTracker->setTokenAuth($config->piwikAuthToken);
    $piwikTracker->setIp($_SERVER['REMOTE_ADDR']);

    $piwikTracker->doTrackPageView($route->getName());
};

$app->get('/', $trackView, function() use($app) {
    $app->render('docs.php');
    $app->response->headers->set('Content-Type', 'text/html');
})->setName('docs');

$app->get('/pages(/)', $trackView, function() use($app, $config, $pdo) {

    /*$pagesController = new PagesController($config, $pdo);
    $pages = $pagesController->getAll();

    $app->response->setBody(json_encode($pages, JSON_PRETTY_PRINT));*/
})->setName('pages');

// ...

$app->run();