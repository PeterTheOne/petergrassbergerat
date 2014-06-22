<?php

use Phinx\Migration\AbstractMigration;

class AddLanguages extends AbstractMigration
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
        $pagetype = $this->table('languages');
        $pagetype->addColumn('name', 'string')
            ->addColumn('tag', 'string')
            ->create();

        $this->execute('INSERT INTO languages (name, tag) VALUES ("English", "en");');
        $this->execute('INSERT INTO languages (name, tag) VALUES ("Deutsch", "de");');

        $pages = $this->table('pagecontents');
        $pages->addColumn('language', 'integer', array('default' => 1, 'after' => 'updated'))
            ->addForeignKey('language', 'languages', 'id')
            ->update();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $pages = $this->table('pagecontents');
        $pages->dropForeignKey('language')
            ->removeColumn('language')
            ->update();

        $this->dropTable('languages');
    }
}