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
            SELECT * FROM pages INNER JOIN pagecontents ON pages.id = pagecontents.page_id;
        ');
        $statement->execute();
        return $statement->fetch();
    }
}