<?php
namespace Qobo\Calendar\Shell\Task;

use Cake\Console\Shell;
use Cake\ORM\TableRegistry;

/**
 * Sync shell task.
 */
class SyncTask extends Shell
{

    /**
     * Manage available options via Parser
     *
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $parser->setDescription(
            __('Synchronize local and remote calendars with the database')
        );

        $parser->addOption('start', [
            'description' => __('Specify start interval for the events to fetch'),
            'help' => __("Start date 'YYYY-MM-DD HH:MM:SS' for events to fetch"),
        ]);

        $parser->addOption('end', [
            'description' => __('Specify end interval for the events to fetch'),
            'help' => __("End date 'YYYY-MM-DD HH:MM:SS' for events to fetch"),
        ]);

        return $parser;
    }

    /**
     * main() method.
     *
     * @return bool|int|null Success or error code.
     */
    public function main()
    {
        $calendarsProcessed = 1;
        $output = [];

        $progress = $this->helper('Progress');
        $progress->init();

        $this->info('Preparing for calendar sync...');

        $result = $options = [];
        $table = TableRegistry::get('Qobo/Calendar.Calendars');

        if (!empty($this->params['start'])) {
            $options['period']['start_date'] = $this->params['start'];
        }

        if (!empty($this->params['end'])) {
            $options['period']['end_date'] = $this->params['end'];
        }

        $result['calendars'] = $table->syncCalendars($options);

        if (empty($result['calendars'])) {
            $this->abort('No calendars found for synchronization');
        }

        foreach ($result['calendars'] as $actionName => $calendars) {
            foreach ($calendars as $k => $calendar) {
                $resultEvents = $table->syncCalendarEvents($calendar, $options);

                $resultAttendees = $table->syncEventsAttendees($calendar, $resultEvents);

                $output[] = [
                    'action' => $actionName,
                    'calendar' => $calendar,
                    'events' => $resultEvents,
                    'attendees' => $resultAttendees,
                ];

                $progress->increment(100 / ++$calendarsProcessed);
                $progress->draw();
            }
        }

        //sync all the attendees from users.
        $usersTable = TableRegistry::get('Users');
        $users = $usersTable->find()->all();
        $attendeesTable = TableRegistry::get('Qobo/Calendar.CalendarAttendees');
        foreach ($users as $user) {
            $existing = $attendeesTable->exists(['contact_details' => $user->email]);

            if (!$existing) {
                $entity = $attendeesTable->newEntity();

                $entity->display_name = $user->name;
                $entity->contact_details = $user->email;

                $saved = $attendeesTable->save($entity);
            }
        }

        $this->out(null);
        $this->out('<success>Synchronization complete!</success>');

        if (true == $this->params['verbose']) {
            print_r($output);
        }

        return $output;
    }
}
