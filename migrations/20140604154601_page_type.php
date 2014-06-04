<?php

use Phinx\Migration\AbstractMigration;

class PageType extends AbstractMigration
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
        $pagetype = $this->table('pagetypes');
        $pagetype->addColumn('name', 'string')
            ->create();

        $this->execute('INSERT INTO pagetypes (name) VALUES ("page");');
        $this->execute('INSERT INTO pagetypes (name) VALUES ("project");');
        $this->execute('INSERT INTO pagetypes (name) VALUES ("post");');

        $pages = $this->table('pages');
        $pages->addColumn('page_type', 'integer', array('default' => 1))
            ->addForeignKey('page_type', 'pagetypes', 'id')
            ->update();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $pages = $this->table('pages');
        $pages->dropForeignKey('page_type')
            ->removeColumn('page_type')
            ->update();

        $this->dropTable('pagetypes');
    }
}