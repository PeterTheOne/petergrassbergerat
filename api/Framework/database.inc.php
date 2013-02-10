<?php

include_once("../config.inc.php");

function db_connect() {
    $db_con = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWD, DB_DBNAME);
    //TODO: error handling..
    if (!$db_con) {
        die('Could not connect: ' . mysqli_error($db_con));
    }
    $db_con->set_charset("utf8");
    return $db_con;
}

function db_hasErrors($db_con, $result) {
    if(!$result){
        if (PRINT_DB_ERRORS) {
            echo "<p>error: " . mysqli_error($db_con) . "</p>";
        }
        return true;
    }
    return false;
}