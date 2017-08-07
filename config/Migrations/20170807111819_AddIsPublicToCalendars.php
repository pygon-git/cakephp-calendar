<?php
use Migrations\AbstractMigration;

class AddIsPublicToCalendars extends AbstractMigration
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
        $table->addColumn('is_public', 'boolean', [
            'default' => 0,
            'null' => true,
        ]);
        $table->update();
    }
}
