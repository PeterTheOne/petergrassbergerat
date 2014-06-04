<?php

use Phinx\Migration\AbstractMigration;

class IndexPage extends AbstractMigration
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
        $table = $this->table('pages');
        $table->addColumn('index', 'boolean', array('default' => false, 'after' => 'created'))
            ->update();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $table = $this->table('pages');
        $table->removeColumn('index')
            ->update();
    }
}