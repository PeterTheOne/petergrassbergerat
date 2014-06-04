<?php

use Phinx\Migration\AbstractMigration;

class CleanTitle extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     *
     * Uncomment this method if you would like to use it.
     *
    public function change()
    {
    }
    */
    
    /**
     * Migrate Up.
     */
    public function up()
    {
        $pagecontents = $this->table('pagecontents');
        $pagecontents->addColumn('title_clean', 'string', array('limit' => 300, 'after' => 'title'))
            ->update();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $pagecontents = $this->table('pagecontents');
        $pagecontents->removeColumn('title_clean');
    }
}