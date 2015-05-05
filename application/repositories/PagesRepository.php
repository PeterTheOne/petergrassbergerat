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
     * @return array
     */
    public function getAll() {
        $statement = $this->pdo->prepare('
            SELECT pages.id, pages.id AS page_id, pages.created, pages.index,
            pages.page_type AS page_type_id, pagecontents.updated,
            languages.id AS language, languages.name AS languageName,
            languages.tag AS languageTag, pagecontents.title, pagecontents.title_clean,
            pagecontents.content, pagetypes.name AS page_type
            FROM pages
            INNER JOIN pagecontents ON pages.id = pagecontents.page_id
            INNER JOIN pagetypes ON pages.page_type = pagetypes.id
            INNER JOIN languages ON pagecontents.language = languages.id
            ORDER BY pagecontents.created DESC;
        ');
        $statement->execute();
        return $statement->fetchAll();
    }

    /**
     * @param $pageType
     * @param array $orderBy
     * @return array
     */
    public function getAllByType($pageType, array $orderBy) {
        $orderByString = implode(', ', $orderBy);
        $statement = $this->pdo->prepare('
            SELECT pages.id, pages.id AS page_id, pages.created, pages.index,
            pages.page_type AS page_type_id, pagecontents.updated,
            languages.id AS language, languages.name AS languageName,
            languages.tag AS languageTag, pagecontents.title, pagecontents.title_clean,
            pagecontents.content, pagetypes.name AS page_type
            FROM pages
            INNER JOIN pagecontents ON pages.id = pagecontents.page_id
            INNER JOIN pagetypes ON pages.page_type = pagetypes.id
            INNER JOIN languages ON pagecontents.language = languages.id
            WHERE pagetypes.name = :pageType
            ORDER BY ' . $orderByString . ';
        ');
        $statement->bindParam(':pageType', $pageType);
        $statement->execute();
        return $statement->fetchAll();
    }

    /**
     * @param $languageTag
     * @return array
     */
    public function getAllByLanguage($languageTag) {
        $statement = $this->pdo->prepare('
            SELECT pages.id, pages.id AS page_id, pages.created, pages.index,
            pages.page_type AS page_type_id, pagecontents.updated,
            languages.id AS language, languages.name AS languageName,
            languages.tag AS languageTag, pagecontents.title, pagecontents.title_clean,
            pagecontents.content, pagetypes.name AS page_type
            FROM pages
            INNER JOIN pagecontents ON pages.id = pagecontents.page_id
            INNER JOIN pagetypes ON pages.page_type = pagetypes.id
            INNER JOIN languages ON pagecontents.language = languages.id
            WHERE languages.tag = :languageTag
            ORDER BY pagecontents.created DESC;
        ');
        $statement->bindParam(':languageTag', $languageTag);
        $statement->execute();
        return $statement->fetchAll();
    }

    /**
     * @param $pageType
     * @param $languageTag
     * @return array
     */
    public function getAllByTypeAndLanguage($pageType, $languageTag) {
        $statement = $this->pdo->prepare('
            SELECT pages.id, pages.id AS page_id, pages.created, pages.index,
            pages.page_type AS page_type_id, pagecontents.updated,
            languages.id AS language, languages.name AS languageName,
            languages.tag AS languageTag, pagecontents.title, pagecontents.title_clean,
            pagecontents.content, pagetypes.name AS page_type
            FROM pages
            INNER JOIN pagecontents ON pages.id = pagecontents.page_id
            INNER JOIN pagetypes ON pages.page_type = pagetypes.id
            INNER JOIN languages ON pagecontents.language = languages.id
            WHERE pagetypes.name = :pageType AND languages.tag = :languageTag
            ORDER BY pagecontents.created DESC;
        ');
        $statement->bindParam(':pageType', $pageType);
        $statement->bindParam(':languageTag', $languageTag);
        $statement->execute();
        return $statement->fetchAll();
    }

    /**
     * @return mixed
     */
    public function getIndexPage() {
        $statement = $this->pdo->prepare('
            SELECT pages.id, pages.id AS page_id, pages.created, pages.index,
            pages.page_type AS page_type_id, pagecontents.updated,
            languages.id AS language, languages.name AS languageName,
            languages.tag AS languageTag, pagecontents.title, pagecontents.title_clean,
            pagecontents.content, pagetypes.name AS page_type
            FROM pages
            INNER JOIN pagecontents ON pages.id = pagecontents.page_id
            INNER JOIN pagetypes ON pages.page_type = pagetypes.id
            INNER JOIN languages ON pagecontents.language = languages.id
            WHERE pagetypes.name = "page" AND pages.index = 1;
        ');
        $statement->execute();
        return $statement->fetchAll();
    }

    /**
     * @param $languageTag
     * @return mixed
     */
    public function getOneIndexPageByLanguage($languageTag) {
        $statement = $this->pdo->prepare('
            SELECT pages.id, pages.id AS page_id, pages.created, pages.index,
            pages.page_type AS page_type_id, pagecontents.updated,
            languages.id AS language, languages.name AS languageName,
            languages.tag AS languageTag, pagecontents.title, pagecontents.title_clean,
            pagecontents.content, pagetypes.name AS page_type
            FROM pages
            INNER JOIN pagecontents ON pages.id = pagecontents.page_id
            INNER JOIN pagetypes ON pages.page_type = pagetypes.id
            INNER JOIN languages ON pagecontents.language = languages.id
            WHERE pagetypes.name = "page" AND languages.tag = :languageTag AND pages.index = 1
            LIMIT 1;
        ');
        $statement->bindParam(':languageTag', $languageTag);
        $statement->execute();
        return $statement->fetch();
    }

    /**
     * @param $pageType
     * @param $pageTitle
     * @return mixed
     */
    public function getByTypeAndTitle($pageType, $pageTitle) {
        $statement = $this->pdo->prepare('
            SELECT pages.id, pages.id AS page_id, pages.created, pages.index,
            pages.page_type AS page_type_id, pagecontents.updated,
            languages.id AS language, languages.name AS languageName,
            languages.tag AS languageTag, pagecontents.title, pagecontents.title_clean,
            pagecontents.content, pagetypes.name AS page_type
            FROM pages
            INNER JOIN pagecontents ON pages.id = pagecontents.page_id
            INNER JOIN pagetypes ON pages.page_type = pagetypes.id
            INNER JOIN languages ON pagecontents.language = languages.id
            WHERE pagetypes.name = :pageType AND
            pagecontents.title_clean = :pageTitle;
        ');
        $statement->bindParam(':pageType', $pageType);
        $statement->bindParam(':pageTitle', $pageTitle);
        $statement->execute();
        return $statement->fetchAll();
    }

    /**
     * @param $pageType
     * @param $languageTag
     * @param $pageTitle
     * @return mixed
     */
    public function getOneByTypeAndLanguageAndTitle($pageType, $languageTag, $pageTitle) {
        $statement = $this->pdo->prepare('
            SELECT pages.id, pages.id AS page_id, pages.created, pages.index,
            pages.page_type AS page_type_id, pagecontents.updated,
            languages.id AS language, languages.name AS languageName,
            languages.tag AS languageTag, pagecontents.title, pagecontents.title_clean,
            pagecontents.content, pagetypes.name AS page_type
            FROM pages
            INNER JOIN pagecontents ON pages.id = pagecontents.page_id
            INNER JOIN pagetypes ON pages.page_type = pagetypes.id
            INNER JOIN languages ON pagecontents.language = languages.id
            WHERE pagetypes.name = :pageType AND languages.tag = :languageTag AND
            pagecontents.title_clean = :pageTitle
            LIMIT 1;
        ');
        $statement->bindParam(':pageType', $pageType);
        $statement->bindParam(':languageTag', $languageTag);
        $statement->bindParam(':pageTitle', $pageTitle);
        $statement->execute();
        return $statement->fetch();
    }

    /**
     * @param $pageType
     * @param $languageTag
     * @param $projectTitle
     * @param $title
     * @param $title_clean
     * @param $content
     * @return bool
     */
    public function updateByTypeAndLanguageAndTitle($pageType, $languageTag, $projectTitle, $title, $title_clean, $content) {
        $statement = $this->pdo->prepare('
            UPDATE pagecontents
            INNER JOIN pages ON pagecontents.page_id = pages.id
            INNER JOIN pagetypes ON pages.page_type = pagetypes.id
            INNER JOIN languages ON pagecontents.language = languages.id
            SET title = :title, title_clean = :title_clean, content = :content, updated = NOW()
            WHERE pagetypes.name = :pageType AND languages.tag = :languageTag AND
            title_clean = :projectTitle;
        ');
        $statement->bindParam(':pageType', $pageType);
        $statement->bindParam(':languageTag', $languageTag);
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
     * @param $language
     * @param $title
     * @param $title_clean
     * @param $content
     * @return bool
     */
    public function createPageContentsByPageId($pageId, $language, $title, $title_clean, $content) {
        $statement = $this->pdo->prepare('
            INSERT INTO pagecontents (page_id, created, updated, language, title, title_clean, content)
            VALUES (:pageId, NOW(), NOW(), :language, :title, :title_clean, :content);
        ');
        $statement->bindParam(':pageId', $pageId);
        $statement->bindParam(':language', $language);
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

    /**
     * @return array
     */
    public function getAllLanguages() {
        $statement = $this->pdo->prepare('
            SELECT * FROM languages;
        ');
        $statement->execute();
        return $statement->fetchAll();
    }

    /**
     * @param $id
     * @return array
     */
    public function getAllLanguagesNotUsedByPageId($id) {
        /*$statement = $this->pdo->prepare('
          SELECT * FROM languages
          INNER JOIN pagecontents ON languages.id = pagecontents.language
          INNER JOIN pages ON pages.id = pagecontents.page_id
          WHERE pages.id = :id;
        ');*/
        $statement = $this->pdo->prepare('
          SELECT * FROM languages
          WHERE id NOT IN (
            SELECT languages.id FROM languages
              INNER JOIN pagecontents ON languages.id = pagecontents.language
              INNER JOIN pages ON pages.id = pagecontents.page_id
              WHERE pages.id = :id
          );
        ');
        $statement->bindParam(':id', $id);
        $statement->execute();
        return $statement->fetchAll();
    }

    /**
     * @param $pageType
     * @param $pageTitle
     */
    /*public function getMissingLanguagesByTypeAndTitle($pageType, $pageTitle) {
        $statement = $this->pdo->prepare('
            SELECT * FROM languages
            INNER JOIN pagecontents ON languages.id = pagecontents.language
            INNER JOIN pages ON pages.id = pagecontents.page_id
            INNER JOIN pagetypes ON pages.page_type = pagetypes.id
            WHERE pagetypes.name = :pageType;
        ');
        $statement->bindParam(':pageType', $pageType);
        $statement->bindParam(':pageTitle', $pageTitle);
        $statement->execute();
        return $statement->fetchAll();
    }*/

    /**
     * @param $pageId
     * @return array
     */
    public function getTagsPageId($pageId) {
        $statement = $this->pdo->prepare('
            SELECT * FROM tags
            INNER JOIN tagtopage ON tags.id = tagtopage.tag_id
            WHERE tagtopage.page_id = :pageId;
        ');
        $statement->bindParam(':pageId', $pageId);
        $statement->execute();
        return $statement->fetchAll();
    }

    /**
     * @return array
     */
    public function getAllTags() {
        $statement = $this->pdo->prepare('
            SELECT * FROM tags;
        ');
        $statement->execute();
        return $statement->fetchAll();
    }

    /**
     * @param $name
     * @param $name_clean
     * @param $color
     * @return bool
     */
    public function createTag($name, $name_clean, $color) {
        $statement = $this->pdo->prepare('
            INSERT INTO tags (name, name_clean, color) VALUES
            (:name, :name_clean, :color);
        ');
        $statement->bindParam(':name', $name);
        $statement->bindParam(':name_clean', $name_clean);
        $statement->bindParam(':color', $color);
        $statement->execute();
        return $this->pdo->lastInsertId();
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getOneTagByName($name) {
        $statement = $this->pdo->prepare('
            SELECT * FROM tags
            WHERE name_clean = :name_clean
            LIMIT 1;
        ');
        $statement->bindParam(':name_clean', $name);
        $statement->execute();
        return $statement->fetch();
    }

    /**
     * @param $tagName
     * @param $name
     * @param $name_clean
     * @param $color
     * @return bool
     */
    public function updateTagByName($tagName, $name, $name_clean, $color) {
        $statement = $this->pdo->prepare('
            UPDATE tags
            SET name = :name, name_clean = :name_clean, color = :color
            WHERE tags.name = :tagName;
        ');
        $statement->bindParam(':tagName', $tagName);
        $statement->bindParam(':name', $name);
        $statement->bindParam(':name_clean', $name_clean);
        $statement->bindParam(':color', $color);
        return $statement->execute();
    }

    /**
     * @param $type
     * @param $projectTitle
     * @return bool
     */
    public function removeAllTagsByTypeAndTitle($type, $projectTitle) {
        $statement = $this->pdo->prepare('
            DELETE tagtopage FROM tagtopage
            INNER JOIN pages ON tagtopage.page_id = pages.id
            INNER JOIN pagecontents ON tagtopage.page_id = pagecontents.page_id
            INNER JOIN pagetypes ON pages.page_type = pagetypes.id
            WHERE pagetypes.name = :pageType AND pagecontents.title_clean = :projectTitle;
        ');
        $statement->bindParam(':pageType', $pageType);
        $statement->bindParam(':projectTitle', $projectTitle);
        return $statement->execute();
    }

    /**
     * @param $pageType
     * @param $projectTitle
     * @param $tags
     * @return bool
     */
    public function addTagsByTypeAndTitle($pageType, $projectTitle, $tags) {
        $statement = $this->pdo->prepare('
            INSERT INTO tagtopage (tag_id, page_id)
                SELECT tags.id, pages.id FROM tags, pages
                INNER JOIN pagecontents ON pages.id = pagecontents.page_id
                INNER JOIN pagetypes ON pages.page_type = pagetypes.id
                WHERE tags.name_clean = :tag_name AND
                pagetypes.name = :pageType AND
                pagecontents.title_clean = :projectTitle;
        ');
        foreach($tags as $tag) {
          $statement->bindParam(':pageType', $pageType);
          $statement->bindParam(':projectTitle', $projectTitle);
          $statement->bindParam(':tag_name', $tag);
          $statement->execute();
        }
        return true;
    }

}