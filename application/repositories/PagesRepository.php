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
     * @param $pageType
     * @return mixed
     */
    public function getAllByType($pageType) {
        $statement = $this->pdo->prepare('
            SELECT pages.id, pages.id AS page_id, pages.created, pages.index,
            pages.page_type AS page_type_id, pagecontents.updated,
            pagecontents.title, pagecontents.title_clean, pagecontents.content,
            pagetypes.name AS page_type
            FROM pages
            INNER JOIN pagecontents ON pages.id = pagecontents.page_id
            INNER JOIN pagetypes ON pages.page_type = pagetypes.id
            WHERE pagetypes.name = :pageType;
        ');
        $statement->bindParam(':pageType', $pageType);
        $statement->execute();
        return $statement->fetchAll();
    }

    /**
     * @return mixed
     */
    public function getOneIndexPage() {
        $statement = $this->pdo->prepare('
            SELECT pages.id, pages.id AS page_id, pages.created, pages.index,
            pages.page_type AS page_type_id, pagecontents.updated,
            pagecontents.title, pagecontents.title_clean, pagecontents.content,
            pagetypes.name AS page_type
            FROM pages
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
    public function getOneByTypeAndTitle($pageType, $pageTitle) {
        $statement = $this->pdo->prepare('
            SELECT pages.id, pages.id AS page_id, pages.created, pages.index,
            pages.page_type AS page_type_id, pagecontents.updated,
            pagecontents.title, pagecontents.title_clean, pagecontents.content,
            pagetypes.name AS page_type
            FROM pages
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

    /**
     * @param $pageType
     * @param $projectTitle
     * @param $title
     * @param $title_clean
     * @param $content
     * @return bool
     */
    public function updateByTypeAndTitle($pageType, $projectTitle, $title, $title_clean, $content) {
        $statement = $this->pdo->prepare('
            UPDATE pagecontents
            INNER JOIN pages ON pagecontents.page_id = pages.id
            INNER JOIN pagetypes ON pages.page_type = pagetypes.id
            SET title = :title, title_clean = :title_clean, content = :content, updated = NOW()
            WHERE pagetypes.name = :pageType AND title_clean = :projectTitle;
        ');
        $statement->bindParam(':pageType', $pageType);
        $statement->bindParam(':projectTitle', $projectTitle);
        $statement->bindParam(':title', $title);
        $statement->bindParam(':title_clean', $title_clean);
        $statement->bindParam(':content', $content);
        return $statement->execute();
    }

    /**
     * @param $pageType
     * @return bool
     */
    public function createPageByType($pageType) {
        $statement = $this->pdo->prepare('
            INSERT INTO pages (page_type, created)
            SELECT id, NOW()
            FROM pagetypes
            WHERE name = :pageType;
        ');
        $statement->bindParam(':pageType', $pageType);
        $statement->execute();
        return $this->pdo->lastInsertId();
    }

    /**
     * @param $pageId
     * @param $title
     * @param $title_clean
     * @param $content
     * @return bool
     */
    public function createPageContentsByPageId($pageId, $title, $title_clean, $content) {
        $statement = $this->pdo->prepare('
            INSERT INTO pagecontents (page_id, created, updated, title, title_clean, content)
            VALUES (:pageId, NOW(), NOW(), :title, :title_clean, :content);
        ');
        $statement->bindParam(':pageId', $pageId);
        $statement->bindParam(':title', $title);
        $statement->bindParam(':title_clean', $title_clean);
        $statement->bindParam(':content', $content);
        return $statement->execute();
    }

    /**
     * @param $pageId
     * @return bool
     */
    public function removePageById($pageId) {
        $statement = $this->pdo->prepare('
            DELETE FROM pages
            WHERE id = :pageId
            LIMIT 1;
        ');
        $statement->bindParam(':pageId', $pageId);
        return $statement->execute();
    }

    /**
     * @param $pageId
     * @return bool
     */
    public function removePageContentsByPageId($pageId) {
        $statement = $this->pdo->prepare('
            DELETE FROM pagecontents
            WHERE page_id = :pageId
            LIMIT 1;
        ');
        $statement->bindParam(':pageId', $pageId);
        return $statement->execute();
    }

}