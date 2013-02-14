<?php
/**
 * @package api-framework
 * @author Peter Grassberger <petertheone@gmail.com>
 * @abstract
 */

include_once('../functions.inc.php');
include_once("../config.inc.php");

class PostController {

    protected $table;
    protected $allowedParameters = array();

    // TODO: add content, description and tags with LIKE
    public function  __construct() {
        $this->table = 'posts';
        $this->allowedParameters = array(
            'id' => 'int',
            'published' => 'int',
            'version' => 'int',
            'isCurrentVersion' => 'int',

            'title' => 'string',
            'title_clean' => 'string',

            'language' => 'string',
            'isFamilyFriendly' => 'int',
            'datetimeModified' => 'timestamp',

            'datetimeCreated' => 'timestamp',
            'datetimeModified' => 'timestamp',
            'datetimePublished' => 'timestamp'
        );
    }

    private function calculateAbsoluteUrl($data) {
        $url = BASEURL;
        $url .= '/blog/' . $data['title_clean'] . '/';

        return $url;
    }

    /**
     * @return mysqli
     */
    private function db_connect() {
        /** @var $db_con mysqli */
        $db_con = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWD, DB_DBNAME);
        //TODO: error handling..
        if (!$db_con) {
            die('Could not connect: ' . mysqli_error($db_con));
        }
        $db_con->set_charset("utf8");
        return $db_con;
    }

    /**
     * @param $db_con
     * @param $result
     * @return bool
     */
    private function db_hasErrors($db_con, $result) {
        if(!$result){
            if (PRINT_DB_ERRORS) {
                echo "<p>error: " . mysqli_error($db_con) . "</p>";
            }
            return true;
        }
        return false;
    }

    /**
     * GET method.
     *
     * @param Request $request
     * @return string
     */
    public function get($request) {
        //TODO: allow for multiple ids in a comma sperated list

        // filter unallowed parameters
        $parameters = whitelist($request->parameters, $this->allowedParameters);

        $db_con = $this->db_connect();
        $query = "
            SELECT
                *,
                DATE(datetimeCreated) as dateCreated,
                DATE(datetimeModified) as dateModified,
                DATE(datetimePublished) as datePublished,
                TIME(datetimeCreated) as timeCreated,
                TIME(datetimeModified) as timeModified,
                TIME(datetimePublished) as timePublished,
                YEAR(datetimeCreated) as copyrightYear,
                title as name,
                title as headline,
                language as inLanguage,
                tags as keywords,
                content as text,
                content as articleBody
                /*categories as articleSection*/
			FROM
			    $this->table";
        $query_where = array();
        $query_order = "";

        foreach ($parameters as $parameterName => $parameterValue) {
            switch ($this->allowedParameters[$parameterName]) {
                case 'int':
                    $parameterValue = filter_var($parameterValue, FILTER_VALIDATE_INT);
                    $query_where[] = "$parameterName = $parameterValue";
                    break;
                case 'string':
                case 'timestamp':
                    // TODO: sanitize string
                    //$parameterValue = filter_var($parameterValue, FILTER_SANITIZE_STRING);
                    $parameterValue = mysqli_real_escape_string($db_con, $parameterValue);
                    $query_where[] = "$parameterName = '$parameterValue'";
                    break;
            }
        }

        if (count($query_where) != 0) {
            $query .= ' WHERE ' . implode(' AND ', $query_where);
        }

        if (isset($request->parameters['orderby'])) {
            $parameterValue = filter_var($request->parameters['orderby'], FILTER_SANITIZE_STRING);
            $parameterValue = mysqli_real_escape_string($db_con, $parameterValue);

            $orderArray = explode(',', $parameterValue);

            $tempArray = array();
            foreach ($orderArray as $key => $value) {
                $value = trim($value);
                $valueTemp = endswithCrop($value, ' ASC');
                $valueTemp = endswithCrop($valueTemp, ' DESC');
                if (isset($this->allowedParameters[$valueTemp])) {
                    $tempArray[$key] = $value;
                }
            }
            $orderArray = $tempArray;

            $query .= ' ORDER BY ' . implode(', ', $orderArray);
        }

        $result = mysqli_query($db_con, $query);
        if ($this->db_hasErrors($db_con, $result)) {
            return false;
        }
        $resultArray = array();
        while($column = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            $column['absoluteUrl'] = $this->calculateAbsoluteUrl($column);
            $column['url'] = $column['absoluteUrl'];
            $resultArray[] = $column;
        }
        return $resultArray;
    }
}