<?php
use Migrations\AbstractMigration;

class AddIndexesToCalendarEvents extends AbstractMigration
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
        $table->addIndex(['calendar_id']);
        $table->addIndex(['is_recurring']);
        $table->addIndex(['start_date']);
        $table->addIndex(['end_date']);
        $table->update();
    }
}
