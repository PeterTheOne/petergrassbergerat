<?php
/**
 * Sample news controller.
 *
 * @package api-framework
 * @author Martin Bean <martin@martinbean.co.uk>
 */
class TestController
{
    /**
     * News file path.
     *
     * @var variable type
     */
    protected $articles_file = '../private/data/news.txt';

    /**
     * GET method.
     *
     * @param Request $request
     * @return string
     */
    public function get($request)
    {
        $articles = $this->readArticles();
        switch (count($request->segments)) {
            case 1:
                return $articles;
                break;
            case 2:
                $article_id = $request->segments[1];
                return $articles[$article_id];
                break;
        }
    }

    /**
     * POST action.
     *
     * @param $request
     * @return null
     */
    public function post($request)
    {
        switch (count($request->segments)) {
            case 1:
                $id = (count($articles) + 1);
                $articles = $this->readArticles();
                $article = array(
                    'id' => $id,
                    'title' => $request->parameters['title'],
                    'content' => $request->parameters['content'],
                    'published' => date('c')
                );
                $articles[] = $article;
                $this->writeArticles($articles);
                header('Location: http://' . $_SERVER['HTTP_HOST'] . '/news/' . $id, null, 201);
                exit;
                break;
        }
    }

    /**
     * Read articles.
     *
     * @return array
     */
    protected function readArticles()
    {
        $articles = unserialize(file_get_contents($this->articles_file));
        if (empty($articles)) {
            $now = new DateTime();
            $articles = array(
                1 => array(
                    'id' => 1,
                    'title' => 'Welcome to your new API framework!',
                    'content' => 'To get started with your new API framework, check out the README on the GitHub repository.',
                    'published' => $now->format('c')
                )
            );
            $this->writeArticles($articles);
        }
        return $articles;
    }

    /**
     * Write articles.
     *
     * @param string $articles
     * @return boolean
     */
    protected function writeArticles($articles)
    {
        return file_put_contents($this->articles_file, serialize($articles));
    }
}