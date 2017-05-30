<?php
namespace Qobo\Calendar\Events;

use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Network\Request;
use Cake\ORM\TableRegistry;

class GetCalendarEventsListener implements EventListenerInterface
{
    /**
     * {@inheritDoc}
     */
    public function implementedEvents()
    {
        return [
            'Calendars.Model.getCalendarEvents' => 'getCalendarEvents',
            'Calendars.Model.getCalendarEventInfo' => 'getCalendarEventInfo',
        ];
    }

    /**
     * Get Calendar Events entities
     *
     * @param Cake\Event\Event $event passed through model
     * @param array $options containing model options
     *
     * @return array $result containing filtered out calendars.
     */
    public function getCalendarEvents(Event $event, $options = [])
    {
        $result = [];
        $table = TableRegistry::get('Qobo/Calendar.CalendarEvents');

        if (!empty($options['calendar_id'])) {
            $resultSet = $table->find()
                    ->where(
                        [
                            'calendar_id' => $options['calendar_id'],
                        ]
                    )->toArray();

            if (!empty($resultSet)) {
                $result = $resultSet;
            }
        }

        $event->result = $result;
    }

    /**
     * Get Calendar Event Info
     *
     * @param Cake\Event\Event $event being broadcasted
     * @param array $options with search conditions
     *
     * @return array $result containing event info
     */
    public function getCalendarEventInfo(Event $event, $options = [])
    {
        $result = [];
        $table = TableRegistry::get('Qobo/Calendar.CalendarEvents');

        if (!empty($options['id'])) {
            $result = $table->get($options['id'], ['contain' => 'Calendars']);
        }

        $event->result = $result;
    }
}
