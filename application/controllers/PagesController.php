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
     *
     */
    public function get() {
        return $this->repository->get();
    }

    /**
     *
     */
    public function getOneIndex() {
        return $this->repository->getOneIndex();
    }

}