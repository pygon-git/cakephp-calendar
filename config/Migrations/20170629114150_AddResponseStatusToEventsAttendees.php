<?php
use Migrations\AbstractMigration;

class AddResponseStatusToEventsAttendees extends AbstractMigration
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
        $table = $this->table('events_attendees');
        $table->addColumn('response_status', 'string', [
            'default' => null,
            'null' => true,
        ]);
        $table->update();
    }
}
