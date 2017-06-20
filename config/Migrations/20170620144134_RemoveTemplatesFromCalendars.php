<?php
use Migrations\AbstractMigration;

class RemoveTemplatesFromCalendars extends AbstractMigration
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
        $table->removeColumn('templates');
        $table->update();
    }
}
