<?php

require '../vendor/autoload.php';
require_once "../includes/piwik/PiwikTracker.php";

$app = new \Slim\Slim(array('debug' => false));

$app->response->headers->set('Content-Type', 'application/json');
$app->response->headers->set('Access-Control-Allow-Origin', '*');

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
    // todo: get siteId and url from config:
    $piwikTracker = new PiwikTracker(2, 'http://piwik.petergrassberger.com/');

    $ssl = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? true:false;
    $url = $ssl ? 'https://' : 'http://';
    $port = $_SERVER['SERVER_PORT'];
    $port = ((!$ssl && $port=='80') || ($ssl && $port=='443')) ? '' : ':' . $port;
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

$app->get('/', $trackView, function() use($app) {
    $app->render('docs.php');
    $app->response->headers->set('Content-Type', 'text/html');
})->setName('docs');

$app->get('/randomRequest(/)', $trackView, function() use($app) {
    $result = array('done' => 'done');
    $app->response->setBody(json_encode($result, JSON_PRETTY_PRINT));
})->setName('randomRequest');

$app->run();