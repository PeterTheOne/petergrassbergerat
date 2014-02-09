<?php

require '../vendor/autoload.php';

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

$app->get('/', function() use($app) {
    $app->render('docs.php');
    $app->response->headers->set('Content-Type', 'text/html');
});

$app->get('/randomRequest(/)', function() use($app) {
    $result = array('done' => 'done');
    $app->response->setBody(json_encode($result, JSON_PRETTY_PRINT));
});

$app->run();