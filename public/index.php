<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Peter Grassberger</title>
    </head>
    <body>
        <h1>Peter Grassberger</h1>

<?php

require_once '../config.php';
require_once '../application/repositories/PagesRepository.php';
require_once '../application/controllers/PagesController.php';

$pdo = new PDO('mysql:host=' . $config->databaseHost . ';dbname=' . $config->databaseName,
    $config->databaseUser, $config->databasePassword);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

$pagesController = new PagesController($config, $pdo);
$pages = $pagesController->get();

?>

        <?php foreach($pages as $page) { ?>
            <h2><?php echo $page->title; ?></h2>
            <?php echo $page->content; ?>
        <?php } ?>

    </body>
</html>