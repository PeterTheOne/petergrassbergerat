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

$app->error(function(\Exception $exception) use ($app, $pdo) {
    $app->response->headers->set('X-Status-Reason', $exception->getMessage());
    $app->response->setBody($exception->getMessage());
    if ($pdo->inTransaction()) {
      $pdo->rollBack();
    }
});

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

$app->notFound(function () use ($app) {
    $app->redirect('/404/');
});

$app->get('/404/', function() use($app, $config, $pdo, $mustache, $language) {
    $notFound = $mustache->loadTemplate('notFound');
    $app->response->setBody($notFound->render(array(
        'title' => 'Peter Grassberger - 404 Not Found',
        'language' => $language
    )));
    $app->response()->setStatus(404);
})->setName('notFound');

/**
 * DISPLAY ROOT, PROJECTS AND BLOG
 */

$app->get('/bio(/)', function() use($app) {
    $app->redirect('/', 301);
})->setName('bioRedirect');

$app->get('/vita(/)', function() use($app) {
    $app->redirect('/', 301);
})->setName('vitaRedirect');

$app->get('/', function() use($app, $config, $pdo, $mustache, $language) {
    $pagesController = new PagesController($config, $pdo);
    $translations = $pagesController->getOneIndex();
    $translations = $pagesController->addUrls($translations);
    $page = $pagesController->filterByLanguage($translations, $language);

    if (!$page) {
        $app->notFound();
    }

    $app->lastModified(strtotime($page->updated));
    $app->expires('+1 week');

    $pageTemplate = $mustache->loadTemplate('page');
    $app->response->setBody($pageTemplate->render(array(
        'title' => 'Peter Grassberger - Index',
        'language' => $language,
        'page' => $page,
        'translations' => $translations
    )));
})->setName('index');

$app->get('/portfolio(/)', function() use($app) {
    $app->redirect('/projects/', 301);
})->setName('portfolioRedirect');

$app->get('/projects', function() use($app) {
    $app->redirect('/projects/', 301);
})->setName('projectsRedirect');

$app->get('/projects/', function() use($app, $config, $pdo, $mustache, $language) {
    $pagesController = new PagesController($config, $pdo);
    $projects = $pagesController->getAllByTypeAndLanguage('project', $language);
    $projects = $pagesController->addTags($projects);

    if (!$projects) {
        $app->notFound();
    }

    $app->lastModified($pagesController->findLastModified($projects));
    $app->expires('+1 week');

    $projectsTemplate = $mustache->loadTemplate('projects');
    $app->response->setBody($projectsTemplate->render(array(
        'title' => 'Peter Grassberger - Projects',
        'language' => $language,
        'projects' => $projects,
        'translations' => array(
            array(
                'url' => 'http://petergrassberger.com/projects/',
                'languageTag' => 'en',
                'languageName' => 'English'
            ),
            array(
                'url' => 'http://petergrassberger.at/projects/',
                'languageTag' => 'de',
                'languageName' => 'Deutsch'
            )
        )
    )));
})->setName('projects');

$app->get('/blog', function() use($app) {
    $app->redirect('/blog/', 301);
})->setName('blogRedirect');

$app->get('/blog/', function() use($app, $config, $pdo, $mustache, $language) {
    $pagesController = new PagesController($config, $pdo);
    $posts = $pagesController->getAllByTypeAndLanguage('post', $language);
    $posts = $pagesController->addDatesDmy($posts);

    if (!$posts) {
        $app->notFound();
    }

    foreach($posts as $post) {
        if (strlen($post->content) < 301) {
            continue;
        }
        $secondParagraphEnd = strpos($post->content, '</p>', 300);
        if ($secondParagraphEnd === false) {
            continue;
        }
        $post->content = substr($post->content, 0, $secondParagraphEnd);
        $post->content .= '<p><a href="/blog/' . $post->title_clean . '">Read more...</a></p>';
    }

    $app->lastModified($pagesController->findLastModified($posts));
    $app->expires('+1 week');

    $blogTemplate = $mustache->loadTemplate('blog');
    $app->response->setBody($blogTemplate->render(array(
        'title' => 'Peter Grassberger - Blog',
        'language' => $language,
        'posts' => $posts,
        'translations' => array(
            array(
                'url' => 'http://petergrassberger.com/blog/',
                'languageTag' => 'en',
                'languageName' => 'English'
            ),
            array(
                'url' => 'http://petergrassberger.at/blog/',
                'languageTag' => 'de',
                'languageName' => 'Deutsch'
            )
        )
    )));
})->setName('blog');

/**
 * ADMIN LOGIN AND LOGOUT
 */

$app->get('/admin/login(/)', function() use($app, $config, $pdo, $mustache) {
    $loginTemplate = $mustache->loadTemplate('login');
    $app->response->setBody($loginTemplate->render());
})->setName('adminLogin');

$app->post('/admin/login(/)', function() use($app, $config, $pdo, $mustache) {
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

$app->get('/admin/logout(/)', function() use($app, $config, $pdo, $mustache) {
    unset($_SESSION['login']);
    unset($_SESSION['HTTP_USER_AGENT']);
    $app->redirect('/admin/login/');
})->setName('adminLogout');

/**
 * ADMIN PAGES
 */

$app->get('/admin(/)', $authenticate($app, $config), function() use($app, $config, $pdo, $mustache) {
    $app->response->setBody($mustache->loadTemplate('admin')->render());
})->setName('admin');

$app->get('/admin/pages(/)', $authenticate($app, $config), function() use($app, $config, $pdo, $mustache) {
    $pagesController = new PagesController($config, $pdo);
    $pages = $pagesController->getAllByType('page', array('pages.id', 'languages.id'));
    $pages = $pagesController->regroupedById($pages);

    $app->response->setBody($mustache->loadTemplate('adminPages')->render(array('pages' => $pages)));
})->setName('adminPages');

$app->get('/admin/pages/:language/:pageTitle(/)', $authenticate($app, $config), function($language, $pageTitle) use($app, $config, $pdo, $mustache) {
    $pagesController = new PagesController($config, $pdo);
    $page = $pagesController->getOneByTypeAndLanguageAndTitle('page', $language, $pageTitle);

    $app->response->setBody($mustache->loadTemplate('adminEditPage')->render(array('page' => $page)));
})->setName('adminEditPage');

$app->post('/admin/pages/:language/:pagesTitle(/)', $authenticate($app, $config), function($language, $pagesTitle) use($app, $config, $pdo, $mustache) {
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

$app->get('/admin/projects(/)', $authenticate($app, $config), function() use($app, $config, $pdo, $mustache) {
    $pagesController = new PagesController($config, $pdo);
    $projects = $pagesController->getAllByType('project', array('pages.id', 'languages.id'));
    $projects = $pagesController->regroupedById($projects);

    $app->response->setBody($mustache->loadTemplate('adminProjects')->render(array('projects' => $projects)));
})->setName('adminProjects');

$app->get('/admin/projects/:language/:projectTitle(/)', $authenticate($app, $config), function($language, $projectTitle) use($app, $config, $pdo, $mustache) {
    $pagesController = new PagesController($config, $pdo);
    $project = $pagesController->getOneByTypeAndLanguageAndTitle('project', $language, $projectTitle);
    $tags = $pagesController->getAllTags();

    $app->response->setBody($mustache->loadTemplate('adminEditProject')->render(array('project' => $project, 'tags' => $tags)));
})->setName('adminEditProject');

$app->post('/admin/projects/:language/:projectsTitle(/)', $authenticate($app, $config), function($language, $projectTitle) use($app, $config, $pdo, $mustache) {
    $title = $app->request()->post('title');
    $title_clean = $app->request()->post('title_clean');
    $tags = $app->request()->post('tags');
    $content = $app->request()->post('content');

    if ($title === null || $title == '' ||
            $title_clean === null || $title_clean == '' ||
            $content === null || $content == ''){
        throw new Exception('Not all params set.');
    }

    $pagesController = new PagesController($config, $pdo);
    $pagesController->updateProjectByLanguageAndTitle($language, $projectTitle, $title, $title_clean, $tags, $content);

    $app->redirect('/admin/projects/');
})->setName('adminEditProjectPost');

$app->get('/admin/posts(/)', $authenticate($app, $config), function() use($app, $config, $pdo, $mustache) {
    $pagesController = new PagesController($config, $pdo);
    $posts = $pagesController->getAllByType('post', array('pages.id', 'languages.id'));
    $posts = $pagesController->regroupedById($posts);

    $app->response->setBody($mustache->loadTemplate('adminPosts')->render(array('posts' => $posts)));
})->setName('adminPosts');

$app->get('/admin/posts/:language/:postTitle(/)', $authenticate($app, $config), function($language, $postTitle) use($app, $config, $pdo, $mustache) {
    $pagesController = new PagesController($config, $pdo);
    $post = $pagesController->getOneByTypeAndLanguageAndTitle('post', $language, $postTitle);

    $app->response->setBody($mustache->loadTemplate('adminEditPost')->render(array('post' => $post)));
})->setName('adminEditPost');

$app->post('/admin/posts/:language/:postsTitle(/)', $authenticate($app, $config), function($language, $postsTitle) use($app, $config, $pdo, $mustache) {
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

$app->get('/admin/tags(/)', $authenticate($app, $config), function() use($app, $config, $pdo, $mustache) {
    $pagesController = new PagesController($config, $pdo);
    $tags = $pagesController->getAllTags();

    $app->response->setBody($mustache->loadTemplate('adminTags')->render(array('tags' => $tags)));
});

$app->get('/admin/tags/:tagName(/)', $authenticate($app, $config), function($tagName) use($app, $config, $pdo, $mustache) {
    $pagesController = new PagesController($config, $pdo);
    $tag = $pagesController->getOneTagByName($tagName);

    $app->response->setBody($mustache->loadTemplate('adminEditTag')->render(array('tag' => $tag)));
});


$app->post('/admin/tags/:tagName(/)', $authenticate($app, $config), function($tagName) use($app, $config, $pdo, $mustache) {
    $name = $app->request()->post('name');
    $name_clean = $app->request()->post('name_clean');
    $color = $app->request()->post('color');

    if ($name === null || $name == '' ||
            $name_clean === null || $name_clean == '' ||
            $color === null || $color == ''){
        throw new Exception('Not all params set.');
    }

    $pagesController = new PagesController($config, $pdo);
    $pagesController->updateTagByName($tagName, $name, $name_clean, $color);

    $app->redirect('/admin/tags/');
});

/**
 * ADMIN CREATE PAGES, PROJECTS, POSTS
 */

$app->get('/admin/create/page(/)', $authenticate($app, $config), function() use($app, $config, $pdo, $mustache) {
    $pagesController = new PagesController($config, $pdo);
    $languages = $pagesController->getAllLanguages();

    $app->response->setBody($mustache->loadTemplate('adminCreatePage')->render(array('languages' => $languages)));
})->setName('adminCreatePage');

$app->post('/admin/create/page(/)', $authenticate($app, $config), function() use($app, $config, $pdo, $mustache) {
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

$app->get('/admin/create/project(/)', $authenticate($app, $config), function() use($app, $config, $pdo, $mustache) {
    $pagesController = new PagesController($config, $pdo);
    $languages = $pagesController->getAllLanguages();

    $app->response->setBody($mustache->loadTemplate('adminCreateProject')->render(array('languages' => $languages)));
})->setName('adminCreateProject');

$app->post('/admin/create/project(/)', $authenticate($app, $config), function() use($app, $config, $pdo, $mustache) {
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

$app->get('/admin/create/post(/)', $authenticate($app, $config), function() use($app, $config, $pdo, $mustache) {
    $pagesController = new PagesController($config, $pdo);
    $languages = $pagesController->getAllLanguages();

    $app->response->setBody($mustache->loadTemplate('adminCreatePost')->render(array('languages' => $languages)));
})->setName('adminCreatePost');

$app->post('/admin/create/post(/)', $authenticate($app, $config), function() use($app, $config, $pdo, $mustache) {
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

$app->get('/admin/create/tag(/)', $authenticate($app, $config), function() use($app, $config, $pdo, $mustache) {
    $app->response->setBody($mustache->loadTemplate('adminCreateTag')->render());
});

$app->post('/admin/create/tag(/)', $authenticate($app, $config), function() use($app, $config, $pdo, $mustache) {
    $name = $app->request()->post('name');
    $name_clean = $app->request()->post('name_clean');
    $color = $app->request()->post('color');

    if ($name === null || $name == '' ||
            $name_clean === null || $name_clean == '' ||
            $color === null || $color == ''){
        throw new Exception('Not all params set.');
    }

    $pagesController = new PagesController($config, $pdo);
    $pagesController->createTag($name, $name_clean, $color);

    $app->redirect('/admin/tags');
});

/**
 * CREATE TRANSLATIONS
 */

$app->get('/admin/translate/page/:pageId(/)', $authenticate($app, $config), function($pageId) use($app, $config, $pdo, $mustache) {
    $pagesController = new PagesController($config, $pdo);
    $languages = $pagesController->getAllLanguagesNotUsedByPageId($pageId);
    if (!$languages) {
        throw new Exception('No more languages to translate to.');
    }

    $app->response->setBody($mustache->loadTemplate('adminTranslatePage')->render(array('pageId' => $pageId, 'languages' => $languages)));
})->setName('adminTranslatePage');

$app->post('/admin/translate/page/:pageId(/)', $authenticate($app, $config), function($pageId) use($app, $config, $pdo, $mustache) {
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

$app->get('/admin/translate/project/:pageId(/)', $authenticate($app, $config), function($pageId) use($app, $config, $pdo, $mustache) {
    $pagesController = new PagesController($config, $pdo);
    $languages = $pagesController->getAllLanguagesNotUsedByPageId($pageId);
    if (!$languages) {
        throw new Exception('No more languages to translate to.');
    }

    $app->response->setBody($mustache->loadTemplate('adminTranslateProject')->render(array('pageId' => $pageId, 'languages' => $languages)));
})->setName('adminTranslateProject');

$app->post('/admin/translate/project/:pageId(/)', $authenticate($app, $config), function($pageId) use($app, $config, $pdo, $mustache) {
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

$app->get('/admin/translate/post/:pageId(/)', $authenticate($app, $config), function($pageId) use($app, $config, $pdo, $mustache) {
    $pagesController = new PagesController($config, $pdo);
    $languages = $pagesController->getAllLanguagesNotUsedByPageId($pageId);
    if (!$languages) {
        throw new Exception('No more languages to translate to.');
    }

    $app->response->setBody($mustache->loadTemplate('adminTranslatePost')->render(array('pageId' => $pageId, 'languages' => $languages)));
})->setName('adminTranslatePost');

$app->post('/admin/translate/post/:pageId(/)', $authenticate($app, $config), function($pageId) use($app, $config, $pdo, $mustache) {
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

$app->get('/admin/remove/page/:pageTitle(/)', $authenticate($app, $config), function($pageTitle) use($app, $config, $pdo, $mustache) {
    $app->response->setBody($mustache->loadTemplate('adminRemovePage')->render(array('pageTitle' => $pageTitle)));
})->setName('adminRemovePage');

$app->post('/admin/remove/page/:pageTitle(/)', $authenticate($app, $config), function($pageTitle) use($app, $config, $pdo, $mustache) {
    $pagesController = new PagesController($config, $pdo);
    $pagesController->removePage($pageTitle);

    $app->redirect('/admin/pages/');
})->setName('adminRemovePagePost');

$app->get('/admin/remove/project/:projectTitle(/)', $authenticate($app, $config), function($projectTitle) use($app, $config, $pdo, $mustache) {
    $app->response->setBody($mustache->loadTemplate('adminRemoveProject')->render(array('projectTitle' => $projectTitle)));
})->setName('adminRemoveProject');

$app->post('/admin/remove/project/:projectTitle(/)', $authenticate($app, $config), function($projectTitle) use($app, $config, $pdo, $mustache) {
    $pagesController = new PagesController($config, $pdo);
    $pagesController->removeProject($projectTitle);

    $app->redirect('/admin/projects/');
})->setName('adminRemoveProjectPost');

$app->get('/admin/remove/post/:postTitle(/)', $authenticate($app, $config), function($postTitle) use($app, $config, $pdo, $mustache) {
    $app->response->setBody($mustache->loadTemplate('adminRemovePost')->render(array('postTitle' => $postTitle)));
})->setName('adminRemovePost');

$app->post('/admin/remove/post/:postTitle(/)', $authenticate($app, $config), function($postTitle) use($app, $config, $pdo, $mustache) {
    $pagesController = new PagesController($config, $pdo);
    $pagesController->removePost($postTitle);

    $app->redirect('/admin/posts/');
})->setName('adminRemovePostPost');

/**
 * FEED
 */

$app->get('/feed(/):wildcard+', function($wildcard) use($app) {
    $app->redirect('/rss/', 301);
})->setName('feedRedirect');

$app->get('/:wildcard+/feed(/)', function($wildcard) use($app) {
    $app->redirect('/rss/', 301);
})->setName('feedRedirect');

$app->get('/rss.php(/)', function() use($app) {
    $app->redirect('/rss/', 301);
})->setName('feedRedirect');

$app->get('/rss(/)', function() use($app, $config, $pdo, $mustache, $language) {
    $pagesController = new PagesController($config, $pdo);
    $pages = $pagesController->getAllByLanguage($language);
    $pages = $pagesController->addUrls($pages);
    $pages = $pagesController->addPubDate($pages);

    $app->lastModified($pagesController->findLastModified($pages));
    $app->expires('+1 week');

    $pageTemplate = $mustache->loadTemplate('rss');
    $pageRendered = $pageTemplate->render(array(
        'rssTitle' => 'Peter Grassberger - RSS feed: everything ' . $language,
        'rssDescription' => 'RSS feed of pages, projects and posts in ' . $language,
        'rssUrl' => $language === 'de' ? 'http://petergrassberger.at/rss/' : 'http://petergrassberger.com/rss/',
        'language' => $language,
        'pages' => $pages
    ));

    $app->response->headers->set('Content-Type', 'application/rss+xml');
    $app->response->setBody($pageRendered);
})->setName('rssEverythingFlexibleLanguages');

$app->get('/rss/pages(/)', function() use($app, $config, $pdo, $mustache, $language) {
    $pagesController = new PagesController($config, $pdo);
    $pages = $pagesController->getAllByTypeAndLanguage('page', $language);
    $pages = $pagesController->addUrls($pages);
    $pages = $pagesController->addPubDate($pages);

    $app->lastModified($pagesController->findLastModified($pages));
    $app->expires('+1 week');

    $pageTemplate = $mustache->loadTemplate('rss');
    $pageRendered = $pageTemplate->render(array(
        'rssTitle' => 'Peter Grassberger - RSS feed: pages ' . $language,
        'rssDescription' => 'RSS feed of pages in ' . $language,
        'rssUrl' => $language === 'de' ? 'http://petergrassberger.at/rss/pages/' : 'http://petergrassberger.com/rss/pages/',
        'language' => $language,
        'pages' => $pages
    ));

    $app->response->headers->set('Content-Type', 'application/rss+xml');
    $app->response->setBody($pageRendered);
})->setName('rssPagesFlexibleLanguages');

$app->get('/rss/projects(/)', function() use($app, $config, $pdo, $mustache, $language) {
    $pagesController = new PagesController($config, $pdo);
    $pages = $pagesController->getAllByTypeAndLanguage('project', $language);
    $pages = $pagesController->addUrls($pages);
    $pages = $pagesController->addPubDate($pages);

    $app->lastModified($pagesController->findLastModified($pages));
    $app->expires('+1 week');

    $pageTemplate = $mustache->loadTemplate('rss');
    $pageRendered = $pageTemplate->render(array(
        'rssTitle' => 'Peter Grassberger - RSS feed: projects ' . $language,
        'rssDescription' => 'RSS feed of projects in ' . $language,
        'rssUrl' => $language === 'de' ? 'http://petergrassberger.at/rss/projects/' : 'http://petergrassberger.com/rss/projects/',
        'language' => $language,
        'pages' => $pages
    ));

    $app->response->headers->set('Content-Type', 'application/rss+xml');
    $app->response->setBody($pageRendered);
})->setName('rssProjectsFlexibleLanguages');

$app->get('/rss/blog(/)', function() use($app, $config, $pdo, $mustache, $language) {
    $pagesController = new PagesController($config, $pdo);
    $pages = $pagesController->getAllByTypeAndLanguage('post', $language);
    $pages = $pagesController->addUrls($pages);
    $pages = $pagesController->addPubDate($pages);

    $app->lastModified($pagesController->findLastModified($pages));
    $app->expires('+1 week');

    $pageTemplate = $mustache->loadTemplate('rss');
    $pageRendered = $pageTemplate->render(array(
        'rssTitle' => 'Peter Grassberger - RSS feed: posts ' . $language,
        'rssDescription' => 'RSS feed of posts in ' . $language,
        'rssUrl' => $language === 'de' ? 'http://petergrassberger.at/rss/blog/' : 'http://petergrassberger.com/rss/blog/',
        'language' => $language,
        'pages' => $pages
    ));

    $app->response->headers->set('Content-Type', 'application/rss+xml');
    $app->response->setBody($pageRendered);
})->setName('rssBlogFlexibleLanguages');

$app->get('/rss/all(/)', function() use($app, $config, $pdo, $mustache, $language) {
    $pagesController = new PagesController($config, $pdo);
    $pages = $pagesController->getAll();
    $pages = $pagesController->addUrls($pages);
    $pages = $pagesController->addPubDate($pages);

    $app->lastModified($pagesController->findLastModified($pages));
    $app->expires('+1 week');

    $pageTemplate = $mustache->loadTemplate('rss');
    $pageRendered = $pageTemplate->render(array(
        'rssTitle' => 'Peter Grassberger - RSS feed: everything all languages',
        'rssDescription' => 'RSS feed of pages, projects and posts in  all languages',
        'rssUrl' => $language === 'de' ? 'http://petergrassberger.at/rss/all/' : 'http://petergrassberger.com/rss/all/',
        'language' => 'en,de',
        'pages' => $pages
    ));

    $app->response->headers->set('Content-Type', 'application/rss+xml');
    $app->response->setBody($pageRendered);
})->setName('rssEverythingAllLanguages');

$app->get('/rss/pages/all(/)', function() use($app, $config, $pdo, $mustache, $language) {
    $pagesController = new PagesController($config, $pdo);
    $pages = $pagesController->getAllByType('page', array('pagecontents.created DESC'));
    $pages = $pagesController->addUrls($pages);
    $pages = $pagesController->addPubDate($pages);

    $app->lastModified($pagesController->findLastModified($pages));
    $app->expires('+1 week');

    $pageTemplate = $mustache->loadTemplate('rss');
    $pageRendered = $pageTemplate->render(array(
        'rssTitle' => 'Peter Grassberger - RSS feed: pages all languages',
        'rssDescription' => 'RSS feed of pages in  all languages',
        'rssUrl' => $language === 'de' ? 'http://petergrassberger.at/rss/pages/all/' : 'http://petergrassberger.com/rss/pages/all/',
        'language' => 'en,de',
        'pages' => $pages
    ));

    $app->response->headers->set('Content-Type', 'application/rss+xml');
    $app->response->setBody($pageRendered);
})->setName('rssPagesAllLanguages');

$app->get('/rss/projects/all(/)', function() use($app, $config, $pdo, $mustache, $language) {
    $pagesController = new PagesController($config, $pdo);
    $pages = $pagesController->getAllByType('project', array('pagecontents.created DESC'));
    $pages = $pagesController->addUrls($pages);
    $pages = $pagesController->addPubDate($pages);

    $app->lastModified($pagesController->findLastModified($pages));
    $app->expires('+1 week');

    $pageTemplate = $mustache->loadTemplate('rss');
    $pageRendered = $pageTemplate->render(array(
        'rssTitle' => 'Peter Grassberger - RSS feed: projects all languages',
        'rssDescription' => 'RSS feed of projects in  all languages',
        'rssUrl' => $language === 'de' ? 'http://petergrassberger.at/rss/projects/all/' : 'http://petergrassberger.com/rss/projects/all/',
        'language' => 'en,de',
        'pages' => $pages
    ));

    $app->response->headers->set('Content-Type', 'application/rss+xml');
    $app->response->setBody($pageRendered);
})->setName('rssProjectsAllLanguages');

$app->get('/rss/blog/all(/)', function() use($app, $config, $pdo, $mustache, $language) {
    $pagesController = new PagesController($config, $pdo);
    $pages = $pagesController->getAllByType('post', array('pagecontents.created DESC'));
    $pages = $pagesController->addUrls($pages);
    $pages = $pagesController->addPubDate($pages);

    $app->lastModified($pagesController->findLastModified($pages));
    $app->expires('+1 week');

    $pageTemplate = $mustache->loadTemplate('rss');
    $pageRendered = $pageTemplate->render(array(
        'rssTitle' => 'Peter Grassberger - RSS feed: posts all languages',
        'rssDescription' => 'RSS feed of posts in  all languages',
        'rssUrl' => $language === 'de' ? 'http://petergrassberger.at/rss/blog/all/' : 'http://petergrassberger.com/rss/blog/all/',
        'language' => 'en,de',
        'pages' => $pages
    ));

    $app->response->headers->set('Content-Type', 'application/rss+xml');
    $app->response->setBody($pageRendered);
})->setName('rssBlogAllLanguages');

/**
 * DISPLAY PAGES, PROJECTS AND POSTS
 */

$app->get('/:pageTitle', function($pageTitle) use($app) {
    $app->redirect('/' . $pageTitle . '/', 301);
})->setName('pagesRedirect');

$app->get('/:pageTitle/', function($pageTitle) use($app, $config, $pdo, $mustache, $language) {
    $pagesController = new PagesController($config, $pdo);
    $translations = $pagesController->getByTypeAndTitle('page', $pageTitle);
    $translations = $pagesController->addUrls($translations);
    $page = $pagesController->filterByLanguage($translations, $language);

    if (!$page) {
        $app->notFound();
    }

    $app->lastModified(strtotime($page->updated));
    $app->expires('+1 week');

    $pageTemplate = $mustache->loadTemplate('page');
    $app->response->setBody($pageTemplate->render(array(
        'title' => 'Peter Grassberger - ' . $page->title,
        'language' => $language,
        'page' => $page,
        'translations' => $translations
    )));
})->setName('pages');

$app->get('/portfolio/:projectTitle(/)', function($projectTitle) use($app, $config, $pdo, $language) {
    $pagesController = new PagesController($config, $pdo);
    $project = $pagesController->getOneByTypeAndLanguageAndTitle('project', $language, $projectTitle);

    if (!$project) {
        $app->notFound();
    }

    $app->redirect('/projects/' . $projectTitle . '/', 301);
})->setName('portfolioProjectRedirect');

$app->get('/projects/:projectTitle', function($projectTitle) use($app) {
    $app->redirect('/projects/' . $projectTitle . '/', 301);
})->setName('projectRedirect');

$app->get('/projects/:projectTitle/', function($projectTitle) use($app, $config, $pdo, $mustache, $language) {
    $pagesController = new PagesController($config, $pdo);
    $translations = $pagesController->getByTypeAndTitle('project', $projectTitle);
    $translations = $pagesController->addUrls($translations);
    $project = $pagesController->filterByLanguage($translations, $language);

    if (!$project) {
        $app->notFound();
    }

    $app->lastModified(strtotime($project->updated));
    $app->expires('+1 week');

    $projectTemplate = $mustache->loadTemplate('project');
    $app->response->setBody($projectTemplate->render(array(
        'title' => 'Peter Grassberger - ' . $project->title,
        'language' => $language,
        'project' => $project,
        'translations' => $translations
    )));
})->setName('project');

$app->get('/blog/:postTitle', function($postTitle) use($app) {
    $app->redirect('/blog/' . $postTitle . '/', 301);
})->setName('projectsRedirect');

$app->get('/blog/:postTitle/', function($postTitle) use($app, $config, $pdo, $mustache, $language) {
    $pagesController = new PagesController($config, $pdo);
    $translations = $pagesController->getByTypeAndTitle('post', $postTitle);
    $translations = $pagesController->addUrls($translations);
    $translations = $pagesController->addDatesDmy($translations);
    $post = $pagesController->filterByLanguage($translations, $language);

    if (!$post) {
        $app->notFound();
    }

    $app->lastModified(strtotime($post->updated));
    $app->expires('+1 week');

    $postTemplate = $mustache->loadTemplate('post');
    $app->response->setBody($postTemplate->render(array(
        'title' => 'Peter Grassberger - ' . $post->title,
        'language' => $language,
        'post' => $post,
        'translations' => $translations
    )));
})->setName('projects');

// ...

$app->run();



