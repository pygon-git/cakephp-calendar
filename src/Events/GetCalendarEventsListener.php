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
     * @param Cake\Table\Entity $calendar object
     * @param array $options containing model options
     *
     * @return void
     */
    public function getCalendarEvents(Event $event, $calendar, $options = [])
    {
        $result = [];

        $event->result = $result;
    }
}
