<?php
use Migrations\AbstractMigration;

class AddEditableToCalendars extends AbstractMigration
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
        $table->addColumn('editable', 'boolean', [
            'default' => 1,
            'null' => true,
        ]);
        $table->update();
    }
}
