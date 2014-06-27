<?php

class PagesController {

    /**
     * @var stdClass
     */
    private $config;

    /**
     * @var PDO
     */
    private $pdo;

    /**
     * @var PagesRepository
     */
    private $repository;

    /**
     * @param stdClass $config
     * @param PDO $pdo
     */
    public function __construct(stdClass $config, PDO $pdo) {
        $this->config = $config;
        $this->pdo = $pdo;
        $this->repository = new PagesRepository($pdo);
    }

    /**
     * @param $pages
     * @return mixed
     */
    public function addUrls($pages) {
        if (!$pages) {
            return $pages;
        }
        foreach($pages as $page) {
            if ($page->languageTag === 'de') {
                $url = 'http://petergrassberger.at/';
            } else {
                $url = 'http://petergrassberger.com/';
            }
            if (!$page->index) {
                if ($page->page_type === 'post') {
                    $url .= 'blog/';
                } else if ($page->page_type === 'project') {
                    $url .= 'projects/';
                }
                $url .= $page->title_clean . '/';
            }
            $page->url = $url;
        }
        return $pages;
    }

    /**
     * @param $pages
     * @return array
     */
    public function regroupedById($pages) {
        if (!$pages) {
            return $pages;
        }
        $results = array();
        $id = -1;
        $currentGroup = array();
        foreach ($pages as $page) {
            if ($id === -1) {
                $id = $page->id;
            }
            if ($page->id > $id) {
                $results[] = $currentGroup;
                $currentGroup = array();
            }
            $currentGroup['id'] = $page->id;
            $currentGroup['title'] = isset($currentGroup['title']) ? $currentGroup['title'] : $page->title;
            $currentGroup['pages'][] = $page;
            $id = $page->id;
        }
        $results[] = $currentGroup;
        //echo '<pre>'; print_r($results); echo '</pre>'; exit;
        return $results;
    }

    /**
     * @param $pages
     * @return mixed
     */
    public function addPubDate($pages) {
        if (!$pages) {
            return $pages;
        }
        foreach($pages as $page) {
            $page->pubDate = (new DateTime($page->created))->format(DATETIME::RSS);
        }
        return $pages;
    }

    /**
     * @param $translations
     * @param $languageTag
     * @return null
     */
    public function filterByLanguage($translations, $languageTag) {
        if(!$translations) {
            return $translations;
        }
        $item = null;
        foreach($translations as $translation) {
            if ($languageTag == $translation->languageTag) {
                $item = $translation;
                break;
            }
        }
        return $item;
    }

    /**
     * @return mixed
     */
    public function getOneIndex() {
        return $this->repository->getIndexPage();
    }

    /**
     * @param $languageTag
     * @return mixed
     */
    public function getOneIndexByLanguage($languageTag) {
        return $this->repository->getOneIndexPageByLanguage($languageTag);
    }

    /**
     * @param $pageType
     * @param $pageTitle
     * @return mixed
     */
    public function getByTypeAndTitle($pageType, $pageTitle) {
        return $this->repository->getByTypeAndTitle($pageType, $pageTitle);
    }

    /**
     * @param $pageType
     * @param $langageTag
     * @param $pageTitle
     * @return mixed
     */
    public function getOneByTypeAndLanguageAndTitle($pageType, $langageTag, $pageTitle) {
        return $this->repository->getOneByTypeAndLanguageAndTitle($pageType, $langageTag, $pageTitle);
    }

    /**
     * @return mixed
     */
    public function getAll() {
        return $this->repository->getAll();
    }

    /**
     * @param $pageType
     * @param array $orderBy
     * @return mixed
     */
    public function getAllByType($pageType, array $orderBy = array('pagecontents.created DESC')) {
        return $this->repository->getAllByType($pageType, $orderBy);
    }

    /**
     * @param $languageTag
     * @return mixed
     */
    public function getAllByLanguage($languageTag) {
        return $this->repository->getAllByLanguage($languageTag);
    }

    /**
     * @param $pageType
     * @param $languageTag
     * @return mixed
     */
    public function getAllByTypeAndLanguage($pageType, $languageTag) {
        return $this->repository->getAllByTypeAndLanguage($pageType, $languageTag);
    }

    /**
     * @param $languageTag
     * @param $projectTitle
     * @param $title
     * @param $title_clean
     * @param $content
     * @return bool
     */
    public function updatePageByLanguageAndTitle($languageTag, $projectTitle, $title, $title_clean, $content) {
        return $this->repository->updateByTypeAndLanguageAndTitle('page', $languageTag, $projectTitle, $title, $title_clean, $content);
    }

    /**
     * @param $languageTag
     * @param $projectTitle
     * @param $title
     * @param $title_clean
     * @param $content
     * @return bool
     */
    public function updateProjectByLanguageAndTitle($languageTag, $projectTitle, $title, $title_clean, $content) {
        return $this->repository->updateByTypeAndLanguageAndTitle('project', $languageTag, $projectTitle, $title, $title_clean, $content);
    }

    /**
     * @param $languageTag
     * @param $projectTitle
     * @param $title
     * @param $title_clean
     * @param $content
     * @return bool
     */
    public function updatePostByLanguageAndTitle($languageTag, $projectTitle, $title, $title_clean, $content) {
        return $this->repository->updateByTypeAndLanguageAndTitle('post', $languageTag, $projectTitle, $title, $title_clean, $content);
    }

    /**
     * @param $title
     * @param $title_clean
     * @param $content
     * @param $language
     * @return bool
     */
    public function createPage($language, $title, $title_clean, $content) {
        //todo: do this with a transaction
        $pageId = $this->repository->createPageByType('page');
        return $this->repository->createPageContentsByPageId($pageId, $language, $title, $title_clean, $content);
    }

    /**
     * @param $language
     * @param $title
     * @param $title_clean
     * @param $content
     * @return bool
     */
    public function createProject($language, $title, $title_clean, $content) {
        //todo: do this with a transaction
        $pageId = $this->repository->createPageByType('project');
        return $this->repository->createPageContentsByPageId($pageId, $language, $title, $title_clean, $content);
    }

    /**
     * @param $language
     * @param $title
     * @param $title_clean
     * @param $content
     * @return bool
     */
    public function createPost($language, $title, $title_clean, $content) {
        //todo: do this with a transaction
        $pageId = $this->repository->createPageByType('post');
        return $this->repository->createPageContentsByPageId($pageId, $language, $title, $title_clean, $content);
    }

    /**
     * @param $title
     * @return mixed
     * @return bool
     */
    public function removePage($title) {
        //todo: do this with a transaction
        $page = $this->repository->getOneByTypeAndTitle('page', $title);
        $this->repository->removePageContentsByPageId($page->id);
        $this->repository->removePageById($page->id);
    }

    /**
     * @param $title
     * @return mixed
     * @return bool
     */
    public function removeProject($title) {
        //todo: do this with a transaction
        $project = $this->repository->getOneByTypeAndTitle('project', $title);
        $this->repository->removePageContentsByPageId($project->id);
        $this->repository->removePageById($project->id);
    }

    /**
     * @param $title
     * @return mixed
     * @return bool
     */
    public function removePost($title) {
        //todo: do this with a transaction
        $post = $this->repository->getOneByTypeAndTitle('post', $title);
        $this->repository->removePageContentsByPageId($post->id);
        $this->repository->removePageById($post->id);
    }

    /**
     * @return array
     */
    public function getAllLanguages() {
        return $this->repository->getAllLanguages();
    }

    /**
     * @param $id
     * @return array
     */
    public function getAllLanguagesNotUsedByPageId($id) {
        return $this->repository->getAllLanguagesNotUsedByPageId($id);
    }

    /**
     * @param $pageType
     * @param $pageTitle
     * @return array
     */
    public function getMissingLanguagesByTypeAndTitle($pageType, $pageTitle) {
        //return $this->repository->getMissingLanguagesByTypeAndTitle($pageType, $pageTitle);
    }

    /**
     * @param $pageId
     * @param $language
     * @param $title
     * @param $title_clean
     * @param $content
     * @return bool
     */
    public function translatePage($pageId, $language, $title, $title_clean, $content) {
        return $this->repository->createPageContentsByPageId($pageId, $language, $title, $title_clean, $content);
    }

    /**
     * @param $projectId
     * @param $language
     * @param $title
     * @param $title_clean
     * @param $content
     * @return bool
     */
    public function translateProject($projectId, $language, $title, $title_clean, $content) {
        return $this->repository->createPageContentsByPageId($projectId, $language, $title, $title_clean, $content);
    }

    /**
     * @param $postId
     * @param $language
     * @param $title
     * @param $title_clean
     * @param $content
     * @return bool
     */
    public function translatePost($postId, $language, $title, $title_clean, $content) {
        return $this->repository->createPageContentsByPageId($postId, $language, $title, $title_clean, $content);
    }

}