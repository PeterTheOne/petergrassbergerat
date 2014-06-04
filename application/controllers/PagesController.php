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
    public function getAll() {
        return $this->repository->get();
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
        $pageTitle = str_replace(' ', '', $pageTitle); // remove spaces
        $pageTitle = str_replace('-', ' ', $pageTitle); // turn dash into space
        return $this->repository->getOneByTypePageAndTitle($pageType, $pageTitle);
        // todo: redirect to root if is index page
    }

    /**
     * @param $pageType
     */
    public function getAllByType($pageType) {
        return $this->repository->getAllByType($pageType);
    }


}