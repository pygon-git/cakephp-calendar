<?php
use Migrations\AbstractMigration;

class AlterCalendars extends AbstractMigration
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
        $hasSource = $table->hasColumn('calendar_source');
        $hasSourceId = $table->hasColumn('calendar_source_id');

        if ($hasSource) {
            $table->renameColumn('calendar_source', 'source');
        }

        if ($hasSourceId) {
            $table->renameColumn('calendar_source_id', 'source_id');
        }
    }
}
