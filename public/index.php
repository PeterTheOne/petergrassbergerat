<?php

require_once '../vendor/autoload.php';

require_once '../libraries/piwik/PiwikTracker.php';
require_once '../config.php';
require_once '../application/repositories/PagesRepository.php';
require_once '../application/controllers/PagesController.php';

$pdo = new PDO('mysql:host=' . $config->databaseHost . ';dbname=' . $config->databaseName,
    $config->databaseUser, $config->databasePassword);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

$pagesController = new PagesController($config, $pdo);
$pages = $pagesController->get();

$mustache = new Mustache_Engine(array(
    'loader' => new Mustache_Loader_FilesystemLoader(dirname(__FILE__).'/templates')
));
$template = $mustache->loadTemplate('index');
echo $template->render(array('pages' => $pages));