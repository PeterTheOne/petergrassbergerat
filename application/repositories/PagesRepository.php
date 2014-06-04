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
    public function get() {
        // todo: this needs more thinking..
        $statement = $this->pdo->prepare('
            SELECT * FROM pagecontents;
        ');
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
    public function getOneIndex() {
        // todo: this needs more thinking..
        $statement = $this->pdo->prepare('
            SELECT * FROM pages
            INNER JOIN pagecontents ON pages.id = pagecontents.page_id
            WHERE pages.index = 1
            LIMIT 1;
        ');
        $statement->execute();
        return $statement->fetch();
    }

    /**
     * @param $pageTitle
     * @return mixed
     */
    public function getOneByTitle($pageTitle) {
        // todo: this needs more thinking..
        $statement = $this->pdo->prepare('
            SELECT * FROM pages
            INNER JOIN pagecontents ON pages.id = pagecontents.page_id
            WHERE pagecontents.title = :pageTitle
            LIMIT 1;
        ');
        $statement->bindParam(':pageTitle', $pageTitle);
        $statement->execute();
        return $statement->fetch();
    }

}