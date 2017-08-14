<?php
use Migrations\AbstractMigration;

class RemoveDurationFromCalendarEvents extends AbstractMigration
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
        $table->removeColumn('duration');
        $table->update();
    }
}
