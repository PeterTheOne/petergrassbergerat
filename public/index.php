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

$app->get('/', $trackView, function() use($app, $config, $pdo) {

    $pagesController = new PagesController($config, $pdo);
    $pages = $pagesController->get();

    $mustache = new Mustache_Engine(array(
        'loader' => new Mustache_Loader_FilesystemLoader(dirname(__FILE__).'/templates')
    ));
    $template = $mustache->loadTemplate('index');
    $app->response->setBody($template->render(array('pages' => $pages)));
})->setName('index');

// ...

$app->run();



