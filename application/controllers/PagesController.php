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
     * @return mixed
     */
    public function getOneIndex() {
        return $this->repository->getOneIndexPage();
    }

    /**
     * @param $pageType
     * @param $pageTitle
     * @return mixed
     */
    public function getOneByTypeAndTitle($pageType, $pageTitle) {
        return $this->repository->getOneByTypeAndTitle($pageType, $pageTitle);
        // todo: redirect to root if is index page
    }

    /**
     * @param $pageType
     * @return mixed
     */
    public function getAllByType($pageType) {
        return $this->repository->getAllByType($pageType);
    }

    /**
     * @param $projectTitle
     * @param $title
     * @param $title_clean
     * @param $content
     * @return bool
     */
    public function updatePage($projectTitle, $title, $title_clean, $content) {
        return $this->repository->updateByTypeAndTitle('page', $projectTitle, $title, $title_clean, $content);
    }

    /**
     * @param $projectTitle
     * @param $title
     * @param $title_clean
     * @param $content
     * @return bool
     */
    public function updateProject($projectTitle, $title, $title_clean, $content) {
        return $this->repository->updateByTypeAndTitle('project', $projectTitle, $title, $title_clean, $content);
    }

    /**
     * @param $projectTitle
     * @param $title
     * @param $title_clean
     * @param $content
     * @return bool
     */
    public function updatePost($projectTitle, $title, $title_clean, $content) {
        return $this->repository->updateByTypeAndTitle('post', $projectTitle, $title, $title_clean, $content);
    }

    /**
     * @param $title
     * @param $title_clean
     * @param $content
     * @return bool
     */
    public function createPage($title, $title_clean, $content) {
        //todo: do this with a transaction
        $pageId = $this->repository->createPageByType('page');
        return $this->repository->createPageContentsByPageId($pageId, $title, $title_clean, $content);
    }

    /**
     * @param $title
     * @param $title_clean
     * @param $content
     * @return bool
     */
    public function createProject($title, $title_clean, $content) {
        //todo: do this with a transaction
        $pageId = $this->repository->createPageByType('project');
        return $this->repository->createPageContentsByPageId($pageId, $title, $title_clean, $content);
    }

    /**
     * @param $title
     * @param $title_clean
     * @param $content
     * @return bool
     */
    public function createPost($title, $title_clean, $content) {
        //todo: do this with a transaction
        $pageId = $this->repository->createPageByType('post');
        return $this->repository->createPageContentsByPageId($pageId, $title, $title_clean, $content);
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

}