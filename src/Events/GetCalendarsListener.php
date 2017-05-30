<?php
namespace Qobo\Calendar\Events;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Network\Request;
use Cake\ORM\TableRegistry;

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
     *
     * @return void
     */
    public function getCalendars(Event $event, $options = [])
    {
        $table = TableRegistry::get('Qobo/Calendar.Calendars');

        $result = $table->getCalendars($options);

        $event->result = $result;
    }
}
