<?php

class PagesRepository {

    /**
     * @var
     */
    protected $pdo;

    /**
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * @return mixed
     */
    public function getAll() {
        // todo: this needs more thinking..
        $statement = $this->pdo->prepare('
            SELECT * FROM pagecontents;
        ');
        $statement->execute();
        return $statement->fetchAll();
    }

    /**
     * @param $pageType
     * @return mixed
     */
    public function getAllByType($pageType) {
        $statement = $this->pdo->prepare('
            SELECT * FROM pages
            INNER JOIN pagecontents ON pages.id = pagecontents.page_id
            INNER JOIN pagetypes ON pages.page_type = pagetypes.id
            WHERE pagetypes.name = :pageType;
        ');
        $statement->bindParam(':pageType', $pageType);
        $statement->execute();
        return $statement->fetchAll();
    }

    /**
     * @param $pageId
     * @return mixed
     */
    public function getOneByPageId($pageId) {
        // todo: this needs more thinking..
        $statement = $this->pdo->prepare('
            SELECT * FROM pagecontents WHERE page_id = :pageId LIMIT 1;
        ');
        $statement->bindParam(':pageId', $pageId);
        $statement->execute();
        return $statement->fetch();
    }

    /**
     * @return mixed
     */
    public function getOneIndexPage() {
        // todo: this needs more thinking..
        $statement = $this->pdo->prepare('
            SELECT * FROM pages
            INNER JOIN pagecontents ON pages.id = pagecontents.page_id
            INNER JOIN pagetypes ON pages.page_type = pagetypes.id
            WHERE pagetypes.name = "page" AND pages.index = 1
            LIMIT 1;
        ');
        $statement->execute();
        return $statement->fetch();
    }

    /**
     * @param $pageType
     * @param $pageTitle
     * @return mixed
     */
    public function getOneByTypePageAndTitle($pageType, $pageTitle) {
        // todo: this needs more thinking..
        $statement = $this->pdo->prepare('
            SELECT * FROM pages
            INNER JOIN pagecontents ON pages.id = pagecontents.page_id
            INNER JOIN pagetypes ON pages.page_type = pagetypes.id
            WHERE pagetypes.name = :pageType AND pagecontents.title_clean = :pageTitle
            LIMIT 1;
        ');
        $statement->bindParam(':pageType', $pageType);
        $statement->bindParam(':pageTitle', $pageTitle);
        $statement->execute();
        return $statement->fetch();
    }

}