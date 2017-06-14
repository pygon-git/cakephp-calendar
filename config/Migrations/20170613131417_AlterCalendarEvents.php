<?php
use Migrations\AbstractMigration;

class AlterCalendarEvents extends AbstractMigration
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
        $table = $this->table('calendar_events');
        $hasSource = $table->hasColumn('event_source');
        $hasSourceId = $table->hasColumn('event_source_id');

        if ($hasSource) {
            $table->renameColumn('event_source', 'source');
        }

        if ($hasSourceId) {
            $table->renameColumn('event_source_id', 'source_id');
        }
    }
}
