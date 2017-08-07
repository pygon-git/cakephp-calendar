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

        $this->syncAttendees();
        $birthdays = $this->syncBirthdays($table);

        $this->out(null);
        $this->out('<success>Synchronization complete!</success>');

        if (true == $this->params['verbose']) {
            print_r($output);
        }

        return $output;
    }

    protected function syncAttendees()
    {
        //sync all the attendees from users.
        $usersTable = TableRegistry::get('Users');
        $users = $usersTable->find()->all();
        $attendeesTable = TableRegistry::get('Qobo/Calendar.CalendarAttendees');

        foreach ($users as $user) {
            if (empty($user->email)) {
                continue;
            }

            $existing = $attendeesTable->exists(['contact_details' => $user->email]);

            if (!$existing) {
                $entity = $attendeesTable->newEntity();

                $entity->display_name = $user->name;
                $entity->contact_details = $user->email;

                $saved = $attendeesTable->save($entity);
            }
        }
    }

    protected function syncBirthdays($table = null)
    {
        $result = [
            'error' => [],
            'added' => [],
            'updated' => [],
        ];

        $eventsTable = TableRegistry::get('Qobo/Calendar.CalendarEvents');
        $usersTable = TableRegistry::get('Users');
        $users = $usersTable->find()->all();

        $calendar = $table->find()
            ->where([
                'source' => 'Plugin__',
                'name' => 'Birthdays',
            ])->first();

        if (empty($calendar)) {
            $entity = $table->newEntity();
            $entity->name = 'Birthdays';
            $entity->source = 'Plugin__';
            $entity->color  = '#05497d';
            $entity->icon = 'birthday-cake';

            $calendar = $table->save($entity);
        }

        foreach ($users as $k => $user) {
            if (empty($user->birthdate)) {
                $result['error'][] = "User ID: {$user->id} doesn't have birth date in the system";
                continue;
            }

            $birthdayEvent = $eventsTable->find()
                ->where([
                    'calendar_id' => $calendar->id,
                    'title' => 'Birthday',
                    'content LIKE' => "%{$user->first_name} {$user->last_name}%",
                    'is_recurring' => 1,
                ])->first();

            if (!$birthdayEvent) {
                $entity = $eventsTable->newEntity();
                $entity->calendar_id = $calendar->id;
                $entity->title = 'Birthday';
                $entity->content = sprintf("%s %s", $user->first_name, $user->last_name);
                $entity->is_recurring = true;

                $entity->start_date = date('Y-m-d 09:00:00', strtotime($user->birthdate));
                $entity->end_date = date('Y-m-d 18:00:00', strtotime($user->birthdate));
                $entity->recurrence = json_encode(['RRULE:FREQ=YEARLY']);
                $birthdayEvent = $eventsTable->save($entity);

                $result['added'][] = $birthdayEvent;
            }
        }

        return $result;
    }
}
