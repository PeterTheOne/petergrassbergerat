<?php

use Phinx\Migration\AbstractMigration;

class AddUpdated extends AbstractMigration
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
        $pagecontents->addColumn('updated', 'datetime', array('after' => 'created'))
            ->update();
        $this->query('UPDATE pagecontents SET updated = created');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $pagecontents = $this->table('pagecontents');
        $pagecontents->removeColumn('updated');
    }
}