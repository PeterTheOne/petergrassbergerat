<?php

use Phinx\Migration\AbstractMigration;

class Tags extends AbstractMigration
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
        $pagetype = $this->table('tags');
        $pagetype->addColumn('name', 'string')
            ->addColumn('name_clean', 'string')
            ->addColumn('color', 'string')
            ->create();

        $pagetype = $this->table('tagtopage');
        $pagetype->addColumn('tag_id', 'integer')
            ->addColumn('page_id', 'integer')
            ->addForeignKey('tag_id', 'tags', 'id')
            ->addForeignKey('page_id', 'pages', 'id')
            ->create();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->dropTable('tagtopage');

        $this->dropTable('tags');
    }
}