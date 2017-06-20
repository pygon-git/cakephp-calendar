<?php
use Migrations\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class AddTemplatesToCalendars extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $table = $this->table('calendars');
        $table->addColumn('templates', 'text', [
            'default' => null,
            'limit' => MysqlAdapter::TEXT_LONG,
            'null' => true,
        ]);
        $table->update();
    }
}
