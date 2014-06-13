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

/**
 * DISPLAY ROOT, PROJECTS AND BLOG
 */

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

/**
 * ADMIN LOGIN AND LOGOUT
 */

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

/**
 * ADMIN PAGES
 */

$app->get('/admin(/)', $trackView, $authenticate($app, $config), function() use($app, $config, $pdo, $mustache) {
    $app->response->setBody($mustache->loadTemplate('admin')->render());
})->setName('admin');

$app->get('/admin/pages(/)', $trackView, $authenticate($app, $config), function() use($app, $config, $pdo, $mustache) {
    $pagesController = new PagesController($config, $pdo);
    $pages = $pagesController->getAllByType('page');

    $app->response->setBody($mustache->loadTemplate('adminPages')->render(array('pages' => $pages)));
})->setName('adminPages');

$app->get('/admin/pages/:pageTitle(/)', $trackView, $authenticate($app, $config), function($pageTitle) use($app, $config, $pdo, $mustache) {
    $pagesController = new PagesController($config, $pdo);
    $page = $pagesController->getOneByTypeAndTitle('page', $pageTitle);

    $app->response->setBody($mustache->loadTemplate('adminPageEdit')->render(array('page' => $page)));
})->setName('adminPageEdit');

$app->post('/admin/pages/:pagesTitle(/)', $trackView, $authenticate($app, $config), function($pagesTitle) use($app, $config, $pdo, $mustache) {
    $title = $app->request()->post('title');
    $title_clean = $app->request()->post('title_clean');
    $content = $app->request()->post('content');

    if ($title === null || $title == '' ||
        $title_clean === null || $title_clean == '' ||
        $content === null || $content == ''){
        throw new Exception('Not all params set.');
    }

    $pagesController = new PagesController($config, $pdo);
    $pagesController->updatePage($pagesTitle, $title, $title_clean, $content);

    $app->redirect('/admin/pages/');
})->setName('adminPageEditPost');

$app->get('/admin/projects(/)', $trackView, $authenticate($app, $config), function() use($app, $config, $pdo, $mustache) {
    $pagesController = new PagesController($config, $pdo);
    $projects = $pagesController->getAllByType('project');

    $app->response->setBody($mustache->loadTemplate('adminProjects')->render(array('projects' => $projects)));
})->setName('adminProjects');

$app->get('/admin/projects/:projectTitle(/)', $trackView, $authenticate($app, $config), function($projectTitle) use($app, $config, $pdo, $mustache) {
    $pagesController = new PagesController($config, $pdo);
    $project = $pagesController->getOneByTypeAndTitle('project', $projectTitle);

    $app->response->setBody($mustache->loadTemplate('adminProjectEdit')->render(array('project' => $project)));
})->setName('adminProjectEdit');

$app->post('/admin/projects/:projectsTitle(/)', $trackView, $authenticate($app, $config), function($projectTitle) use($app, $config, $pdo, $mustache) {
    $title = $app->request()->post('title');
    $title_clean = $app->request()->post('title_clean');
    $content = $app->request()->post('content');

    if ($title === null || $title == '' ||
        $title_clean === null || $title_clean == '' ||
        $content === null || $content == ''){
        throw new Exception('Not all params set.');
    }

    $pagesController = new PagesController($config, $pdo);
    $pagesController->updateProject($projectTitle, $title, $title_clean, $content);

    $app->redirect('/admin/projects/');
})->setName('adminProjectEditPost');

$app->get('/admin/posts(/)', $trackView, $authenticate($app, $config), function() use($app, $config, $pdo, $mustache) {
    $pagesController = new PagesController($config, $pdo);
    $posts = $pagesController->getAllByType('post');

    $app->response->setBody($mustache->loadTemplate('adminPosts')->render(array('posts' => $posts)));
})->setName('adminPosts');

$app->get('/admin/posts/:postTitle(/)', $trackView, $authenticate($app, $config), function($postTitle) use($app, $config, $pdo, $mustache) {
    $pagesController = new PagesController($config, $pdo);
    $post = $pagesController->getOneByTypeAndTitle('post', $postTitle);

    $app->response->setBody($mustache->loadTemplate('adminPostEdit')->render(array('post' => $post)));
})->setName('adminPostEdit');

$app->post('/admin/posts/:postsTitle(/)', $trackView, $authenticate($app, $config), function($postsTitle) use($app, $config, $pdo, $mustache) {
    $title = $app->request()->post('title');
    $title_clean = $app->request()->post('title_clean');
    $content = $app->request()->post('content');

    if ($title === null || $title == '' ||
        $title_clean === null || $title_clean == '' ||
        $content === null || $content == ''){
        throw new Exception('Not all params set.');
    }

    $pagesController = new PagesController($config, $pdo);
    $pagesController->updatePost($postsTitle, $title, $title_clean, $content);

    $app->redirect('/admin/posts/');
})->setName('adminPostEditPost');

/**
 * ADMIN CREATE PAGES, PROJECTS, POSTS
 */

$app->get('/admin/create/page(/)', $trackView, $authenticate($app, $config), function() use($app, $config, $pdo, $mustache) {
    $app->response->setBody($mustache->loadTemplate('adminCreatePage')->render());
})->setName('adminCreatePage');

$app->post('/admin/create/page(/)', $trackView, $authenticate($app, $config), function() use($app, $config, $pdo, $mustache) {
    $title = $app->request()->post('title');
    $title_clean = $app->request()->post('title_clean');
    $content = $app->request()->post('content');

    if ($title === null || $title == '' ||
        $title_clean === null || $title_clean == '' ||
        $content === null || $content == ''){
        throw new Exception('Not all params set.');
    }

    $pagesController = new PagesController($config, $pdo);
    $pagesController->createPage($title, $title_clean, $content);

    $app->redirect('/admin/pages/');
})->setName('adminCreatePagePost');

$app->get('/admin/create/project(/)', $trackView, $authenticate($app, $config), function() use($app, $config, $pdo, $mustache) {
    $app->response->setBody($mustache->loadTemplate('adminCreateProject')->render());
})->setName('adminCreateProject');

$app->post('/admin/create/project(/)', $trackView, $authenticate($app, $config), function() use($app, $config, $pdo, $mustache) {
    $title = $app->request()->post('title');
    $title_clean = $app->request()->post('title_clean');
    $content = $app->request()->post('content');

    if ($title === null || $title == '' ||
        $title_clean === null || $title_clean == '' ||
        $content === null || $content == ''){
        throw new Exception('Not all params set.');
    }

    $pagesController = new PagesController($config, $pdo);
    $pagesController->createProject($title, $title_clean, $content);

    $app->redirect('/admin/projects/');
})->setName('adminCreateProjectPost');

$app->get('/admin/create/post(/)', $trackView, $authenticate($app, $config), function() use($app, $config, $pdo, $mustache) {
    $app->response->setBody($mustache->loadTemplate('adminCreatePost')->render());
})->setName('adminCreatePost');

$app->post('/admin/create/post(/)', $trackView, $authenticate($app, $config), function() use($app, $config, $pdo, $mustache) {
    $title = $app->request()->post('title');
    $title_clean = $app->request()->post('title_clean');
    $content = $app->request()->post('content');

    if ($title === null || $title == '' ||
        $title_clean === null || $title_clean == '' ||
        $content === null || $content == ''){
        throw new Exception('Not all params set.');
    }

    $pagesController = new PagesController($config, $pdo);
    $pagesController->createPost($title, $title_clean, $content);

    $app->redirect('/admin/posts/');
})->setName('adminCreatePostPost');

/**
 * ADMIN REMOVE PAGES, PROJECTS, POSTS
 */

$app->get('/admin/remove/page/:pageTitle(/)', $trackView, $authenticate($app, $config), function($pageTitle) use($app, $config, $pdo, $mustache) {
    $app->response->setBody($mustache->loadTemplate('adminRemovePage')->render(array('pageTitle' => $pageTitle)));
})->setName('adminRemovePage');

$app->post('/admin/remove/page/:pageTitle(/)', $trackView, $authenticate($app, $config), function($pageTitle) use($app, $config, $pdo, $mustache) {
    $pagesController = new PagesController($config, $pdo);
    $pagesController->removePage($pageTitle);

    $app->redirect('/admin/pages/');
})->setName('adminRemovePagePost');

$app->get('/admin/remove/project/:projectTitle(/)', $trackView, $authenticate($app, $config), function($projectTitle) use($app, $config, $pdo, $mustache) {
    $app->response->setBody($mustache->loadTemplate('adminRemoveProject')->render(array('projectTitle' => $projectTitle)));
})->setName('adminRemoveProject');

$app->post('/admin/remove/project/:projectTitle(/)', $trackView, $authenticate($app, $config), function($projectTitle) use($app, $config, $pdo, $mustache) {
    $pagesController = new PagesController($config, $pdo);
    $pagesController->removeProject($projectTitle);

    $app->redirect('/admin/projects/');
})->setName('adminRemoveProjectPost');

$app->get('/admin/remove/post/:postTitle(/)', $trackView, $authenticate($app, $config), function($postTitle) use($app, $config, $pdo, $mustache) {
    $app->response->setBody($mustache->loadTemplate('adminRemovePost')->render(array('postTitle' => $postTitle)));
})->setName('adminRemovePost');

$app->post('/admin/remove/post/:postTitle(/)', $trackView, $authenticate($app, $config), function($postTitle) use($app, $config, $pdo, $mustache) {
    $pagesController = new PagesController($config, $pdo);
    $pagesController->removePost($postTitle);

    $app->redirect('/admin/posts/');
})->setName('adminRemovePostPost');

/**
 * DISPLAY PAGES, PROJECTS AND POSTS
 */

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



