<?php

use Phinx\Migration\AbstractMigration;

class CreatePagecontents extends AbstractMigration
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
        $pages = $this->table('pagecontents');
        $pages->addColumn('page_id', 'integer')
            ->addColumn('created', 'datetime')
            ->addColumn('title', 'string', array('limit' => 300))
            ->addColumn('content', 'text')
            ->addForeignKey('page_id', 'pages', 'id')
            ->create();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->dropTable('pagecontents');
    }
}