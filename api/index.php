<?php

require '../vendor/autoload.php';

use Swagger\Swagger;

/**
 * @SWG\Resource(
 *     apiVersion="0.1",
 *     swaggerVersion="1.2",
 *     resourcePath="/",
 *     basePath="http://petergrassberger.at/api"
 * )
 */

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

$app->get('/swagger.json', function() use($app) {
    $swagger = new Swagger('index.php');
    $app->response->setBody($swagger->getResource('/', array('output' => 'json')));
});

/**
 * @SWG\Api(
 *   path="/randomRequest(/)",
 *   @SWG\Operation(
 *     method="GET",
 *     summary="Find pet by ID",
 *     notes="Returns a pet based on ID",
 *     type="Pet",
 *     nickname="getPetById",
 *     @SWG\Parameter(
 *       name="petId",
 *       description="ID of pet that needs to be fetched",
 *       required=true,
 *       type="integer",
 *       format="int64",
 *       paramType="path",
 *       minimum="1.0",
 *       maximum="100000.0"
 *     ),
 *     @SWG\ResponseMessage(code=400, message="Invalid ID supplied"),
 *     @SWG\ResponseMessage(code=404, message="Pet not found")
 *   )
 * )
 */
$app->get('/randomRequest(/)', function() use($app) {
    $result = array('done' => 'done');
    $app->response->setBody(json_encode($result, JSON_PRETTY_PRINT));
});

$app->run();