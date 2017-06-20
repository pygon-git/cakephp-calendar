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

                $output[] = [
                    'action' => $actionName,
                    'calendar' => $calendar,
                    'events' => $resultEvents
                ];

                $progress->increment(100 / ++$calendarsProcessed);
                $progress->draw();
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