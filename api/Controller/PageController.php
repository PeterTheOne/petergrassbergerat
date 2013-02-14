<?php
/**
 * page controller.
 *
 * @package api-framework
 * @author Peter Grassberger <petertheone@gmail.com>
 */

class PageController extends AbstractDatabaseController {

    public function  __construct() {
        $this->table = 'pages';
        $this->allowedParameters = array(
            'id' => 'int',
            'last_change' => 'timestamp',
            'title' => 'string',
            'title_clean' => 'string',
            'lang' => 'string'
        );
    }

    // TODO: create function that calculates the absolute url to content
}
?>
