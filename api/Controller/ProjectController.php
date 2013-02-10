<?php
/**
 * project controller.
 *
 * @package api-framework
 * @author Peter Grassberger <petertheone@gmail.com>
 */


class ProjectController extends AbstractDatabaseController {

    public function  __construct() {
        $this->table = 'projects';
        $this->allowedParameters = array(
            'id' => 'int',
            'last_change' => 'timestamp',
            'title' => 'string',
            'title_clean' => 'string',
            'lang' => 'string',
            'year' => 'int',
            'wip' => 'int'
        );
    }
}
?>
