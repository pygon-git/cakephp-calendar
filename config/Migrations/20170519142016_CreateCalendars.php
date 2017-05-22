<?php
use Migrations\AbstractMigration;

class CreateCalendars extends AbstractMigration
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
        $table = $this->table('calendars', ['id' => false, 'primary_key' => ['id']]);
        $table->addColumn('id', 'uuid', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('name', 'string', [
            'default' => null,
            'null' => true,
        ]);
        $table->addColumn('color', 'string', [
            'default' => null,
            'null' => true,
        ]);
        $table->addColumn('icon', 'string', [
            'default' => null,
            'null' => true,
        ]);
        $table->addColumn('calendar_source_id', 'string', [
            'default' => null,
            'null' => true,
        ]);
        $table->addColumn('calendar_source', 'string', [
            'default' => null,
            'null' => true,
        ]);
        $table->addColumn('created', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('modified', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('trashed', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->addPrimaryKey([
            'id',
        ]);
        $table->create();
    }
}
