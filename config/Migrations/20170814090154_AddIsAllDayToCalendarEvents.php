<?php
use Migrations\AbstractMigration;

class AddIsAllDayToCalendarEvents extends AbstractMigration
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
        $table->addColumn('is_allday', 'boolean', [
            'default' => 0,
            'null' => true,
        ]);
        $table->update();
    }
}
