<?php
use Migrations\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class CreateCalendarEvents extends AbstractMigration
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
        $table = $this->table('calendar_events', ['id' => false, 'primary_key' => ['id']]);
        $table->addColumn('id', 'uuid', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('calendar_id', 'uuid', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('event_source_id', 'string', [
            'default' => null,
            'null' => true,
        ]);
        $table->addColumn('event_source', 'string', [
            'default' => null,
            'null' => true,
        ]);
        $table->addColumn('title', 'string', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('content', 'text', [
            'default' => null,
            'limit' => MysqlAdapter::TEXT_LONG,
            'null' => false,
        ]);
        $table->addColumn('start_date', 'datetime', [
            'default' => null,
            'null' => true,
        ]);
        $table->addColumn('end_date', 'datetime', [
            'default' => null,
            'null' => true,
        ]);
        $table->addColumn('duration', 'time', [
            'default' => null,
            'null' => true
        ]);
        $table->addColumn('created', 'datetime', [
            'default' => null,
            'null' => false
        ]);
        $table->addColumn('modified', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('trashed', 'datetime', [
            'default' => null,
            'null' => true
        ]);
        $table->addPrimaryKey([
            'id',
        ]);
        $table->create();
    }
}
