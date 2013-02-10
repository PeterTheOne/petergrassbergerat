<?php
/**
 * page controller.
 *
 * @package api-framework
 * @author Martin Bean <martin@martinbean.co.uk>
 */

include_once("Framework/database.inc.php");

class PageController {

    /**
     * GET method.
     *
     * @param Request $request
     * @return string
     */
    public function get($request) {
        /*$articles = array(
            array(
                'name' => 'page 01',
                'last_change' => 'date 01',
                'title' => 'title 01',
                'title_clean' => 'title_clean 01',
                'lang' => 'lang 01',
            ),
            array(
                'name' => 'page 02',
                'last_change' => 'date 02',
                'title' => 'title 02',
                'title_clean' => 'title_clean 02',
                'lang' => 'lang 02',
            )
        );*/

        $db_con = db_connect();
        $query = "SELECT * FROM pages";
        $query_where = array();

        //TODO: allow for multiple ids in a comma sperated list
        if (isset($request->parameters['id'])) {
            // get specific pages
            $id = filter_var($request->parameters['id'], FILTER_VALIDATE_INT);
            $id = mysqli_real_escape_string($db_con, $id);
            $query_where[] = "id = $id";
        }
        if (isset($request->parameters['last_change'])) {
            $last_change = $request->parameters['last_change'];
            $last_change = mysqli_real_escape_string($db_con, $last_change);
            $query_where[] = "last_change = '$last_change'";
        }
        if (isset($request->parameters['title'])) {
            $title = $request->parameters['title'];
            $title = mysqli_real_escape_string($db_con, $title);
            $query_where[] = "title = '$title'";
        }
        if (isset($request->parameters['title_clean'])) {
            $title_clean = $request->parameters['title_clean'];
            $title_clean = mysqli_real_escape_string($db_con, $title_clean);
            $query_where[] = "title_clean = '$title_clean'";
        }
        if (isset($request->parameters['lang'])) {
            $lang = $request->parameters['lang'];
            $lang = mysqli_real_escape_string($db_con, $lang);
            $query_where[] = "lang = '$lang'";
        }

        if (count($query_where) != 0) {
            $query .= ' WHERE ' . implode(' AND ', $query_where);
        }

        $result = mysqli_query($db_con, $query);
        if (db_hasErrors($db_con, $result)) {
            return false;
        }
        $resultArray = mysqli_fetch_array($result);
        if ($resultArray == null) {
            return array();
        }
        return $resultArray;

        //return $articles[$id];
        //return $articles;
    }
}
?>
