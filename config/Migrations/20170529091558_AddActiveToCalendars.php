<?php
use Migrations\AbstractMigration;

class AddActiveToCalendars extends AbstractMigration
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
        $table->addColumn('active', 'boolean', [
            'default' => false,
            'null' => false
        ]);
        $table->update();
    }
}
