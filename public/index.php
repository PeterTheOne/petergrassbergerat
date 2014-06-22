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

$defaultLanguage = 'en';
$language = $defaultLanguage;
if (strpos($_SERVER['HTTP_HOST'], '.com')) {
    $language = 'en';
} else if (strpos($_SERVER['HTTP_HOST'], '.at')) {
    $language = 'de';
}

$app->error(function(\Exception $exception) use ($app) {
    $app->response->headers->set('X-Status-Reason', $exception->getMessage());
    $app->response->setBody($exception->getMessage());
});

$trackView = function(\Slim\Route $route = null) {
    global $config;

    // don't track localhost (development)
    if(in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1', '::1'))) {
        return;
    }

    // todo: get siteId and url from config:
    $piwikTracker = new PiwikTracker(1, 'http://piwik.petergrassberger.com/');

    $piwikTracker->setTokenAuth($config->piwikAuthToken);
    $piwikTracker->setIp($_SERVER['REMOTE_ADDR']);

    if ($route === null) {
        $piwikTracker->doTrackPageView('');
    } else {
        $piwikTracker->doTrackPageView($route->getName());
    }
};

$authenticate = function(\SLIM\SLIM $app, $config) {
    return function() use ($app, $config) {
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
 * 404
 */

$app->notFound(function () use ($app, $trackView) {
    $trackView($app->router()->getCurrentRoute());
    $app->redirect('/404/');
});

$app->get('/404/', $trackView, function() use($app, $config, $pdo, $mustache) {
    $notFound = $mustache->loadTemplate('notFound');
    $app->response->setBody($notFound->render());
})->setName('notFound');

/**
 * DISPLAY ROOT, PROJECTS AND BLOG
 */

$app->get('/', $trackView, function() use($app, $config, $pdo, $mustache, $language) {
    $pagesController = new PagesController($config, $pdo);
    $page = $pagesController->getOneIndexByLanguage($language);

    if (!$page) {
        $app->notFound();
    }

    $pageTemplate = $mustache->loadTemplate('page');
    $app->response->setBody($pageTemplate->render(array('page' => $page)));
})->setName('index');

$app->get('/portfolio(/)', $trackView, function() use($app) {
    $app->redirect('/projects/', 301);
})->setName('portfolioRedirect');

$app->get('/projects', $trackView, function() use($app) {
    $app->redirect('/projects/', 301);
})->setName('projectsRedirect');

$app->get('/projects/', $trackView, function() use($app, $config, $pdo, $mustache, $language) {
    $pagesController = new PagesController($config, $pdo);
    $projects = $pagesController->getAllByTypeAndLanguage('project', $language);

    if (!$projects) {
        $app->notFound();
    }

    $projectsTemplate = $mustache->loadTemplate('projects');
    $app->response->setBody($projectsTemplate->render(array('projects' => $projects)));
})->setName('projects');

$app->get('/blog', $trackView, function() use($app) {
    $app->redirect('/blog/', 301);
})->setName('blogRedirect');

$app->get('/blog/', $trackView, function() use($app, $config, $pdo, $mustache, $language) {
    $pagesController = new PagesController($config, $pdo);
    $posts = $pagesController->getAllByTypeAndLanguage('post', $language);

    if (!$posts) {
        $app->notFound();
    }

    $blogTemplate = $mustache->loadTemplate('blog');
    $app->response->setBody($blogTemplate->render(array('posts' => $posts)));
})->setName('blog');

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
    $pages = $pagesController->getAllByTypeRegroupedById('page');

    $app->response->setBody($mustache->loadTemplate('adminPages')->render(array('pages' => $pages)));
})->setName('adminPages');

$app->get('/admin/pages/:language/:pageTitle(/)', $trackView, $authenticate($app, $config), function($language, $pageTitle) use($app, $config, $pdo, $mustache) {
    $pagesController = new PagesController($config, $pdo);
    $page = $pagesController->getOneByTypeAndLanguageAndTitle('page', $language, $pageTitle);

    $app->response->setBody($mustache->loadTemplate('adminEditPage')->render(array('page' => $page)));
})->setName('adminEditPage');

$app->post('/admin/pages/:language/:pagesTitle(/)', $trackView, $authenticate($app, $config), function($language, $pagesTitle) use($app, $config, $pdo, $mustache) {
    $title = $app->request()->post('title');
    $title_clean = $app->request()->post('title_clean');
    $content = $app->request()->post('content');

    if ($title === null || $title == '' ||
        $title_clean === null || $title_clean == '' ||
        $content === null || $content == ''){
        throw new Exception('Not all params set.');
    }

    $pagesController = new PagesController($config, $pdo);
    $pagesController->updatePageByLanguageAndTitle($language, $pagesTitle, $title, $title_clean, $content);

    $app->redirect('/admin/pages/');
})->setName('adminEditPagePost');

$app->get('/admin/projects(/)', $trackView, $authenticate($app, $config), function() use($app, $config, $pdo, $mustache) {
    $pagesController = new PagesController($config, $pdo);
    $projects = $pagesController->getAllByTypeRegroupedById('project');

    $app->response->setBody($mustache->loadTemplate('adminProjects')->render(array('projects' => $projects)));
})->setName('adminProjects');

$app->get('/admin/projects/:language/:projectTitle(/)', $trackView, $authenticate($app, $config), function($language, $projectTitle) use($app, $config, $pdo, $mustache) {
    $pagesController = new PagesController($config, $pdo);
    $project = $pagesController->getOneByTypeAndLanguageAndTitle('project', $language, $projectTitle);

    $app->response->setBody($mustache->loadTemplate('adminEditProject')->render(array('project' => $project)));
})->setName('adminEditProject');

$app->post('/admin/projects/:language/:projectsTitle(/)', $trackView, $authenticate($app, $config), function($language, $projectTitle) use($app, $config, $pdo, $mustache) {
    $title = $app->request()->post('title');
    $title_clean = $app->request()->post('title_clean');
    $content = $app->request()->post('content');

    if ($title === null || $title == '' ||
        $title_clean === null || $title_clean == '' ||
        $content === null || $content == ''){
        throw new Exception('Not all params set.');
    }

    $pagesController = new PagesController($config, $pdo);
    $pagesController->updateProjectByLanguageAndTitle($language, $projectTitle, $title, $title_clean, $content);

    $app->redirect('/admin/projects/');
})->setName('adminEditProjectPost');

$app->get('/admin/posts(/)', $trackView, $authenticate($app, $config), function() use($app, $config, $pdo, $mustache) {
    $pagesController = new PagesController($config, $pdo);
    $posts = $pagesController->getAllByTypeRegroupedById('post');

    $app->response->setBody($mustache->loadTemplate('adminPosts')->render(array('posts' => $posts)));
})->setName('adminPosts');

$app->get('/admin/posts/:language/:postTitle(/)', $trackView, $authenticate($app, $config), function($language, $postTitle) use($app, $config, $pdo, $mustache) {
    $pagesController = new PagesController($config, $pdo);
    $post = $pagesController->getOneByTypeAndLanguageAndTitle('post', $language, $postTitle);

    $app->response->setBody($mustache->loadTemplate('adminEditPost')->render(array('post' => $post)));
})->setName('adminEditPost');

$app->post('/admin/posts/:language/:postsTitle(/)', $trackView, $authenticate($app, $config), function($language, $postsTitle) use($app, $config, $pdo, $mustache) {
    $title = $app->request()->post('title');
    $title_clean = $app->request()->post('title_clean');
    $content = $app->request()->post('content');

    if ($title === null || $title == '' ||
        $title_clean === null || $title_clean == '' ||
        $content === null || $content == ''){
        throw new Exception('Not all params set.');
    }

    $pagesController = new PagesController($config, $pdo);
    $pagesController->updatePostByLanguageAndTitle($language, $postsTitle, $title, $title_clean, $content);

    $app->redirect('/admin/posts/');
})->setName('adminEditPostPost');

/**
 * ADMIN CREATE PAGES, PROJECTS, POSTS
 */

$app->get('/admin/create/page(/)', $trackView, $authenticate($app, $config), function() use($app, $config, $pdo, $mustache) {
    $pagesController = new PagesController($config, $pdo);
    $languages = $pagesController->getAllLanguages();

    $app->response->setBody($mustache->loadTemplate('adminCreatePage')->render(array('languages' => $languages)));
})->setName('adminCreatePage');

$app->post('/admin/create/page(/)', $trackView, $authenticate($app, $config), function() use($app, $config, $pdo, $mustache) {
    $language = $app->request()->post('language');
    $title = $app->request()->post('title');
    $title_clean = $app->request()->post('title_clean');
    $content = $app->request()->post('content');

    if ($language === null || $language == '' ||
            $title === null || $title == '' ||
            $title_clean === null || $title_clean == '' ||
            $content === null || $content == ''){
        throw new Exception('Not all params set.');
    }

    $pagesController = new PagesController($config, $pdo);
    $pagesController->createPage($language, $title, $title_clean, $content);

    $app->redirect('/admin/pages/');
})->setName('adminCreatePagePost');

$app->get('/admin/create/project(/)', $trackView, $authenticate($app, $config), function() use($app, $config, $pdo, $mustache) {
    $pagesController = new PagesController($config, $pdo);
    $languages = $pagesController->getAllLanguages();

    $app->response->setBody($mustache->loadTemplate('adminCreateProject')->render(array('languages' => $languages)));
})->setName('adminCreateProject');

$app->post('/admin/create/project(/)', $trackView, $authenticate($app, $config), function() use($app, $config, $pdo, $mustache) {
    $language = $app->request()->post('language');
    $title = $app->request()->post('title');
    $title_clean = $app->request()->post('title_clean');
    $content = $app->request()->post('content');

    if ($language === null || $language == '' ||
            $title === null || $title == '' ||
            $title_clean === null || $title_clean == '' ||
            $content === null || $content == ''){
        throw new Exception('Not all params set.');
    }

    $pagesController = new PagesController($config, $pdo);
    $pagesController->createProject($language, $title, $title_clean, $content);

    $app->redirect('/admin/projects/');
})->setName('adminCreateProjectPost');

$app->get('/admin/create/post(/)', $trackView, $authenticate($app, $config), function() use($app, $config, $pdo, $mustache) {
    $pagesController = new PagesController($config, $pdo);
    $languages = $pagesController->getAllLanguages();

    $app->response->setBody($mustache->loadTemplate('adminCreatePost')->render(array('languages' => $languages)));
})->setName('adminCreatePost');

$app->post('/admin/create/post(/)', $trackView, $authenticate($app, $config), function() use($app, $config, $pdo, $mustache) {
    $language = $app->request()->post('language');
    $title = $app->request()->post('title');
    $title_clean = $app->request()->post('title_clean');
    $content = $app->request()->post('content');

    if ($language === null || $language == '' ||
            $title === null || $title == '' ||
            $title_clean === null || $title_clean == '' ||
            $content === null || $content == ''){
        throw new Exception('Not all params set.');
    }

    $pagesController = new PagesController($config, $pdo);
    $pagesController->createPost($language, $title, $title_clean, $content);

    $app->redirect('/admin/posts/');
})->setName('adminCreatePostPost');

/**
 * CREATE TRANSLATIONS
 */

$app->get('/admin/translate/page/:pageId(/)', $trackView, $authenticate($app, $config), function($pageId) use($app, $config, $pdo, $mustache) {
    $pagesController = new PagesController($config, $pdo);
    $languages = $pagesController->getAllLanguagesNotUsedByPageId($pageId);
    if (!$languages) {
        throw new Exception('No more languages to translate to.');
    }

    $app->response->setBody($mustache->loadTemplate('adminTranslatePage')->render(array('pageId' => $pageId, 'languages' => $languages)));
})->setName('adminTranslatePage');

$app->post('/admin/translate/page/:pageId(/)', $trackView, $authenticate($app, $config), function($pageId) use($app, $config, $pdo, $mustache) {
    $language = $app->request()->post('language');
    $title = $app->request()->post('title');
    $title_clean = $app->request()->post('title_clean');
    $content = $app->request()->post('content');

    if ($language === null || $language == '' ||
            $title === null || $title == '' ||
            $title_clean === null || $title_clean == '' ||
            $content === null || $content == ''){
        throw new Exception('Not all params set.');
    }

    $pagesController = new PagesController($config, $pdo);
    $pagesController->translatePage($pageId, $language, $title, $title_clean, $content);

    $app->redirect('/admin/pages/');
})->setName('adminTranslatePagePost');

$app->get('/admin/translate/project/:pageId(/)', $trackView, $authenticate($app, $config), function($pageId) use($app, $config, $pdo, $mustache) {
    $pagesController = new PagesController($config, $pdo);
    $languages = $pagesController->getAllLanguagesNotUsedByPageId($pageId);
    if (!$languages) {
        throw new Exception('No more languages to translate to.');
    }

    $app->response->setBody($mustache->loadTemplate('adminTranslateProject')->render(array('pageId' => $pageId, 'languages' => $languages)));
})->setName('adminTranslateProject');

$app->post('/admin/translate/project/:pageId(/)', $trackView, $authenticate($app, $config), function($pageId) use($app, $config, $pdo, $mustache) {
    $language = $app->request()->post('language');
    $title = $app->request()->post('title');
    $title_clean = $app->request()->post('title_clean');
    $content = $app->request()->post('content');

    if ($language === null || $language == '' ||
            $title === null || $title == '' ||
            $title_clean === null || $title_clean == '' ||
            $content === null || $content == ''){
        throw new Exception('Not all params set.');
    }

    $pagesController = new PagesController($config, $pdo);
    $pagesController->translateProject($pageId, $language, $title, $title_clean, $content);

    $app->redirect('/admin/projects/');
})->setName('adminTranslateProjectPost');

$app->get('/admin/translate/post/:pageId(/)', $trackView, $authenticate($app, $config), function($pageId) use($app, $config, $pdo, $mustache) {
    $pagesController = new PagesController($config, $pdo);
    $languages = $pagesController->getAllLanguagesNotUsedByPageId($pageId);
    if (!$languages) {
        throw new Exception('No more languages to translate to.');
    }

    $app->response->setBody($mustache->loadTemplate('adminTranslatePost')->render(array('pageId' => $pageId, 'languages' => $languages)));
})->setName('adminTranslatePost');

$app->post('/admin/translate/post/:pageId(/)', $trackView, $authenticate($app, $config), function($pageId) use($app, $config, $pdo, $mustache) {
    $language = $app->request()->post('language');
    $title = $app->request()->post('title');
    $title_clean = $app->request()->post('title_clean');
    $content = $app->request()->post('content');

    if ($language === null || $language == '' ||
            $title === null || $title == '' ||
            $title_clean === null || $title_clean == '' ||
            $content === null || $content == ''){
        throw new Exception('Not all params set.');
    }

    $pagesController = new PagesController($config, $pdo);
    $pagesController->translatePost($pageId, $language, $title, $title_clean, $content);

    $app->redirect('/admin/posts/');
})->setName('adminTranslatePostPost');

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

$app->get('/:pageTitle', $trackView, function($pageTitle) use($app) {
    $app->redirect('/' . $pageTitle . '/', 301);
})->setName('pagesRedirect');

$app->get('/:pageTitle/', $trackView, function($pageTitle) use($app, $config, $pdo, $mustache, $language) {
    $pagesController = new PagesController($config, $pdo);
    $page = $pagesController->getOneByTypeAndLanguageAndTitle('page', $language, $pageTitle);

    if (!$page) {
        $app->notFound();
    }

    $pageTemplate = $mustache->loadTemplate('page');
    $app->response->setBody($pageTemplate->render(array('page' => $page)));
})->setName('pages');

$app->get('/portfolio/:projectTitle(/)', $trackView, function($projectTitle) use($app, $config, $pdo, $language) {
    $pagesController = new PagesController($config, $pdo);
    $project = $pagesController->getOneByTypeAndLanguageAndTitle('project', $language, $projectTitle);

    if (!$project) {
        $app->notFound();
    }

    $app->redirect('/projects/' . $projectTitle . '/', 301);
})->setName('portfolioProjectRedirect');

$app->get('/projects/:projectTitle', $trackView, function($projectTitle) use($app) {
    $app->redirect('/projects/' . $projectTitle . '/', 301);
})->setName('projectRedirect');

$app->get('/projects/:projectTitle/', $trackView, function($projectTitle) use($app, $config, $pdo, $mustache, $language) {
    $pagesController = new PagesController($config, $pdo);
    $project = $pagesController->getOneByTypeAndLanguageAndTitle('project', $language, $projectTitle);

    if (!$project) {
        $app->notFound();
    }

    $projectTemplate = $mustache->loadTemplate('project');
    $app->response->setBody($projectTemplate->render(array('project' => $project)));
})->setName('project');

$app->get('/blog/:postTitle', $trackView, function($postTitle) use($app) {
    $app->redirect('/blog/' . $postTitle . '/', 301);
})->setName('projectsRedirect');

$app->get('/blog/:postTitle/', $trackView, function($postTitle) use($app, $config, $pdo, $mustache, $language) {
    $pagesController = new PagesController($config, $pdo);
    $post = $pagesController->getOneByTypeAndLanguageAndTitle('post', $language, $postTitle);

    if (!$post) {
        $app->notFound();
    }

    $postTemplate = $mustache->loadTemplate('post');
    $app->response->setBody($postTemplate->render(array('post' => $post)));
})->setName('projects');

// ...

$app->run();



