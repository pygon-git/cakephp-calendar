<?php
namespace Qobo\Calendar\Shell;

use Cake\Console\ConsoleOptionParser;
use Cake\Console\Shell;

/**
 * SyncCalendars shell command.
 */
class CalendarsShell extends Shell
{

    public $tasks = [
        'Qobo/Calendar.Sync',
    ];

    /**
     * Manage the available sub-commands along with their arguments and help
     *
     * @see http://book.cakephp.org/3.0/en/console-and-shells.html#configuring-options-and-generating-help
     *
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        $parser = new ConsoleOptionParser('console');

        $parser->description(
            'Calendars Shell helps you import and fetch calendars & ' .
            'events from cakephp-calendars plugin'
        )->addSubCommand('sync', [
            'help' => __('Synchronize calendars with the plugin'),
            'parser' => $this->Sync->getOptionParser(),
        ]);

        return $parser;
    }
}
