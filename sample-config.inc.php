<?php

    /* DATABASE CONFIG */
    define('DB_HOST', 		'');
    define('DB_USERNAME', 	'');
    define('DB_PASSWD', 	'');
    define('DB_DBNAME', 	'');

    /* ADMIN MENU CONFIG */ // ADMIN_PASS salted with PASSWORD_SALT
    define('ADMIN_USER', 	'');
    define('ADMIN_PASS', 	'');

    /* SALT */
    define('SESSION_SALT',	'');
    define('PASSWORD_SALT',	'');

    /* OPTIONS */
    define("PRINT_DB_ERRORS", true);
    define('HTTPS_REDIRECT', true);

    /**
     * @config
     * E_ERROR, E_WARNING, E_PARSE, E_NOTICE, E_CORE_ERROR,
     * E_CORE_WARNING, E_COMPILE_ERROR, E_COMPILE_WARNING,
     * E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE, E_STRICT
     * E_RECOVERABLE_ERROR, E_DEPRECATED, E_USER_DEPRECATED
     * E_ALL
     */
    error_reporting(E_ALL | E_STRICT);
    ini_set('display_errors', true);

    /* BaseUrl */
    define('BASEURL', 		'http://' . $_SERVER['HTTP_HOST'] . '/');
    define('API_PATH',      BASEURL . 'api');

?>
