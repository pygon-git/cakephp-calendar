<?php
namespace Qobo\Calendar\Events;

use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Network\Request;

class GetCalendarsListener implements EventListenerInterface
{
    /**
     * {@inheritDoc}
     */
    public function implementedEvents()
    {
        return [
            'Calendars.Model.getCalendars' => 'getCalendars',
        ];
    }

    /**
     * Get Calendar entities
     *
     * @param Cake\Event\Event $event passed through model
     * @param array $options containing model options
     * @param array $entities containing result set of Query execute.
     *
     * @return array $result containing filtered out calendars.
     */
    public function getCalendars(Event $event, $options, $entities)
    {
        $event->result = $entities;
    }
}
